<?php

/**
 * Gestion d'une tâche appelée en ligne de commande
 */

namespace Root;

abstract class Task extends Instanciable {
	
	/**
	 * Identifiant de la tâche
	 * @var string
	 */
	protected string $_identifier;
	
	/**
	 * Paramètres de la tâche
	 * @var array
	 */
	private array $_parameters;
	
	/**
	 * Réponse de la tâche
	 * @var mixed
	 */
	protected $_response = NULL;
	
	/********************************************/
	
	/**
	 * Liste des tâches
	 * @var array
	 */
	private static ?array $_tasks = NULL;
	
	/**
	 * Tâche courante
	 * @var self
	 */
	private static ?self $_current = NULL;
	
	/**
	 * Arguments de la demande
	 * @var array
	 */
	private static ?array $_arguments = NULL;
	
	/********************************************/
	
	/**
	 * Affecte les paramètres de la tâche
	 * @param array $parameters 
	 * @return void
	 */
	private function _parameters(array $parameters) : void
	{
		$this->_parameters = $parameters;
	}
	
	/**
	 * Retourne les paramètres de la tâche
	 * @return array
	 */
	public function parameters() : array
	{
		return $this->_parameters;
	}
	
	/**
	 * Retourne l'identifiant de la tâche
	 * @return string
	 */
	public function identifier() :  string
	{
		return $this->_identifier;
	}
	
	/**
	 * Retourne l'identifiant de la tâche en paramètre
	 * @param string $class Classe de la tâche
	 * @return string
	 */
	public static function getIdentifier(string $class) : ?string
	{
		try {
			$reflectionClass = new \ReflectionClass($class);
			
			if($reflectionClass->isAbstract())
			{
				return NULL;
			}
			
			$propertyName = $reflectionClass->getProperty('_identifier');
			$propertyName->setAccessible(TRUE);
			
			return $propertyName->getValue(new $class);
		} catch(\Exception $exception) {
			return NULL;
		}
	}
	
	/********************************************/
	
	/**
	 * Appel la tâche en paramètre
	 * @param string $class Classe de la tâche à appeler
	 * @param array $parameters Paramètres de la tâche
	 * @return void
	 */
	public static function call(string $class, array $parameters = []) : void
	{
		$identifier = self::getIdentifier($class);
		if($identifier === NULL)
		{
			exception('Tâche inconnue.');
		}
		
		$commandPattern = 'php cli :identifier :parameters';
		
		$command = escapeshellcmd(trim(strtr($commandPattern, [
			':identifier' => $identifier,
			':parameters' => implode(' ', $parameters),
		])));
		
		$system = strtolower(php_uname('s'));
		$isWindowsSystem = (strpos($system, 'windows') !== FALSE);
		
		if($isWindowsSystem)
		{
			pclose(popen('start /B ' . $command, 'r'));
		}
		else
		{
			$command .= ' > /dev/null &';
			exec($command);
		}
	}
	
	/**
	 * Retourne la tâche courante
	 * @return self
	 */
	public static function current() : ?self
	{
		if(! Request::isCLI())
		{
			exception('La requête doit être faite en ligne de commande.', 403);
		}
		
		if(self::$_current === NULL)
		{
			// Récupération de l'identifiant en paramètre de ligne de commande
			$arguments = self::_arguments();
			$expectedIdentifier = getArray($arguments, 'identifier');
			if($expectedIdentifier === NULL)
			{
				return NULL;
			}
			
			$files = array_merge(
				Directory::files('classes/Root/Tasks/'),
				Directory::files('classes/App/Tasks/')
			);
			
			foreach($files as $file)
			{
				$class = '\\' . strtr(rtrim(ltrim($file, 'classes/'), '.php'), [ '/' => '\\' ]);
				try {
					
					$identifier = self::getIdentifier($class);
					
					if($identifier != $expectedIdentifier)
					{
						continue;
					}
					
					$parameters = getArray($arguments, 'parameters', []);
					$task = new $class;
					$task->_parameters($parameters);
					self::$_current = $task; 
				} catch(\Exception $exception) {
					continue;
				}
			}
		}
		
		if(self::$_current === NULL)
		{
			exception('Tâche introuvable.', 404);
		}
		
		return self::$_current;
	}
	
	/********************************************/
	
	/**
	 * Exécute la tâche
	 * @return void
	 */
	abstract protected function _execute() : void;
	
	/**
	 * Règles de validation des paramètres
	 * @return array
	 */
	protected function _validationParametersRules() : array
	{
		return [];
	}
	
	/**
	 * Validation des paramètres
	 * @return void
	 */
	protected function _validatationParameters() : void
	{
		$validation = Validation::factory([
			'data' => $this->parameters(),
			'rules' => $this->_validationParametersRules(),
		]);
		
		$validation->validate();
		
		if(! $validation->success())
		{
			exception('Paramètres incorrects.');
		}
	}
	
	/**
	 * Réponse de la tâche
	 * @return mixed
	 */
	public function response()
	{
		$this->_validatationParameters();
		$this->_execute();
		return $this->_response;
	}
	
	/**
	 * Retourne les arguments de la requête
	 * @return array
	 */
	private static function _arguments() : array
	{
		if(self::$_arguments === NULL)
		{
			global $argv;
			
			$arguments = $argv;
			$identifier = getArray($arguments, 1);
			unset($arguments[0], $arguments[1]);
			
			$parameters = [];
			foreach($arguments as $value)
			{
				$parameters[] = $value;
			}
			
			self::$_arguments = [
				'identifier' => $identifier,
				'parameters' => $parameters,
			];
		}
		return self::$_arguments;
	}
	
	/********************************************/
	
}