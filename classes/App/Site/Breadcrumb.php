<?php

/**
 * Gestion d'un fil d'Ariane
 */

namespace App\Site;

use Root\Arr;
use Root\HTML;
use Root\Instanciable;
use Root\Route;
use Root\View;

class Breadcrumb extends Instanciable {
	
	/**
	 * Instance unique pour le site
	 * @var self
	 */
	private static $_instance = NULL;
	
	/**
	 * Eléments du fil d'Ariane
	 * @var array
	 */
	private $_items = [];
	
	/********************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Retourne l'instance unique du fil d'ariane
	 * @return self
	 */
	public static function instance() : self
	{
		if(static::$_instance === NULL)
		{
			$breadcrumb = new static;
			$breadcrumb->add([
				'href'	=> Route::retrieve('home')->uri(),
				'name'	=> 'Accueil',
				'alt'	=> 'Revenir à  la page d\'accueil.',
			]);
			static::$_instance = $breadcrumb;
		}
		return static::$_instance;
	}
	
	/********************************************/
	
	/**
	 * Ajoute un élément au fil d'ariane
	 * @param array $params
	 * @return self
	 */
	public function add(array $params) : self
	{
		$uri = Arr::get($params, 'href');
		
		$title = $params['name'];
	
		if($uri === NULL)
		{
			$item = $title;
		}
		else
		{
			$item = HTML::anchor($uri, $title, [
				'title' => Arr::get($params, 'alt'),
			]);
		}
		
		
		
		$this->_items[] = $item; 
		
		return $this;
	}
	
	/********************************************/
	
	/**
	 * Retourne le HTML du fil d'ariane
	 * @return View
	 */
	public function render() : ?View
	{
		if(count($this->_items) <= 1)
		{
			return NULL;
		}
		
		// On retire l'ancre pour ne garder que le texte du dernier élément
		$lastIndex = count($this->_items) - 1;
		$this->_items[$lastIndex] = strip_tags($this->_items[$lastIndex]);

		$content = View::factory('tools/breadcrumb', [
			'items' => $this->_items,
		]);
		return $content;
	}
	
	/**
	 * Méthode d'affichage
	 * @return View
	 */
	public function __toString() : string
	{
		return ((string) $this->render());
	}
	
	/********************************************/
	
}

