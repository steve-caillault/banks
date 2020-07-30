<?php

/**
 * Gestion du téléchargement d'un fichier
 */

namespace Root;

class FileUpload extends Instanciable {
	
	/**
	 * Données du fichier retouvé par $_FILES
	 * @var array
	 */
	private $_file_data = NULL;
	
	/**
	 * Vrai s'il s'agit d'une image
	 * @var bool
	 */
	private $_is_image = FALSE;
	
	/********************************************************************/
	
	/**
	 * Constructeur
	 * @param array $fileData
	 */
	protected function __construct(array $fileData)
	{
		$this->_file_data = $fileData;	
	}
	
	/********************************************************************/
	
	/* GET / SET */
	
	/**
	 * Retourne, modifit s'il s'agit d'une image
	 * @param bool $isImage Valeur à affecter
	 * @return bool
	 */
	public function isImage(?bool $isImage = NULL) : bool
	{
		if($isImage !== NULL)
		{
			$this->_is_image = $isImage;
		}
		return $this->_is_image;
	}
	
	/********************************************************************/
	
	/**
	 * Déplace le fichier téléchargé vers le chemin indiqué en paramètre
	 * @param Chemin où déplacer le fichier téléchargé
	 * @return bool
	 */
	public function move(string $filepath) : bool
	{
		// Vérifit que le fichier à pu être téléchargé
		$error = Arr::get($this->_file_data, 'error', UPLOAD_ERR_NO_FILE);
		if($error != UPLOAD_ERR_OK)
		{
			return FALSE;
		}
		
		// Vérifit que le fichier temporaire existe
		$tmpPath = Arr::get($this->_file_data, 'tmp_name');
		if(! is_file($tmpPath))
		{
			return FALSE;
		}
		
		// S'il s'agit d'une image, on vérifit la taille de l'image
		if($this->_is_image)
		{
			$originalSizes = getimagesize($tmpPath);
			$originalWidth = Arr::get($originalSizes, 0, 0);
			$originalHeight = Arr::get($originalSizes, 1, 0);
			if($originalWidth == 0 OR $originalHeight == 0)
			{
				return FALSE;
			}
		}
		
		// Déplace le fichier temporaire dans le répertoire demandé
		return move_uploaded_file($tmpPath, $filepath);
	}
	
	/********************************************************************/
	
}