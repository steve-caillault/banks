<?php

/**
 * Gestion du connexion à une base de données PDO
 * @author Stève Caillault
 */

namespace Root\Database;

use PDO as ConnectionPDO;
/***/
use Root\Database;
use Root\Database\Query\Builder as QueryBuilder;

class PDO extends Database {
	
	
	/**
	 * Instance d'une connection à la base de données
	 * @var ConnectionPDO
	 */
	private ConnectionPDO $_connection;
	
	/************************************************************************/
	
	/**
	 * Constructeur
	 * @param array $configuration Configuration de la base de données 
	 * @return void
	 */
	protected function __construct(array $configuration)
	{
		$dns = getArray($configuration, 'dns');
		$username = getArray($configuration, 'username');
		$password = getArray($configuration, 'password');
		$options = getArray($configuration, 'options', []);
		
		$this->_connection = new ConnectionPDO($dns, $username, $password, $options);
	}
	
	/************************************************************************/
	
	/**
	 * Retourne le résultat de l'éxécution d'une requête
	 * @param QueryBuilder $queryBuilder
	 * @return int Nombre de lignes affectées pour des requêtes INSERT, UPDATE ou DELETE 
	 * @return array Pour une requête SELECT
	 */
	public function execute(QueryBuilder $queryBuilder)
	{
		$query = $this->_connection->prepare($queryBuilder->queryCompiled());
		
		$variables = $queryBuilder->variables();
		foreach($variables as $variableKey => $variableValue)
		{
			$variableType = static::variableType($variableValue);
			$query->bindValue($variableKey, $variableValue, $variableType);
		}
		
		$response = $query->execute();
		
		ob_start();
		$query->debugDumpParams();
		$debugQuery = ob_get_clean();
		
		$matches = [];
		preg_match_all('/^SQL|Sent SQL/', $debugQuery, $matches, PREG_OFFSET_CAPTURE);
		
		foreach(getArray($matches, 0, []) as $match)
		{
			$indexMatch = getArray($match, 1, 0);
			$stringMatch = getArray($match, 0);
			$subQuery = substr($debugQuery, $indexMatch + strlen($stringMatch));
			$subQuery = substr($subQuery, strpos($subQuery, ']') + 1);
			static::$_last_query = trim(substr($subQuery, 0, strpos($subQuery, "\n" /*PHP_EOL*/)));
		}
		
	 	// echo debug(static::$_last_query) . "\n\n";
		
		// S'il y a une erreur lors de l'éxécution de la requête
		if(! $response)
		{
			$errorMessage ='Une erreur s\'est produite lors de l\'exécution de la requête.';
			if($errorInfo = $query->errorInfo() AND $reason = getArray($errorInfo, 2))
			{
				$errorMessage .= ' '. $reason;
			}
			exception($errorMessage);
		}
	
		$queryType = $queryBuilder->type();
		
		if($queryType === QueryBuilder::TYPE_SELECT)
		{
			return $query->fetchAll(ConnectionPDO::FETCH_ASSOC);
		}
		else
		{
			$response = $query->rowCount();
			if($queryType == QueryBuilder::TYPE_INSERT)
			{
				static::$_last_insert_id = $this->_connection->lastInsertId();
			}
			return $response;
		}
	}
	
	/************************************************************************/
	
	/**
	 * Retourne la valeur de la constante PDO à utiliser pour la variable en paramètre
	 * @param mixed $variable
	 * @return int
	 */
	public static function variableType($variable) : int
	{	
		if($variable === NULL)
		{
			return ConnectionPDO::PARAM_NULL;
		}
		elseif(is_bool($variable))
		{
			return ConnectionPDO::PARAM_BOOL;
		}
		elseif(is_int($variable) OR ctype_digit($variable))
		{
			return ConnectionPDO::PARAM_INT;
		}
		else
		{
			// @todo Voir comment faire pour les fichiers binaires (PARAM_LOB)
			return ConnectionPDO::PARAM_STR;
		}
	}
	
	/************************************************************************/
	
}