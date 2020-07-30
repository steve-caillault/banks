<?php

/**
 * Gestion HTML d'une liste de compte bancaire
 */

namespace App\HTML\Collection;

use App\{ Model, Owner };
use App\Collection\{ Collection, AccountCollection };
use Root\{ Arr, HTML };

class AccountCollectionHTML extends CollectionHTML {
	
	/**
	 * Type de tri
	 * @var string
	 */
	protected $_order_by = AccountCollection::ORDER_BY_DATE;
	
	/**
	 * Sens de direction du tri
	 * @var string
	 */
	protected $_direction = AccountCollection::DIRECTION_ASC;
	
	/**
	 * Propriétaire dont on souhaite récupérer la liste des comptes
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/**
	 * Somme totale des valeurs des comptes
	 * @var float
	 */
	private $_amount_total = 0; 
	
	/*****************************************************************/
	
	/**
	 * Constructeur
	 * @param array $params
	 */
	protected function __construct(array $params = [])
	{
		$this->_owner = Arr::get($params, 'owner', $this->_owner);
		
		if(! ($this->_owner instanceof Owner))
		{
			exception('Propriétaire incorrect.');
		}
		
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
		return AccountCollection::factory()->owner($this->_owner);
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
		$account = $model;
		
		// Edition
		$editAnchor = HTML::anchor($account->editUri(), $account->name, [
			'title' => strtr('Modifier les données de :name.', [ ':name' => $account->name, ]),
		]);
		// Opérations
		$operationsAnchor = HTML::anchor($account->operationsUri(), 'Opérations', [
			'title' => strtr('Gérer les opérations de :name.', [ ':name' => $account->name, ]),
		]);
		// Evolution du compte
		$evolutionAnchor = HTML::anchor($account->evolutionUri(), 'Evolution', [
			'title' => strtr('Evolution de la valeur de :name.', [ ':name' => $account->name, ]),
		]);
		// Calcul des opérations non calculés
		$computeAnchor = HTML::anchor($account->computeUri(), 'Calculer', [
			'title' => strtr('Calcule la valeur de :name.', [ ':name' => $account->name, ]),
		]);
		
		return [
			'attributes' => HTML::attributes([ 'class' => 'line', ]),
			'edit' => $editAnchor,
			'operations' => $operationsAnchor,
			'evolution' => $evolutionAnchor,
			'value' => $account->currentAmountFormat(),
			'compute' => $computeAnchor,
		];
	}
	
	/**
	 * Retourne les propriétés HTML de la collection
	 * @return array
	 */
	protected function _htmlAttributes() : array
	{
		$attributes = parent::_htmlAttributes() + [
			'id' => 'accounts',
		];
		
		return $attributes;
	}
	
	/**
	 * Retourne la phrase lorsqu'il n'y a pas d'objet
	 * @return string
	 */
	protected function noItemSentence() : string
	{
		$owner = $this->_owner;
		
		$addAnchor = HTML::anchor($owner->addAccountUri(), 'ici', [
			'title' => strtr('Créer un compte pour :name.', [ ':name' => $owner->fullName(), ]),
		]);
		
		return strtr('Aucun compte n\'a été trouvé. Cliquez :anchor pour en créer un.', [
			':anchor' => $addAnchor,
		]);
	}
	
	/**
	 * Retourne les clés des champs à afficher
	 * @return array
	 */
	protected function _fields() : array
	{
		return [ 'edit', 'operations', 'evolution', 'value', 'compute', ];
	}
	
	/*****************************************************************/
	
	/**
	 * Retourne la somme totale des comptes
	 * @return float
	 */
	public function amountTotal() : float
	{
		$collection = $this->_collection();
		foreach($collection as $model)
		{
			$this->_amount_total += $model->amount_current;
		}
		
		return $this->_amount_total;
	}
	
	/*****************************************************************/
	
}