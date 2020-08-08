<?php

/**
 * Tâche permettant de créer un utilisateur
 * php cli create-user firstName LastName password
 */

namespace App\Tasks;

use Root\{ Task, Validation, URL };
/***/
use App\User;

class CreateUserTask extends Task {
	
	/**
	 * Identifiant de la tâche
	 * @var string
	 */
	protected string $_identifier = 'create-user';
	
	/*******************************************************/
	
	/**
	 * Exécute la tâche
	 * @return void
	 */
	protected function _execute() : void
	{
		$parameters = $this->parameters();
		$rules = [
			[ // Prénom
				array('required'),
				array('max_length', [ 'max' => 100 ]),
			],
			[ // Nom
				array('required'),
				array('max_length', [ 'max' => 100 ]),
			],
			[ // Mot de passe
				array('required'),
			],
		];
		
		$validation = Validation::factory([
			'data' => $parameters, 
			'rules' => $rules,
		]);
		
		$validation->validate();
		
		if(! $validation->success())
		{
			exception('Paramètres incorrects.');
		}
		
		
		$firstName = getArray($parameters, 0);
		$lastName = getArray($parameters, 1);
		$password = getArray($parameters, 2);
		
		$user = User::factory([
			'id' => URL::title(trim($firstName . ' ' . $lastName), '-'),
			'first_name' => $firstName,
			'last_name' => $lastName,
			'password_hashed' => User::passwordCrypted($password),
		]);
		
		$success = $user->save();
		
		$this->_response = ($success) ? 'L\'utilisateur a été créée.' : 'L\'utilisateur n\'a pas été créée.';
	}
	
	/*******************************************************/

	
}