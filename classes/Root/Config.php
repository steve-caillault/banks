<?php

namespace Root;

class Config
{
	
	/**
	 * Tableau des configuration déjà chargées
	 * @var array
	 */
	private static $_loaded = [];

	/************************************************************************/
	
	/**
	 * Retourne la valeur d'une configuration dont on donne la clé
	 * On utilise des . pour accéder aux tableaux enfants
	 * @param string $key
	 * @return mixed
	 */
	public static function load(string $key)
	{
		$environment = environment();
		$keys = explode('.', $key);
		$file = $keys[0];
		
		if(! in_array($file, self::$_loaded))
		{
			$filename = $file . '.php';
			$filepaths = [
				'default' => implode(DIRECTORY_SEPARATOR, [ '.', 'config', $filename, ]),
				'environment' => implode(DIRECTORY_SEPARATOR, [ '.', 'config', 'environments', $environment, $filename, ]),
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
				return NULL;
			}
			unset($keys[0]);
			self::$_loaded[$file] = $fileData;
		}
		
		$data = self::$_loaded[$file];
		
		$value = NULL;
		
		while($key = current($keys))
		{
			$data = Arr::get($data, $key);
			next($keys);
		}
	
		return $data;
	}
	
	/************************************************************************/
	
}
