<?php

/**
 * Vérification qu'une valeur représente le nom du classe PHP qui existe
 */

namespace Root\Validation\Rules;

class ClassExistsRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'La classe n\'existe pas.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		$value = $this->_getValue();
		return class_exists($value);
	}
	
	/********************************************************************************/
	
}