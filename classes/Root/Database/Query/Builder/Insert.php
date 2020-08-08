<?php

/**
 * Gestion d'un constructeur de requête INSERT
 * @author Stève Caillault
 */

namespace Root\Database\Query\Builder;
use Root\Database\Query\Builder;

class Insert extends Builder {
	
	/**
	 * Type de requête
	 * @var string
	 */
	protected string $_type	= self::TYPE_INSERT;
	
	/**
	 * Vrai s'il faut ignorer les clés étrangères
	 * @var bool
	 */
	private bool $_ignore = FALSE;
	
	/**
	 * Valeur à mettre à jour s'il y a un conflit de clé
	 * @var array
	 */
	private array $_on_duplicate_data = [];
	
	/**
	 * Tableau des valeurs à insérer
	 * @var array
	 */
	private array $_values = [];
	
	/************************************************************************/
	
	/**
	 * Constructeur 
	 * @param string $table Table où insérer
	 * @return void
	 */
	protected function __construct(string $table)
	{
		$this->from($table);
	}

	/**
	 * Instanciation
	 * @param string $table Table où insérer
	 * @return self
	 */
	public static function factory(string $table) : self
	{
		return new self($table);
	}
	
	/************************************************************************/
	
	/**
	 * Active / désactive la vérification des clés étangères
	 * @param bool $ignore Si vrai, on ignore la vérification des clés étangères
	 * @return self
	 */
	public function ignore(bool $ignore) : self
	{
		$this->_ignore = $ignore;
		return $this;
	}
	
	/**
	 * Affecte les champs à affecter lors de l'insertion
	 * @var array $fields
	 * @return self
	 */
	public function fields(array $fields) : self
	{
		$this->select($fields);
		return $this;
	}
	
	/**
	 * Affecte les données à mettre à jour s'il y a un conflit de clé
	 * @param array $data Données à mettre à jour en base de données
	 * @return self
	 */
	public function onDuplicateUpdate(array $data) : self
	{
		$this->_on_duplicate_data = $data;
		return $this;
	}
	
	/**
	 * Ajoute les valeurs à insérer
	 * @param array $data Tableau de valeurs à insérer
	 * @return self
	 */
	public function addValues(array $data) : self
	{
		$this->_values[] = $data;
		return $this;
	}
	
	/**
	 * Retourne la requête à éxécuter
	 * @return string
	 */
	protected function _query() : string
	{
		// @todo Gérer les quotes
		
		$query = 'INSERT';
		if($this->_ignore)
		{
			$query .= ' IGNORE';
		}
		$query .= ' INTO ';
		
		// Table où insérer
		$query .= implode(', ', $this->_tables) . ' ';
		
		// Champs 
		if(count($this->_select) > 0)
		{
			$query .= '(' . implode(', ', $this->_select) . ')';
		}
		
		$query .= ' VALUES ';
		
		// Valeurs à insérer
		$values = [];
		foreach($this->_values as $groupValues)
		{
			foreach($groupValues as $key => $value)
			{
				$groupValues[$key] = $this->_fieldValue($value);
			}
			$values[] = '(' . implode(', ', $groupValues) . ')';
		}
		$values = implode(', ', $values);
		$query .= $values;
		
		// Gestion ON DUPLICATE KEY UPDATE
		if(count($this->_on_duplicate_data) > 0)
		{
			$updateData = [];
			foreach($this->_on_duplicate_data as $key => $value)
			{
				$updateData[] = implode(' = ', [ 
					$this->_fieldValue($key),
					$this->_fieldValue($value),
				]);
			}
			
			$query .= ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updateData);
		}
		
		return $query;
	}
	
	/************************************************************************/
	
}
