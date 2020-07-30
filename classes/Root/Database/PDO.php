<?php

namespace Root\Database;
use Root\Database;
use \PDO as ConnectionPDO;
use Root\Arr;
use Root\Database\Query\Builder as QueryBuilder;

/**
 * Gestion du connexion à une base de données PDO
 * @author Stève Caillault
 */

class PDO extends Database {
	
	
	/**
	 * Instance d'une connection à la base de données
	 * @var ConnectionPDO
	 */
	private $_connection = NULL;
	
	/************************************************************************/
	
	/**
	 * Constructeur
	 * @param array $configuration Configuration de la base de données 
	 * @return void
	 */
	protected function __construct(array $configuration)
	{
		$dns = Arr::get($configuration, 'dns');
		$username = Arr::get($configuration, 'username');
		$password = Arr::get($configuration, 'password');
		$options = Arr::get($configuration, 'options', []);
		
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
		
		$response = $this->_connection->query($queryBuilder->queryCompiled());
		
		// S'il y a une erreur lors de l'éxécution de la requête
		if(! $response)
		{
			$errorMessage ='Une erreur s\'est produite lors de l\'éxécution de la requête.';
			if($error_info = $this->_connection->errorInfo() AND $reason = Arr::get($error_info, 2))
			{
				$errorMessage .= ' '.$reason;
			}
			exception($errorMessage);
		}
	
		$queryType = $queryBuilder->type();
		
		if($queryType === QueryBuilder::TYPE_SELECT)
		{
			return $response->fetchAll(ConnectionPDO::FETCH_ASSOC);
		}
		else
		{
			$response = $response->rowCount();
			if($queryType == QueryBuilder::TYPE_INSERT)
			{
				static::$_last_insert_id = $this->_connection->lastInsertId();
			}
			return $response;
		}
	}
	
	/************************************************************************/
}