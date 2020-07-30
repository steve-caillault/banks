<?php

/**
 * Page de la liste des comptes d'un propriétaire
 */

namespace App\Controllers\Owners\Accounts;

use App\HTML\Collection\{ AccountCollectionHTML as CollectionHTML };

class CollectionController extends CreateOrEditController {
	
	/**
	 * Liste des comptes d'un propriétaire
	 */
	public function index() : void
	{	
		$this->_page_title = strtr('Liste des comptes de :name', [
			':name' => $this->_owner->fullName(),
		]);
		
		$collection = CollectionHTML::factory([
			'owner' => $this->_owner,
		]);
		
		$amountTotal = $collection->amountTotal();
		$amountFormatted = strtr('<p>Somme total : :amount &euro;.</p>', [
			':amount' => number_format($amountTotal, 2, ',', ' '),
		]);
		
		$this->_main_content = $collection->render() . $amountFormatted;
	}
	
}