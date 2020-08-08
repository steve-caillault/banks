<?php

/**
 * Gestion d'une image PNG
 */

namespace Root\Image;

use Root\Image;

class ImagePNG extends Image {
	
	/**
	 * Type d'image
	 * @param string
	 */
	protected string $_type = 'png';
	
	/**
	 * Initialise la ressource
	 * @return Resource
	 */
	protected function _initResource()
	{
		return imagecreatefrompng($this->_filepath);
	}
	
	/**
	 * Affichage de l'image
	 * @return void
	 */
	protected function _getContent() : void
	{
		imagepng($this->_resource);
	}
	
	/**
	 * Enregistre l'image
	 * @param int $quality
	 * @return bool
	 */
	public function save(int $quality = 9) : bool
	{
		return imagepng($this->_resource, $this->_filepath, $quality);
	}
	
}