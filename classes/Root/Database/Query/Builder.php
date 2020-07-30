<?php

/**
 * Gestion d'une requête à une base de données
 * @author Stève Caillault
 */

namespace Root\Database\Query;

use Root\Database;
use Root\Arr;
/***/
use Root\Database\Query\Builder\Select as BuilderSelect;

abstract class Builder
{
	
	/**
	 * Type d'une requête SELECT
	 */
	public const TYPE_SELECT = 'SELECT'; 
	
	/**
	 * Type d'une requête INSERT
	 */
	public const TYPE_INSERT = 'INSERT';
	
	/**
	 * Type d'une requête UPDATE
	 */
	public const TYPE_UPDATE = 'UPDATE';
	
	/**
	 * Type d'une requête DELETE
	 */
	public const TYPE_DELETE = 'DELETE';
	
	/***/
	
	/**
	 * Jointure par défaut
	 */
	public const JOIN = '';
	
	/**
	 * Jointure à gauche
	 */
	public const JOIN_LEFT = 'LEFT';
	
	/**
	 * Jointure à droite
	 */
	public const JOIN_RIGHT	= 'RIGHT';
	
	/**
	 * Types de jointure autorisés
	 */
	private const _JOIN_TYPES = [
		self::JOIN, self::JOIN_LEFT, self::JOIN_RIGHT,	
	];
	
	/***/
	
	/**
	 * Clause AND
	 */
	public const CLAUSE_AND	= 'AND';
	
	/**
	 * Clause OR
	 */
	public const CLAUSE_OR = 'OR';
	
	/**
	 * Types de clause WHERE autorisés
	 */
	private const _WHERE_TYPES = [
		self::CLAUSE_AND, self::CLAUSE_OR,
	];
	
	/***/
	
	/**
	 * Opérateur IS (IS NULL, IS NOT NULL)
	 */
	protected const WHERE_IS = 'IS';
	
	/**
	 * Opérateur = 
	 */
	public const WHERE_EQUALS = '=';
	
	/**
	 * Opérateur >
	 */
	public const WHERE_GREATER = '>';
	
	/**
	 * Opérateur >=
	 */
	public const WHERE_GREATER_EQUALS = '>=';
	
	/**
	 * Opérateur <
	 */
	public const WHERE_LESS = '<';
	
	/**
	 * Opérateur <=
	 */
	public const WHERE_LESS_EQUALS = '<=';
	
	/**
	 * Opérateur !=
	 */
	public const WHERE_DIFFERENT = '!=';
	
	/**
	 * Opérateur IN
	 */
	public const WHERE_IN = 'IN';
	
	/**
	 * Opérateur NOT IN
	 */
	public const WHERE_NOT_IN = 'NOT IN';
	
	/**
	 * Opérateur LIKE
	 */
	public const WHERE_LIKE = 'LIKE';
	
	/**
	 * Opérateur NOT LIKE
	 */
	public const WHERE_NOT_LIKE = 'NOT LIKE';
	
	/**
	 * Types d'opérateurs WHERE autorisés
	 */
	private const _WHERE_OPERATORS = [
		self::WHERE_IS,
		self::WHERE_EQUALS,
		self::WHERE_GREATER,
		self::WHERE_GREATER_EQUALS,
		self::WHERE_LESS,
		self::WHERE_LESS_EQUALS,
		self::WHERE_DIFFERENT,
		self::WHERE_IN,
		self::WHERE_NOT_IN,
		self::WHERE_LIKE,
		self::WHERE_NOT_LIKE,
	];
	
	/***/
	
	/**
	 * Sens de direction de tri ascendant
	 */
	public const DIRECTION_ASC = 'ASC';
	
	/**
	 * Sens de direction de tri descendant
	 */
	public const DIRECTION_DESC = 'DESC';
	
	/**
	 * Sens de direction de tri autorisés
	 */
	private const _DIRECTIONS = [
		self::DIRECTION_ASC, self::DIRECTION_DESC,	
	];
	
	/***/
	
	/**
	 * Type de requête
	 * @var string
	 */
	protected $_type		= NULL;
	
	/**
	 * Table où effectuer les opérations
	 * @var array
	 */
	protected $_tables		= array();
	
	/**
	 * Tableau des champs à sélectionner pour une requête SELECT
	 * @var array
	 */
	protected $_select		= array();
	
	/**
	 * Tableau des jointures à effectuer
	 * @var array
	 */
	protected $_joins		= array();
	
	/**
	 * Tableau des clauses WHERE
	 * @var array
	 */
	protected $_where		= array();
	
	/**
	 * Tableau des GROUP BY
	 * @var array
	 */
	protected $_group_by	= array();
	
	/**
	 * Tableau des ORDER BY
	 * @var array
	 */
	protected $_order_by	= array();
	
	/**
	 * Nombre d'éléments affectés par la requête
	 * @var int
	 */
	protected $_limit		= NULL;
	
	/**
	 * Nombre des premiers éléments à exclure de la requête
	 * @var int
	 */
	protected $_offset		= NULL;
	
	/***/
	
	/**
	 * Liste des requêtes exécutées
	 * @var array
	 */
	protected static $_queries = [];
	
	/********************************************************************************/
	
	/**
	 * Retourne si le type de jointure en paramètre est autorisé
	 * @param string $type
	 * @return bool
	 */
	private static function _allowedJoinType(string $type) : bool
	{
		return in_array($type, self::_JOIN_TYPES);
	}
	
	/**
	 * Retourne si le type de clause WHERE en paramètre est autorisé
	 * @param string $type
	 * @return bool
	 */
	private static function _allowedWhereClause(string $type) : bool
	{
		return in_array($type, self::_WHERE_TYPES);
	}
	
	/**
	 * Retourne si l'opérateur where en paramètre est autorisé
	 * @param string $type 
	 * @return bool
	 */
	private static function _allowedWhereOperator(string $operator) : bool
	{
		return in_array($operator, self::_WHERE_OPERATORS);
	}
	
	/**
	 * Retourne si le sens du tri est autorisé
	 * @param string $direction
	 * @return bool
	 */
	private static function _allowedDirection(string $direction) : bool
	{
		return in_array($direction, self::_DIRECTIONS);
	}
	
	/********************************************************************************/
	
	/**
	 * Modifit les tables où effectuer les opérations
	 * @param array $tables Tables où effectuer les opérations
	 * @return self
	 */
	public function tables(array $tables) : self
	{
		foreach($tables as $key => $table)
		{
			$tables[$key] = '`' . $table . '`';
		}
		
		$this->_tables = $tables;
		return $this;
	}
	
	/**
	 * Modifit les tables où effectuer les opérations
	 * @param string $tables Tables où effectuer les opérations
	 * @return self
	 */
	public function from(string $tables) : self
	{
		$tables = explode(',', $tables);
		return $this->tables($tables);
	}
	
	/**
	 * Modifit les champs où effectuer les opérations pour une requête SELECT
	 * @param array $fields Champs à sélectionner
	 * @return self
	 */
	public function select(array $fields) : self
	{
		$this->_select = array_map(function($field) {
			return static::_fieldCompiled($field);
		}, $fields);
		return $this;
	}
	
	/**
	 * Ajoute un GROUP BY
	 * @param string $field Champs à grouper
	 * @return self
	 */
	public function groupBy(string $field) : self
	{
		if(! array_key_exists($field, $this->_group_by))
		{
			$this->_group_by[$field] = static::_fieldCompiled($field); 
		}
		return $this;
	}
	
	/**
	 * Ajoute un ORDER_BY
	 * @param string|Expression $field Champs à trier
	 * @param string $string 
	 */
	public function orderBy($field, $direction = self::DIRECTION_ASC) : self
	{
		$direction = strtoupper($direction);
		if(! self::_allowedDirection($direction))
		{
			exception('Sens de direction du tri incorrect.');
		}
		
		$expression = $this->_fieldValue($field);

		
		$key = strtr($expression, [ '`' => '']);
		
		if(! array_key_exists($key, $this->_order_by))
		{
			$this->_order_by[$key] = $expression . ' ' . $direction;
		}
		
		return $this;
	}
	
	/**
	 * Modifit le nombre d'éléments affectés
	 * @param int $limit
	 * @return self
	 */
	public function limit(int $limit) : self
	{
		$this->_limit = $limit;
		return $this;
	}
	
	/**
	 * Modifit le nombre de premier éléments exclus de la requête
	 * @param int $offset
	 * @return self
	 */
	public function offset(int $offset) : self
	{
		$this->_offset = $offset;
		return $this;
	}
	
	/********************************************************************************/
	
	/**
	 * Ajoute une jointure effectuée sur une à plusieurs table
	 * @param array $tables Tables à joindre
	 * @param string $type Type de jointure
	 * @return self
	 */
	public function joinMultipleTables(array $tables, string $type = self::JOIN) : self
	{
		$type = strtoupper($type);
		// Vérifit si le type de jointure est autorisé
		if(! self::_allowedJoinType($type))
		{
			exception('Le type de jointure n\'est pas autorisé.');
		}
		
		foreach($tables as $key => $table)
		{
			$tables[$key] = '`' . $table . '`';
		}
		
		$this->_joins[] = array(
			'tables'	=> $tables,
			'type'		=> $type,
			'rules'		=> [],
		);
		
		return $this;
	}
	
	/**
	 * Ajoute une jointure (écriture simplifiée)
	 * @param string Tables à joindre
	 * @param string $type Type de jointure
	 * @return self  
	 */
	public function join(string $tables, string $type = self::JOIN) : self
	{
		$tables = explode(',', $tables);
		
		return $this->joinMultipleTables($tables, $type);
	}
	
	/**
	 * Ajoute les règles à la dernière jointure
	 * @param array $rules Régles à appliquer à la jointure
	 * @return self
	 */
	private function _onRules(array $rules) : self
	{
		$latestJoin = end($this->_joins);

		if($latestJoin === FALSE)
		{
			exception('Pas de jointure.');
		}
		
		$keyJoin = key($this->_joins);	
		
		foreach($rules as $rule)
		{
			$this->_joins[$keyJoin]['rules'][] = $rule;
		}
		
		return $this;
	}
	
	/**
	 * Ajoute la règle à la dernière jointure
	 * @param string $field1
	 * @param string $operator
	 * @param string $field2
	 * @return self
	 */
	public function on(string $field1, string $operator = self::WHERE_EQUALS, string $field2) : self
	{
		// Test si l'opérateur est valide
		if(! self::_allowedWhereOperator($operator))
		{
			exception('L\'opérateur est incorrect.');
		}
		
		return $this->_onRules(array(
			$this->_whereRule($field1, $operator, $field2),
		));
	}
	
	/********************************************************************************/
	
	/**
	 * Ajoute une expression WHERE complexe (gestion des parenthèses)
	 * @param callable $callable 
	 * @param string $whereType Type de clause WHERE (AND ou OR)
	 * @return self
	 */
	public function whereExpression(callable $callable, string $whereType = self::CLAUSE_AND) : self
	{
		// On utilise un objet BuilderSelect pour gérer un tableau WHERE isolé
		$builder = new BuilderSelect([]); 
		$builder->_tables = $this->_tables;
		$builder->_select = $this->_select;
		
		// Exécute les clauses WHERE de l'utilisateur
		$callable($builder);
		
		$where = '';
		if(count($builder->_where) > 0)
		{
			$where = '(' . implode(' ', $builder->_where) . ')';
		}
		
		// Ajoute l'expression WHERE dans l'objet Builder initial
		if($where != '')
		{
			if(count($this->_where) > 0)
			{
				$where = ' ' . $whereType . ' ' . $where;
			}
			$this->_where[] = $where;
		}
		
		return $this;
	}
	
	/**
	 * Ajoute un critére de recherche dont on fournit les paramètres en base de données
	 * @param array $params Paramètres du critére
	 * @return self
	 */
	public function addCriteria(array $params) : self
	{
		// Si on fait la jointure avec une table
		$joinTable = Arr::get($params, 'table_to_join');
		$joinType = Arr::get($params, 'join_type', self::JOIN);
		
		$left = Arr::get($params, 'left');
		$operator = Arr::get($params, 'operator', self::WHERE_EQUALS);
		$right = Arr::get($params, 'right');
		$whereType = Arr::get($params, 'where_type', self::CLAUSE_AND);
		
		// Si on ne fait pas de jointure, on ajoute une clause WHERE
		if($joinTable === NULL)
		{
			return $this->_addWhereClause([
				'left' => $left,
				'operator' => $operator,
				'right' => $right,
			], $whereType);
		}
		// Exécute une jointure
		else
		{
			return $this->join($joinTable, $joinType)->on($left, $operator, $right);
		}
	}
	
	/**
	 * Ajoute une clause AND WHERE 
	 * @param mixed $field1
	 * @param string $operator
	 * @param mixed $field2
	 * @return self
	 */
	public function where($field1, string $operator = self::WHERE_EQUALS, $field2) : self
	{
		return $this->_addwhereClause([
			'left' => $field1,
			'operator' => $operator,
			'right' => $field2,
		], self::CLAUSE_AND);
	}
	
	/**
	 * Ajoute une clause OR WHERE
	 * @param string $field1
	 * @param string $operator
	 * @param mixed $field2
	 * @return self
	 */
	public function orWhere(string $field1, string $operator = self::WHERE_EQUALS, $field2) : self
	{
		return $this->_addwhereClause([
			'left' => $field1,
			'operator' => $operator,
			'right' => $field2,
		], self::CLAUSE_OR);
	}
	
	/**
	 * Ajoute une clause WHERE
	 * @param array $rule Paramètre de la clause WHERE
	 * @param string $whereType Type de clause WHERE (AND ou OR)
	 * @return self
	 */
	private function _addwhereClause(array $params, string $whereType) : self
	{
		if(! self::_allowedWhereClause($whereType))
		{
			exception('Type de clause incorrect.');
		}
		
		$rule = $this->_whereRule($params['left'], $params['operator'], $params['right']);
		
		$where = '';
		if(count($this->_where) > 0)
		{
			$where .= ' ' . $whereType . ' ';
		}
		$where .= $rule['left'] . ' ' . $rule['operator'] . ' ' . $rule['right'];
		$this->_where[] = $where;
		
		return $this;
	}
	
	/**
	 * Retourne une règle d'une clause WHERE
	 * @param mixed $field1
	 * @param string $operator
	 * @param mixed $field2
	 * @return array
	 */
	 private function _whereRule($field1, string $operator = self::WHERE_EQUALS, $field2) : array
	 {
	 	if($field2 === NULL)
	 	{
	 		$operator = self::WHERE_IS;
	 	}
	 	
	 	// Test si l'opérateur est valide
	 	if(! self::_allowedWhereOperator($operator))
	 	{
	 		exception('L\'opérateur est incorrect.');
	 	}
	 	
	 	$field2Value = $this->_fieldValue($field2);
	 		
	 	$rule = [
	 		// On part du principe que le paramètre de gauche sera un champs
	 		'left'		=> static::_fieldCompiled($field1), // ($isField($field1)) ? static::_fieldCompiled($field1) : '\'' . $field1 . '\'',
			'operator'	=> $operator,
	 		'right'		=> $field2Value,
		];
	 	
	 	
	 	return $rule;
	 }
	
	/********************************************************************************/
	
	/**
	 * Exécute la requête
	 * @param string $name Nom de la base de données sur laquelle appliquer la requête
	 * @return mixed
	 */
	public function execute(string $name = Database::INSTANCE_DEFAULT)
	{
		$response = Database::instance($name)->execute($this);
		static::$_queries[] = $this->_query();
		return $response;
	}
	
	/**
	 * Exécute la requête en paramètre en base de données
	 * @param string $query Requête à exécuter
	 * @param string $database Nom de la base de données sur laquelle appliquer la requête
	 * @return void
	 */
	public static function executeQuery(string $query, string $database) : void
	{
		$response = Database::instance($database)->executeQuery($query);
		static::$_queries[] = $query;
	}
	
	/**
	 * Retourne la chaine des clauses WHERE compilées
	 * @return string
	 */
	protected function _whereCompiled() : ?string
	{
		// Clauses WHERE
		$where = NULL;
		
		if(count($this->_where) > 0)
		{
			if($where !== NULL)
			{
				$where .= ' ';
			}
			$where .= ' WHERE '.implode('', $this->_where);
		}
		return $where;
	}
	
	/**
	 * Retourne la chaine du champs avec les quotes
	 * @param string|Expression $field
	 * @return string
	 */
	private static function _fieldCompiled($field) : string
	{
		if($field instanceof Expression)
		{
			
			return $field->getValue();
		}
		
		return implode('.', array_map(function($value) {
		    return '`' . $value . '`';
		}, explode('.', $field)));
	}
	
	/**
	 * Retourne si la valeur en paramètre est un champs en base de données
	 * @param mixed $value
	 * @return bool
	 */
	private function _isField($value) : bool
	{
		$selects = $this->_select;
		
		$stringIsField = function(string $value) use ($selects) : bool {
			// On vérifit que le champs est dans la liste des champs select
			foreach($selects as $queryField)
			{
				if(strpos($queryField, '.') !== FALSE)
				{
					list($table, $field) = explode('.', $queryField);
				}
				else
				{
					$field = $queryField;
				}
				
				if($value == trim($field, '`'))
				{
					return TRUE;
				}
			}
			return FALSE;
		};
		
		if(! is_string($value) OR is_numeric($value))
		{
			return FALSE;
		}
		elseif(preg_match('/^[\.a-zA-Z0-9_]+\.[\.a-zA-Z0-9_]+$/D', $value))
		{
			$values = explode('.', $value);
			$isField = FALSE;
			foreach($values as $currentValue)
			{
				$isField = $stringIsField($currentValue);
			}
			return $isField;
		}
		elseif(preg_match('/^[\.a-zA-Z0-9_]+$/D', $value))
		{
			return $stringIsField($value);
		}
		return FALSE;
	}
	
	/**
	 * Retourne la valeur du champs en paramètre
	 * @param mixed $value
	 * @return string
	 */
	protected function _fieldValue($value) : string
	{
		// Détermine la valeur de la partie de droite
		if($this->_isField($value))
		{
			return static::_fieldCompiled($value);
		}
		elseif($value === NULL)
		{
			return 'NULL';
		}
		elseif(is_array($value))
		{
			return '(' . implode(',', $value) . ')';
		}
		elseif(is_bool($value))
		{
			return (($value) ? 1 : 0);
		}
		elseif(is_numeric($value))
		{
			return $value;
		}
		elseif($value instanceof Expression)
		{
			return $value->getValue();
		}
		else
		{
			return '\'' . addslashes($value) . '\'';
		}
	}
	
	/**
	 * Retourne la requête à éxécuter
	 * @return string
	 */
	abstract protected function _query() : string;
	
	/**
	 * Retourne la liste des requêtes exécutées
	 * @return array
	 */
	public static function queries() : array
	{
		return self::$_queries;
	}
	
	/**
	 * Retourne la réquête à éxécuter
	 * @return string
	 */
	public function queryCompiled() : string
	{
		$query = $this->_query();
		// echo $query . '<br /><br />';
		return $query;
	}
	
	/**
	 * Retourne le type de la requête
	 * @return string
	 */
	public function type() : string
	{
		return $this->_type;
	}
	
	/********************************************************************************/
	
}