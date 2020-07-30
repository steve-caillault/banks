<?php

/**
 * Formulaire de connexion
 */

namespace App\Forms\Users;

use App\Forms\ProcessForm;
use App\User;
/***/
use Root\{ Arr, Validation, Request };

class LoginForm extends ProcessForm
{
	public const DATA_ID = 'id';
	public const DATA_PASSWORD = 'password';
	
	/**********************************************************************************************************/
	
	/**
	 * Noms de champs autorisés
	 * @var array
	 */
	protected static $_allowed_names = [
		self::DATA_ID => self::FIELD_TEXT,
		self::DATA_PASSWORD => self::FIELD_PASSWORD,
	];
	
	/**
	 * Données du formulaire
	 * @var array
	 */
	protected $_data = [
		self::DATA_ID => NULL,
		self::DATA_PASSWORD => NULL,
	];
	
	/**
	 * Listes des labels
	 * @var array
	 */
	protected static $_labels = [
		self::DATA_ID => 'Identifiant',
		self::DATA_PASSWORD => 'Mot de passe',
	];
	
	/**
	 * Vrai si on doit afficher le titre du formulaire
	 * @var bool
	 */
	protected static $_with_title = FALSE;
	
	/**********************************************************************************************************/
	
	/* VALIDATION */
	
	/**
	 * Retourne l'objet Validation initialisé avec les réglés de validation
	 * @return Validation
	 */
	protected function _initValidation() : Validation
	{
		$rules = [
			self::DATA_ID => [
				array('required'),
				array('min_length', array('min' => 5)),
				array('max_length', array('max' => 50)),
				array('model_exists', array(
					'class' => User::class,
					'criterias' => [ 'id' => ':value:' ],
				)),
			],
			self::DATA_PASSWORD => [
				array('required'),
				array('min_length', array('min' => 5)),
				array('max_length', array('max' => 50)),
			],
		];
		
		$validation = Validation::factory([
			'data' 	=> $this->_data,
			'rules'	=> $rules,
		]);
		
		return $validation;
	}
	
	/**********************************************************************************************************/
	
	/* TRAITEMENT DU FORMULAIRE */
	
	/**
	 * Méthode à exécuter si le formulaire est valide
	 * @return bool
	 */
	protected function _onValid() : bool
	{
		$id = Arr::get($this->_data, self::DATA_ID);
		$password = Arr::get($this->_data, self::DATA_PASSWORD);
		
		$user = User::factory($id);
		
		// Vérification du mot de passe
		$passwordVerified = $user->checkPassword($password);
		if(! $passwordVerified)
		{
			$this->_validation->addError(self::DATA_PASSWORD, 'incorrect', 'Le mot de passe est incorrect.');
			$this->_errors = $this->_onErrors();
			return FALSE;
		}
		
		// On met en session l'utilisateur connecté
		$user->login();
		
		return TRUE;
	}
	
	/**
	 * Retourne l'URL de redirection où rediriger en cas de succès
	 * @return string
	 */
	public function redirectUrl() : ?string
	{
		if($this->success())
		{
			$next = Arr::get(Request::current()->query(), 'next', '');
			return urldecode($next);
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
		return 'Connexion';
	}
	
	/**********************************************************************************************************/
	
}