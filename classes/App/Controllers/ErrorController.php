<?php

/**
 * Page d'erreur
 */

namespace App\Controllers;

use App\Controllers\HTML\ContentController;
use Root\Arr;

class ErrorController extends ContentController {
	
	/**
	 * Vrai si la page demande un utilisateur connecté
	 * @var bool
	 */
	protected $_required_user = FALSE;
	
	public function index()
	{
		$inputs = $this->request()->post();
		$message = Arr::get($inputs, 'message', 'Une erreur s\'est produite.');
		
		$this->_page_title = 'Erreur';
		
		$this->_main_content = '<p>' . $message . '</p>';
	}
	
}