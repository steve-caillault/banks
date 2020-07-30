<?php

/**
 * Gestion d'une liste de statistique d'un compte
 */

namespace App\Collection;

use Root\DB;
use App\{ 
	Account, 
	Account\Statistic as AccountStatistic,
	Owner
};

class AccountStatisticCollection extends Collection {
	
	const ORDER_BY_DATE = 'date';
	
	/**
	 * Classe du modèle à utiliser pour la récupération de la table, des colonnes et de l'instanciation des objets
	 * @var string
	 */
	protected $_model_class = AccountStatistic::class;
	
	/******************************************************/
	
	/* FILTRES */
	
	/**
	 * Filtre les comptes du propriétaire en paramètre
	 * @param Owner $owner
	 * @return self
	 */
	public function owner(Owner $owner) : self
	{
		$accountsTable = Account::$table;
		
		$this->_join($accountsTable, $this->_table . '.account_id', $accountsTable . '.id');
		$this->_query->where($accountsTable . '.owner_id', '=', $owner->id);
		
		return $this;
	}
	
	/**
	 * Filtre le compte
	 * @param Account $account
	 * @return self
	 */
	public function account(Account $account) : self
	{
		$this->_query->where($this->_table . '.account_id', '=', $account->id);
		return $this;
	}
	
	/**
	 * Filtre par type (année, mois ou jour)
	 * @param string $type
	 * @return self
	 */
	public function type(string $type) : self
	{
		$this->_query->where($this->_table . '.type', '=', $type);
		return $this;
	}
	
	/**
	 * Filtre les statistiques avant le timestamp en paramètre
	 * @param int $timestamp
	 * @return self
	 */
	public function until(int $timestamp) : self
	{
		$field = DB::expression('UNIX_TIMESTAMP(' . $this->_table . '.date)');
		$this->_query->where($field, '<=', $timestamp);
		return $this;
	}
	
	/**
	 * Filtre les statistiques après le timestamp en paramètre
	 * @param int $timestamp
	 * @return self
	 */
	public function since(int $timestamp) : self
	{
		$field = DB::expression('UNIX_TIMESTAMP(' . $this->_table . '.date)');
		$this->_query->where($field, '>=', $timestamp);
		return $this;
	}
	
	/******************************************************/
	
	/* TRIS */
	
	/**
	 * Tri par date
	 * @param string $direction
	 * @return self
	 */
	protected function _orderByDate(string $direction = self::DIRECTION_ASC) : self
	{
		$this->_query->orderBy($this->_table . '.date', $direction);
		return $this;
	}
	
	/******************************************************/
	
}