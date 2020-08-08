<?php

/**
 * Redirection
 */

namespace Root;

class Redirect {
	
	/**
	 * Redirection
	 * @param string $path Chemin où rediriger
	 * @return void
	 */
	public static function process(string $path) : void
	{
		$url = getURL($path, TRUE);
		header('Location: ' . $url);
		exit;
	}
	
}