<?php

/**
 * Gestion d'une image GIF
 */

namespace Root\Image;

use Root\Image;

class ImageGIF extends Image {
	
	/**
	 * Type d'image
	 * @param string
	 */
	protected string $_type = 'gif';
	
	/**
	 * Initialise la ressource
	 * @return Resource
	 */
	protected function _initResource()
	{
		return imagecreatefromgif($this->_filepath);
	}
	
	/**
	 * Affichage de l'image
	 * @return void
	 */
	protected function _getContent() : void
	{
		imagegif($this->_resource);
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