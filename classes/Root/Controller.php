<?php

/**
 * Gestion d'un contrôleur
 */

namespace Root;

abstract class Controller {
	
	/**
	 * Méthode à éxécuter
	 * @var string
	 */
	private $_method = NULL;
	
	/**
	 * La requête du contrôleur
	 * @var Request
	 */
	private $_request = NULL;
	
	/**
	 * Réponse de la méthode principale du contrôleur
	 * @var mixed
	 */
	protected $_response = NULL;
	
	/********************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Constructeur
	 */
	public function __construct()
	{
		// Rien de particulier
	}
	
	/********************************************************************************/
	
	/**
	 * Retourne ou modifit la méthode à éxécuter
	 * @param string $method Si renseigné, affecte la méthode à affecter
	 * @return string
	 */
	final public function method(?string $method = NULL) : ?string
	{
	    if($method !== NULL)
	    {
	        $this->_method = $method;
	    }
	    return $method;
	}
	
	/**
	 * Affecte ou retourne la requête du contrôleur
	 * @param ?Request $request La requête à affecter
	 * @return Request
	 */
	public function request(?Request $request = NULL) : Request
	{
		if($request !== NULL)
		{
			$this->_request = $request;
		}
		return $this->_request;
	}
	
	/**
	 * Méthode à éxécuter avant la méthode principale du contrôleur
	 * @return void
	 */
	public function before() : void
	{
	    // Rien de particulier
	}
	
	/**
	 * Méthode à éxécuter après la méthode principale du contrôleur
	 * @return void
	 */
	public function after() : void
	{
	    // Rien de particulier
	}
	
	/**
	 * Exécute la méthode principale du contrôleur
	 * @return void
	 */
	final public function execute() : void
	{
		$this->{ $this->_method }();
	}
	
	/********************************************************************************/
	
	/**
	 * Retourne la réponse du contrôleur
	 * @param mixed $response Si renseignée, la valeur à effecter 
	 * @return mixed
	 */
	public function response($response = NULL)
	{
		if($response !== NULL)
		{
			$this->_response = $response;
		}
	    return $this->_response;
	}
	
	/********************************************************************************/
	
}