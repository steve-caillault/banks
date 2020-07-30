<?php

/**
 * Débogage de scripts
 */

namespace Root;

class Debug {
	
	
	/**
	 * Affichage de la variable à déboguer
	 * @param mixed $variable
	 * @return string
	 */
	public static function show($variable) : string 
	{
		return strtr('<pre>:variable</pre>', [
			':variable' => print_r($variable, TRUE),
		]);
	}
	
}