<?php

/**
 * Gestion du menu des pages gérant les banques
 */

namespace App\HTML\Menu;

use Root\{ Instanciable, Request };
/***/
use App\{ Bank };

class BankMenuHTML extends Instanciable {
	
	/**
	 * Banque à gérer
	 * @var Bank
	 */
	private $_bank = NULL;
	
	/**********************************************************/
	
	/**
	 * Constructeur
	 * @param Bank $bank
	 */
	protected function __construct(?Bank $bank = NULL)
	{
		$this->_bank = $bank;
	}
	
	/**********************************************************/
	
	/**
	 * Retourne le menu des pages liées aux propriétaires
	 * @return MenuHTML
	 */
	public function get() : MenuHTML
	{
		$currentRoute = Request::current()->route()->name();
		
		return MenuHTML::factory(MenuHTML::TYPE_SECONDARY)->addItem('bank-list', [
			'label' => 'Liste des banques',
			'href' 	=> Bank::listUri(),
			'class' => ($currentRoute == 'banks.list') ? 'selected' : '',
			'title' => 'Consulter la liste des banques.',
		])->addItem('add-bank', [
			'label'	=> 'Ajouter une banque',
			'href'	=> Bank::addUri(),
			'class' => ($currentRoute == 'banks.add') ? 'selected' : '',
			'title'	=> 'Ajouter un banque.',
		]);
	}
	
	/**********************************************************/
	
}