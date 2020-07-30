<?php

/**
 * Vérification des extensions
 */

namespace Root\Validation\Rules;

use Root\Arr;
use Root\File;

class UploadExtensionsRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected $_error_message = 'Le fichier doit-être de type :types.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		// Pas de vérification si le fichier n'a pas été téléchargé
		$error = Arr::get($this->_getValue(), 'error');
		if($error != UPLOAD_ERR_OK)
		{
			return TRUE;
		}
		
		$extensions = $this->_getParameter('types', []);
		$filename = Arr::get($this->_getValue(), 'name');
		$extension = File::extension($filename);
		return (in_array($extension, $extensions));
	}
	
	/********************************************************************************/
	
}