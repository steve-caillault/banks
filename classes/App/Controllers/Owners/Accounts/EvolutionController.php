<?php

/**
 * Page d'évolution de la valeur d'un compte
 */

namespace App\Controllers\Owners\Accounts;

use App\HTML\AccountsStatistics\SingleStatisticsHTML;

class EvolutionController extends CreateOrEditController {
	
	/**
	 * Liste des comptes d'un propriétaire
	 */
	public function index() : void
	{
		$this->_page_title = strtr('Evolution de :name', [
			':name' => $this->_account->name,
		]);
	
		$content = SingleStatisticsHTML::factory($this->_account)->render();
		$this->_main_content = $content;
	}
	
}