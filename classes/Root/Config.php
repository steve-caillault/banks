<?php

namespace Root;

class Config
{
	private const HEADER = "<?php defined('INITIALIZED') OR die('Vous n\'êtes pas autorisé à accéder à ce fichier.');";
	
	/**
	 * Tableau des configuration déjà chargées
	 * @var array
	 */
	private static array $_loaded = [];
	
	/************************************************************************/
	
	/**
	 * Création d'un fichier de configuration
	 * @param string $key Chemin vers le fichier
	 * @param array $data Données à écrire
	 * @return void
	 */
	public static function updateFile(string $key, array $data) : bool
	{
		$environment = environment();
		$filename = $key . '.php';
		$filepath = implode('/', [
			'config', 'environments', strtolower($environment), $filename
		]);
		
		$defaultContent = implode(PHP_EOL, [ self::HEADER, 'return [];', ]);
		
		$content = strtr($defaultContent, [
			'[]' => var_export($data, TRUE),
		]);
		$wrote = @ file_put_contents($filepath, $content);
		
		if(! $wrote)
		{
			return FALSE;
		}
		
		return ($wrote > 0);
	}

	/************************************************************************/
	
	/**
	 * Retourne la valeur d'une configuration dont on donne la clé
	 * On utilise des . pour accéder aux tableaux enfants
	 * @param string $key
	 * @param mixed $defaultValue Valeur par défaut à retourner
	 * @return mixed
	 */
	public static function load(string $key, $defaultValue = NULL)
	{
		$environment = environment();
		$keys = explode('.', $key);
		$file = $keys[0];
		
		if(! in_array($file, self::$_loaded))
		{
			$filename = $file . '.php';
			$filepaths = [
				'default' => implode(DIRECTORY_SEPARATOR, [ '.', 'config', $filename, ]),
				'environment' => implode(DIRECTORY_SEPARATOR, [ '.', 'config', 'environments', strtolower($environment), $filename, ]),
			];
			
			$fileData = [];
			$loaded = FALSE;
			
			foreach($filepaths as $filepath)
			{
				if(realpath($filepath))
				{
					$currentData = include $filepath;
					$fileData = array_replace_recursive($fileData, $currentData);
					$loaded = TRUE;
				}
			}
			
			// Si le fichier ne peut être chargé
			if($loaded === FALSE)
			{
				return $defaultValue;
			}
			unset($keys[0]);
			self::$_loaded[$file] = $fileData;
		}
		
		$data = self::$_loaded[$file];
		
		while($key = current($keys))
		{
			$data = getArray($data, $key, $defaultValue);
			next($keys);
		}
	
		return $data;
	}
	
	/************************************************************************/
	
}
