<?php

/**
 * Gestion de données en cache
 */

namespace Root;

class Cache extends Instanciable {
	
	private const DIRECTORY = 'resources/cache/';
	/***/
	public const KEY_ENVIRONMENT = 'environment';
	
	/**
	 * Données en cache
	 * @var array
	 */
	private array $_data = [];
	
	/****************************************************/
	
	/**
	 * Retourne le chemin du fichier de cache de la clé en paramètre
	 * @param string $key
	 * @return string
	 */
	private static function _filePath(string $key) : string
	{
		return (self::DIRECTORY . hash('sha256', $key));
	}
	
	/****************************************************/
	
	/**
	 * Retourne les données du cache dont la clé est en paramètre
	 * @param string $key Clé pour identifier le cache
	 * @param int $lifetime Durée de vie en seconde des données
	 * @param mixed $default Valeur à retourner par défaut
	 * @return mixed
	 */
	public function get(string $key, int $lifetime, $default = NULL)
	{
		if(! array_key_exists($key, $this->_data))
		{
			$filePath = $this->_filePath($key);
			
			$data = $default;
			
			// Le fichier de cache n'existe pas
			if(file_exists($filePath))
			{
				// Les données de cache ont expirés
				if((time() - filemtime($filePath)) > $lifetime)
				{
					unlink($filePath);
				}
				else
				{
					$fileContent = @ file_get_contents($filePath);
					if($fileContent)
					{
						try {
							$data = unserialize($fileContent);
						} catch(\Exception $exception) {
							
						}
					}
				}
			}
			
			$this->_data[$key] = $data;
		}
		
		return getArray($this->_data, $key);
	}
	
	/****************************************************/
	
	/**
	 * Met les données en cache
	 * @param string $key Clé pour identifier le cache
	 * @param mixed $data Données à mettre en cache
	 * @return bool
	 */
	public function update(string $key, $data) : bool
	{
		$filePath = $this->_filePath($key);
		
		try {
			$fileData = serialize($data);
			$written = file_put_contents($filePath, $fileData, LOCK_EX);
			return (is_int($written) AND $written > 0);
		} catch(\Exception $exception) {
			return FALSE;
		}
	}
	
	/****************************************************/
	
}