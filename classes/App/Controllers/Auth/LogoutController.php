<?php

/**
 * Page de connexion d'un utilisateur
 */

namespace App\Controllers\Auth;

use App\Controllers\HTML\ContentController;
use App\User;
/***/
use Root\{ Route, Redirect };

class LogoutController extends ContentController {
	
	
	public function index()
	{
		User::logout();
		$redirectUri = Route::retrieve('home')->uri();
		Redirect::process($redirectUri);
	}
	
	/**************************************************************/
	
}