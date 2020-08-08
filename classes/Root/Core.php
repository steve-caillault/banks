<?php 

namespace Root;

final class Core
{
 
	public const 
		LOCALE_EN_GB = 'en_GB',
		LOCALE_FR_FR = 'fr_FR'
	;
	
	/**
	 * Tableau des fichiers qui ont été charger
	 * @var array
	 */
	private static array $_files_loaded = [];
	
	/**
	 * Langue utilisée
	 * @var string
	 */
	private static string $_locale = self::LOCALE_EN_GB;
	
	/**
	 * Tableau des traduction par language
	 * @var array
	 */
	private static array $_translations = [];
	
	/************************************************************************/

	/**
	 * Initialisation de Beyond PHP
	 * @return void
	 */
	public static function initialize() : void
	{
		define('INITIALIZED', TRUE);
		
		// Autochargement des classes
		spl_autoload_register([ self::class, 'autoload', ]);
		
		// Chargement des fonctions
		self::_loadFunctions();
		
		$config = getConfig('beyond');
		
		// Rapport d'erreurs
		$errorsLevel = E_ALL;
		$modeDebug = getArray($config, 'debug', FALSE);
		if(! $modeDebug)
		{
			$errorsLevel = E_ALL & ~E_WARNING & ~E_NOTICE;
		}
		error_reporting($errorsLevel);
		
		// Modification de la langue
		$language = getArray($config, 'locale', self::$_locale);
		setLanguage($language);
		
		// Chargement des routes
		require('./routes/index.php');
	}
	
	/************************************************************************/
	
	/**
	 * Charge un fichier PHP et enregistre le chemin dans self::_files_loaded
	 * @param string $path Chemin du fichier
	 * @return bool
	 */
	private static function _loadFile(string $path) : bool
	{
		if(! in_array($path, self::$_files_loaded))
		{
			require($path);
			self::$_files_loaded[] = $path;
			return TRUE;
		}
		
		return TRUE;
	}
	
	/**
	 * Méthode d'auto-chargement des classes
	 * @param string Nom de la classe à charger
	 * @return bool
	 */
	public static function autoload(string $class) : bool
	{
		$path = '.' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
		
		if($lastNamespacePosition = strripos($class, '\\'))
		{
			$namespace = substr($class, 0, $lastNamespacePosition);
			$class = substr($class, $lastNamespacePosition + 1);
			$path .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		
		$path .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		
		$pathToLoad = realpath($path);
		if($pathToLoad)
		{
			return self::_loadFile($pathToLoad);
		}
		else 
		{
			return FALSE;
		}
	}
	
	/**
	 * Chargement des fonctions
	 * @return void
	 */
	private static function _loadFunctions() : void
	{
		$path = implode(DIRECTORY_SEPARATOR, [
			'.', 'classes', 'Root', 'Functions.php',
		]);
		self::_loadFile($path);
	}
	
	/************************************************************************/
	
	/**
	 * Retourne les messages de traductions du language actuel
	 * @param string $locale
	 * @return array
	 */
	public static function translations(?string $locale = NULL) : array
	{
		$locale = ($locale) ?: self::getLanguage();
		if(! array_key_exists($locale, self::$_translations))
		{
			$translations = [];
			$file = strtolower(strtr($locale, [ '_' => '-' ])) . '.php';
			$path = realpath('.' . DIRECTORY_SEPARATOR . 'translations' . DIRECTORY_SEPARATOR . $file);
			if($path !== FALSE)
			{
				$translations = include $path;
			}
			self::$_translations[$locale] = $translations;
		}
		return self::$_translations[$locale];
	}
	
	/**
	 * Modifit le language du site
	 * @param string $locale fr_FR, en_GB, en_US
	 * @return void
	 */
	public static function setLanguage(string $locale) : void
	{
		self::$_locale = $locale;
		setlocale(LC_COLLATE, $locale);
		// setlocale(LC_NUMERIC, self::LOCALE_EN_GB); // Pause problème pour les nombres à virgule flottante en base de données sinon
	}
	
	/**
	 * Retourne le language du site
	 * @return string
	 */
	public static function getLanguage() : string
	{
		return self::$_locale;
	}
	
	/************************************************************************/

}