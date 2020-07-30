<?php

/**
 * Création et édition d'un propriétaire de compte
 */

namespace App\Forms\Owners;

use App\Forms\ProcessForm;
use App\Owner;
/***/
use Root\Arr;
use Root\Validation;

class CreateOrEditForm extends ProcessForm
{
	public const DATA_FIRST_NAME = 'first_name';
	public const DATA_LAST_NAME = 'last_name';
	
	/**********************************************************************************************************/
	
	/**
	 * Noms de champs autorisés
	 * @var array
	 */
	protected static $_allowed_names = [
		self::DATA_FIRST_NAME => self::FIELD_TEXT,
		self::DATA_LAST_NAME => self::FIELD_TEXT,
	];
	
	/**
	 * Données du formulaire
	 * @var array
	 */
	protected $_data = [
		self::DATA_FIRST_NAME => NULL,
		self::DATA_LAST_NAME => NULL,
	];
	
	/**
	 * Listes des labels
	 * @var array
	 */
	protected static $_labels = [
		self::DATA_FIRST_NAME => 'Prénom du propriétaire',
		self::DATA_LAST_NAME => 'Nom du propriétaire',
	];
	
	/**
	 * Vrai si on doit afficher le titre du formulaire
	 * @var bool
	 */
	protected static $_with_title = FALSE;
	
	/**********************************************************************************************************/
	
	/**
	 * Propriétaire à editer
	 * @var Owner
	 */
	private $_owner = NULL;

	/**********************************************************************************************************/
	
	/* VALIDATION */
	
	/**
	 * Retourne l'objet Validation initialisé avec les réglés de validation
	 * @return Validation
	 */
	protected function _initValidation() : Validation
	{
		$rules = [
			self::DATA_FIRST_NAME => [
				array('required'),
				array('min_length', array('min' => 5)),
				array('max_length', array('max' => 100)),
			],
			self::DATA_LAST_NAME => [
				array('required'),
				array('min_length', array('min' => 5)),
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
		
		$this->_owner = Arr::get($params, 'owner', $this->_owner);
		if($this->_owner !== NULL)
		{
			$params['data'] = array_merge($this->_data, $this->_owner->asArray(), $data);
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
		$owner = Owner::factory(array_merge($this->_data, [
			'id' => ($this->_owner === NULL) ? NULL : $this->_owner->id,
		]));
		return $owner->save();
	}
	
	/**
	 * Retourne l'URL de redirection où rediriger en cas de succès
	 * @return string
	 */
	public function redirectUrl() : ?string
	{
		if($this->success())
		{
			return Owner::listUri();
		}
		return NULL;
	}
	
	/**********************************************************************************************************/

	/* RENDU */
	
	/**
	 * Retourne le titre du formulaire
	 * @return string
	 */
	public function title() : string
	{
		if($this->_owner === NULL)
		{
			return 'Création d\'un propriétaire';
		}
		else
		{
			return strtr('Modification de :owner', [
				':owner' => $this->_owner->fullName(),
			]);
		}
	}
	
	/**********************************************************************************************************/
	
}