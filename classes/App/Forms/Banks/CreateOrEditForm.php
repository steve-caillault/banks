<?php

/**
 * Création et édition d'une banque
 */

namespace App\Forms\Banks;

use App\Forms\ProcessForm;
use App\Bank;
/***/
use Root\Arr;
use Root\Validation;

class CreateOrEditForm extends ProcessForm
{
	public const DATA_NAME = 'name';
	
	/**********************************************************************************************************/
	
	/**
	 * Noms de champs autorisés
	 * @var array
	 */
	protected static $_allowed_names = [
		self::DATA_NAME => self::FIELD_TEXT,
	];
	
	/**
	 * Données du formulaire
	 * @var array
	 */
	protected $_data = [
		self::DATA_NAME => NULL,
	];
	
	/**
	 * Listes des labels
	 * @var array
	 */
	protected static $_labels = [
		self::DATA_NAME => 'Nom de la banque',
	];
	
	/**
	 * Vrai si on doit afficher le titre du formulaire
	 * @var bool
	 */
	protected static $_with_title = FALSE;
	
	/**********************************************************************************************************/
	
	/**
	 * Banque à editer
	 * @var Bank
	 */
	private $_bank = NULL;

	/**********************************************************************************************************/
	
	/* VALIDATION */
	
	/**
	 * Retourne l'objet Validation initialisé avec les réglés de validation
	 * @return Validation
	 */
	protected function _initValidation() : Validation
	{
		$rules = [
			self::DATA_NAME => [
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
		
		$this->_bank = Arr::get($params, 'bank', $this->_bank);
		if($this->_bank !== NULL)
		{
			$params['data'] = array_merge($this->_data, $this->_bank->asArray(), $data);
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
		$owner = Bank::factory(array_merge($this->_data, [
			'id' => ($this->_bank === NULL) ? NULL : $this->_bank->id,
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
			return Bank::listUri();
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
		if($this->_bank === NULL)
		{
			return 'Création d\'une banque';
		}
		else
		{
			return strtr('Modification de :owner', [
				':owner' => $this->_bank->name,
			]);
		}
	}
	
	/**********************************************************************************************************/
	
}