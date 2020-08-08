<?php

/**
 * Raccourcis pour les effectuer des requêtes à la base de données
 * @author Stève Caillault
 */

namespace Root;

use Root\Database\Query\Expression as QueryExpression;
/***/
use Root\Database\Query\Builder;
use Root\Database\Query\Builder\{ Select, Insert, Update, Delete };

class DB {
	
    /**
     * Retourne une expression protégée avec la valeur en paramètre
     * @param $expression $string
     * @return QueryExpression
     */
    public static function expression($expression) : QueryExpression
    {
        return QueryExpression::factory($expression);
    }
    
    /**
     * Retourne la liste des requêtes exécutées
     * @return array
     */
    public static function queries() : array
    {
    	return Builder::queries();
    }
    
	/**
	 * Retourne le dernier identifiant inséré
	 * @return string
	 */
	public static function lastInsertId() : ?string
	{
		return Database::lastInsertId();
	}
	
	/**
	 * Retourne la dernière requête exécutée
	 * @return string
	 */
	public static function lastQuery() : ?string
	{
		return Database::lastQuery();
	}
	
	/**
	 * Retourne une instance pour une requête SELECT
	 * @param array $fields Champs à sélectionner
	 * @return Select
	 */
	public static function select(array $fields) : Select
	{
		return Select::factory($fields); 
	}
	
	/**
	 * Retourne une instance pour une requête INSERT
	 * @param string $table Table où insérer
	 * @param array $fields Champs où insérer
	 * @return Insert
	 */
	public static function insert(string $table, array $fields = []) : Insert
	{
		return Insert::factory($table)->fields($fields);
	}
	
	/**
	 * Retourne une instance pour une requête UPDATE
	 * @param string $table Table à mettre à jour
	 * @return Update
	 */
	public static function update(string $table) : Update
	{
		return Update::factory($table);
	}
	
	/**
	 * Retourne une instance pour une requête DELETE
	 * @param string $table Table dans laquelle supprimer
	 * @return Delete
	 */
	public static function delete(string $table) : Delete
	{
		return Delete::factory($table);	
	}
	
}

