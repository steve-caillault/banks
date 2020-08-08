<?php

/**
 * Gestion d'une requête HTTP
 */
 
namespace Root;

class Request extends Instanciable {

	private const 
		METHOD_GET = 'GET',
		METHOD_POST = 'POST',
		/***/
		PROTOCOL_HTTP = 'http',
		PROTOCOL_HTTPS = 'https'
	;
	
	/**
	 * Route de la requête
	 * @var Route
	 */
	private ?Route $_route = NULL;
	
	/**
	 * URI de la requête courante
	 * @var string
	 */
	private static ?string $_current_uri = NULL;
	
	/**
	 * Requête courante
	 * @var Request
	 */
	private static ?Request $_current = NULL;
	
	/**
	 * Protocol
	 * @var string
	 */
	private static ?string $_protocol = NULL;
	
	/**
	 * Paramètre en GET
	 * @var array
	 */
	private ?array $_query = NULL;
	
	/**
	 * Données postés ($_POST)
	 * @var array
	 */
	private ?array $_post = NULL;
	
	/**
	 * Fichiers téléchargés ($_FILES)
	 * @var array
	 */
	private ?array $_files = NULL;
	
	/********************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Constructeur
	 */
	protected function __construct(array $params = [])
	{
		$this->_route = getArray($params, 'route', Route::current());
		if(! $this->_route)
		{
			exception('Page introuvable.', 404);
		}
	}
	
	/********************************************************************************/

	/* GET */
	
	/**
	 * Retourne / affecte la requête courante
	 * @param self $request Si renseigné, la requête a affecter
	 * @return self
	 */
	public static function current(?self $request = NULL) : self
	{
		if($request !== NULL)
		{
			self::$_current = $request;
		}
		elseif(self::$_current === NULL)
		{
			$request = new static;
			self::$_current = $request;
		}
		return self::$_current;
	}
	
	/**
	 * Retourne le protocol des réquêtes
	 * @return string
	 */
	public static function protocol() : string
	{
		if(self::$_protocol === NULL)
		{
			$allowedProtocols = [ self::PROTOCOL_HTTP, self::PROTOCOL_HTTPS, ];
			$protocol = self::PROTOCOL_HTTP;
			$scheme = getArray($_SERVER, 'REQUEST_SCHEME');
			$formardedProtocol = getArray($_SERVER, 'HTTP_X_FORWARDED_PROTO');
			$withSSL = (getArray($_SERVER, 'HTTPS') === 'on');
			
			if($withSSL)
			{
				$protocol = self::PROTOCOL_HTTPS;
			}
			elseif($scheme !== NULL)
			{
				$protocol = strtoupper($scheme);
			}
			elseif($formardedProtocol !== NULL)
			{
				$protocol = strtoupper($formardedProtocol);
			}
			else
			{
				$protocol = self::PROTOCOL_HTTP;
			}
			
			if(! in_array($protocol, $allowedProtocols))
			{
				$protocol = self::PROTOCOL_HTTP;
			}
			
			self::$_protocol = $protocol;
		}
		return self::$_protocol;
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
			
			$queryString = getArray($_SERVER, 'QUERY_STRING', NULL);
			$querySegments = ($queryString == '') ? [] : explode('&', $queryString);
			
			foreach($querySegments as $segment)
			{
				if(strpos($segment, '=') !== FALSE)
				{
					list($key, $value) = explode('=', $segment);
					$params[$key] = ($value == '') ? NULL : urldecode($value);
				}
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
		$method = getArray($_SERVER, 'REQUEST_METHOD', self::METHOD_GET);
		
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
	 * Retourne le paramètre de la route dont la clé est en paramètre
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function parameter(string $key, $default = NULL)
	{
		return getArray($this->parameters(), $key, $default);
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
		return (strtolower(getArray($_SERVER, 'HTTP_X_REQUESTED_WITH', NULL)) == 'xmlhttprequest');
	}
	
	/**
	 * Retourne si la requête est appelé en ligne de commande
	 * @return bool
	 */
	public static function isCLI() : bool
	{
		return (strtolower(php_sapi_name()) == 'cli');
	}
	
	/********************************************************************************/
	
	/**
	 * Réponse de la requête
	 * @return string
	 */
	public function response() : ?Response
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
		
		$controller = new $controllerClass;
		
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
		$response = $controller->response();
		
		// Envoie les en-têtes
		$response->sendHeaders();
		
		return $response;
	}
	
	/********************************************************************************/
	
}