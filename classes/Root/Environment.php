<?php

/**
 * Gestion de l'environnement
 */

namespace Root;

final class Environment extends Instanciable {
	
	public const 
		KEY_NAME = 'name',
		KEY_MAINTENANCE = 'maintenance',
		/***/
		DEVELOPMENT = 'DEVELOPMENT',
		TESTING = 'TESTING',
		DEMO = 'DEMO',
		PRODUCTION = 'PRODUCTION'
	;
	/***/
	private const
		KEY = 'BEYOND_PHP_ENVIRONMENT',
		FILENAME = '.environment',
		DEFAULT = self::DEVELOPMENT
	;
	
	/**
	 * Tableau des environnements autorisés
	 * @var array
	 */
	private const ENVIRONMENTS = [
		self::DEVELOPMENT, self::TESTING, self::DEMO, self::PRODUCTION,
	];
	
	/**
	 * Données du fichier, ensemble de clé, valeur
	 * @var array
	 */
	private ?array $_data = NULL;
	
	/**
	 * Nom de l'environement
	 * @var string
	 */
	private static ?string $_name = NULL;
	
	/**
	 * Vrai si le site est en maintenance
	 * @var bool
	 */
	private static ?bool $_maintenance = NULL;
	
	/****************************************************************/
	
	/**
	* Chargement des données du fichier /.environment
	* @return array
	*/
	private function _data() : array
	{
		if($this->_data === NULL)
		{
			$data = [];
			$pattern = '/^[^\=]+\=[^\=]+$/D';
			
			$file = @ fopen('.environment', 'r');
			if(! $file)
			{
				self::$_maintenance = FALSE;
				self::change(self::DEFAULT);
				$data = [
					self::KEY_NAME => self::DEFAULT,
					self::KEY_MAINTENANCE => self::$_maintenance,
				];
				
			}
			else
			{
				while(! feof($file))
				{
					$line = trim(fgets($file));
					if(preg_match($pattern, $line))
					{
						list($key, $value) = explode('=', $line);
						$data[$key] = $value;
					}
				}
				fclose($file);
				
				
			}
			$this->_data = $data;
		}
		
		return $this->_data;
	}
	
	/****************************************************************/
	
	/**
	 * Retourne la données dont la clé est en paramètre
	 * @param string $key
	 * @param mixed $default Valeur par défaut
	 * @return mixed
	 */
	public function getValue(string $key, $default = NULL)
	{
		return getArray($this->_data(), $key, $default);
	}
	
	/**
	 * Retourne si le site est en maintenance
	 * @return bool
	 */
	public static function inMaintenance() : bool
	{
		if(self::$_maintenance === NULL)
		{
			$value = self::instance()->getValue(self::KEY_MAINTENANCE);
			self::$_maintenance = ($value == 1);
		}
		return self::$_maintenance;
	}
	
	/**
	 * Détection de l'environnement du site
	 * @return string
	 */
	public static function getName() : string
	{
		if(self::$_name === NULL)
		{
			// Vérifit le cache
			$cacheKey = Cache::KEY_ENVIRONMENT;
			$cacheLifetime = 24 * 3600;
			$cache = Cache::instance();
			$name = $cache->get($cacheKey, $cacheLifetime);
			
			$mustUpdateCache = FALSE;
			// Chargement du fichier d'environnement
			if($name === NULL)
			{
				$name = self::instance()->getValue(self::KEY_NAME);
				$mustUpdateCache = TRUE;
			}
			
			if(! in_array($name, self::ENVIRONMENTS))
			{
				exception('Environnement incorrect.');
			}
			
			// Mise à jour du cache
			if($mustUpdateCache)
			{
				$cache->update($cacheKey, $name);
			}
			
			self::$_name = $name;
		}
		return self::$_name;
	}
	
	/**
	 * Création du fichier d'environnement
	 * @param array $data Données à écrire dans le fichier
	 * @return void
	 */
	private static function _createFile(array $data) : bool
	{
		@ $file = fopen(self::FILENAME, 'w');
		if(! $file)
		{
			return FALSE;
		}
		
		$environment = getArray($data, self::KEY_NAME);
		$maintenance = getArray($data, self::KEY_MAINTENANCE);
		
		fwrite($file, self::KEY_NAME . '=' . $environment . "\n");
		fwrite($file, self::KEY_MAINTENANCE . '=' . $maintenance);
		fclose($file);
		
		return TRUE;
	}
	
	/**
	 * Met le site en maintenance, ou réactive le site
	 * @param bool $maintenance Vrai si le site doit être mis en maintenance, faux s'il faut réactiver le site
	 * @return bool
	 */
	public static function maintenance(bool $maintenance) : bool
	{
		$updated = self::_createFile([
			self::KEY_NAME => self::getName(),
			self::KEY_MAINTENANCE => (($maintenance) ?: 0),
		]);
		
		if($updated)
		{
			self::$_maintenance = $updated;
		}
		
		return $updated;
	}
	
	/**
	 * Modification de l'environnement
	 * @param string $environment
	 * @return bool
	 */
	public static function change(string $environment) : bool
	{
		if(! in_array($environment, self::ENVIRONMENTS))
		{
			exception('Environnement incorrect.');
		}
		
		$maintenance = (self::inMaintenance()) ?: 0;
		
		$updated = self::_createFile([
			self::KEY_NAME => $environment,
			self::KEY_MAINTENANCE => $maintenance,
		]);
		
		if(! $updated)
		{
			return FALSE;
		}
		
		Cache::instance()->update(Cache::KEY_ENVIRONMENT, $environment);
		self::$_name = $environment;
		
		return TRUE;
	}
	
	/****************************************************************/
	
}
