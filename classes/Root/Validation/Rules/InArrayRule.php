<?php

/**
 * Vérification qu'une valeur est présent dans un tableau de valeur
 */

namespace Root\Validation\Rules;

class InArrayRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected $_error_message = 'La valeur doit être présent dans le tableau :array.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		$value = $this->_getValue();
		$allowedValues = $this->_getParameter('array');
		
		if(! is_array($allowedValues) OR count($allowedValues) == 0)
		{
			exception('Le tableau de valeur est invalide ou vide.');
		}
		
		return in_array($value, $allowedValues);
	}
	
	/********************************************************************************/
	
}