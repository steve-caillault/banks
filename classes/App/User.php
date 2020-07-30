<?php

/**
 * Gestion des utilisateurs 
 */

namespace App;

use Root\{ Route, Request };

class User extends Model {
	
	/**
	 * Algorythme de cryptage du mot de passe
	 */
	private const PASSWORD_ALGORITHM = PASSWORD_ARGON2I;
	
	/**
	 * Clé en session
	 */
	private const SESSION_KEY = 'user';
	
	/********************************************************/
	
	/**
	 * Table du modèle
	 * @var string
	 */
	public static $table = 'users';
	
	/**
	 * Vrai si la clé primaire est un auto-incrément
	 * @var bool
	 */
	protected static $_autoincrement = FALSE;
	
	/********************************************************/
	
	/**
	 * Identifiant de l'utilisateur
	 * @var string
	 */
	public $id = NULL;
	
	/**
	 * Prénom
	 * @var string
	 */
	public $first_name = NULL;
	
	/**
	 * Nom
	 * @var string
	 */
	public $last_name = NULL;
	
	/**
	 * Mot de passe crypté
	 * @var string
	 */
	public $password_hashed = NULL;
	
	/********************************************************/
	
	/**
	 * Utilisateur connecté
	 * @var self
	 */
	private static $_current = FALSE; // La valeur est à FALSE tant qu'elle n'a pas été initialisé
	
	/********************************************************/
	
	/**
	 * Retourne, affecte l'utilisateur connecté
	 * @param self $user Si renseigné, l'utilisateur à stocker en session
	 * @return self
	 */
	public static function current(?self $user = NULL) : ?self
	{
		if($user !== NULL)
		{
			self::$_current = $user;
			session()->change(self::SESSION_KEY, $user);
		}
		elseif(self::$_current === FALSE)
		{
			self::$_current = session()->retrieve(self::SESSION_KEY);
		}
		return self::$_current;
	}
	
	/**
	 * Connexion du membre
	 * @return void
	 */
	public function login() : void
	{
		self::current($this);
	}
	
	/**
	 * Déconnexion de l'utilisateur connecté
	 * @return void
	 */
	public function logout() : void
	{
		session()->delete(self::SESSION_KEY);
		self::$_current = NULL;
	}
	
	/********************************************************/
	
	/**
	 * Retourne la valeur crypté du mot de passe en paramètre
	 * @param string $password
	 * @return string
	 */
	public static function passwordCrypted(string $password) : string
	{
		return password_hash($password, self::PASSWORD_ALGORITHM);
	}
	
	/**
	 * Vérifit que le mot de passe en paramètre correspond au mot de passe de l'utilisateur
	 * @param string $password
	 * @return bool
	 */
	public function checkPassword(string $password) : string
	{
		return password_verify($password, $this->password_hashed);
	}
	
	/********************************************************/
	
	/**
	 * Retourne l'URI de connexion
	 * @return string
	 */
	public static function loginUri() : string
	{
		$nextUri = Request::detectUri();
		
		$uri = Route::retrieve('login')->uri();
		
		if($nextUri != NULL)
		{
			$uri .= '?' . http_build_query([
				'next' => $nextUri,
			]);
		}
	
		return $uri;
	}
	
	/**
	 * Retourne l'URI de déconnexion
	 * @return string
	 */
	public function logoutUri() : string
	{
		return Route::retrieve('logout')->uri();
	}
	
	/********************************************************/
	
	/**
	 * Retourne le nom complet de l'utilisateur
	 * @return string
	 */
	public function fullName() : string
	{
		return trim($this->first_name . ' ' . $this->last_name);
	}
	
	/********************************************************/
	
}