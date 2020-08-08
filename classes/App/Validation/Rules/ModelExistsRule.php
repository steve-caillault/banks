<?php

/**
 * Vérification qu'un modèle existe en base de données
 */

namespace App\Validation\Rules;

class ModelExistsRule extends ModelExistingRule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'Le modèle n\'existe pas en base de données.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		if(! parent::check())
		{
			return FALSE;
		}
		
		return ($this->_model_searched !== NULL);
	}
	
	/********************************************************************************/
	
}