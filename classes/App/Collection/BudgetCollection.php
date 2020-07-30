<?php

/**
 * Gestion d'une liste de budget
 */

namespace App\Collection;

use App\{ Owner, Budget };

class BudgetCollection extends Collection {
	
	/**
	 * Tri par date d'initialisation du compte
	 */
	public const ORDER_BY_YEAR = 'year';
	
	/**
	 * Classe du modèle à utiliser pour la récupération de la table, des colonnes et de l'instanciation des objets
	 * @var string
	 */
	protected $_model_class = Budget::class;
	
	/************************************************************/
	
	/* FILTRES */
	
	/**
	 * Filtre le propriétaire
	 * @param Owner $owner
	 * @return self
	 */
	public function owner(Owner $owner) : self
	{
		$this->_query->where($this->_table . '.owner_id', '=', $owner->id);
		return $this;
	}
	
	/**
	 * Filtre l'année
	 * @param int $year
	 * @return self
	 */
	public function year(int $year) : self
	{
		$this->_query->where($this->_table . '.year', '=', $year);
		return $this;
	}
	
	/************************************************************/
	
	/* TRI */
	
	/**
	 * Tri par année
	 * @param string $direction Sens de direction du tri
	 * @return self
	 */
	public function _orderByYear(string $direction = self::DIRECTION_ASC) : self
	{
		$this->_query->orderBy($this->_table . '.year', $direction);
		return $this;
	}
	
	/************************************************************/
	
}