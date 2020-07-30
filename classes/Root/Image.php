<?php

/**
 * Gestion d'une image
 */

namespace Root;

abstract class Image {

	/**
	 * Types de fichiers autorisés
	 * @var array
	 */
	private const ALLOWED_TYPES = [
		IMAGETYPE_GIF 	=> 'gif',
		IMAGETYPE_JPEG	=> 'jpg',
		IMAGETYPE_PNG	=> 'png',
	];
	
	/**********************************************************************************/
	
	/**
	 * Type d'image
	 * @param string
	 */
	private $_type = NULL;
	
	/**
	 * Chemin de l'image
	 * @var string
	 */
	protected $_filepath = NULL;
	
	/**
	 * Ressource de l'mage
	 * @var resource
	 */
	protected $_resource = NULL;
	
	/**********************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Constructeur
	 * @param string $filepath
	 */
	protected function __construct(string $filepath)
	{
		$this->_filepath = $filepath;
		$this->_resource = $this->_initResource();
	}
	
	/**
	 * Instanciation
	 * @param string $filepath
	 * @return self
	 */
	public static function factory(string $filepath) : self
	{
		// Vérifit le type de fichier
		$type = exif_imagetype($filepath);
		if(! array_key_exists($type, self::ALLOWED_TYPES))
		{
			exception('Type de fichier non autorisé.');
		}
		
		$class = __NAMESPACE__ . '\Image\Image' . strtoupper(Arr::get(self::ALLOWED_TYPES, $type));
		return new $class($filepath);
	}
	
	/**********************************************************************************/
	
	/**
	 * Initialise la ressource
	 * @return resource
	 */
	abstract protected function _initResource();
	
	/**********************************************************************************/
	
	/**
	 * Redimensionne une image
	 * @param int $width
	 * @param int $height
	 */
	public function resize(int $width, int $height) : void
	{
		list($originalWidth, $originalHeight) = getimagesize($this->_filepath);

		// Modifit les dimensions pour respecter les proportions
		if(($originalWidth / $width) > ($originalHeight / $height))
		{
			$height = $originalHeight * $width / $originalWidth;
		}
		else
		{
			$width = $originalWidth * $height / $originalHeight;
		}
		
		$imageDest = imagecreatetruecolor($width, $height);
		imagecopyresampled($imageDest, $this->_resource, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
		$this->_resource = $imageDest;
	}
	
	/**********************************************************************************/
	
	/**
	 * Enregistre l'image
	 * @param int $quality
	 * @return bool
	 */
	abstract public function save(int $quality = 100) : bool;
	
	/**********************************************************************************/
	
}
	