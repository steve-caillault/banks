<?php

/**
 * Vérification qu'une valeur a une longueur exacte
 */

namespace Root\Validation\Rules;

class ExactLengthRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'La valeur doit avoir exactement :length caractères.';
	
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
		$expected = $this->_getParameter('length');
		
		if(! is_numeric($expected) OR $expected < 1)
		{
			exception('Le longueur doit être un entier positif.');
		}
		
		return (is_string($value) AND $length == ((int) $expected));
	}
	
	/********************************************************************************/
	
}