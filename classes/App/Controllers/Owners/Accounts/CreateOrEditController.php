<?php

/**
 * Page création ou d'édition d'un compte d'un propriétaire
 */

namespace App\Controllers\Owners\Accounts;

use App\Controllers\Owners\CreateOrEditController as Controller;
use App\Forms\Owners\Accounts\CreateOrEditForm as Form;
use App\Account;
use App\HTML\Menu\{ MenuHTML, AccountMenuHTML };
/***/
use Root\{ Arr, Redirect };

class CreateOrEditController extends Controller {
	
	/**
	 * Compte à editer
	 * @var Account
	 */
	protected $_account = NULL;
	
	public function before() : void
	{
		parent::before();
		
		// Fil d'Ariane
		$this->_site_breadcrumb->add([
			'href' => $this->_owner->accountsUri(),
			'name' => 'Comptes',
			'alt' => strtr('Consulter la liste des comptes de :name.', [ ':name' => $this->_owner->fullName(), ]),
		]);
		
		// Chargement du compte à editer
		$id = Arr::get($this->request()->parameters(), 'accountId', NULL);
		if($id)
		{
			$this->_account = Account::factory($id);
			if($this->_account === NULL OR $this->_account->owner_id != $this->_owner->id)
			{
				exception('Le compte du propriétaire n\'existe pas.');
			}
			$this->_account->owner($this->_owner);
			
			// Fil d'Ariane
			$this->_site_breadcrumb->add([
				'href' => $this->_account->editUri(),
				'name' => $this->_account->name,
				'alt' => strtr('Modifier les informations du compte :name.', [ ':name' => $this->_account->name, ]),
			]);
		}
	}
	
	public function index() : void
	{
		// Fil d'Ariane
		if($this->_account === NULL)
		{
			$this->_site_breadcrumb->add([
				'href' => $this->_owner->addAccountUri(),
				'name' => 'Création',
				'alt' => strtr('Création d\'un compte pour :name.', [ ':name' => $this->_owner->fullName(), ]),
			]);
		}
		else
		{
			$this->_site_breadcrumb->add([
				'href' => $this->_account->editUri(),
				'name' => 'Edition',
				'alt' => strtr('Modifier les informations du compte :name.', [ ':name' => $this->_account->name, ]),
			]);
		}
		
		$data = $this->request()->inputs();
		
		$form = Form::factory([
			'owner' => $this->_owner,
			'account' => $this->_account,
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
	protected function _secondaryMenu() : MenuHTML
	{
		return AccountMenuHTML::factory([
			'account' => $this->_account,
			'owner' => $this->_owner,
		])->get();
	}
}