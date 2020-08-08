<?php

/**
 * Gestion de répertoire
 */

namespace Root;

class Directory {
	
	/**
	 * Liste des fichiers du répertoires
	 * @param string $directoryPath
	 * @return array
	 */
	public static function files(string $directoryPath) : array
	{
		$directory = @ opendir($directoryPath);
		if(! $directory)
		{
			return [];
		}
		
		$files = [];
		
		while($file = readdir($directory)) 
		{
			$fullPath = rtrim($directoryPath, '/') . '/' . $file;
			
			if(is_file($fullPath))
			{
				$files[] = $fullPath;
			}
			elseif(is_dir($fullPath) AND ! in_array($file, [ '.', '..', ]))
			{
				$files = [...$files, ...self::files($fullPath)];
			}
		}
		
		closedir($directory);
		
		return $files;
		
	}
	
	/**
	 * Cré le répertoire s'il n'existe pas
	 * @param string $path
	 * @return bool
	 */
	public static function create(string $path) : bool
	{
	    if(is_dir($path))
	    {
	        return TRUE;
	    }
	    
	    return (@ mkdir($path, 0755, TRUE));
	}
}