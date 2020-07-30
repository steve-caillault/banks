<?php

namespace App\HTML\Traits;

use Root\{ Arr, View };
use App\Account\Statistic as AccountStatistic;

trait AccountsStatisticsRenderize {
	
	/**
	 * Périodes
	 * @var array
	 */
	protected static $_periods = [
		AccountStatistic::TYPE_YEAR => 5,
		AccountStatistic::TYPE_MONTH => 12,
		AccountStatistic::TYPE_DAY => 30,
	];
	
	/**
	 * Retourne les données par période
	 * @return array
	 */
	abstract public function dataByPeriod() : array;
	
	/**********************************************************/
	
	/**
	 * Retourne le titre des sections
	 * @return string
	 */
	abstract protected function _title() : string;
	
	/**
	 * Formatage des données
	 * @param string $frequency
	 * @param array $data
	 * @return array
	 */
	private function _formatData(string $frequency, array $data) : array
	{
		$previous = NULL;
		$statistics = [];
		$total = 0;
		$totalAverage = 0;
		foreach($data as $timestamp => $currentData)
		{
			$statistic = AccountStatistic::factory([
				'type' => $frequency,
				'amount' => $currentData['amount'],
			]);
			
			$average = ($previous === NULL) ? 0 : $statistic->amount - $previous;
			$totalAverage += ($previous === NULL) ? 0 : $statistic->amount - $previous;
			$statistics[$timestamp] = [
				'date' => $currentData['date'],
				'amount' => number_format($average, 2, ',', ' '),
				'found' => TRUE
			];
			$previous = $statistic->amount;
		}
		
		unset($statistics[key($statistics)]); // On supprime la première entrée qui sert de référence pour calculer l'évolution 
		
		if(count($data) > 0)
		{
			$firstAmount = Arr::get(current($data), 'amount', 0);
			$total = $firstAmount + $totalAverage;
		}
		
		return [
			'total' => number_format($total, 2, ',', ' ') . ' &euro;',
			'totalAverage' => number_format($totalAverage, 2, ',', ' ') . ' &euro;',
			'statistics' => $statistics,
		];
	}
	
	/**
	 * Retourne le contenu de la page de statistiques
	 * @return string
	 */
	public function render() : string
	{
		$content = [];
		
		$periods = self::$_periods;
		
		$data = $this->dataByPeriod();
		
		foreach($periods as $period => $sincePeriod)
		{
			$title = strtr($this->_title(), [
				':number' => $sincePeriod,
				':what' => translate(strtolower($period) . 's'),
			]);
			
			$periodData = Arr::get($data, $period);
			$formatData = $this->_formatData($period, $periodData);
			
			
			$content[] = View::factory('items/accounts/statistics', [
				'title' => $title,
				'data'	=> $formatData,
			]);
		}
		
		return implode('', $content);
	}
	
	/**********************************************************/
	
}