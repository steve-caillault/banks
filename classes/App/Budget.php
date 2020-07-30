<?php

/**
 * Gestion d'un budget
 */

namespace App;

use Root\{ Arr, Route };

class Budget extends Model {
	
	public const TYPE_CREDIT = 'CREDIT';
	public const TYPE_DEBIT = 'DEBIT';
	/***/
	public const FRENQUENCY_YEARLY = 'YEARLY';
	public const FRENQUENCY_MONTHLY = 'MONTHLY';
	public const FRENQUENCY_DAILY = 'DAILY';
	
	/**
	 * Table du modèle
	 * @var string
	 */
	public static $table = 'budgets';
	
	/**************************************************************/
	
	/* PROPRIETES EN BASE DE DONNEES */
	
	/**
	 * Identifiant en base de données
	 * @var int
	 */
	public $id = NULL;
	
	/**
	 * Année
	 * @var int
	 */
	public $year = NULL;
	
	/**
	 * Identifiant du propriétaire dont on gére le budget
	 * @var int
	 */
	public $owner_id = NULL;
	
	/**
	 * Type d'opération (crédit ou débit)
	 * @var string
	 */
	public $type = NULL;
	
	/**
	 * Fréquence de l'opération
	 * @var string
	 */
	public $frequency = NULL;
	
	/**
	 * Valeur de l'opération
	 * @var float
	 */
	public $value = 0;
	
	/**
	 * Nom de l'opération
	 * @var string
	 */
	public $name = NULL;

	/**************************************************************/
	
	/**
	 * Propriétaire
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/**************************************************************/
	
	/* GET */
	
	/**
	 * Retourne, affecte le propriétaire
	 * @param Owner $owner Si renseigné, le propriétaire à affecter
	 * @return Owner
	 */
	public function owner(?Owner $owner = NULL) : Owner
	{
		if($owner !== NULL)
		{
			$this->owner_id = $owner->id;
			$this->_owner = $owner;
		}
		elseif($this->_owner === NULL AND $this->owner_id !== NULL)
		{
			$this->_owner = Owner::factory($this->owner_id);
		}
		return $this->_owner;
	}
	
	/**************************************************************/
	
	/* URI */
	
	/**
	 * Retourne l'URI d'édition du budget
	 * @return string
	 */
	public function editUri()
	{
		return Route::retrieve('owners.budget.edit')->uri([
			'ownerId' => $this->owner_id,
			'year' => $this->year,
			'budgetId' => $this->id,
		]);
	}
	
	/**************************************************************/
	
	/* FORMATAGE DES DONNEES */
	
	/**
	 * Retourne la valeur sur l'année
	 * @return float
	 */
	public function value() : float
	{
		$value = (($this->type == self::TYPE_CREDIT) ? $this->value : -$this->value);
		
		$products = [
			self::FRENQUENCY_YEARLY => 1,
			self::FRENQUENCY_MONTHLY => 12,
			self::FRENQUENCY_DAILY => 365,
		];
		
		$product = Arr::get($products, $this->frequency);
		
		return ($value * $product);
	}
	
	/**
	 * Retourne la fréquence formatée
	 * @return string
	 */
	public function frequencyFormatted() : string 
	{
		switch($this->frequency)
		{
			case self::FRENQUENCY_DAILY:
				return 'quotidienne';
			case self::FRENQUENCY_MONTHLY:
				return 'mensuelle';
			case self::FRENQUENCY_YEARLY:
				return 'annuelle';
			default: 
				return 'inconnue';
		}
	}
	
	/**************************************************************/
	
}