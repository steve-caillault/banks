<?php

/**
 * Page de création ou d'édition d'une opération bancaire
 */

namespace App\Controllers\Owners\Accounts\Operations;

use App\Controllers\Owners\Accounts\CreateOrEditController as Controller;
use App\Forms\Owners\Accounts\Operations\CreateOrEditForm as Form;
use App\Operation;
/***/
use Root\{
	Arr,
	Redirect
};

class CreateOrEditController extends Controller {
	
	/**
	 * Opération bancaire à gérer
	 * @var Operation
	 */
	protected $_operation = NULL;
	
	/**************************************************************/
	
	public function before() : void
	{
		parent::before();
		
		// Fil d'Ariane
		$this->_site_breadcrumb->add([
			'href' => $this->_account->operationsUri(),
			'name' => 'Opérations',
			'alt' => strtr('Consulter la liste des opérations bancaire du compte :name.', [ ':name' => $this->_account->name, ]),
		]);
		
		// Chargement de l'opération à editer
		$id = Arr::get($this->request()->parameters(), 'operationId', NULL);
		if($id)
		{
			$this->_operation = Operation::factory($id);
			if($this->_operation === NULL OR $this->_operation->account_id != $this->_account->id)
			{
				exception('L\'opération bancaire ne correspond pas.');
			}
			
			// Fil d'Ariane
			$this->_site_breadcrumb->add([
				'href' => $this->_operation->editUri(),
				'name' => $this->_operation->name,
				'alt' => strtr('Modifier l\'opération :name.', [ ':name' => $this->_operation->name, ]),
			]);
		}
	}
	
	/**************************************************************/
	
	public function index() : void
	{
		// Fil d'Ariane
		if($this->_operation === NULL)
		{
			$this->_site_breadcrumb->add([
				'href' => $this->_account->addOperationUri(),
				'name' => 'Ajouter',
				'alt' => strtr('Ajouter une opération au compte :name.', [ ':name' => $this->_account->name, ]),
			]);
		}
		else
		{
			$this->_site_breadcrumb->add([
				'href' => $this->_operation->editUri(),
				'name' => 'Editer',
				'alt' => strtr('Modifier l\'opération :name.', [ ':name' => $this->_operation->name, ]),
			]);
		}
		
		$data = $this->request()->inputs();
		
		$form = Form::factory([
			'operation' => $this->_operation,
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
	
	/**************************************************************/

}