<?php

/**
 * Gestion des valeurs des comptes par jour, mois ou année
 */

namespace App\Account;

use Root\{ DB, Arr };
/***/
use App\{ Model, Account, Date };
use App\Collection\{ Collection };

abstract class Statistic extends Model {
	
	public const TYPE_DAY = 'DAY';
	public const TYPE_MONTH = 'MONTH';
	public const TYPE_YEAR = 'YEAR';
	
	/* Format d'enregistrement des dates */
	public const DATE_FORMAT = 'Y-m-d';
	
	/***/
	
	/**
	 * Table du modèle
	 * @var string
	 */
	public static $table = 'accounts_statistics';
	
	/***/
	
	/**
	 * Identifiant
	 * @var int
	 */
	public $id = NULL;
	
	/**
	 * Identifiant du compte
	 * @var int
	 */
	public $account_id = NULL;
	
	/**
	 * Type de statistiques (jour, mois, année)
	 * @var string
	 */
	public $type = NULL;
	
	/**
	 * Date de statistiques
	 * @var string
	 */
	public $date = NULL;
	
	/**
	 * Valeur de statistiques
	 * @var float
	 */
	public $amount = 0;
	
	/***/
	
	/**
	 * Classe par défaut pour instancier un objet de base (pour récupérer les propriétés)
	 * @var string
	 */
	protected static $_default_class = StatisticYear::class;
	
	/**
	 * Types de fréquence
	 *  @var array
	 */
	private static $_frequencies = [ self::TYPE_DAY, self::TYPE_MONTH, self::TYPE_YEAR, ];
	
	/************************************************************/
	
	/* CONSTRUCTEUR / INSTANCATION */
	
	/**
	 * Méthode de construction statique à partir des paramètres nécessaire pour l'instanciation
	 * @param array $params Paramètre de l'objet
	 * @return self
	 */
	public static function _construct(array $params) : Model
	{
		$type = Arr::get($params, 'type');
		$class = __NAMESPACE__ . '\Statistic' . ucfirst(strtolower($type));
		return new $class($params);
	}
	
	/**
	 * Retourne la dernière statistique du compte avant la date en paramètre
	 * @param Account $account
	 * @param string $type
	 * @param Date $date
	 */
	public static function latest(Account $account, string $type, Date $date) : ?self
	{
		return static::searchWithCriterias([
			[ 
				'left' => 'account_id',
				'right' => $account->id,
			],
			[
				'left' => 'type',
				'right' => $type,
			],
			[
				'left' => DB::expression('UNIX_TIMESTAMP(`date`)'),
				'operator' => '<',
				'right' => $date->getTimestamp(),
			],
			[
				'left' => 'date',
				'operator' => '!=',
				'right' => $date->format('Y-m-d'),
			],
		], [
			'field' => 'date',
			'direction' => Collection::DIRECTION_DESC,
		]);
	}
	
	/************************************************************/
	
	/**
	 * Met à jour la valeur du compte pour la statistique en base de données
	 * @return void
	 */
	public function compute() : void
	{
		$this->date = $this->date();
		$response = DB::insert(static::$table, static::columns())
			->addValues($this->asArray())
			->onDuplicateUpdate([
				'amount' => $this->amount,
			])
			->execute();
	}
	
	/************************************************************/

	/**
	 * Retourne les types de fréquences
	 * @return array
	 */
	public static function frequencies() : array
	{
		return self::$_frequencies;
	}
	
	/**
	 * Retourne la date
	 * @return string
	 */
	public function date() : string
	{
		$this->date = Date::instance($this->date)->format(static::DATE_FORMAT);
		return $this->date;
	}
	
	/**
	 * Retourne la date formatée pour l'affichage
	 * @return string
	 */
	abstract public function dateFormat() : string;
	
	/**
	 * Retourne la valeur formatée pour l'affichage
	 * @return string
	 */
	public function amountFormat() : string
	{
		$value = number_format($this->amount, 2, ',', ' ');
		return ($value . ' &euro;');
	}
	
	/************************************************************/
	
}