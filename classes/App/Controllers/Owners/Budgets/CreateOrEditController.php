<?php

/**
 * Page création ou d'édition d'un compte d'un propriétaire
 */

namespace App\Controllers\Owners\Budgets;

use App\Controllers\Owners\CreateOrEditController as Controller;
use App\Budget;
use App\Forms\Owners\Budgets\CreateOrEditForm as Form;
use App\HTML\Menu\BudgetMenuHTML;
use App\HTML\Menu\MenuHTML;
/***/
use Root\{ Arr, Redirect };

class CreateOrEditController extends Controller {
	
	/**
	 * L'année du budget gérée
	 * @var int
	 */
	protected $_year = NULL;
	
	/**
	 * Le budget à editer
	 * @var Budget
	 */
	protected $_budget = NULL;
	
	/*************************************************************/
	
	public function before() : void
	{
		parent::before();
		
		// Fil d'Ariane
		$this->_site_breadcrumb->add([
			'href' => $this->_owner->budgetListUri(),
			'name' => 'Budgets',
			'alt' => strtr('Consulter les budgets de :name.', [ ':name' => $this->_owner->fullName(), ]),
		]);
		
		// Chargement de l'année 
		$params = $this->request()->parameters();
		$year = Arr::get($params, 'year');
		
		if($year)
		{
			$this->_year = $year;
			$this->_site_breadcrumb->add([
				'href' => $this->_owner->budgetUri($year),
				'name' => $year,
				'alt' => strtr('Consulter le budgets :year de :name.', [ 
					':year' => $year,
					':name' => $this->_owner->fullName(),
				]),
			]);
		}
		
		// Chargement du budget
		$budgetId = Arr::get($params, 'budgetId');
		if($budgetId !== NULL)
		{
			$this->_budget = Budget::factory($budgetId);
			if($this->_budget === NULL OR ($this->_budget->year != $year OR $this->_budget->owner_id != $this->_owner->id))
			{
				exception('Le budget n\'existe pas.');
			}
			$this->_budget->owner($this->_owner);
			
			// Fil d'Ariane
			$this->_site_breadcrumb->add([
				'href' => $this->_budget->editUri(),
				'name' => $this->_budget->name,
				'alt' => 'Modifier le budget.',
			]);
		}
	}
	
	public function index() : void
	{
		// Fil d'Ariane
		if($this->_budget === NULL)
		{
			$this->_site_breadcrumb->add([
				'href' => $this->_owner->addBudgetUri($this->_year),
				'name' => 'Création',
				'alt' => 'Ajout d\'un budget.',
			]);
		}
		else
		{
			$this->_site_breadcrumb->add([
				'href' => $this->_budget->editUri(),
				'name' => 'Edition',
				'alt' => 'Modifier le budget.',
			]);
		}
		
		$data = $this->request()->inputs();
		
		$form = Form::factory([
			'owner' => $this->_owner,
			'budget' => $this->_budget,
			'year' => $this->_year,
			'data'	=> $data,
		]);
		
		if(count($data) > 0)
		{
			$form->process();
			if($form->success())
			{
				Redirect::process($form->redirectUrl());
			}
		}
		
		$this->_page_title = $form->title();
		
		$this->_main_content = $form->render();
	}
	
	/**
	 * Retourne le menu secondaire
	 * @return MenuHTML
	 */
	protected function _secondaryMenu() : ?MenuHTML
	{
		return BudgetMenuHTML::factory([
			'owner' => $this->_owner,
			'budget' => $this->_budget,
			'year' => $this->_year,
		])->get();
	}
	
}