<?php

/**
 * Règle vérifiant la taille d'un fichier téléchargé
 */

namespace Root\Validation\Rules;

class UploadSizeRule extends Rule {
	
	private const 
		SIZE_MEGA = 1000000,
		SIZE_KILO = 1000
	;
	
	/**
	 * Message en cas d'erreur
	 * @var string
	 */
	protected string $_error_message = 'Le fichier ne doit pas dépasser :size Mo.';
	
	/********************************************************************************/
	
	/* VERIFICATION */
	
	/**
	 * Retourne si la valeur respecte la règle
	 * @return bool
	 */
	public function check() : bool
	{
		// Pas de vérification si le fichier n'a pas été téléchargé
		$error = getArray($this->_getValue(), 'error');
		if($error != UPLOAD_ERR_OK)
		{
			return TRUE;
		}
		
		$size = getArray($this->_getValue(), 'size', 0);
		$sizeAllowed = $this->_getParameter('size', 1) * self::SIZE_MEGA; 
		
		return ($size < $sizeAllowed);
	}
	
	/********************************************************************************/
	
}