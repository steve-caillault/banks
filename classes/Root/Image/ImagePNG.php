<?php

/**
 * Gestion d'une image PNG
 */

namespace Root\Image;

use Root\Image;

class ImagePNG extends Image {
	
	/**
	 * Initialise la ressource
	 * @return Resource
	 */
	protected function _initResource()
	{
		return imagecreatefrompng($this->_filepath);
	}
	
	/**
	 * Enregistre l'image
	 * @param int $quality
	 * @return bool
	 */
	public function save(int $quality = 100) : bool
	{
		return imagepng($this->_resource, $this->_filepath, $quality);
	}
	
}