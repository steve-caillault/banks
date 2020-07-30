<?php

/**
 * Mise à jour de la valeur courante du compte à partir des opérations non calculées
 */

namespace App\Controllers\Owners\Accounts\Operations;

use Root\{ Redirect };
/***/
use App\Collection\{ Collection, OperationCollection };

class ComputeController extends CreateOrEditController {

	public function index() : void
	{
		$operations = OperationCollection::factory()
			->account($this->_account)
			->uncomputed()
			->orderBy(OperationCollection::ORDER_BY_DATE, Collection::DIRECTION_ASC)
			->get()
		;
		foreach($operations as $operation)
		{
			$operation->account($this->_account);
			$operation->compute();
		}
		
		if(count($operations) > 0)
		{
			$this->_account->save();
		}
		
		Redirect::process($this->_owner->accountsUri());
	}

}