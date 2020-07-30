<?php

/**
 * Gestion du HTML d'une collection
 */

namespace App\HTML\Collection;

use Root\{ Instanciable, Arr, Pagination, Request, View, HTML };
use App\{ Model, Collection\Collection };

abstract class CollectionHTML extends Instanciable {
	
	protected const ITEMS_PER_PAGE = 20;
	
	/**
	 * Collection des modèles sélectionnés
	 * @var Collection
	 */
	protected $_collection = NULL;
	
	/**
	 * Type de tri
	 * @var string
	 */
	protected $_order_by = NULL;
	
	/**
	 * Sens de direction du tri
	 * @var string
	 */
	protected $_direction = Collection::DIRECTION_DESC;
	
	/**
	 * Nombre d'éléments à retourner
	 * @var int
	 */
	protected $_limit = self::ITEMS_PER_PAGE;
	
	/**
	 * Offset
	 * @var int
	 */
	protected $_offset = 0;
	
	/**
	 * Numéro de la page courante
	 * @var int
	 */
	protected $_page		= 1;
	
	/**
	 * Nombre total d'éléments
	 * @var int
	 */
	protected $_total_items	= NULL;
	
	/**
	 * Nom de la vue utilisée pour l'affichage
	 * @var string
	 */
	protected $_view_name	= 'items/list';
	
	/**
	 * Objets formatés de la collection
	 * @var array
	 */
	protected $_items = NULL;
	
	/*****************************************************************/
	
	/**
	 * Constructeur
	 * @param array $params
	 */
	protected function __construct(array $params = [])
	{
		$parameters = Request::current()->parameters();
		
		$page = Arr::get($parameters, 'page', $this->_page);
		if(is_numeric($page) AND $page >= 1)
		{
			$this->_page = (int) $page;
		}
		
		$this->_offset = ($this->_page - 1) * $this->_limit;
	}
	
	/*****************************************************************/
	
	/* GESTION DE LA LISTE */
	
	/**
	 * Initialisation de la collection
	 * @return Collection
	 */
	abstract protected function _initCollection() : Collection;
	
	/**
	 * Retourne la liste des modèles retournés par la requête
	 * @return array
	 */
	protected function _collection() : array
	{
		if($this->_collection === NULL)
		{
			$this->_retrieveCollection();
		}
		return $this->_collection;
	}
	
	/**
	 * Récupération de la liste des modèles (éxécution des requêtes)
	 * @return void
	 */
	protected function _retrieveCollection() : void
	{
		$this->_collection = $this->_initCollection();
		
		// Tri
		if($this->_order_by !== NULL)
		{
			$this->_collection->orderBy($this->_order_by, $this->_direction);
		}
		
		$collection = $this->_collection;
		
		// Nombre total d'élèments
		$this->_total_items = $collection->totalCount();
		
		// Limit, offset
		$this->_collection = $this->_collection->get($this->_limit, $this->_offset);
	}
	
	/*****************************************************************/
	
	/* METHODES DE RENDU */
	
	/**
	 * Retourne la pagination
	 * @return Pagination
	 */
	protected function _pagination() : Pagination
	{
		return Pagination::factory([
			'total_items' => $this->_total_items,
		]);
	}
	
	/**
	 * Retourne les éléments formatés
	 * @return array
	 */
	protected function _items() : array
	{
		if($this->_items === NULL)
		{
			$this->_items = $this->_getItems();
		}
		return $this->_items;
	}
	
	/**
	 * Retourne les éléments formatés de la liste
	 * @return array
	 */
	protected function _getItems() : array
	{
		// Récupération de la liste
		$collection = $this->_collection();
		
		// Formatage des données
		$items = [];
		foreach($collection as $index => $model)
		{
			$items[] = $this->_formatModelData($model, $index);
		}
		
		return $items;
	}
	
	/**
	 * Formatage des données d'un modèle
	 * @param Model $model
	 * @param mixed Index dans le tableau de la liste
	 * @return array
	 */
	abstract protected function _formatModelData(Model $model, $index) : array;
	
	/**
	 * Retourne les propriétés HTML de la collection
	 * @return array
	 */
	protected function _htmlAttributes() : array
	{
		return [
			'class' => 'collection',
		];
	}
	
	/**
	 * Retourne la phrase lorsqu'il n'y a pas d'objet
	 * @return string
	 */
	protected function noItemSentence() : string 
	{
		return 'Aucun élément n\'a été trouvé.';
	}
	
	/**
	 * Retourne les clés des champs à afficher
	 * @return array 
	 */
	abstract protected function _fields() : array;
	
	/**
	 * Rendu de la liste
	 * @return View
	 */
	public function render() : View
	{
		$items = $this->_items();
		
		$params = [
			'noItemSentence' => $this->noItemSentence(),
			'fields' => $this->_fields(),
			// 'labels'	=> $this->labelsFields(),
			'totalItems' => $this->_total_items,
			'items' => $items,
			'pagination' => $this->_pagination()->render(),
			'collectionAttributes'	=> HTML::attributes($this->_htmlAttributes()),
		];
		
		return View::factory($this->_view_name, $params);
	}
	
	/*****************************************************************/
	
}