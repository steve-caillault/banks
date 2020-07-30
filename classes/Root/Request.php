<?php

/**
 * Gestion d'une requête HTTP
 */
 
namespace Root;

class Request extends Instanciable {

	private const METHOD_GET = 'GET';
	private const METHOD_POST = 'POST';
	
	/**
	 * Route de la requête
	 * @var Route
	 */
	private $_route = NULL;
	
	/**
	 * URI de la requête courante
	 * @var string
	 */
	private static $_current_uri = NULL;
	
	/**
	 * Requête courante
	 * @var Request
	 */
	private static $_current = NULL;
	
	/**
	 * Paramètre en GET
	 * @var array
	 */
	private $_query = NULL;
	
	/**
	 * Données postés ($_POST)
	 * @var array
	 */
	private $_post = NULL;
	
	/**
	 * Fichiers téléchargés ($_FILES)
	 * @var array
	 */
	private $_files = NULL;
	
	/**
	 * Retourne les paramètres de la route
	 * @var array
	 */
	private $_parameters = NULL;
	
	/********************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Constructeur
	 */
	protected function __construct(array $params = [])
	{
		$this->_route = Arr::get($params, 'route', Route::current());
		if(! $this->_route)
		{
			exception('Page introuvable.', 404);
		}
	}
	
	/********************************************************************************/

	/* GET */
	
	/**
	 * Retourne / affecte la requête courante
	 * @return self
	 */
	public static function current() : self
	{
		if(self::$_current === NULL)
		{
			$request = new static;
			self::$_current = $request;
		}
		return self::$_current;
	}
	
	/**
	 * Retourne l'URI courante
	 * @return string
	 */
	public static function detectUri() : string
	{
		if(self::$_current_uri === NULL)
		{
			$scriptName = $_SERVER['SCRIPT_NAME'];
			$requestUri = $_SERVER['REQUEST_URI'];
			
			$baseUri = URL::root();
			$uri = substr($requestUri, strpos($scriptName, $baseUri) + strlen($baseUri));
			
			$pos = strpos($uri, '?');
			if($pos !== FALSE)
			{
				$uri = substr($uri, 0, $pos);
			}
			
			self::$_current_uri = $uri;
		}
		return self::$_current_uri;
	}
	
	/**
	 * Retourne les paramètres en GET
	 * @return array
	 */
	public function query() : array
	{
		if($this->_query === NULL)
		{
			$params = [];
			
			$queryString = filter_input(INPUT_SERVER, 'QUERY_STRING');
			$querySegments = ($queryString == '') ? [] : explode('&', $queryString);
			
			foreach($querySegments as $segment)
			{
				list($key, $value) = explode('=', $segment);
				$params[$key] = ($value == '') ? NULL : $value;
			}
			
			$this->_query = $params;
		}
		
		return $this->_query;
	}
	
	/**
	 * Retourne les données postées ($_POST)
	 * @param array $data Si renseigné, les données à affecter
	 * @return array
	 */
	public function post(array $data = NULL) : array
	{
		if($data !== NULL)
		{
			$this->_post = $data;
		}
		elseif($this->_post === NULL)
		{
			$this->_post = (array) $_POST;
		}
		return $this->_post;
	}
	
	/**
	 * Retourne les fichiers téléchargés ($_FILES)
	 * @return array
	 */
	public function files() : array
	{
		if($this->_files === NULL)
		{
			$this->_files = (array) $_FILES;
		}
		return $this->_files;
	}
	
	/**
	 * Retourne les données envoyées par un formulaire
	 * @return array
	 */
	public function inputs() : array
	{
		$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
		
		$data = ($method == self::METHOD_GET) ? $this->query() : $this->post();
		if(count($data) > 0)
		{
			$files = $this->files();
			$data = array_replace($data, $files);
		}
		
		return $data;
	}
	
	/**
	 * Retourne les paramètres de la route
	 * @return array
	 */
	public function parameters() : array
	{
		return $this->_route->parameters();
	}
	
	/**
	 * Retourne la route de la requête
	 * @return Route
	 */
	public function route() : Route
	{
		return $this->_route;
	}
	
	/**
	 * Retourne si la requête a été appelé en Ajax
	 * @return bool
	 */
	public function isAjax() : bool
	{
		return (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest');
	}
	
	/********************************************************************************/
	
	/**
	 * Réponse de la requête
	 * @return string
	 */
	public function response() : ?string
	{
		$controllerName = $this->_route->controller();
		$method = $this->_route->method();
		
		$controllerClass = 'App\\Controllers\\' . $controllerName;
		
		// Vérifit si le contrôleur existe
		if(! class_exists($controllerClass))
		{
			exception(strtr('Le contrôleur :name n\'existe pas', [
				':name' => $controllerClass,	
			]));
		}
		
		$controller = new $controllerClass();
		
		// Vérifit si la méthode du contrôleur exsite
		if(! method_exists($controller, $method))
		{
			exception(strtr('La méthode :method n\'existe pas pour le contrôleur :controller.', [
				':method'		=> $method,
				':controller'	=> $controllerClass,
			]));
		}
		
		$controller->method($method);
		$controller->request($this);
		
		$controller->before();
		$controller->execute();
		$controller->after(); 
		
		// Exécute la méthode du contrôleur
		return $controller->response();
	}
	
	/********************************************************************************/
	
}