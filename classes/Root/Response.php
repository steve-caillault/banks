<?php

/**
 * Gestion de la gestion de la réponse d'une requête
 */

namespace Root;

class Response extends Instanciable {
	
	/**
	 * Corps de la réponse
	 * @var mixed
	 */
	private $_body = NULL;
	
	/**
	 * En-tête de la réponse
	 * @var array
	 */
	private array $_headers = [];
	
	/*************************************************/
	
	/**
	 * Constructeur
	 * @param mixed $body
	 */
	public function __construct($body)
	{
		$this->_body = $body;
	}
	
	/*************************************************/
	
	/**
	 * Ajout un en-tête
	 * @param string $key
	 * @param string $value
	 * @return self
	 */
	public function addHeader(string $key, string $value) : self
	{
		$this->_headers[$key] = $value;
		return $this;
	}
	
	/**
	 * Envoie des en-têtes
	 * @return void
	 */
	public function sendHeaders() : void
	{
		foreach($this->_headers as $name => $value)
		{
			header($name . ': ' . $value);
		}
	}
	
	/**
	 * Retourne le rendu pour l'affichage
	 * @return string
	 */
	public function __toString() : string
	{
		return $this->_body;
	}
	
	/*************************************************/
	
}