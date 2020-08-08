<?php

/**
 * Gestion du propriétaire d'un compte 
 */

namespace App;

use Root\Route;
use App\{ Collection\AccountCollection };

class Owner extends Model {
	
	/**
	 * Table du modèle
	 * @var string
	 */
	public static $table = 'owners';
	
	/**********************************************************************************************************/
	
	/* PROPRIETES EN BASE DE DONNEES */
	
	/**
	 * Identifiant 
	 * @var int
	 */
	public $id = NULL;
	
	/**
	 * Identifiant de l'utilisateur gérant le compte
	 * @var int
	 */
	public $user_id = NULL;
	
	/**
	 * Prénom
	 * @var string
	 */
	public $first_name = NULL;
	
	/**
	 * Nom
	 * @var string
	 */
	public $last_name = NULL;
	
	/**********************************************************************************************************/
	
	/**
	 * Comptes du propriétaire
	 * @var array
	 */
	private $_accounts = NULL;
	
	/**********************************************************************************************************/
	
	/* GET */
	
	/**
	 * Retourne la liste des comptes du propriétaire
	 * @return array
	 */
	public function accounts() : array
	{
		if($this->_accounts === NULL)
		{
			$this->_accounts = AccountCollection::factory()->owner($this)->get();
		}
		return $this->_accounts;
	}
	
	/**********************************************************************************************************/
	
	/**
	 * Retourne le nom comlplet du propriétaire
	 * @return string
	 */
	public function fullName() : string
	{
		return trim(implode(' ', [
			$this->first_name, $this->last_name,
		]));
	}
	
	/**********************************************************************************************************/
	
	/* URLs */
	
	/**
	 * Retourne l'URI de la liste des propriétaires
	 * @return string
	 */
	public static function listUri() : string
	{
		return Route::retrieve('owners.list')->uri();
	}
	
	/**
	 * Retourne l'URI de création d'un propriétaire
	 * @return string
	 */
	public static function addUri() : string
	{
		return Route::retrieve('owners.add')->uri();
	}
	
	/**
	 * Retourne l'URI d'édition d'un propriétaire
	 * @return string
	 */
	public function editUri() : string
	{
		return Route::retrieve('owners.edit')->uri([
			'ownerId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI de la liste des comptes du propriétaire
	 * @return string
	 */
	public function accountsUri() : string
	{
		return Route::retrieve('owners.accounts.list')->uri([
			'ownerId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI de création d'un compte pour le propriétaire
	 * @return string
	 */
	public function addAccountUri() : string
	{
		return Route::retrieve('owners.accounts.add')->uri([
			'ownerId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI de l'évolution des comptes du propriétaire
	 * @return string
	 */
	public function evolutionUri() : string
	{
		return Route::retrieve('owners.evolution')->uri([
			'ownerId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI de comparaison des années des comptes du propriétaire
	 * @return string
	 */
	public function comparisonUri() : string
	{
		return Route::retrieve('owners.comparison')->uri([
			'ownerId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI de la liste des budgets
	 * @return string  
	 */
	public function budgetListUri() : string
	{
		return Route::retrieve('owners.budgets.list')->uri([
			'ownerId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI de l'année d'un budget
	 * @param int $year
	 * @return string
	 */
	public function budgetUri(int $year) : string
	{
		return Route::retrieve('owners.budgets.year')->uri([
			'ownerId' => $this->id,
			'year' => $year,
		]);
	}
	
	/**
	 * Retourne l'URI d'initialisation d'un budget
	 * @return string
	 */
	public function initBudgetUri() : string
	{
		return Route::retrieve('owners.budgets.init')->uri([
			'ownerId' => $this->id,
		]);
	}
	
	/**
	 * Retourne l'URI de l'ajout de budget
	 * @param int $year L'année dont on gére le budget
	 * @return string
	 */
	public function addBudgetUri(int $year) : string
	{
		return Route::retrieve('owners.budgets.add')->uri([
			'ownerId' => $this->id,
			'year' => $year,
		]);
	}
	
	/**********************************************************************************************************/
	
}