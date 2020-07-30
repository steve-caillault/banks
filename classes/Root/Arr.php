<?php

namespace Root;

/**
 * Méthodes utilitaires sur les tableaux
 */

class Arr
{
	/**
	 * Retourne la valeur d'une clé dans un tableau. 
	 * On retourne la valeur par défaut en paramètre si la clé n'est pas présente
	 * @param array $array Le tableau visé
	 * @param mixed $key La clé visée
	 * @param mixed $default La valeur par défault retournée si le clé n'est pas présente dans le tableau
	 * @return mixed
	 */
	public static function get(?array $array, $key, $default = NULL)
	{
		return ($array[$key] ?? $default);
	}

}