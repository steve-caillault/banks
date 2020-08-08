<?php

/**
 * Gestion d'un fichier
 */

namespace Root;

class File {
	
	
	/**
	 * Retourne l'extension d'un fichier
	 * @return string
	 */
	public static function extension(string $file) : ?string
	{
		$position = strrpos($file, '.');
		if($position === FALSE)
		{
			return NULL;
		}
		
		$extension = substr($file, $position + 1);
		
		return strtolower($extension);
	}
		
	/**
	 * Retourne le nom du fichier sans l'extension
	 * @return string
	 */
	public static function name(string $file) : string
	{
		$position = strrpos($file, '.');
		if($position === FALSE)
		{
			return $file;
		}
		
		return substr($file, 0, $position);
	}
	
}