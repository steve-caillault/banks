<?php

/**
 * Vérification qu'un modèle n'existe pas en base de données
 */

namespace App\Validation\Rules;
 
class ModelNotExistsRule extends ModelExistingRule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'Un modèle existe déjà en base de données.';
	
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
		
		$modelExpected = $this->_getParameter('model');
		$modelSearched = $this->_model_searched;
		
		// Si on n'a pas précisé d'objet à comparer
		if($modelExpected === NULL)
		{
			return ($modelSearched === NULL);
		}
		// Vérifit que l'objet n'existe pas où que c'est le même que celui qui est attendu
		else
		{
			return ($modelSearched === NULL OR $modelSearched->samePrimaryKey($modelExpected));
		}
	}
	
	/********************************************************************************/
	
}