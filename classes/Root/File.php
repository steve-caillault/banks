<?php

/**
 * Gestion d'un fichier
 */

namespace Root;

class File {
	
	
	/**
	 * Retourne l'estension d'un fichier
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
		
	
	
}