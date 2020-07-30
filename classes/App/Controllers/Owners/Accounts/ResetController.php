<?php

/**
 * Réinitialisation du calcul des valeurs des comptes du propriétaire
 * - Supprime les entrées de la table accounts_statistics
 * - Modifit operations.computed à 0
 * - Réinitialise la valeur des comptes
 */

namespace App\Controllers\Owners\Accounts;

use Root\DB;
/***/
use App\{ 
	Account, 
	Account\Statistic as AccountStatistic,
	Collection\AccountCollection,
	Operation 
};

class ResetController extends CreateOrEditController {
	
	public function index() : void
	{
		$accountIds = [];
		$accounts = AccountCollection::factory()->owner($this->_owner)->get();
		foreach($accounts as $account)
		{
			$accountIds[] = $account->id;
		}
		
		if(count($accountIds) > 0)
		{
			// Suppression des statistiques des comptes du propriétaire
			DB::delete(AccountStatistic::$table)->where('account_id', 'IN', $accountIds)->execute();
			// Remise à 0 du calcul de l'opération
			DB::update(Operation::$table)->set([
				'computed' => 0,
			])->where('account_id', 'IN', $accountIds)->execute();
		}
		
		// Réinitialisation de la valeur des comptes
		DB::update(Account::$table)->set([
			'amount_current' => DB::expression('amount_initial'),
		])->where('owner_id', '=', $this->_owner->id)->execute();
	}
	
}