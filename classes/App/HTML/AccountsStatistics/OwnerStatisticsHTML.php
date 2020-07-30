<?php

/**
 * Gestion du HTML des statistiques des comptes bancaires d'un propriétaire
 */

namespace App\HTML\AccountsStatistics;

use Root\Instanciable;
/***/
use App\{
	Date,
	Owner,
	Collection\AccountCollection,
};
use App\HTML\Traits\AccountsStatisticsRenderize; 

class OwnerStatisticsHTML extends Instanciable {
	
	use AccountsStatisticsRenderize;
	
	/**
	 * Le compte du propriétaire
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/**
	 * Récupération des comptes du propriétaire
	 * @var array
	 */
	private $_accounts = NULL;
	
	/********************************************************/
	
	/**
	 * Constructeur
	 * @param Owner $account
	 */
	protected function __construct(Owner $owner)
	{
		$this->_owner = $owner;
		$this->_accounts = AccountCollection::factory()->owner($owner)->get();
		if(count($this->_accounts) == 0)
		{
			exception('Aucun compte pour ce propriétaire.');
		}
	}
	
	/********************************************************/
	
	/**
	 * Retourne le titre des sections
	 * @return string
	 */
	protected function _title() : string
	{
		return strtr('Evolution des comptes de :owner depuis :number :what', [
			':owner' => $this->_owner->fullName(),
		]);
	}
	
	/********************************************************/
	
	/**
	 * Retourne les données par période
	 * @return array
	 */
	public function dataByPeriod() : array
	{
		$data = [];
		foreach($this->_accounts as $account)
		{
			$statisticsByFrequency = SingleStatisticsHTML::factory($account)->dataByPeriod();
			
			foreach($statisticsByFrequency as $frequency => $statistics)
			{
				if(! array_key_exists($frequency, $data))
				{
					$data[$frequency] = [];
				}
				foreach($statistics as $timestamp => $currentData)
				{
					if(! array_key_exists($timestamp, $data[$frequency]))
					{
						$data[$frequency][$timestamp] = [
							'date' => $currentData['date'],
							'amount' => 0,
						];
					}
					$data[$frequency][$timestamp]['amount'] += $currentData['amount'];
				}
			}
		}
	
		return $data;
	}
	
	/********************************************************/
	
	/**
	 * Retourne les données d'une période depuis la date en paramètre
	 * @param string $type
	 * @param Date $since
	 */
	public function dataPeriodSince(string $type, Date $since)
	{
		$data = [];
		foreach($this->_accounts as $account)
		{
			$statistics = SingleStatisticsHTML::factory($account)->dataPeriodSince($type, $since);
			foreach($statistics as $timestamp => $currentData)
			{
				if(! array_key_exists($timestamp, $data))
				{
					$data[$timestamp] = [
						'date' => $currentData['date'],
						'amount' => 0,
					];
				}
				$data[$timestamp]['amount'] += $currentData['amount'];
			}
			
		}
		
		return $data;
	}
	
	/********************************************************/
	
}