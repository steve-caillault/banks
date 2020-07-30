<?php

/**
 * Gestion HTML du menu du budget d'un propriétaire
 */

namespace App\HTML\Menu;

use Root\{ Instanciable, Arr, Request };
/***/
use App\{ Owner, Budget };

class BudgetMenuHTML extends Instanciable {
	
	/**
	 * Propriétaire du compte
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/**
	 * Budget à gérer
	 * @var Budget
	 */
	private $_budget = NULL;
	
	/**
	 * Année du budget
	 * @var int
	 */
	private $_year = NULL;
	
	/*************************************************************/
	
	/**
	 * Constructeur
	 * @param array $params
	 */
	protected function __construct(array $params = [])
	{
		$this->_owner = Arr::get($params, 'owner', $this->_owner);
		if($this->_owner !== NULL AND ! $this->_owner instanceof Owner)
		{
			exception('Propriétaire de compte incorrect.');
		}
		
		$this->_budget = Arr::get($params, 'budget', $this->_budget);
		if($this->_budget !== NULL AND ! $this->_budget instanceof Budget)
		{
			exception('Budget incorrecte.');
		}
		
		$this->_year = Arr::get($params, 'year', $this->_year);
		if($this->_year !== NULL AND ! is_numeric($this->_year))
		{
			exception('Année incorrect');
		}
	}
	
	/*************************************************************/
	
	/**
	 * Retourne le menu
	 * @return MenuHTML
	 */
	public function get() : MenuHTML
	{
		$currentRoute = Request::current()->route()->name();
		
		$menu = OwnerMenuHTML::factory($this->_owner)->get();
		
		$menu->addItem('budget-add', [
			'label' => 'Créer un budget',
			'href' => $this->_owner->addBudgetUri(),
			'class'	=> ($this->_year === NULL AND $currentRoute == 'owners.budgets.add') ? 'selected' : NULL,
			'title' => 'Démarrer la création d\'un budget.',
		]);
		
		if($this->_year !== NULL)
		{
			$menu->addItem('budget-year', [
				'label' => strtr('Budget :year', [ ':year' => $this->_year ]),
				'href' => $this->_owner->budgetUri($this->_year),
				'class'	=> ($currentRoute == 'owners.budgets.year') ? 'selected' : NULL,
				'title' => strtr('Consulter le budget de l\'année :year.', [ ':year' => $this->_year, ]),
			])->addItem('budget-year-add', [
				'label' => 'Ajouter un budget',
				'href' => $this->_owner->addBudgetUri($this->_year),
				'class'	=> ($this->_year !== NULL AND $currentRoute == 'owners.budgets.add') ? 'selected' : NULL,
				'title' => strtr('Ajouter un budget à l\'année :year.', [ ':year' => $this->_year ]),
			]);
		}
		
		if($this->_budget !== NULL)
		{
			$menu->addItem('budget-year-item-edit', [
				'label' => 'Modifier un budget',
				'href' => $this->_budget->editUri(),
				'class'	=> ($currentRoute == 'owners.budget.edit') ? 'selected' : NULL,
				'title' => strtr('Modifier le budget :name de l\'année :year.', [ 
					':year' => $this->_year, 
					':name' => $this->_budget->name,
				]),
			]);
		}
		
		return $menu;
	}
}