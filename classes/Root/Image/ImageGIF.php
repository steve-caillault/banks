<?php

/**
 * Gestion d'une image GIF
 */

namespace Root\Image;

use Root\Image;

class ImageGIF extends Image {
	
	/**
	 * Initialise la ressource
	 * @return Resource
	 */
	protected function _initResource()
	{
		return imagecreatefromgif($this->_filepath);
	}
	
	/**
	 * Enregistre l'image
	 * @param int $quality
	 * @return bool
	 */
	public function save(int $quality = 100) : bool
	{
		return imagegif($this->_resource, $this->_filepath);
	}
	
}