<?php

/**
 * Gestion d'une liste d'opérations bancaires sur un compte
 */

namespace App\Collection;

use Root\DB;
use App\{ Account, Operation };

class OperationCollection extends Collection {
	
	public const ORDER_BY_DATE = 'date';
	
	/**
	 * Classe du modèle à utiliser pour la récupération de la table, des colonnes et de l'instanciation des objets
	 * @var string
	 */
	protected $_model_class = Operation::class;
	
	/**************************************************************/
	
	/* FILTRES */
	
	/**
	 * Filtre par compte bancaire
	 * @param Account $account
	 * @return self
	 */
	public function account(Account $account) : self
	{
		$this->_query->where($this->_table . '.account_id', '=', $account->id);
		return $this;
	}
	
	/**
	 * Filtre les opérations non calculé
	 * @return self
	 */
	public function uncomputed() : self
	{
		$this->_query->where($this->_table . '.computed', '=', FALSE);
		return $this;
	}
	
	/**
	 * Filtre l'année
	 * @param int $year L'année à filtrer
	 * @return self
	 */
	public function year(?int $year) : self
	{
		if($year !== NULL)
		{
			$field = DB::expression('YEAR(' . $this->_table . '.date)');
			$this->_query->where($field, '=', $year);
		}
		return $this;
	}
	
	/**
	 * Filtre le mois 
	 * @param int $month Le mois à filtrer
	 * @return self
	 */
	public function month(?int $month) : self
	{
		if($month !== NULL)
		{
			$field = DB::expression('MONTH(' . $this->_table . '.date)');
			$this->_query->where($field, '=', $month);
		}
		return $this;
	}
	
	/**
	 * Filtre le jour
	 * @param string $date Date du jour à filtrer
	 * @return self
	 */
	public function day(string $date) : self
	{
		$this->_query->where($this->_table . '.date', '=', $date);
		return $this;
	}
	
	/**************************************************************/
	
	/* TRIS */
	
	/**
	 * Tri par date
	 * @param string $direction Sens de direction du tri
	 * @return self
	 */
	protected function _orderByDate(string $direction = self::DIRECTION_ASC) : self
	{
		$this->_query->orderBy($this->_table . '.date', $direction);
		return $this;
	}
	
	/**************************************************************/
	
}