<?php

/**
 * Fichiers des fonctions
 */

use Root\{ Core, Environment, Arr, Session, Debug, Log, Config, Redirect, URL };
	
/**
 * Redirection
 * @param string $path Chemin où rediriger
 * @return void
 */
function redirect(string $path) : void
{
	Redirect::process($path);
}

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
	return Environment::getName();
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
 * Retourne la valeur en session
 * @param string $key
 * @param mixed $defaultValue
 * @return mixed
 */
function getConfig(string $key, $defaultValue = NULL)
{
	return Config::load($key, $defaultValue);
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
 * Retourne la valeur d'une clé dans un tableau.
 * On retourne la valeur par défaut en paramètre si la clé n'est pas présente
 * @param array $array Le tableau visé
 * @param mixed $key La clé visée
 * @param mixed $default La valeur par défault retournée si le clé n'est pas présente dans le tableau
 * @return mixed
 */
function getArray(?array $array, $key, $default = NULL)
{
	return Arr::get($array, $key, $default);
}

/**
 * Retourne l'URL du chemin que l'on donne en paramètre
 * @param string $uri
 * @param bool $absolute Vrai si on retourne l'URL absolut
 * @return string
 */
function getURL(string $uri, bool $absolute = FALSE) : string
{
	return URL::get($uri, $absolute);
}

/**
 * Traduction d'une chaine de caractère dans la langue courante
 * @param string $string La chaine de caractères à traduire
 * @param string $locale
 * @return string La chaine traduite
 */
function translate(string $string, array $options = [], ?string $locale = NULL) : string 
{
	$dataTranslate = getArray(Core::translations($locale), $string, $string);
	if(is_string($dataTranslate))
	{
		return $dataTranslate;
	}
	// Gestion des options
	else
	{
		$count = getArray($options, 'count');
		$gender = getArray($options, 'gender');
		
		if($gender !== NULL)
		{
			$dataTranslate = getArray($dataTranslate, $gender, $string);
			if(is_string($dataTranslate))
			{
				return $dataTranslate;
			}
		}
		if($count !== NULL)
		{
			if($count > 1)
			{
				return getArray($dataTranslate, 'several', $string);
			}
			else
			{
				return getArray($dataTranslate, 'zero', $string);
			}
		}
		return $string;
	}
}