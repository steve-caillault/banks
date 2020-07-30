<?php

/**
 * Gestion d'une liste de comptes
 */

namespace App\Collection;

use Root\DB;
use App\{ Account, Owner };

class AccountCollection extends Collection {
	
	/**
	 * Tri par date d'initialisation du compte
	 */
	public const ORDER_BY_DATE = 'date';
	
	/**
	 * Classe du modèle à utiliser pour la récupération de la table, des colonnes et de l'instanciation des objets
	 * @var string
	 */
	protected $_model_class = Account::class;
	
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

	/************************************************************/
	
	/* TRI */
	
	/**
	 * Tri par date d'initialisation
	 * @param string $direction Sens de direction du tri
	 * @return self
	 */
	public function _orderByDate(string $direction = self::DIRECTION_ASC) : self
	{
		$field = DB::expression('UNIX_TIMESTAMP(' . $this->_table . '.date_initial)');
		$this->_query->orderBy($field, $direction);
		return $this;
	}
	
	/************************************************************/
	
}