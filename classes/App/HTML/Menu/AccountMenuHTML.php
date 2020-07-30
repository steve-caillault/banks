<?php

/**
 * Gestion HTML du menu d'un compte bancaire
 */

namespace App\HTML\Menu;

use Root\{ Instanciable, Arr, Request };
/***/
use App\{ Owner, Account };

class AccountMenuHTML extends Instanciable {
	
	/**
	 * Propriétaire du compte
	 * @var Owner
	 */
	private $_owner = NULL;
	
	/**
	 * Compte bancaire à gérer
	 * @var Account
	 */
	private $_account = NULL;
	
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
		
		$this->_account = Arr::get($params, 'account', $this->_account);
		if($this->_account !== NULL AND ! $this->_account instanceof Account)
		{
			exception('Compte bancaire incorrect.');
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
		
		if($this->_account !== NULL)
		{
			$accountName = $this->_account->name;
			$owner = $this->_account->owner();
			
			$menu->addItem('account-operations-list', [
				'label' => 'Liste des opérations',
				'href' => $this->_account->operationsUri(),
				'class'	=> ($currentRoute == 'owners.accounts.operations.list') ? 'selected' : NULL,
				'title' => strtr('Consulter les opérations sur le compte :name.', [ ':name' => $accountName, ]),
			])->addItem('account-operations-add', [
				'label' => 'Ajouter une opération',
				'href' => $this->_account->addOperationUri(),
				'class' => ($currentRoute == 'owners.accounts.operations.add') ? 'selected' : NULL,
				'title' => strtr('Ajouter une opération au compte :name.', [ ':name' => $accountName, ]),
			])->addItem('account-evolution', [
				'label' => 'Evolution du compte',
				'href' => $this->_account->evolutionUri(),
				'class' => ($currentRoute == 'owners.accounts.evolution') ? 'selected' : NULL,
				'title' => strtr('Evolution de la valeur du compte :name.', [ ':name' => $accountName, ]),
			]);
		}
		
		return $menu;
	}
}