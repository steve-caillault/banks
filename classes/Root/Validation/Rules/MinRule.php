<?php

/**
 * Vérification qu'une valeur a une valeur minimale
 */

namespace Root\Validation\Rules;

class MinRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'La valeur doit être supérieur ou égale à :min.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		$value = $this->_getValue();
		$minimum = $this->_getParameter('min');
		
		if(! is_numeric($minimum))
		{
			exception('Le minimum doit être une valeur numérique.');
		}
		
		return ($value >= ((int) $minimum));
	}
	
	/********************************************************************************/
	
}