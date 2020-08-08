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
	 * @var string
	 */
	protected string $_type;
	
	/**
	 * Chemin de l'image
	 * @var string
	 */
	protected string $_filepath;
	
	/**
	 * Ressource de l'mage
	 * @var resource
	 */
	protected $_resource = NULL;
	
	/**
	 * Dimensions de l'image
	 * @var array
	 */
	private array $_dimensions = [
		'width' => NULL,
		'height' => NULL,
	];
	
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
		
		$class = __NAMESPACE__ . '\Image\Image' . strtoupper(getArray(self::ALLOWED_TYPES, $type));
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
	public function resize(?int $width, ?int $height) : void
	{
		$originalDimensions = $this->getDimensions();
		$originalWidth = getArray($originalDimensions, 'width');
		$originalHeight = getArray($originalDimensions, 'height');
		
		// Modifit les dimensions pour respecter les proportions
		
		if($width !== NULL OR $height !== NULL)
		{
			if($height === NULL AND $width !== NULL)
			{
				$height = $originalHeight * ($width / $originalWidth);  
			}
			elseif($width === NULL AND $height !== NULL)
			{
				$width = $originalWidth * ($height / $originalHeight);
			}
			elseif(($originalWidth / $width) > ($originalHeight / $height))
			{
				$height = $originalHeight * ($width / $originalWidth);
			}
			else
			{
				$width = $originalWidth * ($height / $originalHeight);
			}
		}
		
		$this->_setDimensions($width, $height);
		
		$imageDest = imagecreatetruecolor($width, $height);
		imagecopyresampled($imageDest, $this->_resource, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
		$this->_resource = $imageDest;
	}
	
	/**
	 * Modifit les dimensions de l'image
	 * @param float $width
	 * @param float $height
	 * @return void
	 */
	private function _setDimensions(float $width, float $height) : void
	{
	    $this->_dimensions = [
	        'width' => $width,
	        'height' => $height,
	    ];
	}
	        	
	/**
	 * Retourne les dimensions de l'image
	 * @return array
	 */
	 public function getDimensions() : array
	 {
	 	$width = getArray($this->_dimensions, 'width');
	 	$height = getArray($this->_dimensions, 'height');
	 	
	 	if($width === NULL OR $height === NULL)
	 	{
	 		list($width, $height) = getimagesize($this->_filepath);
	 		$this->_dimensions = [
	 			'width' => $width,
	 			'height' => $height,
	 		];
	 	}
	 	
	 	return $this->_dimensions;
	}
	
	/**********************************************************************************/
	
	/**
	 * Retourne le contenu de l'image
	 * @return string
	 */
	public function getContent() : string
	{
	    ob_start();
	    $this->_getContent();
	    $content = ob_get_contents();
	    ob_end_clean();
	    return $content;
	}
	
	/**
	 * Affichage de l'image
	 * @return void
	 */
	abstract protected function _getContent() : void;

	/**
	 * Enregistre l'image
	 * @param int $quality
	 * @return bool
	 */
	abstract public function save(int $quality = 100) : bool;
	
	/**
	 * Retourne le type d'image
	 * @return string
	 */
	public function type() : string
	{
	    return $this->_type;
	}
	
	/**********************************************************************************/
	
}
	