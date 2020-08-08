<?php

/**
 * Enregistrement de messages dans un fichier
 */

namespace Root;

use DateTime, DateTimeZone;

class Log extends Instanciable {
	
	/**
	 * Chemin d'enregistrement des fichiers
	 */
	private const DIRECTORY = 'resources/logs/';
	
	/**
	 * Date d'enregistrement
	 * @var Datetime
	 */
	private Datetime $_datetime;
	
	/**
	 * URI où le message a été enregistré
	 * @var string
	 */
	private string $_uri;
	
	/**
	 * Message 
	 * @var string
	 */
	private string $_message;
	
	/************************************************************/
	
	/**
	 * Constructeur
	 * @param array $params
	 */
	protected function __construct(array $params)
	{
		$this->_datetime = new DateTime('now', new DateTimeZone('UTC'));
		$this->_uri = (Request::isCLI()) ? Task::current()->identifier() : Request::detectUri();
		$this->_message = getArray($params, 'message');
	}
	
	/************************************************************/
	
	/**
	 * Retourne le chemin du fichier où enregistrer le message
	 * @return string
	 */
	private function _filepath() : ?string
	{
		$filepath = realpath(self::DIRECTORY);
		if($filepath === FALSE)
		{
			return NULL;
		}
		$filepath .= DIRECTORY_SEPARATOR . 'logs.txt';
		return $filepath;
	}
	
	/**
	 * Ajoute un message au fichier
	 * @param string $message
	 * @return bool
	 */
	public static function add(string $message) : bool
	{
		return static::factory([
			'message' => $message,
		])->_save();
	}
	
	/**
	 * Enregistrement du message
	 * @return bool
	 */
	private function _save() : bool
	{
		$filepath = $this->_filepath();
		if(! $filepath)
		{
			return FALSE;
		}
		
		$file = fopen($filepath, 'a');
		if(! $file)
		{
			return FALSE;
		}
		
		$texts = [
			$this->_datetime->format('Y-m-d H:i:s') . ' - ' . $this->_uri,
			$this->_message . PHP_EOL,
			'-------------------------------' . PHP_EOL,
		];
		
		foreach($texts as $text)
		{
			fwrite($file, $text . PHP_EOL);
		}
		fclose($file);
		
		return TRUE;
	}
	
	/************************************************************/
	
}