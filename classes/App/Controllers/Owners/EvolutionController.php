<?php

/**
 * Evolution des comptes du propriétaire
 */

namespace App\Controllers\Owners;

use App\HTML\AccountsStatistics\OwnerStatisticsHTML;

class EvolutionController extends CreateOrEditController {
	
	public function index() : void
	{
		$this->_page_title = strtr('Evolution des comptes de :name', [
			':name' => $this->_owner->fullName(),
		]);
		$this->_main_content = OwnerStatisticsHTML::factory($this->_owner)->render();
	}
	
}