<?php

/**
 * Vérification qu'une valeur représente une date
 */

namespace Root\Validation\Rules;

use DateTime;

class DateRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'La valeur doit être une date valide.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		$value = $this->_getValue();
		$dateFormat = $this->_getParameter('format');
		
		if($dateFormat !== NULL)
		{
			$this->_error_message = 'La valeur doit respecter le format :format.';
			$valid = (DateTime::createFromFormat($dateFormat, $value) !== FALSE);
		}
		else
		{
			try {
				new DateTime($value);
				$valid = TRUE;
			} catch(\Exception $exception) {
				$valid = FALSE;
			}
		}
		
		return $valid;
	}
	
	/********************************************************************************/
	
}