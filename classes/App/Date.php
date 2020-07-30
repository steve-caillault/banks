<?php

/**
 * Gestion de date
 */

namespace App;

use DateTime, DateTimeZone, DateInterval;

final class Date {
	
	public const PERIOD_DAY = 'day';
	public const PERIOD_MONTH = 'month';
	public const PERIOD_YEAR = 'year';
	
	/**
	 * Fuseau horaire à utiliser
	 * @var string
	 */
	private $_timezone = 'Europe/Paris';
	
	/**
	 * Objet DateTime pour la manipulation de la date
	 * @var DateTime
	 */
	private $_datetime = NULL;
	
	/**
	 * Liste des périodes autorisées
	 * @var array
	 */
	private static $_periods_allowed = [ 
		self::PERIOD_YEAR, 
		self::PERIOD_MONTH, 
		self::PERIOD_DAY, 
	];
	
	/**************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Retourne une instance de Date
	 * @param string $date
	 * @param string $timezone
	 * @return self
	 */
	public static function instance(string $date, ?string $timezone = NULL) : self
	{
		return new static($date, $timezone);
	}
	
	/**
	 * Instancie une date de la date actuelle
	 * @param string $timezone
	 * @return self
	 */
	public static function now(?string $timezone = NULL) : self
	{
		return static::instance('now', $timezone);
	}
	
	/**
	 * Contructeur
	 * @param string $date
	 * @param string $timezone
	 */
	private function __construct(string $date, ?string $timezone = NULL)
	{
		$this->_timezone = ($timezone !== NULL) ? $timezone : $this->_timezone;
		$dateTimeZone = new DateTimeZone($this->_timezone);
		$this->_datetime = new DateTime($date, $dateTimeZone);
	}
	
	/**
	 * Retourne les dates comprisent entre les dates en paramètre
	 * @param string $frequency (jour, mois ou année
	 * @param self $dateSince
	 * @param self $dateUntil
	 * @return array
	 */
	public static function between(string $frequency, self $dateSince, self $dateUntil) : array
	{
		$timestampSince = $dateSince->getTimestamp();
		$timestampUntil = $dateUntil->getTimestamp();
		
		if($timestampSince > $timestampUntil)
		{
			exception('La date de début doit être inférieur à la date de fin.');
		}
		
		$dates = [];
		
		$currentDate = clone $dateSince;
		
		while($currentDate->getTimestamp() <= $dateUntil->getTimestamp())
		{
			$dates[] = Date::instance($currentDate->format('Y-m-d'), $currentDate->_timezone);
			$currentDate = $currentDate->addPeriod($frequency, 1);
		}
		
		return $dates;
	}
	
	/**************************************************************************/
	
	/**
	 * Modification de la date au début de l'année
	 * @return self
	 */
	public function startOfYear()
	{
		return $this->setDate(NULL, 1, 1)->setTime(0, 0, 0);
	}
	
	/**
	 * Modification de l'horaire
	 * @param int $hour
	 * @param int $minute
	 * @param int $second
	 * @return self
	 */
	public function setTime(int $hour = 0, int $minute = 0, int $second = 0) : self
	{
		$this->_datetime->setTime($hour, $minute, $second);
		return $this;
	}
	
	/**
	 * Modification de la date
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 * @return self
	 */
	public function setDate(?int $year = NULL, ?int $month = NULL, ?int $day = NULL) : self
	{
		if($year === NULL)
		{
			$year = (int) $this->_datetime->format('Y');
		}
		if($month === NULL)
		{
			$month = (int) $this->_datetime->format('n');
		}
		if($day === NULL)
		{
			$day = (int) $this->_datetime->format('j');
		}
		$this->_datetime->setDate($year, $month, $day);
		return $this;
	}
	
	/**
	 * Ajoute une période à la date
	 * @param string $type Type de période (jour, mois ou date)
	 * @param int $value Le nombre à ajouter à la date 
	 */
	public function addPeriod(string $type, int $value) : self
	{
		if(! in_array($type, static::$_periods_allowed))
		{
			exception('Type de période non autorisé.');
		}
		
		$method = ($value >= 0) ? 'add' : 'sub';
		$interval = 'P' . abs($value) . ucfirst($type[0]);
		$dateInterval = new DateInterval($interval);
		
		$this->_datetime->{ $method }($dateInterval);
		
		// echo debug($this->_datetime) . '<br />';

		return $this;
	}
	
	/**************************************************************************/
	
	/**
	 * Retourne le nom du mois en paramètre
	 * @param int $month
	 * @return string
	 */
	public static function monthName(int $month) : string
	{
		return DateTime::createFromFormat('!m', $month)->format('F');
	}
	
	/**
	 * Retourne la liste des mois de l'années
	 * @return array
	 */
	public static function monthsName() : array
	{
		$months = [];
		for($month = 1 ; $month <= 12 ; $month++)
		{
			$months[] = translate(Date::monthName($month));
		}
		return $months;
	}
	
	/**
	 * Retourne la date au format demandé
	 * @param string $format Voir la fonction date de PHP
	 * @return string 
	 */
	public function format(string $format) : string
	{
		return $this->_datetime->format($format);
	}
	
	/**
	 * Modifit le timestamp de la date
	 * @param int $timstamp
	 * @return self
	 */
	public function setTimestamp(int $timestamp) : self
	{
		$this->_datetime->setTimestamp($timestamp);
		return $this;
	}
	
	/**
	 * Retourne le timestamp de la date
	 * @return int
	 */
	public function getTimestamp() : int
	{
		return $this->_datetime->getTimestamp();
	}
	
	/**************************************************************************/

	public function __clone() 
	{
		$this->_datetime = clone $this->_datetime;
	}
	
}