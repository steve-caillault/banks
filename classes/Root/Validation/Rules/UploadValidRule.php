<?php

/**
 * Règle vérifiant qu'un fichier a été uploadé
 */

namespace Root\Validation\Rules;

use Root\Arr;

class UploadValidRule extends Rule {
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected $_error_message = 'Le fichier n\'a pas pu être téléchargé.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		$errorsAllowed = [ UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE, ];
		$error = Arr::get($this->_getValue(), 'error');
		
		return (in_array($error, $errorsAllowed));
	}
	
	/********************************************************************************/
	
}