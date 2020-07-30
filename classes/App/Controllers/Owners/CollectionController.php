<?php

/**
 * Page de la liste des propriétaires de compte
 */

namespace App\Controllers\Owners;

use App\HTML\Collection\OwnerCollectionHTML as CollectionHTML;

class CollectionController extends CreateOrEditController {
	
	public function index() : void
	{
		// Titre
		$this->_page_title = 'Propriétaires de compte';
		
		// Contenu
		$this->_main_content = CollectionHTML::factory([])->render();
	}
	
}