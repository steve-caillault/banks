<?php

/**
 * Gestion de l'environnement
 */

namespace Root;

final class Environment {
	
	public const DEVELOPMENT = 'DEVELOPMENT';
	public const TESTING = 'TESTING';
	public const PRODUCTION = 'PRODUCTION';
	/***/
	private const KEY = 'BEYOND_PHP_ENVIRONMENT';
	
	/**
	 * Tableau des environnements autorisés
	 * @var array
	 */
	private const ENVIRONMENTS = [
		self::DEVELOPMENT, self::TESTING, self::PRODUCTION,
	];
	
	/**
	 * Environnement du site
	 * @var string
	 */
	private static $_value = NULL; 
	
	/****************************************************************/
	
	/**
	 * Détection de l'environnement du site
	 * @return string
	 */
	public static function retrieve() : string
	{
		if(self::$_value === NULL)
		{
			$value = filter_input(INPUT_SERVER, self::KEY);
			if(! in_array($value, self::ENVIRONMENTS))
			{
				exception('Environnement incorrect.');
			}
			self::$_value = $value;
		}
		return self::$_value;
	}
	
	/****************************************************************/
	
}