<?php

/**
 * Page de connexion d'un utilisateur
 */

namespace App\Controllers\Auth;

use App\Controllers\HTML\ContentController;
use App\User;
use App\Forms\Users\LoginForm as Form;
/***/
use Root\{ Route, Redirect };

class LoginController extends ContentController {
	
	/**
	 * Vrai si la page demande un utilisateur connecté
	 * @var bool
	 */
	protected $_required_user = FALSE;
	
	/**************************************************************/
	
	public function before() : void
	{
		// Si un utilisateur est déjà connecté, on le redirige vers la page d'accueil
		if(User::current() !== NULL)
		{
			$redirectUri = Route::retrieve('home')->uri();
			Redirect::process($redirectUri);
		}
		
		parent::before();
	}
	
	public function index()
	{
		$data = $this->request()->inputs();
		unset($data['next']);
		
		
		$form = Form::factory([
			'data' => $data,
		]);
		
		if(count($data) > 0)
		{
			$form->process();
			if($form->success())
			{
				Redirect::process($form->redirectUrl());
			}
		}
		
		$this->_page_title = $form->title();
		
		$this->_main_content = $form->render();
	}
	
	/**************************************************************/
	
}