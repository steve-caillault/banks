<?php

/**
 * Fichiers des fonctions
 */

use Root\{ Core, Environment, Arr, Session, Debug, Log };
	
/**
 * Affichage du contenu d'une variable
 * @param mixed $variable
 * @param bool $exit Vrai si on doit arrêté l'exécution du script
 * @return string
 */
function debug($variable, bool $exit = FALSE) : string
{
	$response = Debug::show($variable);
	
	if($exit)
	{
		exit($response);
	}
	
	return $response;
}

/**
 * Ajoute un message dans un fichier
 * @param string $message
 * @return void
 */
function logMessage(string $message) : void
{
	Log::add($message);
}

/**
 * Déclenchement d'une exception
 * @param string $message
 * @param int $code
 * @return void
 */
function exception(string $message, int $code = 500) : void
{
	throw new \Exception($message, $code);
}

/**
 * Retourne l'environnement du site
 * @return string
 */
function environment() : string
{
	return Environment::retrieve();
}

/**
 * Retourne la session
 * @return Session
 */
function session() : Session
{
	return Session::instance();
}


/**
 * Modifit la langue du site
 * @param string $locale fr_FR, en_GB
 * @return void
 */
function setLanguage(string $locale) : void
{
	Core::setLanguage($locale);
}

/**
 * Traduction d'une chaine de caractère dans la langue courante
 * @param string $string La chaine de caractères à traduire
 * @return string La chaine traduite
 */
function translate(string $string) : string 
{
	return Arr::get(Core::translations(), $string, $string);
}