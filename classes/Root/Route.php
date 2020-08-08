<?php

/**
 * Gestion d'une route
 */
 
namespace Root;

class Route {

	/**
	 * Nom, clé unique de la route
	 * @var string
	 */
	private string $_name;
	
	/**
	 * Chemin de la route
	 * @var string
	 */
	private string $_uri;
	
	/**
	 * Chemin du contrôleur à affecter à la route
	 * @var string
	 */
	private string $_controller;
	
	/**
	 * Méthode du contrôleur à appeler
	 * @var string
	 */
	private string $_method ;
	
	/**
	 * Retourne les paramètres de la requête
	 * @var array
	 */
	private array $_parameters = [];
	
	/**
	 * Liste des expressions régulières des paramètres dans la route
	 * @var array
	 */
	private array $_where = [];
	
	/**
	 * Liste des paramètres par défaut de la route
	 * @var array
	 */
	private array $_defaults = [];

	/**
	 * Routes affectées
	 * @var array
	 */
	private static array $_list = [];
	
	/**
	 * Route de la requête courante
	 * @var string
	 */
	private static ?self $_current;
	
	/**
	 * Vrai si la requête courante a été initialisé
	 * @var bool
	 */
	private static bool $_current_initialialized = FALSE;
	
	/********************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Constructeur
	 * @param array $params
	 */
	private function __construct(array $params)
	{
		$name = getArray($params, 'name');
		$uri = getArray($params, 'uri');
		
		if($name === NULL)
		{
			exception('Le nom de la route est nécessaire.');
		}
		
		if($uri === NULL)
		{
			exception('Le chemin de la route est obligatoire.');
		}
		
		$this->_name = $name;
		$this->_uri = $uri;
	}
	
	/********************************************************************************/
	
	/**
	 * Retourne la requête courante
	 * @return self
	 */
	public static function current() : ?self
	{
		if(! self::$_current_initialialized)
		{
			self::$_current = NULL;
			self::$_current_initialialized = TRUE;
			$currentUri = Request::detectUri();
			foreach(self::$_list as $route)
			{
				$pattern = $route->_patternUri();
				
				// La route a été trouvé
				if(preg_match_all($pattern, $currentUri) == 1)
				{
					// Récupération des paramètres de l'URI
					$route->_retrieveUriParams();
					return (self::$_current = $route);
				}
			}
		}
		return self::$_current;
	}
	
	/**
	 * Retourne les paramètres dans l'URI
	 * @retur array
	 */
	private function _retrieveUriParams() : array
	{
		$this->_parameters = $this->_defaults;
		
		$paramsByIndices = [];
		$segmentsParams = [];
		$segments = [];
		
		$uriWithoutParenthesis = strtr($this->_uri, [
			'(' => '',
			')' => '',
		]);
		
		preg_match_all('/[^\/]+/', trim($uriWithoutParenthesis, '()'), $segments);
		preg_match_all('/{[^\/]+}/', $uriWithoutParenthesis, $segmentsParams);
		
		foreach(getArray($segmentsParams, 0) as $segmentParams)
		{
			// On détermine la valeurs par défaut du paramètre
			$keyParam = trim($segmentParams, '{}');
			$this->_parameters[$keyParam] = getArray($this->_defaults, $keyParam);
			// On détermine l'indice du paramètre par rapport au nombre de segment
			$paramsByIndices[$keyParam] = NULL;
			foreach(getArray($segments, 0) as $indexSegment => $segment)
			{
				if(preg_match('/' . $segmentParams . '/', $segment) == 1)
				{
					$paramsByIndices[trim($segmentParams, '{}')] = $indexSegment;
				}
			}
		}
		
		// Détermine les valeurs des paramètres de l'URI
		preg_match_all('/[^\/]+/', Request::detectUri(), $segments);
		foreach(getArray($segments, 0) as $indexSegment => $segment)
		{
			if(in_array($indexSegment, $paramsByIndices))
			{
				$keyParam = array_search($indexSegment, $paramsByIndices);
				$rule = getArray($this->_where, $keyParam);
				$paramMatch = [];
				if($rule !== NULL AND preg_match('/(' . $rule . ')+/', $segment, $paramMatch) == TRUE)
				{
					$this->_parameters[$keyParam] = getArray($paramMatch, 0, getArray($this->_parameters, $keyParam));
				}
			}
		}
		
		return $this->_parameters;
	}
	
	/**
	 * Affecte une route
	 * @param string $name Nom / clé unique de la route
	 * @param string $uri Chemin relatif de la route
	 * @return self
	 */
	public static function add(string $name, string $uri, string $action) : self
	{
		if(array_key_exists($name, self::$_list))
		{
			exception(strtr('La route :name existe déjà.', [
				':name' => $name,
			]));
		}
		
		$route = new self([
			'name'	=> $name,
			'uri'	=> $uri,
		]);
		
		$separator = '@';
		$strPosSepAction = strpos($action, $separator);
		if($strPosSepAction === FALSE)
		{
			exception('L\'action de la route est incorrecte.');
		}
		
		list($controller, $method) = explode($separator, $action);
		
		$route->_controller = $controller;
		$route->_method = $method;
		
		self::$_list[$name] = $route;
		
		return $route;
	}
	
	/**
	 * Affecte les expressions régulières pour les paramètres de la route
	 * @param array $where
	 * @return self
	 */
	public function where(array $where) : self
	{
		$this->_where = $where;
		return $this;
	}
	
	/**
	 * Affecte les paramètres par défaut de la route
	 * @param array $defaults
	 * @return self
	 */
	public function defaults(array $defaults) : self
	{
		$this->_defaults = $defaults;
		return $this;
	}
	
	/********************************************************************************/
	
	/* GET */
	
	/**
	 * Retourne le nom de la route
	 * @return string
	 */
	public function name() : string
	{
		return $this->_name;
	}
	
	/**
	 * Retourne une route dont on donne le nom
	 * @param string $name Nom de la route
	 * @return Route
	 */
	public static function retrieve(string $name) : ?self
	{
		return getArray(self::$_list, $name);
	}
	
	/**
	 * Retourne le nom de la classe du contrôleur
	 * @return string
	 */
	public function controller() : string
	{
		return $this->_controller;
	}
	
	/**
	 * Retourne la méthode du contrôleur à exécuter
	 * @return string
	 */
	public function method() : string
	{
		return $this->_method;
	}
	
	/**
	 * Retourne les paramètres de la route
	 * @return array
	 */
	public function parameters() : array
	{
		return $this->_parameters;
	}
	
	/**
	 * Retourne l'expression régulière de l'URI de la route
	 * @return string
	 */
	private function _patternUri() : string
	{
		$pattern = strtr($this->_uri, ['/' => '\/']);

		// Gestion des segments facultatif
		$optionalsSegments = [];
		preg_match_all('/\([^\(\)]+\)/D', $pattern, $optionalsSegments);
		foreach(getArray($optionalsSegments, 0) as $segment)
		{
			$pattern = strtr($pattern, [
				$segment => $segment . '*',
			]);
		}
		
		// Gestion des paramètres
		$searchParams = [];
		preg_match_all('/{[^\/]+}/', $pattern, $searchParams);
		foreach(getArray($searchParams, 0) as $tag)
		{
			$paramKey = trim($tag, '{}');
			if($rule = getArray($this->_where, $paramKey))
			{
				$pattern = strtr($pattern, [
					$tag => '(' . $rule . ')+',
				]);
			}
		}
		
		return '/^(' . $pattern . ')+$/D';
	}
	
	/**
	 * Retourne l'URI de la route
	 * @param array $params Paramètres à affecter à la route
	 * @return string
	 */
	public function uri(array $params = []) : string
	{
		$uri = $this->_uri;
		$searchParams = [];
		preg_match_all('/{[^\/]+}/', $uri, $searchParams);
		
		$uriParams = getArray($searchParams, 0, []);
		
		foreach($uriParams as $tag)
		{
			$paramKey = trim($tag, '{}');
			$default = getArray($this->_defaults, $paramKey);
			$paramValue = getArray($params, $paramKey, $default);
			
			// Le paramètre est obligatoire mais non renseigné, on déclenche une erreur
			if($paramValue === NULL)
			{
				$message = strtr('Le paramètre :param est manquant pour l\'URI :uri.', [
					':param' => $paramKey,
					':uri' => $this->_uri,
				]);
				exception($message);
			}
			
			$uri = strtr($uri, [
				$tag => $paramValue,
			]);
		}
		
		// On supprime les pararenthéses
		return strtr($uri, [
			'('	=> '',
			')'	=> '',
		]);
	}
	
	/********************************************************************************/
	
}