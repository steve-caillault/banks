<?php

/**
 * Vérification de l'existance ou non en base de données d'un modèle
 */

namespace App\Validation\Rules;

use Root\Validation\Rules\Rule;
use App\Model;

abstract class ModelExistingRule extends Rule {
	
	/**
	 * Modèle recherché
	 * @var Model
	 */
	protected $_model_searched = NULL;
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		$class = $this->_getParameter('class');
		$criterias = $this->_getParameter('criterias', []);
		
		// Vérifit que la classe du modèle existe
		if(! class_exists($class))
		{
			exception(strtr('La classe :class n\'existe pas.', [
				':class' => $class,
			]));
		}
		
		// Vérifit que la classe du modèle à utiliser est bien une classe d'un modèle
		$allowedClass = FALSE;
		try {
			$defaultObject = $class::defaultObject();
			$allowedClass = is_subclass_of($defaultObject, 'App\Model');
		} catch(\Exception $exception) {
			return $allowedClass = FALSE;
		}
		if(! $allowedClass)
		{
			exception(strtr('La classe :class n\'est pas une classe de modèle autorisée.', [
				':class' => $class,
			]));
		}
		
		// Vérifit s'il y a des critères
		if(count($criterias) == 0)
		{
			exception('Il n\'y a aucun critère de recherche.');
		}
		
		$modelCriterias = [];
		foreach($criterias as $key => $value)
		{
			if($value === ':value:')
			{
				$value = $this->_getValue();
			}
			$modelCriterias[] = [
				'left' => $key,
				'right' => $value,
			];
		}
		
		// Recherche du modèle en base de données
		$this->_model_searched = $class::searchWithCriterias($modelCriterias);
		
		return TRUE;
	}
	
	/********************************************************************************/
	
}