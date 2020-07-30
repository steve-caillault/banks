<?php

namespace Root;

use Root\Database\PDO;

/**
 * Gestion d'une base de données
 */

abstract class Database {
	
	/**
	 * Configuration de la base de données par défaut
	 */
	public const INSTANCE_DEFAULT = 'DEFAULT';
	
	/**
	 * API PDO
	 */
	public const API_PDO = 'PDO';
	
	/************************************************************************/
	
	/**
	 * Instances de Database déjà chargés
	 * @var array
	 */
	private static $_instances = [];
	
	/**
	 * Identifiant de la dernière insertion
	 * @var string
	 */
	protected static $_last_insert_id = NULL;
	
	/************************************************************************/
	
	/**
	 * Constructeur
	 * @param array $configuration Configuration de la base de données
	 * @return void
	 */
	abstract protected function __construct(array $configuration);
	
	/**
	 * Instanciation
	 * @param string $key Clé de la configuration de la base de donnée
	 * @return Database
	 */
	private static function factory(string $key = self::INSTANCE_DEFAULT) : Database
	{
		// Chargement de la configuration
		$configuration_key = 'database.'.strtolower($key);
		$configuration = Config::load($configuration_key);
		if(! $configuration)
		{
			exception('Configuration de la base de données manquante.');
		}
		
		$connection = Arr::get($configuration, 'connection');
		$api = Arr::get($configuration, 'api', self::API_PDO);
	
		switch($api)
		{
			case self::API_PDO:
				return new PDO($connection);
			default:
				exception('API de base de données inconnue.');
		}
	}
	
	/**
	 * Retourne une instance de Database
	 * @param string $key Clé de la configuration de la base de données
	 * @return Database
	 */
	public static function instance(string $key = self::INSTANCE_DEFAULT) : Database
	{
		$instance = Arr::get(self::$_instances, $key);
		if(! $instance)
		{
			$instance = self::factory($key);
		}
		return $instance;
	}
	
	/**
	 * Retourne le dernier identifiant inséré
	 * @var string
	 */
	public static function lastInsertId() : ?string
	{
		return static::$_last_insert_id;
	}
	
	/************************************************************************/
	
}