<?php

/**
 * Gestion des statistiques par jours d'un compte bancaire
 */

namespace App\Account;

use App\{ Date };

class StatisticDay extends Statistic {
	
	public const DATE_FORMAT = 'Y-m-d';
	
	/**
	 * Retourne la date formatée pour l'affichage
	 * @return string
	 */
	public function dateFormat() : string
	{
		return Date::instance($this->date())->format('d-m-Y');
	}
	
}