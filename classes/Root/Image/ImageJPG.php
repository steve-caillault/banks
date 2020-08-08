<?php

/**
 * Gestion d'une image JPEG
 */

namespace Root\Image;

use Root\Image;

class ImageJPG extends Image {
	
	/**
	 * Type d'image
	 * @param string
	 */
	protected string $_type = 'jpg';
	
	/**
	 * Initialise la ressource
	 * @return Resource
	 */
	protected function _initResource()
	{
		return imagecreatefromjpeg($this->_filepath);
	}
	
	/**
	 * Affichage de l'image
	 * @return void
	 */
	protected function _getContent() : void
	{
		imagejpeg($this->_resource);
	}
	
	/**
	 * Enregistre l'image
	 * @param int $quality
	 * @return bool
	 */
	public function save(int $quality = 100) : bool
	{
		return imagejpeg($this->_resource, $this->_filepath, $quality);
	}
	
}