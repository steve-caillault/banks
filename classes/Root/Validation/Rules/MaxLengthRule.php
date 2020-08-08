<?php

/**
 * Vérification qu'une valeur a une longueur maximale
 */

namespace Root\Validation\Rules;

class MaxLengthRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'La valeur doit avoir au plus :max caractères.';
	
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
		$maximum = $this->_getParameter('max');
		
		if(! is_numeric($maximum) OR $maximum < 1)
		{
			exception('Le maximum doit être un entier positif.');
		}
		
		return (is_string($value) AND $length <= ((int) $maximum));
	}
	
	/********************************************************************************/
	
}