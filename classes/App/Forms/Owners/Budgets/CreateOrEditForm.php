<?php

/**
 * Création et édition d'un budget
 */

namespace App\Forms\Owners\Budgets;

use App\Forms\ProcessForm;
use App\{ Owner, Budget };
/***/
use Root\Arr;
use Root\Validation;

class CreateOrEditForm extends ProcessForm
{
	public const DATA_YEAR = 'year';
	public const DATA_TYPE = 'type';
	public const DATA_FREQUENCY = 'frequency';
	public const DATA_VALUE = 'value';
	public const DATA_NAME = 'name';
	
	/**********************************************************************************************************/
	
	/**
	 * Noms de champs autorisés
	 * @var array
	 */
	protected static $_allowed_names = [
		self::DATA_YEAR => self::FIELD_TEXT,
		self::DATA_TYPE => self::FIELD_SELECT,
		self::DATA_FREQUENCY => self::FIELD_SELECT,
		self::DATA_VALUE => self::FIELD_TEXT,
		self::DATA_NAME => self::FIELD_TEXT,
	];
	
	/**
	 * Données du formulaire
	 * @var array
	 */
	protected $_data = [
		self::DATA_YEAR => NULL,
		self::DATA_TYPE => NULL,
		self::DATA_FREQUENCY => NULL,
		self::DATA_VALUE => NULL,
		self::DATA_NAME => NULL,
	];
	
	/**
	 * Listes des labels
	 * @var array
	 */
	protected static $_labels = [
		self::DATA_YEAR => 'Année du budget',
		self::DATA_TYPE => 'Type d\'opération',
		self::DATA_FREQUENCY => 'Fréquence de l\'opération',
		self::DATA_VALUE => 'Valeur',
		self::DATA_NAME => 'Nom de l\'opération', 
	];
	
	/**
	 * Vrai si on doit afficher le titre du formulaire
	 * @var bool
	 */
	protected static $_with_title = FALSE;
	
	/**********************************************************************************************************/
	
	/**
	 * Propriétaire
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/**
	 * Budget édité
	 * @var Budget
	 */
	private $_budget = NULL;
	
	/**
	 * Année dont on gére le budget
	 * @var int
	 */
	private $_year = NULL;

	/**********************************************************************************************************/
	
	/* VALIDATION */
	
	/**
	 * Retourne l'objet Validation initialisé avec les réglés de validation
	 * @return Validation
	 */
	protected function _initValidation() : Validation
	{
		$types = [ Budget::TYPE_CREDIT, Budget::TYPE_DEBIT, ];
		$frequencies = [ Budget::FRENQUENCY_DAILY, Budget::FRENQUENCY_MONTHLY, Budget::FRENQUENCY_YEARLY, ];
		
		$rules = [
			self::DATA_YEAR => [
				array('required'),
				array('exact_length', array('length' => 4)),
				array('numeric'),
			],
			self::DATA_TYPE => [
				array('required'),
				array('in_array', array('array' => $types)),
			],
			self::DATA_FREQUENCY => [
				array('required'),
				array('in_array', array('array' => $frequencies)),
			],
			self::DATA_VALUE => [
				array('required'),
				array('numeric'),
			],
			self::DATA_NAME => [
				array('required'),
				array('min_length', array('min' => 3)),
				array('max_length', array('max' => 100)),
			],
		];
		
		$validation = Validation::factory([
			'data' 	=> $this->_data,
			'rules'	=> $rules,
		]);
		
		return $validation;
	}

	/**********************************************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Constructeur
	 * @param array $params Paramètres
	 * @return array
	 */
	protected function __construct(array $params)
	{
		$data = Arr::get($params, 'data');
		
		$year = Arr::get($params, 'year');
		
		// Chargement du propriétaire
		$this->_owner = Arr::get($params, 'owner', $this->_owner);
		if(! $this->_owner instanceof Owner)
		{
			abort(500, 'Le propriétaire est incorrect.');
		}
		
		// Chargement du budget
		$this->_budget = Arr::get($params, 'budget', $this->_budget);
		if($this->_budget !== NULL)
		{
			if(! $this->_budget instanceof Budget)
			{
				abort(500, 'Le budget est incorrect.');
			}
			$params['data'] = array_merge($this->_data, $this->_budget->asArray(), $data);
		}
		// Affectation de l'année
		elseif(is_numeric($year))
		{
			$this->_year = $year;
			$params['data'][self::DATA_YEAR] = $year;
		}
	
		parent::__construct($params);
	}

	/**********************************************************************************************************/
	
	/* TRAITEMENT DU FORMULAIRE */
	
	/**
	 * Méthode à exécuter si le formulaire est valide
	 * @return bool
	 */
	protected function _onValid() : bool
	{
		$budget = Budget::factory(array_merge($this->_data, [
			'id' => ($this->_budget === NULL) ? NULL : $this->_budget->id,
			'owner_id' => $this->_owner->id,
		]));
		
		return $budget->save();
	}
	
	/**
	 * Retourne l'URL de redirection où rediriger en cas de succès
	 * @return string
	 */
	public function redirectUrl() : ?string
	{
		if($this->success())
		{
			$year = Arr::get($this->_data, self::DATA_YEAR);
			return $this->_owner->budgetUri($year);
		}
		return NULL;
	}
	
	/**********************************************************************************************************/

	/* RENDU */
	
	/**
	 * Retourne les options du champs select dont le nom du champs est en paramètre
	 * @param string $name Nom du champs
	 * @return string
	 */
	protected function _selectOptions(string $name) : array
	{
		if($name == self::DATA_TYPE)
		{
			return [
				NULL => 'Sélectionnez un type',
				Budget::TYPE_CREDIT => 'Crédit',
				Budget::TYPE_DEBIT => 'Débit',
			];
		}
		elseif($name == self::DATA_FREQUENCY)
		{
			return [
				NULL => 'Sélectionnez une fréquence',
				Budget::FRENQUENCY_DAILY => 'Quotidien',
				Budget::FRENQUENCY_MONTHLY => 'Mensuel',
				Budget::FRENQUENCY_YEARLY => 'Annuel',
			];
		}
		
		return parent::_selectOptions($name);
	}
	
	/**
	 * Retourne le titre du formulaire
	 * @return string
	 */
	public function title() : string
	{
		$year = $this->_year;
		if($this->_budget === NULL)
		{
			$title = ($year === NULL) ? 'Création d\'un budget' : 'Ajout d\'un budget pour l\'année :year';
		}
		else
		{
			$title = 'Modification du budget :year pour l\'année :year';
		}
		
		return strtr($title, [
			':year' => $year,
		]);
	}
	
	/**********************************************************************************************************/
	
}