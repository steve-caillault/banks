<?php

/**
 * Gestion de la validation d'un tableau
 */

namespace Root;

class Validation extends Instanciable {
	
	/**
	 * Données d'un tableau à valider
	 * @var array
	 */
	private $_data = [];
	
	/**
	 * Règles pour la validation pour chaque champs
	 * @var array
	 */
	private $_groupRules = [];
	
	/**
	 * Listes des erreurs
	 * @var array
	 */
	private $_errors = [];
	
	/********************************************************************************/
	
	/* CONTRUCTEUR / INSTANCIATION */
	
	/**
	 * Contructeur
	 * @var array $params :
	 * 	'data' 	=> array, // Données du formulaire
	 *  'rules'	=> array, // Règles de validation
	 */
	protected function __construct(array $params = [])
	{
		$this->_data = Arr::get($params, 'data', $this->_data);
		$this->_groupRules = Arr::get($params, 'rules', $this->_groupRules);
	}

	/********************************************************************************/
	
	/**
	 * Validation des données, affectations des erreurs
	 * @return void
	 */
	public function validate() : void
	{
		$namespaces = [ 'App', __NAMESPACE__, ];
		
		foreach($this->_groupRules as $field => $rules)
		{
			$fieldValue = Arr::get($this->_data, $field);
			$rulesKeys = array_keys($rules);
			$required = FALSE;
			
			foreach($rules as $ruleData)
			{
				$ruleName = Arr::get($ruleData, 0);
				
				if($ruleName == 'required')
				{
					$required = TRUE;
				}
				
				if($ruleName != 'required' AND ! $required AND $fieldValue == NULL)
				{
					continue;
				}
				
				$ruleParams = Arr::get($ruleData, 1, []);
				
				$classFound = FALSE;
				$classRule = NULL;
				
				foreach($namespaces as $namespace)
				{
					$classRule = $namespace . '\Validation\Rules\\' . strtr(ucwords(strtr(strtolower($ruleName), [ '_' => ' '])), [ ' ' => '' ]) . 'Rule';
					if($classFound = class_exists($classRule))
					{
						break;
					}
				}
				
				if(! $classFound)
				{
					exception(strtr('La classe :class n\'a pas été trouvé.', [
						':class' => $classRule,
					]));
				}
				
				$rule = $classRule::factory([
					'value' 		=> $fieldValue,
					'parameters'	=> $ruleParams,
				]);
				
				if(! $rule->check())
				{
					$this->addError($field, $ruleName, $rule->errorMessage());
					break;
				}
			}
		}
	}
	
	/**
	 * Ajoute une erreur
	 * @param string $field Champs pour lequel il y a une erreur
	 * @param string $ruleName Nom de la règle invalide 
	 * @param string $defaultMessage Message d'erreur par défaut
	 * @return void
	 */
	public function addError(string $field, string $ruleName, string $defaultMessage) : void
	{
		if(! Arr::get($this->_errors, $field))
		{
			$this->_errors[$field] = [];
		}
		
		$this->_errors[$field] = $defaultMessage;
	}
	
	/********************************************************************************/
	
	/**
	 * Retourne s'il n'y a pas d'erreur de validation
	 * @return bool
	 */
	public function success() : bool
	{
		return (count($this->_errors) == 0);
	}
	
	/**
	 * Retourne les erreurs
	 * @return  array
	 */
	public function errors() : array
	{
		return $this->_errors;
	}
	
	/********************************************************************************/
	
}