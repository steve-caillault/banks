<?php

/**
 * Gestion HTML d'une liste de banques
 */

namespace App\HTML\Collection;

use Root\{ HTML };
use App\{ Model, Bank };
use App\Collection\{ Collection, BankCollection };

class BankCollectionHTML extends CollectionHTML {
	
	/**
	 * Type de tri
	 * @var string
	 */
	protected $_order_by = BankCollection::ORDER_BY_NAME;
	
	/**
	 * Sens de direction du tri
	 * @var string
	 */
	protected $_direction = BankCollection::DIRECTION_ASC;
	
	/*****************************************************************/
	
	/* GESTION DE LA LISTE */
	
	/**
	 * Initialisation de la collection
	 * @return Collection
	 */
	protected function _initCollection() : Collection
	{
		return BankCollection::factory();
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
		$name = HTML::anchor($model->editUri(), $model->name, [
			'title' => strtr('Modifier les informations de :name.', [ ':name' => $model->name, ]),
		]);
		
		return [
			'attributes' => HTML::attributes([ 'class' => 'line', ]),
			'name' => $name,
		];
	}
	
	/**
	 * Retourne les propriétés HTML de la collection
	 * @return array
	 */
	protected function _htmlAttributes() : array
	{
		$attributes = parent::_htmlAttributes() + [
			'id' => 'banks',
		];
		
		return $attributes;
	}
	
	/**
	 * Retourne la phrase lorsqu'il n'y a pas d'objet
	 * @return string
	 */
	protected function noItemSentence() : string 
	{
		$anchor = HTML::anchor(Bank::addUri(), 'ici', [
			'title' => 'Démarrer la création d\'une banque.',
		]);
		
		return strtr('Aucune banque n\'a été trouvé. Cliquez :anchor pour en créer une.', [
			':anchor' => $anchor,
		]);
	}
	
	/**
	 * Retourne les clés des champs à afficher
	 * @return array
	 */
	protected function _fields() : array
	{
		return [ 'name', ];
	}
	
	/*****************************************************************/
	
}