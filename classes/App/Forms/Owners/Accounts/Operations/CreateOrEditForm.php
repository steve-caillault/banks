<?php

/**
 * Création et édition d'une opéération bancaire
 */

namespace App\Forms\Owners\Accounts\Operations;

use App\{
	Forms\ProcessForm,
	Account,
	Operation
};
/***/
use Root\{
	Arr,
	Validation
};

class CreateOrEditForm extends ProcessForm
{
	public const DATA_TYPE = 'type';
	public const DATA_NAME = 'name';
	public const DATA_AMOUNT = 'amount';
	public const DATA_DATE = 'date';
	
	/**
	 * Nom du formulaire
	 * @var string
	 */
	public static $name	= 'operation-create-or-edit';
	
	/**********************************************************************************************************/
	
	/**
	 * Noms de champs autorisés
	 * @var array
	 */
	protected static $_allowed_names = [
		self::DATA_TYPE => self::FIELD_SELECT,
		self::DATA_NAME => self::FIELD_TEXT,
		self::DATA_AMOUNT => self::FIELD_TEXT,
		self::DATA_DATE => self::FIELD_DATE,
	];
	
	/**
	 * Données du formulaire
	 * @var array
	 */
	protected $_data = [
		self::DATA_TYPE => NULL,
		self::DATA_NAME => NULL,
		self::DATA_AMOUNT => 0,
		self::DATA_DATE => NULL,
	];
	
	/**
	 * Listes des labels
	 * @var array
	 */
	protected static $_labels = [
		self::DATA_TYPE => 'Type',
		self::DATA_NAME => 'Nom',
		self::DATA_AMOUNT => 'Montant',
		self::DATA_DATE => 'Date',
	];
	
	/**
	 * Vrai si on doit afficher le titre du formulaire
	 * @var bool
	 */
	protected static $_with_title = FALSE;
	
	/**********************************************************************************************************/
	
	/**
	 * Opération à éditer
	 * @var Operation
	 */
	private $_operation = NULL;
	
	/**
	 * Compte affecté par l'opération
	 * @var Account
	 */
	private $_account = NULL;
	
	/**********************************************************************************************************/
	
	/* VALIDATION */
	
	/**
	 * Retourne l'objet Validation initialisé avec les réglés de validation
	 * @return Validation
	 */
	protected function _initValidation() : Validation
	{
		$rules = [
			self::DATA_TYPE => [
				array('required'),
				array('in_array', array('array' => [ Operation::TYPE_CREDIT, Operation::TYPE_DEBIT, ])),
			],
			self::DATA_NAME => [
				array('required'),
				array('min_length', array('min' => 5)),
				array('max_length', array('max' => 100)),
			],
			self::DATA_AMOUNT => [
				array('required'),
				array('numeric'),
			],
			self::DATA_DATE => [
				array('required'),
				array('date',  array('format' => 'Y-m-d',)),
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
		
		$this->_account = Arr::get($params, 'account', $this->_account);
		if($this->_account === NULL)
		{
			exception('Le compte bancaire est manquant.');
		}
		
		$this->_operation = Arr::get($params, 'operation', $this->_operation);
		if($this->_operation !== NULL)
		{
			$this->_data = array_merge($this->_data, $this->_operation->asArray(), $data);
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
		$operation = Operation::factory(array_merge($this->_data, [
			'id' => $this->_operation->id ?? NULL,
			'account_id' => $this->_account->id,
		]));
		
		return $operation->save();
	}
	
	/**
	 * Retourne l'URL de redirection où rediriger en cas de succès
	 * @return string
	 */
	public function redirectUrl() : ?string
	{
		if($this->success())
		{
			return $this->_account->operationsUri();
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
		$data = [];
		
		if($name == self::DATA_TYPE)
		{
			$data = [
				NULL => 'Choisissez un type',
				Operation::TYPE_CREDIT => 'Crédit',
				Operation::TYPE_DEBIT => 'Débit',
			];
		}
		
		return $data;
	}
	
	/**
	 * Retourne le titre du formulaire
	 * @return string
	 */
	public function title() : string
	{
		$params = [
			':account_name' => $this->_account->name,
		];
		
		if($this->_operation === NULL)
		{
			$message = 'Ajout d\'une opération pour :account_name';
		}
		else
		{
			$params[':operation_name'] = $this->_operation->name;
			$message = 'Modification de :operation_name de :account_name';
		}
		
		return strtr($message, $params);
	}
	
	/**********************************************************************************************************/
	
}