<?php

/**
 * Gestion HTML d'une liste de banques
 */

namespace App\HTML\Collection;

use Root\{ Arr, HTML };
use App\{ Model, Budget, Owner };
use App\Collection\{ Collection, BudgetCollection };

class BudgetYearCollectionHTML extends CollectionHTML {
	
	/**
	 * Type de tri
	 * @var string
	 */
	protected $_order_by = BudgetCollection::ORDER_BY_YEAR;
	
	/**
	 * Sens de direction du tri
	 * @var string
	 */
	protected $_direction = BudgetCollection::DIRECTION_ASC;
	
	/**
	 * Propriétaire dont on gére les budgets
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/*****************************************************************/
	
	/**
	 * Constructeur
	 * @param array $params
	 */
	protected function __construct(array $params = [])
	{
		$this->_owner = Arr::get($params, 'owner');
		if(! $this->_owner instanceof Owner)
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
		$table = Budget::$table;
		
		return BudgetCollection::factory()->select([
			$table . '.year',
		])->owner($this->_owner)->groupBy($table . '.year');
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
		$uri = $this->_owner->budgetUri($model->year);
		
		$anchor = HTML::anchor($uri, $model->year, [
			'title' => strtr('Consulter le budget :year de :name.', [
				':year' => $model->year,
				':name' => $this->_owner->fullName(),
			]),
		]);
		
		return [
			'attributes' => HTML::attributes([ 'class' => 'line', ]),
			'year' => $anchor,
		];
	}
	
	/**
	 * Retourne la phrase lorsqu'il n'y a pas d'objet
	 * @return string
	 */
	protected function noItemSentence() : string
	{
		$anchor = HTML::anchor($this->_owner->addBudgetUri(), 'ici', [
			'title' => 'Démarrer la création d\'un budget.',
		]);
		
		return strtr('Aucune budget n\'a été trouvé. Cliquez :anchor pour en créer un.', [
			':anchor' => $anchor,
		]);
	}
	
	/**
	 * Retourne les clés des champs à afficher
	 * @return array
	 */
	protected function _fields() : array
	{
		return [ 'year', ];
	}
	
	/*****************************************************************/
	
}