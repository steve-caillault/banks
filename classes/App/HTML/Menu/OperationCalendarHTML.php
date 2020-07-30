<?php

/**
 * Gestion HTML d'une opération bancaire
 */

namespace App\HTML\Menu;

use Root\{ Instanciable, Arr, Request };
/***/
use App\{ Date, Account };

class OperationCalendarHTML extends Instanciable {
	
	/**
	 * Retourne le menu de sélection de l'année et du mois des opérations bancaires du compte en paramètre
	 * @param Account $account
	 * @return string
	 */
	public function get(Account $account) : ?string
	{
		$inputs = Request::current()->inputs();
		$currentYear = Arr::get($inputs, 'year');
		$currentMonth = Arr::get($inputs, 'month');
		
		$years = ($account === NULL) ? [] : $account->operationYears();
		if(count($years) == 0)
		{
			return NULL;
		}
		
		$menuYears = MenuHTML::factory(MenuHTML::TYPE_MODULES);
		$menuMonths = MenuHTML::factory(MenuHTML::TYPE_MODULES);
		
		$currentMonths = [];
		
		// Menu des années
		foreach($years as $year => $months)
		{
			if($currentYear == $year)
			{
				$currentMonths = $months;
			}
			
			$keyYear = 'account-operation-calendar-' . $year;
			$uri = $account->operationsUri() . '?' . http_build_query([
				'year' => $year,
			]);
			$menuYears->addItem($keyYear, [
				'label' => $year,
				'href' => $uri,
				'class' => ($currentYear == $year) ? 'selected' : NULL,
				'title' => strtr('Consulter les opérations bancaires de l\'année :year.', [ ':year' => $year, ]),
			]);
		}
		
		// Menu des mois
		foreach($currentMonths as $month)
		{
			$month = (int) $month;
			$monthName = Date::monthName($month);
			$keyMonth = 'account-operation-calendar-' . $currentYear . '-' . strtolower($monthName);
			$uri = $account->operationsUri() . '?' . http_build_query([
				'year' => $currentYear,
				'month' => $month,
			]);
			$currentTitle = strtr('Consulter les opérations bancaires du mois de :month de l\'année :year.', [
				':year' => $currentYear,
				':month' => mb_strtolower(translate($monthName)),
			]);
			$menuMonths->addItem($keyMonth, [
				'label' => translate($monthName),
				'href' => $uri,
				'class' => ($currentMonth == $month) ? 'selected' : NULL,
				'title' => $currentTitle,
			]);
		}
		
		return $menuYears->render() . $menuMonths->render();
	}
	
}