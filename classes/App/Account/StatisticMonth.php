<?php

/**
 * Gestion des statistiques par mois d'un compte bancaire
 */

namespace App\Account;

use App\{ Date };

class StatisticMonth extends Statistic {
	
	public const DATE_FORMAT = 'Y-m-01';
	
	/**
	 * Retourne la date formatée pour l'affichage
	 * @return string
	 */
	public function dateFormat() : string
	{
		$date = Date::instance($this->date());
		$year = $date->format('Y');
		$month = $date->format('n');
		$format = translate(Date::monthName($month)) . ' ' . $year;
		return $format;
	}
	
}