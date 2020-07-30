<?php

/**
 * Gestion d'une liste de propriétaires de compte 
 */

namespace App\Collection;

use Root\DB;
use App\Owner;

class OwnerCollection extends Collection {
	
	public const ORDER_BY_NAME = 'name';
	
	/**
	 * Classe du modèle à utiliser pour la récupération de la table, des colonnes et de l'instanciation des objets
	 * @var string
	 */
	protected $_model_class = Owner::class;
	
	/**
	 * Tri par nom
	 * @param string $direction Sens de direction du tri
	 * @return self
	 */
	protected function _orderByName(string $direction = self::DIRECTION_ASC)
	{
		$table = $this->_table;
		$field = strtr('CONCAT(:field1, :field2)', [
			':field1' => $table . '.last_name',
			':field2' => $table . '.first_name',
		]);
		$this->_query->orderBy(DB::expression($field), $direction);
		return $this;
	}
	
}