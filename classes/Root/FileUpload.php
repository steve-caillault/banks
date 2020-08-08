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
	private array $_file_data = [];
	
	/**
	 * Vrai s'il s'agit d'une image
	 * @var bool
	 */
	private bool $_is_image = FALSE;
	
	/**
	 * Contenu du fichier
	 * @var string
	 */
	private ?string $_content = NULL;
	
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
	 * Vérifit que le fichier a été téléchargé
	 * @return bool
	 */
	public function valid() : bool
	{
		// Vérifit que le fichier à pu être téléchargé
		$error = getArray($this->_file_data, 'error', UPLOAD_ERR_NO_FILE);
		if($error != UPLOAD_ERR_OK)
		{
			return FALSE;
		}
		
		// Vérifit que le fichier temporaire existe
		$tmpPath = getArray($this->_file_data, 'tmp_name');
		if(! is_file($tmpPath))
		{
			return FALSE;
		}
		
		// S'il s'agit d'une image, on vérifit la taille de l'image
		if($this->_is_image)
		{
			$originalSizes = getimagesize($tmpPath);
			$originalWidth = getArray($originalSizes, 0, 0);
			$originalHeight = getArray($originalSizes, 1, 0);
			if($originalWidth == 0 OR $originalHeight == 0)
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	/**
	 * Retourne le chemin du fichier
	 * @return string
	 */
	public function path() : ?string
	{
		if(! $this->valid())
		{
			return NULL;
		}
		
		return getArray($this->_file_data, 'tmp_name');
	}
	
	/**
	 * Retourne le contenu du fichier
	 * @return string
	 */
	public function content() : string
	{
		if($this->_content === NULL)
		{
			$path = getArray($this->_file_data, 'tmp_name');
			$content = file_get_contents($path);
			$this->_content = $content;
		}
		return $this->_content;
	}
	
	/**
	 * Déplace le fichier téléchargé vers le chemin indiqué en paramètre
	 * @param Chemin où déplacer le fichier téléchargé
	 * @return bool
	 */
	public function move(string $filepath) : bool
	{
		if(! $this->valid())
		{
			return FALSE;
		}
		
		// Déplace le fichier temporaire dans le répertoire demandé
		$tmpPath = getArray($this->_file_data, 'tmp_name');
		return move_uploaded_file($tmpPath, $filepath);
	}
	
	/********************************************************************/
	
}