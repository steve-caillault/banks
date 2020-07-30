<?php

/**
 * Vérification qu'une valeur représente une valeur numérique
 */

namespace Root\Validation\Rules;

class NumericRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected $_error_message = 'La valeur doit être une valeur numérique.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		$value = $this->_getValue();
		return is_numeric($value);
	}
	
}