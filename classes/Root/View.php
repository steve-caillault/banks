<?php

/**
 * Gestion d'une vue
 */ 

namespace Root;

class View {
	
	private const DIRECTORY = 'views/';
	
	/**
	 * Fichier de la vue 
	 * @var string
	 */
	private $_path = NULL;
	
	/**
	 * Données aux données de la vue
	 * @var array
	 */
	private $_data	= [];
	
	/********************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Constructeur
	 * @var string $path Chemin de la vue
	 * @var array $data Données à transmettre à la vue
	 */
	private function __construct(string $path, array $data = [])
	{
		$path = self::DIRECTORY . $path . '.php';
		
		// Vérifit si le fichier de la vue existe
		if(! is_file($path))
		{
			exception(strtr('La vue :file n\'existe pas.', [
				':file' => $path,	
			]));
		}
		
		$this->_path = $path;
		$this->_data = $data;
	}
	
	/**
	 * Instanciation
	 * @var string $path Chemin de la vue
	 * @var array $data Données à transmettre à la vue
	 * @return self
	 */
	public static function factory(string $path, array $data = []) : self
	{
		return new self($path, $data);
	}
	
	/********************************************************************************/
	
	/**
	 * Affecte une variable à la vue
	 * @var string $key Nom de la variable dans la vue
	 * @var mixed $value Valeur de la variable
	 * @return self
	 */
	public function setVar(string $key, $value) : self
	{
		$this->_data[$key] = $value;
		return $this;
	}
	
	/**
	 * Affecte plusieurs variables à la vue
	 * @var array $data
	 * @return self
	 */
	public function setVars(array $data) : self
	{
	   $this->_data = array_merge($this->_data, $data);
	   return $this;
	}
	
	/**
	 * Méthode de rendu
	 * @return string
	 */
	public function render() : string
	{
		// Importation des variables dans la table des symboles
		extract($this->_data, EXTR_SKIP);
		
		ob_start();
		require $this->_path;
		
		return ob_get_clean();
	}
	
	/**
	 * Affichage de la vue
	 * @return string
	 */
	public function __toString() : string
	{
		return $this->render();
	}
	
	/********************************************************************************/
	
}