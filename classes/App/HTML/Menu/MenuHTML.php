<?php

/**
 * Gestion des menus du site
 * @author Stève Caillault
 */

namespace App\HTML\Menu;

use Root\Arr;
use Root\HTML;
use Root\Instanciable;
use Root\View;

class MenuHTML extends Instanciable
{
	const TYPE_PRIMARY		= 'primary';
	const TYPE_SECONDARY	= 'secondary';
	const TYPE_TERTIARY		= 'tertiary';
	const TYPE_MODULES		= 'modules'; // Utilisé sur la page d'accueil du panneau d'administration
	
	/**
	 * Le type de menu
	 * @var string $_type
	 */
	private $_type = self::TYPE_PRIMARY;
	
	/**
	 * Tableau des éléments du menu
	 * @var array $_items
	 */
	private $_items = [];
	
	/**
	 * Compte le nombre d'élèments ajoutés
	 * @var int
	 */
	private $_count_elements = 0; 
	
	/****************************************************************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Constructeur
	 * @param string $type Le type de menu
	 */
	protected function __construct(string $type)
	{
		$this->_type = $type;
		$this->_items = [
			self::TYPE_PRIMARY => [],
		];
	}
	
	/****************************************************************************************************************************/
	
	/**
	 * Ajoute un élément au menu
	 * @param string Clé de l'élement (level1::level2::level3)
	 * @param array $item Les données de l'objet à ajouter au menu
	 * @return self
	 */
	public function addItem(string $keys, array $item) : self
	{
		$keys = explode('::', $keys);
		foreach($keys as $level => $key)
		{
			$type = self::_type($level);
		}
		
		$label = Arr::get($item, 'label');
		$href = Arr::get($item, 'href');
		$attributes = [
			'class'	=> Arr::get($item, 'class'),
			'title'	=> Arr::get($item, 'title'),
		];
		$newItem = [
			'class'		=> trim($type . ' ' . Arr::get($item, 'class')),
			'anchor'	=> HTML::anchor($href, $label, $attributes),
		];
		switch($type)
		{
			case self::TYPE_PRIMARY:
				$this->_items[$type][$key]['item'] = $newItem;
				break;
			case self::TYPE_SECONDARY:
				$this->_items[self::TYPE_PRIMARY][$keys[0]][$type][$key]['item'] = $newItem;
				break;
			case self::TYPE_TERTIARY:
				$this->_items[self::TYPE_PRIMARY][$keys[0]][self::TYPE_SECONDARY][$keys[1]][$type][$key]['item'] = $newItem;
				break;
		}
		
		$this->_count_elements++;
		
		return $this;
	}
	
	/****************************************************************************************************************************/
	
	/**
	 * Affichage
	 * @return string
	 */
	public function __toString() : string
	{
		$view = $this->render();
		return ($view) ? $view->render() : '';
	}
	
	/**
	 * Méthode de rendu
	 * @return View
	 */
	public function render() : ?View
	{
		if($this->_count_elements == 0)
		{
			return NULL;
		}
		
		return View::factory('site/menu/item', [
			'type'    => $this->_type,
			'items'   => $this->_items,
		]);
	}
	
	/****************************************************************************************************************************/
	
	/**
	 * Retourne le type du niveau du menu
	 * @param int $level Le niveau que l'on recherche
	 * @return int
	 */
	private static function _type(int $level) : string
	{
		switch($level)
		{
			case 0:
				return self::TYPE_PRIMARY;
			case 1:
				return self::TYPE_SECONDARY;
			case 2:
				return self::TYPE_TERTIARY;
			default:
				exception('Le type de la clé n\'est pas reconnu.');
		}
	}
	
	/****************************************************************************************************************************/
	
}