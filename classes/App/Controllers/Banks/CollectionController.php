<?php

/**
 * Page de la liste des banques
 */

namespace App\Controllers\Banks;

use App\HTML\Collection\BankCollectionHTML as CollectionHTML;

class CollectionController extends CreateOrEditController {
	
	public function index() : void
	{	
		// Titre
		$this->_page_title = 'Liste des banques';
		
		// Contenu
		$this->_main_content = CollectionHTML::factory([])->render();
	}
	
}