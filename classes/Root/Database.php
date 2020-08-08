<?php

/**
 * Gestion d'une base de données
 */

namespace Root;

use Root\Database\PDO;

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
	private static array $_instances = [];
	
	/**
	 * Identifiant de la dernière insertion
	 * @var string
	 */
	protected static ?string $_last_insert_id = NULL;
	
	/**
	 * Dernière requête exécutée
	 * @var string
	 */
	protected static ?string $_last_query = NULL;
	
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
	private static function factory(string $key = self::INSTANCE_DEFAULT) : self
	{
		// Chargement de la configuration
		$configurationKey = 'database.' . strtolower($key);
		$configuration = getConfig($configurationKey);
		if(! $configuration)
		{
			exception('Configuration de la base de données manquante.');
		}
		
		$connection = getArray($configuration, 'connection');
		$api = getArray($configuration, 'api', self::API_PDO);
	
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
	public static function instance(string $key = self::INSTANCE_DEFAULT) : self
	{
		$instance = getArray(self::$_instances, $key);
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
	
	/**
	 * Retourne la dernière requête exécutée
	 * @return string
	 */
	public static function lastQuery() : ?string
	{
		return static::$_last_query;
	}
	
	/************************************************************************/
	
}