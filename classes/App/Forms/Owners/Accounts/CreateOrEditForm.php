<?php

/**
 * Création et édition d'un compte bancaire
 */

namespace App\Forms\Owners\Accounts;

use App\{
	Forms\ProcessForm,
	Account,
	Owner, 
	Bank, 
	Collection\BankCollection
};
/***/
use Root\{
	Arr,
	Validation
};

class CreateOrEditForm extends ProcessForm
{
	public const DATA_BANK_ID = 'bank_id';
	public const DATA_NAME = 'name';
	public const DATA_AMOUNT_INITIAL = 'amount_initial';
	public const DATA_DATE_INITIAL = 'date_initial';
	
	/**
	 * Nom du formulaire
	 * @var string
	 */
	public static $name	= 'owners-account-create-or-edit';
	
	/**********************************************************************************************************/
	
	/**
	 * Noms de champs autorisés
	 * @var array
	 */
	protected static $_allowed_names = [
		self::DATA_BANK_ID => self::FIELD_SELECT,
		self::DATA_NAME => self::FIELD_TEXT,
		self::DATA_AMOUNT_INITIAL => self::FIELD_TEXT,
		self::DATA_DATE_INITIAL => self::FIELD_DATE,
	];
	
	/**
	 * Données du formulaire
	 * @var array
	 */
	protected $_data = [
		self::DATA_BANK_ID => NULL,
		self::DATA_NAME => NULL,
		self::DATA_AMOUNT_INITIAL => 0,
		self::DATA_DATE_INITIAL => NULL,
	];
	
	/**
	 * Listes des labels
	 * @var array
	 */
	protected static $_labels = [
		self::DATA_BANK_ID => 'Banque',
		self::DATA_NAME => 'Nom du compte',
		self::DATA_AMOUNT_INITIAL => 'Valeur initiale',
		self::DATA_DATE_INITIAL => 'Date initiale',
	];
	
	/**
	 * Vrai si on doit afficher le titre du formulaire
	 * @var bool
	 */
	protected static $_with_title = FALSE;
	
	/**********************************************************************************************************/
	
	/**
	 * Propriétaire du compte
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/**
	 * Compte à éditer
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
			self::DATA_BANK_ID => [
				array('required'),
				array('model_exists', [
					'class' 	=> Bank::class,
					'criterias' => [
						'id'	=> Arr::get($this->_data, self::DATA_BANK_ID),
					],
				]),
			],
			self::DATA_NAME => [
				array('required'),
				array('min_length', array('min' => 5)),
				array('max_length', array('max' => 100)),
			],
			self::DATA_AMOUNT_INITIAL => [
				array('required'),
				array('numeric'),
			],
			self::DATA_DATE_INITIAL => [
				array('required'),
				array('date', array('format' => 'Y-m-d',)),
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
		
		$this->_owner = Arr::get($params, 'owner', $this->_owner);
		$this->_account = Arr::get($params, 'account', $this->_account);
		
		if($this->_owner === NULL)
		{
			exception('Le propriétaire du compte est manquant.'); 
		}
		
		if($this->_account !== NULL)
		{
			$this->_data = array_merge($this->_data, $this->_account->asArray(), $data);
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
		$account = Account::factory(array_merge($this->_data, [
			'id' => $this->_account->id ?? NULL,
			'owner_id' => $this->_owner->id,
			'amount_current' => $this->_account->amount_current ?? Arr::get($this->_data, self::DATA_AMOUNT_INITIAL),
			'date_initial' => Arr::get($this->_data, self::DATA_DATE_INITIAL),
		]));
		
		return $account->save();
	}
	
	/**
	 * Retourne l'URL de redirection où rediriger en cas de succès
	 * @return string
	 */
	public function redirectUrl() : ?string
	{
		if($this->success())
		{
			return $this->_owner->accountsUri();
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
		
		if($name == self::DATA_BANK_ID)
		{
			$banks = BankCollection::factory()->get();
			$data[NULL] = 'Choisissez une banque';
			foreach($banks as $bank)
			{
				$data[$bank->id] = $bank->name;
			}
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
			':owner_name' => $this->_owner->fullName(),
		];
		
		if($this->_account === NULL)
		{
			$message = 'Création d\'un compte pour :owner_name';
		}
		else
		{
			$params[':account_name'] = $this->_account->name;
			$message = 'Modification du compte :account_name de :owner_name';
		}
		
		return strtr($message, $params);
	}
	
	/**********************************************************************************************************/
	
}