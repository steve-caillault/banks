<?php

/**
 * Gestion des nombres
 * @author Stève Caillault
 */

namespace App;

class Number {
	
	/**
	 * Retourne la chaine de caractère du nombre en paramètre formatée pour l'affichage
	 * @param mixed $value
	 * @return string
	 */
	public static function format($value) : string
	{
		$str = $value;
		if(strpos($value, '.') !== FALSE)
		{
			$str = number_format($value, 1, ',', ' ');
		}
		return $str;
	}
	
}