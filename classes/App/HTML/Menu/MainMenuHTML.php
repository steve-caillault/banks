<?php

/**
 * Menu principal du site
 */

namespace App\HTML\Menu;

use Root\Instanciable;
use Root\View;
/***/
use App\Owner;
use App\Bank;

class MainMenuHTML extends Instanciable
{
	/**
	 * Menu du site
	 * @var MenuHTML
	 */
	private $_menu = NULL;
	
	/****************************************************************************************************************************/
	
	/**
	 * Constructeur
	 * Initialise le menu en affectant tous les items
	 */
	protected function __construct()
	{
		$this->_menu = MenuHTML::factory(MenuHTML::TYPE_PRIMARY)->addItem('owners', [
			'label'		=> 'Propriétaires',
			'href'		=> Owner::listUri(),
			'title'		=> 'Consulter la liste des propriétaires de comptes.',
		])->addItem('owners::add', [
			'label'		=> 'Ajouter un propriétaire',
			'href'		=> Owner::addUri(),
			'title'		=> 'Démarrer la gestion d\'un propriétaire de compte.',
		])->addItem('banks', [
			'label' 	=> 'Banques',
			'href'		=> Bank::listUri(),
			'title'		=> 'Cosnulter la liste des banques.',
		])->addItem('banks::add', [
			'label'		=> 'Ajouter une banque',
			'href'		=> Bank::addUri(),
			'title'		=> 'Démarrer la gestion d\'une banque.',
		]);
	}
	
	/****************************************************************************************************************************/
	
	/**
	 * Affichage
	 * @return string
	 */
	public function __toString() : string
	{
		return (string) $this->_menu;
	}
	
	/**
	 * Méthode de rendu
	 * @return View
	 */
	public function render() : ?View
	{
		if($this->_menu === NULL)
		{
			return NULL;
		}
		return $this->_menu->render();
	}
	
	/****************************************************************************************************************************/
}