<?php

/**
 * Gestion du menu d'un propriétaire de compte bancaire 
 */

namespace App\HTML\Menu;

use Root\{ Instanciable, Request };
/***/
use App\{ Owner };

class OwnerMenuHTML extends Instanciable {
	
	/**
	 * Propriétaire à gérer
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/*************************************************************/
	
	/**
	 * Constructeur
	 * @param Owner $owner
	 */
	protected function __construct(Owner $owner = NULL)
	{
		$this->_owner = $owner;
	}
	
	/*************************************************************/
	
	/**
	 * Retourne le menu des pages liées aux propriétaires
	 * @return MenuHTML
	 */
	public function get() : MenuHTML
	{
		$currentRoute = Request::current()->route()->name();
		
		$owner = $this->_owner;
		
		$menu = MenuHTML::factory(MenuHTML::TYPE_SECONDARY)->addItem('owner-list', [
			'label' => 'Liste des propriétaires',
			'href' 	=> Owner::listUri(),
			'class' => ($currentRoute == 'owners.list') ? 'selected' : '',
			'title' => 'Consulter la liste des propriétaires.',
		])->addItem('add-owner', [
			'label'	=> 'Ajouter un propriétaire',
			'href'	=> Owner::addUri(),
			'class'	=> ($currentRoute == 'owners.add') ? 'selected' : '',
			'title'	=> 'Ajouter un propriétaire.',
		]);
		
		if($owner !== NULL)
		{
			$menu->addItem('owner-edit', [
				'label' => 'Modifier le propriétaire',
				'href' 	=> $owner->editUri(),
				'class' => ($currentRoute == 'owners.edit') ? 'selected' : '',
				'title' => strtr('Modifier le compte de :name.', [ ':name' => $owner->fullName(), ]),
			])->addItem('add-account', [
				'label'	=> 'Ajouter un compte',
				'href'	=> $owner->addAccountUri(),
				'class'	=> ($currentRoute == 'owners.accounts.add') ? 'selected' : NULL,
				'title'	=> strtr('Ajouter un compte à :name.', [ ':name' => $owner->fullName(), ]),
			])->addItem('owner-accounts', [
				'label'	=> 'Liste des comptes',
				'href'	=> $owner->accountsUri(),
				'class'	=> ($currentRoute == 'owners.accounts.list') ? 'selected' : '',
				'title'	=> strtr('Consulter la liste des comptes de :name.', [ ':name' => $owner->fullName(), ]),
			])->addItem('owner-evolution', [
				'label'	=> 'Evolution des comptes',
				'href'	=> $owner->evolutionUri(),
				'class'	=> ($currentRoute == 'owners.evolution') ? 'selected' : '',
				'title'	=> strtr('Consulter l\'évolution des comptes de :name.', [ ':name' => $owner->fullName(), ]),
			])->addItem('owner-comparison', [
				'label' => 'Comparaison des années',
				'href' => $owner->comparisonUri(),
				'class' => ($currentRoute == 'owners.comparison') ? 'selected' : '',
				'title'	=> strtr('Consulter la comparaison des années des comptes de :name.', [ ':name' => $owner->fullName(), ]),
			])->addItem('owner-budgets', [
				'label' => 'Gestion des bugdets',
				'href' => $owner->budgetListUri(),
				'class'	=> ($currentRoute == 'owners.budgets.list') ? 'selected' : '',
				'title'	=> strtr('Consulter les budgets de :name.', [ ':name' => $owner->fullName(), ]),
			]);
		}
		
		return $menu;
	}
	
	/*************************************************************/
	
}