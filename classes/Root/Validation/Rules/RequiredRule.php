<?php

/**
 * Vérification qu'une valeur est présente et non vide
 */

namespace Root\Validation\Rules;

class RequiredRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'La valeur ne doit pas être vide.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		return ($this->_getValue() === FALSE OR $this->_getValue() != NULL);
	}
	
	/********************************************************************************/
	
}