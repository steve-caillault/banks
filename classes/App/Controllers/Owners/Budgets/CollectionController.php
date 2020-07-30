<?php

/**
 * Page de la liste des budgets d'un propriétaire
 */

namespace App\Controllers\Owners\Budgets;

use App\HTML\Collection\BudgetYearCollectionHTML as CollectionHTML;

class CollectionController extends CreateOrEditController {
	
	
	/**
	 * Liste des opérations d'un compte
	 */
	public function index() : void
	{
		$this->_page_title = strtr('Liste des budgets de :name', [
			':name' => $this->_owner->fullName(),
		]);
		
		$collection = CollectionHTML::factory([
			'owner' => $this->_owner,
		])->render();
			
		$this->_main_content = $collection;
	}
	
}