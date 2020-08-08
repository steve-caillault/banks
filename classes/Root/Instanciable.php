<?php

/**
 * Gestion d'un objet instanciable
 * @author Stève Caillault
 */

namespace Root;

abstract class Instanciable
{
	/**
	 * Instances chargées
	 * @var self
	 */
	protected static array $_instance = [];
	
	/**********************************************************/
	
	/**
	 * Instanciation
	 * @return self
	 */
	public static function factory($params = NULL) : self
	{
		return static::_construct($params);
	}
	
	/**
	 * Méthode de construction statique à partir des paramètres nécessaire pour l'instanciation
	 * @param array $params Paramètre de l'objet
	 * @return self
	 */
	public static function _construct($params = NULL) : self
	{
		return new static($params);
	}
	
	/**
	 * Retourne l'instance unique
	 * @return self
	 */
	public static function instance() : self
	{
		$class = get_called_class();
		
		if(! array_key_exists($class, self::$_instance))
		{
			static::$_instance[$class] = static::_initInstance();
		}
		
		return static::$_instance[$class];
		
	}
	
	/**
	 * Initialisation de l'instance unique
	 * @return self
	 */
	protected static function _initInstance() : self
	{
		return static::factory();
	}
	
	/**********************************************************/
	
}