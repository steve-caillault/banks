<?php

/**
 * Gestion d'un constructeur de requête SELECT
 * @author Stève Caillault
 */

namespace Root\Database\Query\Builder;
use Root\Database\Query\Builder;

class Select extends Builder {
	
	/**
	 * Type de requête
	 * @var string
	 */
	protected $_type	= self::TYPE_SELECT;
	
	/**
	 * Vrai si la selection utilise DISTINCT
	 * @var boolean
	 */
	private $_distinct	= FALSE;
	
	/************************************************************************/
	
	/**
	 * Constructeur 
	 * @param array $fields Champs à sélectionner
	 * @return void
	 */
	protected function __construct(array $fields)
	{
		$this->select($fields);
	}

	/**
	 * Instanciation
	 * @param array $fields Champs à sélectionner
	 * @return Select
	 */
	public static function factory(array $fields) : Select
	{
		return new self($fields);
	}
	
	/**
	 * Active la sélection de champs distinct
	 * @param bool $distinct Si vrai, active la sélection de champs distinct
	 * @return Select
	 */
	public function distinct(bool $distinct = FALSE) : self
	{
		$this->_distinct = $distinct;
		return $this;
	}
	
	/**
	 * Retourne la requête à éxécuter
	 * @return string
	 */
	protected function _query() : string
	{
		// @todo Gérer les quotes
		
		// Champs à sélectionner
		$fields = (count($this->_select) == 0) ? '*' : implode(', ', $this->_select);
		if($this->_distinct)
		{
			$fields = 'DISTINCT(' . $fields . ')';
		}
		
		// Tables
		$tables = implode(', ', $this->_tables);
		
		// Clauses WHERE
		$where = $this->_whereCompiled();
		
		// Jointures
		$joins = NULL;
		foreach($this->_joins as $join)
		{
			$joinType = ' ' . trim($join['type'] . ' JOIN');
			$joins .= $joinType.' (' . implode(', ', $join['tables']) . ')';
			$on = NULL;
			foreach($join['rules'] as $rule)
			{
				if($on !== NULL)
				{
					$on .= ' AND ';
				}
				$on .= implode(' ', $rule);
			}
			$joins .= ' ON (' . $on . ')';
		}
		
		// Group by
		$groupBy = NULL;
		if(count($this->_group_by) > 0)
		{
			$groupBy = ' GROUP BY ' . implode(', ', $this->_group_by);
		}
		
		// Order by
		$orderBy = NULL;
		
		if(count($this->_order_by) > 0)
		{
			$orderBy = ' ORDER BY ' . implode(', ', $this->_order_by); 
		}
		
		$query = 'SELECT ' . $fields . ' FROM ' . $tables . $joins . $where . $groupBy . $orderBy;
		
		// LIMIT et OFFSET
		if($this->_limit !== NULL)
		{
			$query .= ' LIMIT ' . $this->_limit;
			if($this->_offset !== NULL)
			{
				$query .= ' OFFSET ' . $this->_offset;
			}
		}
		
		return $query;
	}
}
