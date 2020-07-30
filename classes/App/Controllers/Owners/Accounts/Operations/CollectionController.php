<?php

/**
 * Page de la liste des opérations d'un compte bancaire
 */

namespace App\Controllers\Owners\Accounts\Operations;

use Root\{ Arr };
/***/
use App\HTML\{ 
	Collection\OperationCollectionHTML as CollectionHTML, 
	Menu\OperationCalendarHTML as CalendarHTML 
};

class CollectionController extends CreateOrEditController {
	
	
	/**
	 * Liste des opérations d'un compte
	 */
	public function index() : void
	{
		$this->_page_title = strtr('Liste des opérations sur :name', [
			':name' => $this->_account->name,
		]);
		
		$inputs = $this->request()->inputs();
		
		$collection = CollectionHTML::factory([
			'account' => $this->_account,
			'year' => Arr::get($inputs, 'year'),
			'month' => Arr::get($inputs, 'month'),
		])->render();
		
		// Calendrier
		$calendar = CalendarHTML::factory()->get($this->_account);
		
		$this->_main_content = $calendar . $collection;
	}
	
}