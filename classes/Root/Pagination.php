<?php

/**
 * Gestion d'une pagination
 * @author Stève Caillault
 */

namespace Root;

class Pagination extends Instanciable {
	
	private const 
		METHOD_QUERY = 'query',
		METHOD_ROUTE = 'route'
	;
	
	/**
	 * Chemin où se trouve la page où l'on se trouve
	 * Format : route|query:{value} ; exemple : route:page
	 * @var string
	 */
	private string $_page_path = 'route:page';
	
	/**
	 * Page actuelle
	 * @return int
	 */
	private ?int $_current_page = NULL;
	
	/**
	 * Nombre total d'éléments de la pagination
	 * @return int
	 */
	private int $_total_items = 0;
	
	/**
	 * Nombre d'éléments par page
	 * @return int
	 */
	private int $_items_per_page = 20;
	
	/**
	 * Nombre de page
	 * @var int
	 */
	private int $_total_pages = 0;
	/**
	 * Chemin de la vue à utiliser par défaut
	 * @var string
	 */
	private string $_path_view = 'pagination/default';
	
	/*********************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Contructeur
	 * @param array $params
	 */
	protected function __construct($params = NULL)
	{
		$keys = [ 'page_path', 'total_items', 'items_per_page', 'path_view', ];
		
		foreach($keys as $key)
		{
			$property = '_' . $key;
			$this->{ $property } = getArray($params, $key, $this->{ $property });
		}
		
		// Nombre de page
		$this->_total_pages = ($this->_items_per_page == 0 OR $this->_total_items == 0) ? 0 : ceil($this->_total_items / $this->_items_per_page);
		
		// Validation du chemin pour les URIs
		$validPagePath = (bool) preg_match('/(route|query):[^\/.]+/D', $this->_page_path);
		if(! $validPagePath)
		{
			exception('Paramètre page_path de la pagination incorrect.');
		}
		// Numéro de page incorrect
		if($this->_currentPage() > $this->_total_pages)
		{
			$this->_current_page = $this->_total_pages;
		}
	}
	
	/*********************************************************************************/
	
	/* GET */
	
	/**
	 * Retourne le numéro de la page actuelle
	 * @return int
	 */
	private function _currentPage() : int
	{
		if($this->_current_page === NULL)
		{
			list($method, $param) = explode(':', $this->_page_path);
			
			$request = Request::current();
			
			if($method == self::METHOD_QUERY)
			{
				$this->_current_page = getArray($request->query(), $param, 1);
			}
			else
			{
				$this->_current_page = getArray($request->parameters(), $param, 1);
			}
			
		}
		return $this->_current_page;
	}
	
	/**
	 * Détermine l'URL de la page dont on fournit le numéro de la page en paramètre
	 * @param int $number Numéro de la page
	 * @return string
	 */
	public function uri(int $number) : ?string
	{
		$pages = range(1, $this->_total_pages);
		
		if(! in_array($number, $pages))
		{
			return NULL;
		}
		
		list($method, $param) = explode(':', $this->_page_path);
		
		$request = Request::current();
		$route = Route::current();
		
		$parameters = $request->parameters();
		$query = $request->query();
		
		if($method == self::METHOD_QUERY)
		{
			$query[$param] = $number;
		}
		else
		{
			$parameters[$param] = $number;
		}
		
		$uri = $route->uri($parameters);
		
		if(count($query) > 0)
		{
			$uri .= '?' . http_build_query($query);
		}
		
		return getURL($uri);
	}
	
	/*********************************************************************************/
	
	/* METHODE DE RENDUS */
	
	/**
	 * Retourne la vue de la pagination
	 * @return View
	 */
	public function render() : ?View
	{
		if($this->_total_pages < 2)
		{
			return NULL;
		}
		
		$pages = [];
		for($i = 1 ; $i <= $this->_total_pages ; $i++)
		{
			$pages[$i] = $this->uri($i);
		}
		
		$params = [
			'pages' 	=> $pages,
			'current'	=> $this->_currentPage(),
			'total'		=> $this->_total_pages,
		];
		
		return View::factory($this->_path_view, $params);
	}
	
	/*********************************************************************************/
	
}