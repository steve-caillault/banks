<?php

/**
 * Page de la création et de l'édition d'un propriétaire de compte
 */

namespace App\Controllers\Owners;

use App\Controllers\HTML\ContentController;
/***/
use App\{ Owner, User };
use App\Forms\Owners\CreateOrEditForm as Form;
use App\HTML\Menu\OwnerMenuHTML;
use App\HTML\Menu\MenuHTML;
/***/
use Root\{ Arr, Redirect };

class CreateOrEditController extends ContentController {
	
	/**
	 * Propriétaire à éditer
	 * @var Owner
	 */
	protected $_owner = NULL;
	
	public function before() : void
	{
		parent::before();
		
		$user = User::current();
		
		// Fil d'Ariane
		$this->_site_breadcrumb->add([
			'href' => Owner::listUri(),
			'name' => 'Propriétaires',
			'alt' => 'Consulter la liste des propriétaires de comptes.',
		]);
		
		// Chargement du propriétaire
		$id = Arr::get($this->request()->parameters(), 'ownerId', NULL);
		if($id !== NULL)
		{
			$this->_owner = Owner::factory($id);
			if($this->_owner === NULL OR $this->_owner->user_id != $user->id)
			{
				exception('Le propriétaire n\'existe pas.', 404);
			}
			
			// Fil d'ariane
			$this->_site_breadcrumb->add([
				'href' => $this->_owner->editUri(),
				'name' => $this->_owner->fullName(),
				'alt' => strtr('Modifier les informations de :name.', [ ':name' => $this->_owner->fullName(), ]),
			]);
		}
		
		
		
		
	}
	
	/**
	 * Création et édition d'un propriétaire
	 */
	public function index() : void
	{
		$data = $this->request()->inputs();
		
		$form = Form::factory([
			'owner' => $this->_owner,
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
		return OwnerMenuHTML::factory($this->_owner)->get();
	}
	
	public function after() : void
	{
		// Gestion du menu secondaire
		$this->_menus[MenuHTML::TYPE_SECONDARY] = $this->_secondaryMenu();
		
		parent::after();
	}
	
}