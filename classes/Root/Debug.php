<?php

/**
 * Débogage de scripts
 */

namespace Root;

class Debug {
	
	/**
	 * Affichage de la variable à déboguer
	 * @param mixed $variable
	 * @param bool $exit Vrai si on arrête le script
	 * @return string
	 */
	public static function show($variable, bool $exit = FALSE) : string 
	{
		$pattern = '<pre>:variable</pre>';
		if(Request::isCLI())
		{
			$pattern = ':variable';
		}
		
		$content = strtr($pattern, [
			':variable' => print_r($variable, TRUE),
		]);
		
		if($exit)
		{
			exit($content);
		}
		
		return $content;
	}
	
}