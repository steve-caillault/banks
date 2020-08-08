<?php

/**
 * Tâche permettant de changer l'environnement
 * php cli environment name
 */

namespace Root\Tasks;

use Root\{ Task, Environment };

class ChangeEnvironmentTask extends Task {
	
	/**
	 * Identifiant de la tâche
	 * @var string
	 */
	protected string $_identifier = 'environment';
	
	/*******************************************************/
	
	/**
	 * Règles de validation des paramètres
	 * @return array
	 */
	protected function _validationParametersRules() : array
	{
		$allowedValues = explode(',', strtolower(implode(',', [
			Environment::DEVELOPMENT, Environment::TESTING, Environment::DEMO, Environment::PRODUCTION,
		])));
		
		return [
			[ 
				array('required'),
				array('in_array', [ 'array' => $allowedValues, ]),
			],
		];
	}
	
	/**
	 * Exécute la tâche
	 * @return void
	 */
	protected function _execute() : void
	{
		$environment = getArray($this->parameters(), 0);
		$success = Environment::change(strtoupper($environment));
		$this->_response = ($success) ? 'L\'environnement a été modifié.' : 'L\'environnement n\'a pas été modifié.';
	}
	
	/*******************************************************/

	
}