<?php

/**
 * Page de l'année d'un budget
 */

namespace App\Controllers\Owners\Budgets;

use App\HTML\Collection\BudgetCollectionHTML as CollectionHTML;

class BudgetController extends CreateOrEditController {
	
	
	/**
	 * Liste des opérations d'un compte
	 */
	public function index() : void
	{
		$this->_page_title = strtr('Budget de l\'année :year', [
			':year' => $this->_year,
		]);
		
		$collectionHTML = CollectionHTML::factory([
			'owner' => $this->_owner,
			'year' => $this->_year,
		]);
		$collection = $collectionHTML->render();
		
		$amount = strtr('<p>Gain estimé : :amount &euro;</p>', [
			':amount' => $collectionHTML->totalAmount(),
		]);
		
		$this->_main_content = $collection . $amount;
	}
	
}