<?php

/**
 * Vérification qu'une valeur a une longueur minimale
 */

namespace Root\Validation\Rules;

class MinLengthRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'La valeur doit avoir au moins :min caractères.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		$value = $this->_getValue();
		$length = mb_strlen($value);
		$minimum = $this->_getParameter('min');
	
		if(! is_numeric($minimum) OR $minimum < 1)
		{
			exception('Le minimum doit être un entier positif.');
		}

		return (is_string($value) AND $length >= ((int) $minimum));
	}
	
	/********************************************************************************/
	
}