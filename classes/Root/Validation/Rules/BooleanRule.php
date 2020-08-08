<?php

/**
 * Vérification qu'une valeur est une valeur booléenne
 */

namespace Root\Validation\Rules;

class BooleanRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'La valeur doit être une valeur booléenne.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		return is_bool($this->_getValue());
	}
	
	/********************************************************************************/
	
}