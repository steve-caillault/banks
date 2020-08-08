<?php

/**
 * Gestionnaire de session
 */

namespace Root;

class Session extends Instanciable {
	
	/**
	 * Données en session
	 * @var array
	 */
	private $_data = [];
	
	/**************************************************************/
	
	/**
	 * Constructeur
	 */
	protected function __construct()
	{
		session_start();
		$this->_data = $_SESSION;
	}
	
	/**************************************************************/
	
	/**
	 * Retourne la valeur de la clé en session
	 * @param string $key
	 * @param mixed $default Valeur à retourner si la clé n'a pas été trouvé
	 * @return mixed
	 */
	public function retrieve(string $key, $default = NULL)
	{
		return getArray($this->_data, $key, $default);
	}
	
	/**
	 * Modfifit la valeur de la clé en session
	 * @param string $key
	 * @param mixed $value Valeur à affecter
	 * @return void
	 */
	public function change(string $key, $value) : void
	{
		$_SESSION[$key] = $value;
		$this->_data[$key] = $value;
	}
	
	/**
	 * Supprime la valeur de la clé en paramètre
	 * @param string $key
	 * @return void
	 */
	public function delete(string $key) : void
	{
		unset($_SESSION[$key], $this->_data[$key]);
		
	}
	
	/**************************************************************/
	
}