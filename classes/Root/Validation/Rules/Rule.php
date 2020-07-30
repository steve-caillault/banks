<?php

/**
 * Vérification d'une règle de validation
 */

namespace Root\Validation\Rules;

use Root\Instanciable;
use Root\Arr;

abstract class Rule extends Instanciable {
	
	/**
	 * Valeur à vérifier
	 * @var mixed
	 */
	private $_value = NULL;
	
	/**
	 * Paramètre de la règle
	 * @var array
	 */
	private $_parameters = [];
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected $_error_message = NULL;
	
	/********************************************************************************/
	
	/* CONSTRUCTEUR /*
	
	/**
	 * Constructeur
	 * @param array $parameters : [
	 * 		'value' 		=> <mixed>, // Valeur à vérifier
	 * 		'parameters'	=> <array>, // Paramètres de la règle
	 * ]
	 */
	protected function __construct(array $parameters = [])
	{
		$this->_value = Arr::get($parameters, 'value', $this->_value);
		$this->_parameters = Arr::get($parameters, 'parameters', $this->_parameters);
	}
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	abstract public function check() : bool;
	
	/********************************************************************************/
	
	/* GET */
	
	/**
	 * Retourne le message d'erreur
	 * @return string
	 */
	public function errorMessage() : string
	{
		$keys = array_keys($this->_parameters);
		array_walk($keys, function(&$item, $key) {
			$item = ':' . $item;
		});

		$values = array_values($this->_parameters);
		array_walk($values, function(&$item, $key) {
			$item = (is_array($item) ? implode(', ', $item) : $item);
		});
		
		return strtr($this->_error_message, array_combine($keys, $values));
	}
	
	/**
	 * Retourne la valeur
	 * @return mixed
	 */
	protected function _getValue()
	{
		return $this->_value;
	}
	
	/**
	 * Retourne le paramètre de la règle dont on donne la clé en paramètre
	 * @param string $key
	 * @param mixed $defaultValue Valeur par défaut
	 * @return mixed
	 */
	protected function _getParameter(string $key, $defaultValue = NULL)
	{
		return Arr::get($this->_parameters, $key, $defaultValue);
	}
	
	/********************************************************************************/
	
}