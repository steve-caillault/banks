<?php 

/**
 * Modèle abstrait
 * @author Stève Caillault
 */

namespace App;

use Root\DB;
use Root\Arr;

abstract class Model 
{	
	public const IMAGE_FORMAT_ORIGINAL	= 'original';
	public const IMAGE_FORMAT_MEDIUM	= 'medium';
	public const IMAGE_FORMAT_SMALL		= 'small';
	
	/****************************************************************************************************************************/
	
	/**
	 * Table du modèle
	 * @var string 
	 */
	public static $table			= '';
	
	/**
	 * Colonnes de la table du modèle
	 * @var array 
	 */
	protected static $_columns		= [];
	
	/**
	 * Clé primaire
	 * @var string
	 */
	protected static $_primary_key	= 'id';
	
	/**
	 * Vrai si la clé primaire est un auto-incrément
	 * @var bool
	 */
	protected static $_autoincrement	= TRUE;
	
	/**
	 * Classe par défaut pour instancier un objet de base (pour récupérer les propriétés)
	 * @var string
	 */
	protected static $_default_class	= NULL;
	
	/**
	 * Données en plus sélectionnées en base de données
	 * @var array
	 */
	protected $_more_data = [];
	
	/**
	 * Vrai si l'objet a été supprimé de la base de données 
	 * @var bool
	 */
	protected $_deleted					= FALSE;
	
	/**
	 * Chemin d'accès aux images des personnages
	 * @var string
	 */
	protected static $_images_directory = 'files/images/';
	
	/**
	 * Formats des images
	 * @var array
	 */
	protected static $_images_formats = [];
	
	/****************************************************************************************************************************/
	
	/* CONSTRUCTION ET INSTANCIATION */
	
	/**
	 * Constructeur
	 * @param mixed $params
	 */
	protected function __construct($params = NULL)
	{
		if($params !== NULL AND is_array($params))
		{			
			$this->_init($params);
		}
	}
	
	/**
	 * Retourne une instance d'un modèle
	 * @param mixed $params
	 * @return Model
	 */
	public static function factory($params = NULL) : ?self
	{
		if($params !== NULL)
		{
			if(is_array($params))
			{
				return static::_construct($params);
			}
			return static::_selectOne($params);
		}
		return NULL;
	}
	
	/**
	 * Méthode de construction statique à partir des paramètres nécessaire pour l'instanciation
	 * @param array $params Paramètre de l'objet
	 * @return self
	 */
	public static function _construct(array $params) : Model
	{
		return new static($params);
	}
	
	/****************************************************************************************************************************/
	
	/* MANIPULATION DE LA CLE PRIMAIRE ET DES COLONNES */
	
	/**
	 * Retourne si la clé primaire est à champs multiple
	 * @return bool
	 */
	private function _isMultiplePrimaryKey() : bool
	{
		return (is_string(static::$_primary_key) AND strpos(static::$_primary_key, '|') !== FALSE);
	}
	
	/**
	 * Retourne la valeur de la clé primaire
	 * @return string 
	 */
	protected function _primaryKey() : string
	{
		// Clé à champs multiple
		if($this->_isMultiplePrimaryKey())
		{
			$keys = explode('|', static::$_primary_key);
			$data = [];
			foreach($keys as $key)
			{
				$data[] = $this->{ $key };
			}
			return implode('|', $data);
		}
		// Clé à champs unique
		else
		{
			return $this->{ static::$_primary_key };
		}
	}
	
	/**
	 * Retourne un tableau des règles de sélection de la clé primaire
	 * @return array
	 */
	private function _primaryKeyRules() : array
	{
		// Clé à champs multiples
		if($this->_isMultiplePrimaryKey())
		{
			$keys = explode('|', static::$_primary_key);
			$rules = [];
			foreach($keys as $key)
			{
				$rules[$key] = $this->{$key};
			}
		}
		// Clé primaire à champs unique
		else
		{
			$rules = [
				static::$_primary_key => $this->{ static::$_primary_key },
			];
		}
		return $rules;
	}
	
	/**
	 * Retourne et affecte (si ce n'est pas le cas) les colonnes du mod�le
	 * @return array
	 */
	public static function columns() : array
	{
		if(! Arr::get(static::$_columns, static::$table))
		{
			$data = [];
			$prefix = static::$table . '.'; // Peut servir lorsqu'il y a des jointures
			$defaultValues = static::defaultValues();
			foreach($defaultValues as $key => $value)
			{
				$data[] = $prefix . $key;
			}
			
			// On affecte les champs au tableau de colonnes
			static::$_columns[static::$table] = $data;
		}
		return Arr::get(static::$_columns, static::$table);
	}
	
	/**
	 * Vérifit qu'un objet à la même clé primaire qu'un autre objet
	 * @param self $object
	 * @return bool
	 */
	public function samePrimaryKey(self $object) : bool
	{
		return ($this->_primaryKey() == $object->_primaryKey());
	}
	
	/****************************************************************************************************************************/
	
	/* RECHERCHE DES DONNEES */
	
	/**
	 * Recherche un modèle en fonction des critères en paramètres
	 * @param array $criterias Critères de recherche
	 * @param array $order Données pour le tri (champs et direction)
	 * @param bool $distinct Vrai si on doit ajouter DISTINCT lors de la sélection
	 * @return self
	 */
	public static function searchWithCriterias(array $criterias, ?array $order = NULL, bool $distinct = FALSE) : ?self
	{
		if(count($criterias) == 0)
		{
			exception('Il n\'y a pas de critére de recherche.');
		}
		
		$query = DB::select(static::columns())
			->distinct($distinct)
			->from(static::$table);
		
		foreach($criterias as $criteria)
		{
			$query->addCriteria($criteria);
		}
		
		if($order !== NULL)
		{
			$query->orderBy($order['field'], $order['direction']);
		}
		
		$response = $query->limit(1)->execute();
		$data = Arr::get($response, 0);
		$model = ($data !== NULL) ? static::factory($data) : NULL;
		return $model;
	}
	
	/**
	 * Retourne les données du modèle dont on fourni la valeur de la clé primaire en paramètre
	 * @param string $primaryKey Clé primaire
	 * @return self
	 */
	protected static function _selectOne(string $primaryKey) : ?self
	{
		$keys = explode('|', static::$_primary_key);
		$values = explode('|', $primaryKey);
		
		$criterias = [];
		
		$prepareCriterias = array_combine($keys, $values);
		foreach($prepareCriterias as $key => $value)
		{
			$criterias[] = [
				'left' => $key,
				'right' => $value,
			];
		}
		
		return static::searchWithCriterias($criterias);
	}
	
	/****************************************************************************************************************************/
	
	/* RETOURNE LES DONNES PAR DEFAUT ET LES VALEURS DE L'OBJET COURANT DANS UN TABLEAU */
	
	/**
	 * Retourne un tableau contenant les propriétés de l'objet
	 * @return array
	 */
	public function asArray() : array
	{
		$data = [];
		foreach($this as $key => $val) 
		{
			if($key[0] != '_')
			{
				$data[$key] = $val;
			}
		}
		return $data;
	}
	
	/**
	 * Retourne un objet avec les valeurs par défaut
	 * @return self
	 */
	public static function defaultObject() : self
	{
		if(static::$_default_class !== NULL)
		{
			return new static::$_default_class;
		}
		else
		{
			return new static;
		}
	}
	
	/**
	 * Retourne les valeurs par défaut de chaque propriété du modèle
	 * @return array
	 */
	public static function defaultValues() : array
	{
		return static::defaultObject()->asArray();
	}
	
	/**
	 * Retourne la données en plus dont on fournit la clé
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function moreData(string $key, $default = NULL)
	{
		return Arr::get($this->_more_data, $key, $default);
	}
	
	/****************************************************************************************************************************/
	
	/* INITIALISATION DES PROPRIETES DE L'OBJET */
	
	/**
	 * Initialisation des propriétés de l'objet
	 * @param array $params Les valeurs des propriétés à affecter
	 * @return void
	 */
	protected function _init(array $params) : void
	{
		$defaultValues = static::defaultValues();
		
		foreach($defaultValues as $property => $defaultValue)
		{
			$this->{ $property } = Arr::get($params, $property, $defaultValue);
			unset($params[$property]);
		}
		
		// Données en plus qui ont été sélectionné 
		if(count($params) > 0)
		{
			$this->_setMoreData($params);
		}
	}
	
	/**
	 * Affecte des données en plus à l'objet
	 * @param array $data
	 * @return void
	 */
	protected function _setMoreData(array $data) : void
	{
		$this->_more_data = $data;
	}
	
	/**
	 * Clone l'objet
	 */
	public function __clone()
	{
        foreach($this as $key => $value) 
		{
            if(is_object($value) OR (is_array($value))) 
			{
                $this->{ $key } = unserialize(serialize($value));
            }
        }
    }
	
	/****************************************************************************************************************************/
	
	/* SAUVEGARDE ET SUPPRESSION */
	
	/**
	 * Sauvegarde de l'objet en base de donn�es
	 * @return bool
	 */
	public function save() : bool
	{
		$success = FALSE;
		$create = FALSE;
		
		// Modification d'une propriété par sa valeur par défaut s'il s'agit d'une chaine vide
		$defaultValues = static::defaultValues();
		foreach($defaultValues as $property => $value)
		{
			if($this->{ $property } == '')
			{
				$this->{ $property } = $value;
			}
		}
		// Clé primaire à champs multiples ou pas de champs auto-incrément
		if($this->_isMultiplePrimaryKey() OR ! static::$_autoincrement)
		{
			$model = static::_selectOne($this->_primaryKey());
			if($model === NULL)
			{
				$create = TRUE;
			}
			$success = (($select === NULL) ? $this->create() : $this->update());
		}
		// La clé primaire est un champs auto-incrément
		elseif(static::$_autoincrement === TRUE)
		{
			if($this->{ static::$_primary_key } === NULL)
			{
				$create = TRUE;
				$success = $this->create();
			}
			else
			{
				$success = $this->update();
			}
		}
		
		if($success)
		{
			$this->_onSave($create);
		}
		
		return $success;
	}
	
	/**
	 * Méthode à exécuter à chaque sauvegarde
	 * @param bool $create Vrai s'il s'agit d'une création
	 * @return void
	 */
	protected function _onSave(bool $create = FALSE) : void
	{
		// Rien par défaut
	}

	/**
	 * Ajout de l'objet en base de donn�es
	 * @return bool
	 */
	public function create() : bool
	{
		$response = DB::insert(static::$table, static::columns())
			->addValues($this->asArray())
			->execute();
		
		if($response === 1)
		{
			if(static::$_autoincrement)
			{
				$id = $this->{ static::$_primary_key } = DB::lastInsertId();
				return ($id !== NULL);
			}
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Mise à jour de l'objet dans la base de donn�es
	 * @return bool
	 */
	public function update() : bool
	{
		$rules = $this->_primaryKeyRules();
		$query = DB::update(static::$table);
		// Ajout des régles de la clé primaire
		foreach($rules as $property => $value)
		{
			$query->where($property, '=', $value);
		}
		// Mise à jour
		$response = $query->set($this->asArray())->execute(); // Nombre de ligne affectées
		
		return ($response === 1);
	}
	
	/**
	 * Suppression de l'objet en base de donn�es
	 * @return bool
	 */
	public function delete() : bool
	{
		$rules = $this->_primaryKeyRules();
		$query = DB::delete(static::$table);
		
		// Ajout des régles de la clé primaire
		foreach($rules as $property => $value)
		{
			$query->where($property, '=', $value);
		}
		// Suppression
		$response = $query->execute();
		
		if($response > 0)
		{
			$this->_deleted = TRUE;
		}
		
		return ($response === 1);
	}
	
	/****************************************************************************************************************************/
	
	/* METHODES POUR L'ENREGISTREMENT DES IMAGES */
	
	/**
	 * Retourne le répertoire où stocker les images
	 * @return array
	 */
	protected function _imagesDirectory() : string
	{
		return static::$_images_directory;
	}
	
	/**
	 * Retourne les formats des images à créer avec leurs dimensions
	 * @return array
	 */
	protected function _imagesFormats() : array
	{
		return static::$_images_formats;
	}
	
	/**
	 * Retourne le nom de fichier sans l'extension
	 * @return string
	 */
	protected function _filename() : string
	{
		$filename = NULL;
		
		// Le nom de fichier est l'identifiant unique
		if(! $this->_isMultiplePrimaryKey())
		{
			$filename = $this->{ static::$_primary_key };
		}
		
		// Si le nom de fichier n'a pas pu être identifié, on en génére un aléatoirement
		if($filename === NULL)
		{
			$filename = md5(uniqid(mt_rand(1, 1000)));
		}
		
		return $filename;
	}
	
	/****************************************************************************************************************************/
	
}