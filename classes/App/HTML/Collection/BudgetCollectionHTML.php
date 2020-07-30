<?php

/**
 * Gestion HTML d'une liste de banques
 */

namespace App\HTML\Collection;

use Root\{ Arr, HTML };
use App\{ Model, Owner };
use App\Collection\{ Collection, BudgetCollection };

class BudgetCollectionHTML extends CollectionHTML {
	
	/**
	 * Propriétaire dont on gére les budgets
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/**
	 * Année du budget géré
	 * @var int
	 */
	private $_year = NULL;
	
	/**
	 * Somme total du budget
	 * @var float
	 */
	private $_total_amount = 0;
	
	/*****************************************************************/
	
	/**
	 * Constructeur
	 * @param array $params
	 */
	protected function __construct(array $params = [])
	{
		// Récupération du propriétaire
		$this->_owner = Arr::get($params, 'owner');
		if(! $this->_owner instanceof Owner)
		{
			exception('Propriétaire incorrect.');
		}
		
		// Récupération de l'année
		$this->_year = Arr::get($params, 'year');
		if(! is_numeric($this->_year))
		{
			exception('Année incorrecte.');
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
		return BudgetCollection::factory()->owner($this->_owner)->year($this->_year);
	}
	
	/*****************************************************************/
	
	/**
	 * Retourne la valeur totale après calcul des dépenses
	 * @return string
	 */
	public function totalAmount() : string
	{
		return number_format($this->_total_amount, 2, ',', ' ');
	}
	
	/*****************************************************************/
	
	/* METHODES DE RENDU */
	
	/**
	 * Retourne les éléments formatés de la liste
	 * @return array
	 */
	protected function _getItems() : array
	{
		$items = parent::_getItems();
		
		$total = 0;
		foreach($this->_collection as $model)
		{
			$total += $model->value();
		}
		
		$this->_total_amount = $total;
		
		return $items;
	}
	
	/**
	 * Formatage des données d'un modèle
	 * @param Model $model
	 * @param mixed Index dans le tableau de la liste
	 * @return array
	 */
	protected function _formatModelData(Model $model, $index) : array
	{
		$uri = $model->editUri();
		
		$anchor = HTML::anchor($uri, $model->name, [
			'title' => strtr('Modifier la valeur :name.', [
				':name' => $model->name,
			]),
		]);
		
		return [
			'attributes' => HTML::attributes([ 'class' => 'line', ]),
			'name' => $anchor,
			'value' => number_format($model->value(), 2, ',', ' '),
		];
	}
	
	/**
	 * Retourne les clés des champs à afficher
	 * @return array
	 */
	protected function _fields() : array
	{
		return [ 'name', 'value', ];
	}
	
	/*****************************************************************/
	
}