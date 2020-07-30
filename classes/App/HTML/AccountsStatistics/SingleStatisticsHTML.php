<?php

/**
 * Gestion du HTML des statistiques d'un compte bancaire
 */

namespace App\HTML\AccountsStatistics;

use App\{
	Date,
	Account,
	Account\Statistic as AccountStatistic,
	Collection\AccountStatisticCollection as StatisticCollection 
};

class SingleStatisticsHTML extends StatisticsHTML {

	/**
	 * Le compte à gérer
	 * @var Account
	 */
	protected $_account = NULL;
	
	/********************************************************/
	
	/**
	 * Constructeur
	 * @param Account $account
	 */
	protected function __construct(Account $account)
	{
		$this->_account = $account;
	}
	
	/********************************************************/
	
	/**
	 * Retourne le titre des sections
	 * @return string
	 */
	protected function _title() : string
	{
		return 'Evolution du compte depuis :number :what';
	}
	
	/**
	 * Retourne la dernière statistique connu avant le timestamp en paramètre
	 * @param string $type
	 * @param Date $dateSince
	 * @return AccountStatistic
	 */
	protected function _latestStatistic(string $type, Date $dateSince) : ?AccountStatistic
	{
		return AccountStatistic::latest($this->_account, $type, $dateSince);
	}
	
	/**
	 * Retourne les données des statistiques en base de données
	 * @param string $type
	 * @param int $since
	 * @param int $until
	 * @return array
	 */
	protected function _dataStatistics(string $type, int $since, int $until) : array
	{
		$response = StatisticCollection::factory()
			->account($this->_account)
			->type($type)
			->since($since)
			->until($until)
			->orderBy(StatisticCollection::ORDER_BY_DATE)
			->get();
		return $response;
	}
	
	/********************************************************/
	
}