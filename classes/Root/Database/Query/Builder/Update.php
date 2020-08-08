<?php

/**
 * Gestion d'un constructeur de requête UPDATE
 * @author Stève Caillault
 */

namespace Root\Database\Query\Builder;
use Root\Database\Query\Builder;

class Update extends Builder {
	
	/**
	 * Type de requête
	 * @var string
	 */
	protected string $_type = self::TYPE_UPDATE;
	
	/**
	 * Tableau des modification à effectuer
	 * @var array
	 */
	private array $_set = [];
	
	/************************************************************************/
	
	/**
	 * Constructeur
	 * @param string $table Table où supprimer
	 */
	protected function __construct(string $table)
	{
		$this->from($table);
	}
	
	/**
	 * Instanciation
	 * @param string $table Table où supprimer
	 * @return self
	 */
	public static function factory(string $table) : self
	{
		return new self($table);
	}
	
	/************************************************************************/
	
	/**
	 * Affecte les données à mettre à jour
	 * @param array $data
	 * @return self
	 */
	public function set(array $data) : self
	{
		foreach($data as $key => $value)
		{
			$queryValue = $this->_fieldValue($value);
			$this->_set[] = $key .' = ' . $queryValue;
		}
		return $this;
	}
	
	/************************************************************************/
	
	/**
	 * Retourne la chaine SET compilée
	 * return string
	 */
	protected function _setCompiled() : ?string
	{
		$set = NULL;
		if(count($this->_set) > 0)
		{
			$set .= ' SET '.implode(', ', $this->_set);
		}
		return $set;
	}
	
	/**
	 * Retourne la requête à éxécuter
	 * @return string
	 */
	protected function _query() : string
	{
		$tables = implode(', ', $this->_tables);
		$where = $this->_whereCompiled();
		$set = $this->_setCompiled();
	
		$query = 'UPDATE ' . $tables . $set . $where;
		
		return $query;
	}
	
	/************************************************************************/
	
}