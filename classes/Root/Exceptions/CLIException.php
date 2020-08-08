<?php

/**
 * Gestion des exceptions
 */

namespace Root\Exceptions;

class CLIException {
	
	/**
	 * Gestionnaire d'exception
	 * @param \Throwable $exception
	 * @return void
	 */
	public static function handler(\Throwable $exception) : void
	{
		$message = $exception->getMessage();
		exit($message);
		
		$modeDebug = getConfig('beyond.debug');
		if($modeDebug)
		{
			debug($exception, TRUE);
		}
		
		debug($message, TRUE);
	}
	
}