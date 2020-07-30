<?php

/**
 * Gestion d'une liste des banques
 */

namespace App\Collection;

use App\Bank;

class BankCollection extends Collection {
	
	public const ORDER_BY_NAME = 'name';
	
	/**
	 * Classe du modèle à utiliser pour la récupération de la table, des colonnes et de l'instanciation des objets
	 * @var string
	 */
	protected $_model_class = Bank::class;
	
	/**
	 * Tri par nom
	 * @param string $direction Sens de direction du tri
	 * @return self
	 */
	protected function _orderByName(string $direction = self::DIRECTION_ASC)
	{
		$this->_query->orderBy($this->_table . '.name', $direction);
		return $this;
	}
	
}