<?php

/**
 * Gestion d'un constructeur de requête DELETE
 * @author Stève Caillault
 */

namespace Root\Database\Query\Builder;
use Root\Database\Query\Builder;

class Delete extends Builder {
	
	/**
	 * Type de requête
	 * @var string
	 */
	protected string $_type = self::TYPE_DELETE;
	
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
	 * @return Delete
	 */
	public static function factory(string $table) : self
	{
		return new self($table);
	}
	
	/************************************************************************/
	
	/**
	 * Retourne la requête à éxécuter
	 * @return string
	 */
	protected function _query() : string
	{
		$tables = implode(', ', $this->_tables);
		$where = $this->_whereCompiled();
		
		$query = 'DELETE FROM ' . $tables . $where;
		
		return $query;
	}
	
	/************************************************************************/
	
}