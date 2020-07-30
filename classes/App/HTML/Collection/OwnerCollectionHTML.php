<?php

/**
 * Gestion HTML d'une liste de propriétaire
 */

namespace App\HTML\Collection;

use App\{ Model, Owner };
use App\Collection\{ Collection, OwnerCollection };
use Root\{ HTML };

class OwnerCollectionHTML extends CollectionHTML {
	
	/**
	 * Type de tri
	 * @var string
	 */
	protected $_order_by = OwnerCollection::ORDER_BY_NAME;
	
	/**
	 * Sens de direction du tri
	 * @var string
	 */
	protected $_direction = OwnerCollection::DIRECTION_ASC;
	
	/*****************************************************************/
	
	/* GESTION DE LA LISTE */
	
	/**
	 * Initialisation de la collection
	 * @return Collection
	 */
	protected function _initCollection() : Collection
	{
		return OwnerCollection::factory();
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
		$owner = $model;
		
		$editAnchor = HTML::anchor($owner->editUri(), $owner->fullName(), [
			'title' => strtr('Modifier les informations de :name.', [ ':name' => $owner->fullName(), ]),
		]);
		
		$countAccounts = count($owner->accounts());
		$labelAccountsAnchors = ($countAccounts == 0) ? 'Aucun compte' : strtr(':count compte(s)', [ ':count' => $countAccounts, ]);
		$accountsAnchor = HTML::anchor($owner->accountsUri(), $labelAccountsAnchors, [
			'title' => strtr('Gérer les comptes de :name.', [ ':name' => $owner->fullName(), ]),
		]);
		
		return [
			'attributes' => HTML::attributes([ 'class' => 'line', ]),
			'edit' => $editAnchor,
			'accounts' => $accountsAnchor,
		];
	}
	
	/**
	 * Retourne les propriétés HTML de la collection
	 * @return array
	 */
	protected function _htmlAttributes() : array
	{
		$attributes = parent::_htmlAttributes() + [
			'id' => 'owners',
		];
		
		return $attributes;
	}
	
	/**
	 * Retourne la phrase lorsqu'il n'y a pas d'objet
	 * @return string
	 */
	protected function noItemSentence() : string
	{
		$anchor = HTML::anchor(Owner::addUri(), 'ici', [
			'title' => 'Démarrer la création d\'un propriétaire de compte.',
		]);
		return strtr('Aucun propriétaire n\'a été trouvé. Cliquez :anchor pour en créer un.', [
			':anchor' => $anchor,
		]);
	}
	
	/**
	 * Retourne les clés des champs à afficher
	 * @return array
	 */
	protected function _fields() : array
	{
		return [ 'edit', 'accounts', ];
	}
	
	/*****************************************************************/
	
}