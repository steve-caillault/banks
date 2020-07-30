<?php

/**
 * Gestion du HTML des statistiques de compte(s) bancaire(s)
 */

namespace App\HTML\AccountsStatistics;

use Root\{
	Instanciable,
	Arr
};
/***/
use App\{
	Date,
	Account\Statistic as AccountStatistic
};
use App\HTML\Traits\AccountsStatisticsRenderize;

abstract class StatisticsHTML extends Instanciable {

	use AccountsStatisticsRenderize;
	
	/**
	 * Tableau faisant la correspondance entre le type de statistique et le type de période
	 * @var array
	 */
	private static $_periodTypes = [
		AccountStatistic::TYPE_YEAR => Date::PERIOD_YEAR,
		AccountStatistic::TYPE_MONTH => Date::PERIOD_MONTH,
		AccountStatistic::TYPE_DAY => Date::PERIOD_DAY,
	];
	
	/***************************************************************************/
	
	/**
	 * Retourne les données par période
	 * @return array
	 */
	public function dataByPeriod() : array
	{
		$periods = self::$_periods;
		
		$data = [];
		
		foreach($periods as $period => $sincePeriod)
		{
			$data[$period] = $this->_retrieveStatisticsByPeriod($period, $sincePeriod);
		}
		
		return $data;
	}
	
	/**
	 * Retourne les données d'une période depuis la date en paramètre
	 * @param string $type Type de période
	 * @param Date $since
	 * @return array
	 */
	public function dataPeriodSince(string $type, Date $since)
	{
		return $this->_retrieveStatistics($type, $since);
	}
	
	/***************************************************************************/
	
	/**
	 * Retourne les données des statistiques en base de données
	 * @param string $type
	 * @param int $since
	 * @param int $until
	 * @return array
	 */
	abstract protected function _dataStatistics(string $type, int $since, int $until) : array;
	
	/**
	 * Retourne la dernière statistique connu avant le timestamp en paramètre
	 * @param string $type
	 * @param Date $dateSince
	 * @return AccountStatistic
	 */
	abstract protected function _latestStatistic(string $type, Date $since) : ?AccountStatistic;
	
	/***************************************************************************/
	
	/**
	 * Retourne les statistiques depuis la date en paramètre
	 * @param string $type
	 * @param Date $since
	 * @return array
	 */
	private function _retrieveStatistics(string $type, Date $since)
	{
		$statisticDefault = AccountStatistic::factory([
			'type' => $type,
			'date' => Date::now()->format('Y-m-d'),
		]);
		
		$until = Date::instance($statisticDefault->date())->setTime(0, 0 ,0);
		/***/
		$timestampUntil = $until->getTimestamp();
		$timestampSince = $since->getTimestamp();
		/***/
		$periodType = Arr::get(self::$_periodTypes, $type);
		$dates = Date::between($periodType, $since, $until);
		
		// Initialisation des données
		$data = [];
		foreach($dates as $currentDate)
		{
			$latest = NULL;
			if(current($data) === FALSE)
			{
				$latest = $this->_latestStatistic($type, $since);
			}
			$statisticDefault->date = $currentDate->format('Y-m-d');
			$data[$currentDate->getTimestamp()] = [
				'date' => $statisticDefault->dateFormat(),
				'amount' => ($latest) ? $latest->amount : 0,
				'found' => ($latest !== NULL),
			];
		}
		
		// Récupération des valeurs en base de données
		$statistics = $this->_dataStatistics($type, $timestampSince, $timestampUntil);
		foreach($statistics as $statistic)
		{
			$currentDate = Date::instance($statistic->date());
			$data[$currentDate->getTimestamp()] = array_merge($data[$currentDate->getTimestamp()], [
				'amount' => $statistic->amount,
				'found' => TRUE,
			]);
		}
		
		$foundPrevious = function($timestamp) use ($data) {
			$value = NULL;
			foreach($data as $key => $statistic) {
				if($key < $timestamp AND $statistic['found']) {
					$value = $statistic;
				}
			}
			return $value;
		};
		
		// Pour toutes les valeurs qui n'ont pas été trouvé
		foreach($data as $timestamp => $statistic)
		{
			if(! $statistic['found'])
			{
				$previousStatistic = $foundPrevious($timestamp);
				if($previousStatistic)
				{
					$data[$timestamp] = array_merge($data[$timestamp], [
						'found' => TRUE,
						'amount' => $previousStatistic['amount'],
					]);
				}
			}
		}
		
		return $data;
	}
	
	/**
	 * Retourne les statistiques du type en paramètre
	 * @param string $type Statistique par jour, mois, ou année
	 * @param int $sincePeriod Nombre de jour, mois ou années précédentes
	 * @return array Tableau de données avec les statistiques par années et le total
	 */
	private function _retrieveStatisticsByPeriod(string $type, int $sincePeriod) : array
	{
		$periodType = Arr::get(self::$_periodTypes, $type);
		
		$statisticDefault = AccountStatistic::factory([
			'type' => $type,
			'date' => Date::now()->format('Y-m-d'),
		]);
		
		$since = Date::instance($statisticDefault->date())->setTime(0, 0 ,0)->addPeriod($periodType, -$sincePeriod);
		
		return $this->_retrieveStatistics($type, $since);
	}
	
	/***************************************************************************/
	
}