<?php

/**
 * Page de la création et de l'édition d'une banque
 */

namespace App\Controllers\Banks;

use App\Controllers\HTML\ContentController as Controller;
/***/
use App\Bank;
use App\Forms\Banks\CreateOrEditForm as Form;
use App\HTML\Menu\{ MenuHTML, BankMenuHTML };
/***/
use Root\{ Arr, Redirect };

class CreateOrEditController extends Controller {
	
	/**
	 * Banque à éditer
	 * @var Bank
	 */
	protected $_bank = NULL;
	
	/**************************************************************/
	
	public function before() : void
	{
		parent::before();
		
		// Fil d'Ariane
		$this->_site_breadcrumb->add([
			'href' => Bank::listUri(),
			'name' => 'Banques',
			'alt' => 'Consulter la liste des banques.',
		]);
		
		// Chargement de la banque
		$id = Arr::get($this->request()->parameters(), 'bankId', NULL);
		if($id !== NULL)
		{
			$this->_bank = Bank::factory($id);
			if($this->_bank === NULL)
			{
				exception('La banque n\'existe pas.');
			}
			
			// Fil d'Ariane
			$this->_site_breadcrumb->add([
				'href' => $this->_bank->editUri(),
				'name' => $this->_bank->name,
				'alt' => strtr('Modifier les informations de la banque :name.', [ ':name' => $this->_bank->name, ]),
			]);
		}
	}
	
	/**************************************************************/
	
	/**
	 * Création et édition d'une banque
	 */
	public function index() : void
	{
		// Fil d'Ariane
		if($this->_bank === NULL)
		{
			$this->_site_breadcrumb->add([
				'href' => Bank::addUri(),
				'name' => 'Création',
				'alt' => 'Création d\'une banque.',
			]);
		}
		else
		{
			$this->_site_breadcrumb->add([
				'href' => $this->_bank->editUri(),
				'name' => 'Edition',
				'alt' => strtr('Modifier les informations de :name.', [ ':name' => $this->_bank->name, ]),
			]);
		}
		
		$data = $this->request()->inputs();
		
		$form = Form::factory([
			'bank' => $this->_bank,
			'data'		=> $data,
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
	
	/**************************************************************/
	
	/**
	 * 
	 */
	public function after() : void
	{
		// Gestion du menu secondaire
		$this->_menus[MenuHTML::TYPE_SECONDARY] = BankMenuHTML::factory($this->_bank)->get();
		parent::after();
	}
	
}