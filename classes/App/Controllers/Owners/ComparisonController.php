<?php

/**
 * Comparaison des années des comptes du propriétaire
 */

namespace App\Controllers\Owners;

use Root\View;
/***/
use App\Date;
use App\Account\{ Statistic };
use App\HTML\AccountsStatistics\OwnerStatisticsHTML;

class ComparisonController extends CreateOrEditController {
	
	public function index() : void
	{
		$until = Date::now();
		// Récupére le mois de décembre d'il y a 4 ans
		$since = Date::now()->addPeriod(Date::PERIOD_YEAR, -4)->startOfYear()->addPeriod(Date::PERIOD_MONTH, -1);
		
		// Initialisation des données
		$dataByYear = [];
		$currentDate = clone $since;
		while($currentDate->getTimestamp() < $until->getTimestamp())
		{
			$year = $currentDate->format('Y');
		 	$month = translate($currentDate->format('F'));
		 	if(! array_key_exists($year, $dataByYear))
		 	{
		 		$dataByYear[$year] = [
		 			'total' => 0,
		 		];
			}
		 
		 	$dataByYear[$year][$month] = [
		 		'amount' => 0,
		 		'progress' => 0,
		 	];
		 
		 	$currentDate->addPeriod(Date::PERIOD_MONTH, 1);
		}
		
		// Récupération des données
		$dataByMonth = OwnerStatisticsHTML::factory($this->_owner)->dataPeriodSince(Statistic::TYPE_MONTH, $since);
		
		// Formatage des données
		$previousAmount = NULL;
		$progressAmount = NULL;
		foreach($dataByMonth as $timestamp => $statistic)
		{
			$date = Date::now()->setTimestamp($timestamp);
			
			
			$year = $date->format('Y');
			$month = translate($date->format('F'));
			
			$difference = $statistic['amount'] - $previousAmount;
			
			
			
			$dataByYear[$year]['total'] += $difference;
			
			
			// Le mois de janvier, on réinitialise la progression
			if($date->format('n') == 1)
			{
				$progressAmount = 0;
			}
			
			$progressAmount += $difference;
			
			$dataByYear[$year][$month] = [
				'amount' => number_format($difference, 2, ',', ' '),
				'progress' => number_format($progressAmount, 2, ',', ' '),
			];

			$previousAmount = $statistic['amount'];
			
		}
		
		foreach($dataByYear as $year => $data)
		{
			$dataByYear[$year]['total'] = number_format($data['total'], 2, ',', ' ');
		}
		
		// Supprime la valeur du premier mois de décembre qui servait de première référence
		unset($dataByYear[key($dataByYear)]);
		
		$this->_page_title = strtr('Comparaison de l\'évolution des comptes de :name depuis :year ans', [
			':name' => $this->_owner->fullName(),
			':year' => count($dataByYear),
		]);
	
		$this->_main_content = View::factory('items/accounts/comparison', [
			'years' => array_keys($dataByYear),
			'months' => Date::monthsName(),
			'data' => $dataByYear,
		])->render();
	}
	
}