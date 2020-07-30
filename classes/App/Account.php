<?php

/**
 * Gestion d'un compte bancaire
 */

namespace App;

use Root\{ Route, DB };
use App\Collection\{ Collection, OperationCollection };

class Account extends Model {
	
	/**
	 * Table du modèle
	 * @var string
	 */
	public static $table = 'accounts';
	
	/***************************************************************************************************/
	
	/* PROPRIETES EN BASE DE DONNEES */
	
	/**
	 * Identifiant
	 * @var int
	 */
	public $id = NULL;
	
	/**
	 * Identifiant du propriétaire du compte
	 * @var int
	 */
	public $owner_id = NULL;
	
	/**
	 * Identifiant de la banque gérant le compte
	 * @var int
	 */
	public $bank_id = NULL;
	
	/**
	 * Nom
	 * @var string
	 */
	public $name = NULL;
	
	/**
	 * Valeur initiale du compte
	 * @var float
	 */
	public $amount_initial = 0;
	
	/**
	 * Valeur courante du compte
	 * @var float
	 */
	public $amount_current = 0;
	
	/**
	 * Date initiale du compte
	 * @var string
	 */
	public $date_initial = NULL;
	
	/***************************************************************************************************/
	
	/**
	 * Propriétaire du compte
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/***************************************************************************************************/
	
	/**
	 * Affecte, retourne le propriétaire du compte
	 * @param Owner $owner Si renseigné, le propriétaire du compte à affecter
	 * @return Owner
	 */
	public function owner(?Owner $owner = NULL) : ?Owner
	{
		if($owner !== NULL)
		{
			$this->_owner = $owner;
			$this->owner_id = $owner->id;
		}
		elseif($this->_owner === NULL AND $this->owner_id !== NULL)
		{
			$this->_owner = Owner::factory($this->owner_id);
		}
		return $this->_owner;
	}
	
	/**
	 * Retourne les opérations sur le compte
	 * @param int $limit
	 * @param int offset
	 * @param int $year 
	 * @param int $month
	 * @return array Tableau avec les opérations et le nombre total d'éléments pour la pagination
	 */
	public function operations(int $limit = 20, int $offset = 0, ?int $year, ?int $month) : array
	{
		$collection = OperationCollection::factory()
			->account($this)
			->year($year)
			->month($month)
			->orderBy(OperationCollection::ORDER_BY_DATE, Collection::DIRECTION_DESC);
			
		$totalItems = $collection->totalCount();	
		$operations = $collection->get($limit, $offset);
		foreach($operations as $operation)
		{
			$operation->account($this); // Affectation du compte pour ne pas avoir à faire une requête SQL pour le retourner
		}
		return [
			'count' => $totalItems,
			'items' => $operations,
		];
	}
	
	/**
	 * Retourne les années où il y a eu des opérations sur le compte
	 * @return array
	 */
	public function operationYears() : array
	{
		$table = Operation::$table;
		$select = strtr('DISTINCT(CONCAT(YEAR($field), \'-\', MONTH($field))) AS `date`', [
			'$field' => $table . '.date',
		]);
		
		$years = [];
		
		$response = DB::select([
			DB::expression($select),
		])->from($table)->where($table . '.account_id', '=', $this->id)->execute();
	
		foreach($response as $data)
		{
			list($year, $month) = explode('-', $data['date']);
			if(! array_key_exists($year, $years))
			{
				$years[$year] = [];
			}
			$years[$year][$month] = str_pad($month, 2, '0', STR_PAD_LEFT);
			ksort($years[$year]);
		}
		
		krsort($years);
		
		return $years;
	}
	
	/***************************************************************************************************/
	
	/* FORMATAGE DES DONNEES */
	
	/**
	 * Retourne la valeur courante formatée du compte
	 */
	public function currentAmountFormat() : string
	{
		$value = number_format($this->amount_current, 2, ',', ' ');
		return ($value . ' &euro;');
	}
	
	/***************************************************************************************************/
	
	/* URIs */
	
	/**
	 * Retourne l'URI d'édition du compte
	 * @return string
	 */
	public function editUri() : string
	{
		return Route::retrieve('owners.accounts.edit')->uri([
			'ownerId' => $this->owner_id,
			'accountId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI de la liste des opérations d'un compte
	 * @return string 
	 */
	public function operationsUri() : string
	{
		return Route::retrieve('owners.accounts.operations.list')->uri([
			'ownerId' => $this->owner_id,
			'accountId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI de l'évolution de la valeur du compte
	 * @return string 
	 */
	public function evolutionUri() : string
	{
		return Route::retrieve('owners.accounts.evolution')->uri([
			'ownerId' => $this->owner_id,
			'accountId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI d'ajout d'une opération au compte
	 * @return string
	 */
	public function addOperationUri() : string
	{
		return Route::retrieve('owners.accounts.operations.add')->uri([
			'ownerId' => $this->owner_id,
			'accountId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI de calcul de la valeur du compte
	 * @return string
	 */
	public function computeUri() : string
	{
		return Route::retrieve('owners.accounts.operations.compute')->uri([
			'ownerId' => $this->owner_id,
			'accountId' => $this->id,
		]);
	}
	
	/***************************************************************************************************/
	
}