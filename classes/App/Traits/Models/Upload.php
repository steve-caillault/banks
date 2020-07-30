<?php

/**
 * Trait gérant le téléchargement de l'image d'un modèle
 */

namespace App\Traits\Models;

use Root\Arr;
use Root\HTML;
use Root\File;
use Root\Image;
use Root\FileUpload;

trait Upload {
	
	/**
	 * Retourne le répertoire où stocker les images
	 * @return string
	 */
	abstract protected function _imagesDirectory() : string; 
	
	/**
	 * Retourne les formats des images à créer avec leurs dimensions
	 * @return array
	 */
	abstract protected function _imagesFormats() : array;
	
	/**
	 * Retourne le nom de fichier sans l'extension
	 * @return string
	 */
	abstract protected function _filename() : string;
	
	/************************************************************************/
	
	/**
	 * Retourne l'URL de l'image
	 * @param string $field Champs en base de données
	 * @param string $type Format de l'image
	 * @return string
	 */
	public function imageUrl(string $field, string $format = self::IMAGE_FORMAT_MEDIUM) : ?string
	{
		$filename = $this->{ $field };
		if($filename === NULL)
		{
			return NULL;
		}
		
		$imageUrl = $this->_imagesDirectory() . $format . '/' . $filename;
		
		return $imageUrl;
	}
	
	/**
	 * Retourne l'image sous forme de HTML
	 * @param string $field Champs en base de données
	 * @param string $format Type de l'image
	 * @param array $attributes Propriétés de la balise image
	 * @return string
	 */
	public function image(string $field, string $format = self::IMAGE_FORMAT_MEDIUM, array $attributes = []) : ?string
	{
		$imageUrl = $this->imageUrl($field, $format);
		if($imageUrl === NULL)
		{
			return NULL;
		}
		
		$imageAttributes = array_replace([
			'alt' => '',	
		], $attributes);
		
		$image = HTML::image($imageUrl, $imageAttributes);
		
		return $image;
	}
	
	/**
	 * Déplace l'image téléchargé
	 * @param string $field Nom du champs correspondant à l'image
	 * @param array $fileData Les données du fichier téléchargé
	 * @return bool
	 */
	public function uploadImage(string $field, array $fileData) : bool
	{
		$formats = Arr::get($this->_imagesFormats(), $field, []);
		if(count($formats) == 0)
		{
			return FALSE;
		}
		
		$filename = $this->_filename() . '.' . File::extension(Arr::get($fileData, 'name'));
		$originalPath = $this->_imagesDirectory() . 'original/' . $filename;
		
		// Télécharge le fichier original
		$fileUpload = FileUpload::factory($fileData);
		$fileUpload->isImage(TRUE);
		if(! $fileUpload->move($originalPath))
		{
			return FALSE;
		}

		// Création des diffèrents formats
		foreach($formats as $format => $dimensions)
		{
			// On copie le fichier original dans le répertoire du format
			$formatPath = $this->_imagesDirectory() . $format . '/' . $filename;
			$copy = @ copy($originalPath, $formatPath);
			if(! $copy)
			{
				return FALSE;
			}
			
			$image = Image::factory($formatPath);
			$width = Arr::get($dimensions, 'width');
			$height = Arr::get($dimensions, 'height');
			$image->resize($width, $height);
			$image->save();
		}
		
		// En enregistre en base de données si la propriété existe
		if(property_exists($this, $field))
		{
			$this->{ $field } = $filename;
			$this->save();
		}
		
		return TRUE;
	}
	
	/************************************************************************/
	
}