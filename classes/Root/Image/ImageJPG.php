<?php

/**
 * Gestion d'une image JPEG
 */

namespace Root\Image;

use Root\Image;

class ImageJPG extends Image {
	
	/**
	 * Initialise la ressource
	 * @return Resource
	 */
	protected function _initResource()
	{
		return imagecreatefromjpeg($this->_filepath);
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