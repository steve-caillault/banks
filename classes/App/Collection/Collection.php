<?php

/**
 * Gestion d'une liste de modèle
 */

namespace App\Collection;

use ArrayObject;
use Root\Database\Query\Builder\Select as QueryBuilder;
use Root\{ DB, Arr, Str };
use App\Model;

abstract class Collection extends ArrayObject
{
    /**
     * Sens de tri croissant
     */
    public const DIRECTION_ASC = 'ASC';
    /**
     * Sens de tri décroissant
     */
   public const DIRECTION_DESC = 'DESC';
    
	/****************************************************************************************************************************/
    
	/**
	 * La base de données sur laquelle éxécuter la requête
	 * @var string
	 */
	protected $_database = 'default';
	
	/**
	 * Classe du modèle à utiliser pour la récupération de la table, des colonnes et de l'instanciation des objets
	 * @var string
	 */
	protected $_model_class = NULL;
   
    /**
     * Table du modèle
     * @var string
     */
    protected $_table = NULL;
    
    /**
     * Colonnes à charger
     * @var array
     */
    protected $_columns = NULL;
    
    /**
     * Requête à éxécuter
     * @var QueryBuilder
     */
    protected $_query = NULL;
    
    /**
     * Liste des jointures à effectuer
     * @var array
     */
    private $_joins = [];
    
    /**
     * Nombre d'éléments de la liste
     * @var int
     */
    protected $_total_count = NULL;
    
    /**
     * Sens de tri possible
     * @var array
     */
    protected static $_authorized_direction	= [
        self::DIRECTION_ASC, self::DIRECTION_DESC,
    ];
    
    /**
     * Liste des identifiants de la liste
     * @var array
     */
    protected $_ids	= NULL;
    
    /****************************************************************************************************************************/
    
    /* CONSTRUCTEUR ET INSTANCIATION */
    
    /**
     * Constructeur
     * @param mixed $params
     */
    public function __construct($params = NULL)
    {
        $this->_query = DB::select($this->_columns())
            ->from($this->_table());
    }
    
    /**
     * Instanciation
     * @return self
     */
    public static function factory($params = NULL) : self
    {
        return new static($params);
    }
    
    /****************************************************************************************************************************/
    
    /* GESTION DES JOINTURES */
    
    /**
	 * Ajoute une jointure à la requête si elle ne l'a pas déjà été
	 * @param string $tableToJoin La table avec laquelle on doit effectuer la jointure
	 * @param string $leftColumn
	 * @param string $rightColumn
	 * @param string $type Le type de jointure
	 * @param string $operator Le type d'opérateur
	 * @return self
	 */
    protected function _join(string $tableToJoin, string $leftColumn, string $rightColumn, string $type = QueryBuilder::JOIN, ?string $operator = QueryBuilder::WHERE_EQUALS) : self
	{
		$this->_joinMultipleRules($tableToJoin, [
			[
				'left'		=> $leftColumn,
				'operator'	=> $operator,
				'right'		=> $rightColumn,
			]
		], $type);
		return $this;
	}
	
	/**
	 * Ajoute une jointure avec plusieurs règles 
	 * @param string $tableToJoin La table avec laquelle on doit effectuer la jointure
	 * @param array $rules Règles de la joiture : [
	 *		[
	 *			'left'		=> <string>,	// Membre de gauche
	 *			'operator'	=> <string>,	// Opérateur de la règle à utiliser
	 *			'right'		=> <string>,	// Membre de droite
	 *		]
	 *		...
	 * ]
	 * @param string $type Type de jointure
	 * @return self
	 */
	protected function _joinMultipleRules(string $tableToJoin, array $rules, string $type = QueryBuilder::JOIN) : self
	{
		if(! array_key_exists($tableToJoin, $this->_joins))
		{
            $this->_query->join($tableToJoin, $type);
            foreach($rules as $rule)
            {
                $this->_query->on($rule['left'], $rule['operator'], $rule['right']);
            }
            
			$this->_joins[$tableToJoin] = TRUE;
		}
		
		return $this;
	}
    
    /****************************************************************************************************************************/
    
    /* RETOURNE LA TABLE ET LES COLONNES A CHARGER */
	
	/**
	 * Modifit les colonnes à sélectionner
	 * @param array $columns
	 * @return self
	 */
	public function select(array $select) : self
	{
		$this->_query->select($select);
		return $this;
	}
    
    /**
     * Retourne le nom de la table
     * @return string
     */
    private function _table() : string
    {
        if($this->_table === NULL)
        {
            $model = $this->_modelClass();
            $this->_table = $model::$table;
        }
        return $this->_table;
    }
    
    /**
     * Retourne un tableau des champs de la table à charger
     * @return array
     */
    private function _columns() : array
    {
        if($this->_columns === NULL)
        {
            $model = $this->_modelClass();
            $this->_columns = $model::columns();
        }
        return $this->_columns;
    }
    
    /**
     * Retourne la classe du modèle à utiliser
     * @return string
     */
    private function _modelClass() : string
    {
    	if($this->_model_class === NULL)
    	{
    		$collectionClass = get_class($this);
    		$this->_model_class = substr($collectionClass, 0, strpos($collectionClass, 'Collection'));
    	}
    	return $this->_model_class;
    }
    
    /****************************************************************************************************************************/
    
    /**
     * Retourne la liste des identifiants de la liste
     * @return array
     */
    public function ids() : array
    {
        // @todo Gestion des clés primaires à champs multiples
        if($this->_ids === NULL)
        {
            foreach($this as $model)
            {
                if(! property_exists($model, 'id'))
                {
                    return NULL;
                }
                $this->_ids[] = $model->id;
            }
        }
        return $this->_ids;
    }
    
    /**
     * Exclus la liste des identifiants en paramètre
     * @param array $excludeIds
     * @return self
     */
    public function excludeIds(array $excludeIds) : self
    {
    	$this->_query->where($this->_table . '.id', 'NOT IN', $excludeIds);
    	return $this;
    }
    
    /****************************************************************************************************************************/
    
    /* EXECUTION DES REQUÊTES */
    
    /**
     * Retourne le nombre total de résultats
     * @param bool $clone Doit-on clôner la requête pour la conserver en l'état ou peut-on l'utiliser
     * @return int
     */
    public function totalCount(bool $clone = TRUE) : int
    {
        if($this->_total_count === NULL)
        {
            $this->_total_count = 0;
            
            $query = ($clone) ? (clone $this->_query) : $this->_query;
            
            $query->limit = NULL;
            $query->offset = NULL;
            
            $response = $query->select([
                DB::expression('COUNT(*) AS nb'),
            ])->execute($this->_database);
            
           ;
            
            $firstItem = Arr::get($response, 0);
            $this->_total_count = ($firstItem !== NULL) ? (int) (Arr::get($firstItem, 'nb')) : 0;
        }
        return $this->_total_count;
    }
    
    /**
     * Exécute la requête
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get(?int $limit = NULL, int $offset = 0) : array
    {
        $this->exchangeArray([]);
        if($limit !== NULL)
        {
            $this->_query
                ->limit($limit)
                ->offset($offset);
        }
        
        $response = $this->_query->execute($this->_database);
        
        $collection = [];
         
        if(count($response) > 0)
        {
            foreach($response as $objectData)
            {
                try
                {
                    $model = $this->_instanceModel($objectData);
                    if($model)
                    {
                        $collection[] = $model;
                    }
                }
                catch(\Exception $e)
                {
                    continue;
                }
            }
        }
        
        return $collection;
    }
    
    /**
     * Méthode d'instanciation des modèles
     * @param array Données pour instancier l'objet
     * @return Model
     */
    protected function _instanceModel(array $data) : ?Model
    {
        $modelClass = $this->_modelClass();
        return call_user_func($modelClass . '::factory', $data);
    }
    
    /****************************************************************************************************************************/
    
    /* METHODES DE TRIS */
    
    /**
	 * Méthode de tris
	 * @param string $type Type de tris
	 * @param string $direction Sens de tris ASC|DESC
	 * @return self
	 */
	public function orderBy(string $type, string $direction = self::DIRECTION_ASC) : self
	{
		$method = '_orderBy' . ucfirst(Str::camelCase($type));
		if(in_array($direction, static::$_authorized_direction))
		{
			if(method_exists($this, $method))
			{
				call_user_func_array([ $this, $method ], [ $direction ]);
			}
			else
			{
				exception(strtr('La méthode :method n\'existe pas.', [ ':method' => $method, ]));
			}
		}	
		else
		{
		    exception('Direction du tri incorrecte.');
		}
		
		return $this;
	}
    
    /****************************************************************************************************************************/
	
	/**
	 * Groupe les données
	 * @param string $field
	 * @return self
	 */
	public function groupBy(string $field) : self
	{
		$this->_query->groupBy($field);
		return $this;
	}
	
    /****************************************************************************************************************************/
    
}