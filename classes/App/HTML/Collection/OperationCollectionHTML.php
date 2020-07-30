<?php

/**
 * Gestion HTML d'une liste d'opération sur un compte bancaire
 */

namespace App\HTML\Collection;

use App\{ Model, Account };
use App\Collection\{ Collection, OperationCollection };
use Root\{ Arr, HTML };

class OperationCollectionHTML extends CollectionHTML {
	
	/**
	 * Type de tri
	 * @var string
	 */
	protected $_order_by = OperationCollection::ORDER_BY_DATE;
	
	/**
	 * Compte bancaire dont on veut récupérer la liste des opérations
	 * @var Account
	 */
	private $_account = NULL;
	
	/**
	 * Année dont on veut récupérer les opérations
	 * @var int
	 */
	private $_year = NULL;
	
	/**
	 * Mois dont on veut récupérer les opérations
	 * @var int
	 */
	private $_month = NULL;
	
	/*****************************************************************/
	
	/**
	 * Constructeur
	 * @param array $params
	 */
	protected function __construct(array $params = [])
	{
		// Récupération du compte
		$this->_account = Arr::get($params, 'account', $this->_account);
		if(! ($this->_account instanceof Account))
		{
			exception('Compte incorrect.');
		}
		
		// Année
		$this->_year = Arr::get($params, 'year', $this->_year);
		
		// Mois
		$this->_month = Arr::get($params, 'month', $this->_month);
		
		parent::__construct($params);
	}
	
	/*****************************************************************/
	
	/* GESTION DE LA LISTE */
	
	/**
	 * Initialisation de la collection
	 * @return Collection
	 */
	protected function _initCollection() : Collection
	{
		return OperationCollection::factory()
			->account($this->_account)
			->year($this->_year)
			->month($this->_month)
		;
	}
	
	/*****************************************************************/
	
	/* METHODES DE RENDU */
	
	/**
	 * Formatage des données d'un modèle
	 * @param Model $model
	 * @param mixed Index dans le tableau de la liste
	 * @return array
	 */
	protected function _formatModelData(Model $model, $index) : array
	{
		$operation = $model;
		
		$model->account($this->_account);
		
		$editAnchor = HTML::anchor($operation->editUri(), $operation->name, [
			'title' => strtr('Modifier :name.', [ ':name' => $operation->name, ]),
		]);
		
		$attributes = [ 
			'class' => 'line ' . strtolower($operation->type),
		];
		
		return [
			'attributes' => HTML::attributes($attributes),
			'date' => $operation->dateFormatted(),
			'name' => $editAnchor,
			'type' => $operation->typeFormatted(),
			'amount' => $operation->amountFormatted(),
		];
	}
	
	/**
	 * Retourne les propriétés HTML de la collection
	 * @return array
	 */
	protected function _htmlAttributes() : array
	{
		$attributes = parent::_htmlAttributes() + [
			'id' => 'operations',
		];
		
		return $attributes;
	}
	
	/**
	 * Retourne la phrase lorsqu'il n'y a pas d'objet
	 * @return string
	 */
	protected function noItemSentence() : string
	{
		$anchor = HTML::anchor($this->_account->addOperationUri(), 'ici', [
			'title' => strtr('Ajouter une opération pour :name.', [ ':name' => $this->_account->name, ]),
		]);
		
		return strtr('Aucune opération n\'a été trouvé. Cliquez :anchor pour en ajouter une.', [
			':anchor' => $anchor,
		]);
	}
	
	/**
	 * Retourne les clés des champs à afficher
	 * @return array
	 */
	protected function _fields() : array
	{
		return [ 'date', 'name', 'type', 'amount', ];
	}
	
	/*****************************************************************/
	
}