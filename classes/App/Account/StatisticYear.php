<?php

/**
 * Gestion des statistiques par années d'un compte bancaire
 */

namespace App\Account;

use App\{ Date };

class StatisticYear extends Statistic {
	
	public const DATE_FORMAT = 'Y-01-01';
	
	/**
	 * Retourne la date formatée pour l'affichage
	 * @return string
	 */
	public function dateFormat() : string
	{
		return Date::instance($this->date())->format('Y');
	}
	
}