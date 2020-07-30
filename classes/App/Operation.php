<?php

/**
 * Gestion d'une opération bancaire
 */

namespace App;

use Root\{
	Arr,
	Route
};
/***/
use App\Account\Statistic as AccountStatistic;

class Operation extends Model {
	
	public const TYPE_CREDIT = 'CREDIT';
	public const TYPE_DEBIT = 'DEBIT';
	
	/**
	 * Table du modèle
	 * @var string
	 */
	public static $table = 'operations';
	
	/**************************************************************/
	
	/* PROPRIETES EN BASE DE DONNEES */
	
	/**
	 * Identifiant
	 * @var int
	 */
	public $id = NULL;
	
	/**
	 * Identifiant du compte bancaire
	 * @var int
	 */
	public $account_id = NULL;
	
	/**
	 * Type d'opération
	 * @var string
	 */
	public $type = NULL;
	
	/**
	 * Nom
	 * @var string
	 */
	public $name = NULL;
	
	/**
	 * Valeur de l'opértion
	 * @var float
	 */
	public $amount = 0;
	
	/**
	 * Date de l'opération
	 * @var string
	 */
	public $date = NULL;
	
	/**
	 * Vrai si l'opération a été comptabilisé dans le calcul de la valeur actuelle du compte
	 * @var bool
	 */
	public $computed = FALSE;
	
	/**************************************************************/
	
	/**
	 * Compte bancaire
	 * @var Account
	 */
	private $_account = NULL;
	
	/**************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Initialisation des propriétés de l'objet
	 * @param array $params Les valeurs des propriétés à affecter
	 * @return void
	 */
	protected function _init(array $params) : void
	{
		parent::_init($params);
		$this->amount = (float) Arr::get($params, 'amount', $this->amount);
	}
	
	/**************************************************************/
	
	/**
	 * Affecte, retourne le compte bancaire
	 * @param Account $account Si renseigné, le compte bancaire à affecter
	 * @return Account
	 */
	public function account(?Account $account = NULL) : ?Account
	{
		if($account !== NULL)
		{
			$this->_account = $account;
			$this->account_id = $account->id;
		}
		elseif($this->_account === NULL AND $this->account_id !== NULL)
		{
			$this->_account = Account::factory($this->account_id);
		}
		return $this->_account;
	}
	
	/**
	 * Retourne la valeur avec le signe
	 * @return float
	 */
	public function amount() : float
	{
		return (($this->type == self::TYPE_CREDIT) ? $this->amount : -$this->amount);
	}
	
	/**************************************************************/
	
	/* CALCUL DE L'OPERATION */
	
	/**
	 * Calcul l'opérration pour mettre à jour la valeur du compte
	 * @return void
	 */
	public function compute()
	{
		$account = $this->account();
	
		// Ajout des statistiques
		$frequencies = AccountStatistic::frequencies();
	
		$account->amount_current += $this->amount();
		
		
		foreach($frequencies as $type)
		{
			AccountStatistic::factory([
				'account_id' => $this->account_id,
				'type' => $type,
				'date' => $this->date,
				'amount' => $account->amount_current,
			])->compute();
		}
		
		$this->computed = TRUE;
		$this->update();
	}
	
	/**************************************************************/
	
	/**
	 * Retourne la date de l'opération formatée pour l'affichage
	 * @return string
	 */
	public function dateFormatted() : string
	{
		return Date::instance($this->date)->format('d-m-Y');
	}
	
	/**
	 * Retourne le type formaté pour l'affichage
	 * @return string
	 */
	public function typeFormatted() : string
	{
		$traductions = [
			self::TYPE_CREDIT => 'Crédit',
			self::TYPE_DEBIT => 'Débit',
		];
		
		return Arr::get($traductions, $this->type);
	}
	
	/**
	 * Retourne la valeur de l'opération formatée pour l'affichage
	 * @return string
	 */
	public function amountFormatted() : string
	{
		$value = number_format($this->amount(), 2, ',', ' ');
		return ($value . ' &euro;');
	}
	
	/**************************************************************/
	
	/* URIs */
	
	/**
	 * Retourne l'URI d'édition de l'opération
	 * @return string
	 */
	public function editUri() : string
	{
		return Route::retrieve('owners.accounts.operations.edit')->uri([
			'ownerId' => $this->account()->owner_id,
			'accountId' => $this->account_id,
			'operationId' => $this->id,
		]);
	}
	
	/**************************************************************/
	
}