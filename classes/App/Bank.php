<?php

/**
 * Gestion d'une banque
 */

namespace App;

use Root\Route;

class Bank extends Model {
	
	/**
	 * Table du modèle
	 * @var string
	 */
	public static $table = 'banks';
	
	/****************************************************************************************************************************/
	
	/* PROPRIETES EN BASE DE DONNEES */
	
	/**
	 * Identifiant
	 * @var int
	 */
	public $id = NULL;
	
	/**
	 * Nom
	 * @var string
	 */
	public $name = NULL;
	
	/****************************************************************************************************************************/
	
	/**
	 * Retourne l'URI de la liste des banques
	 * @return string
	 */
	public static function listUri() : string
	{
		return Route::retrieve('banks.list')->uri();
	}
	
	/**
	 * Retourne l'URI de création d'une banque
	 * @return string
	 */
	public static function addUri() : string
	{
		return Route::retrieve('banks.add')->uri();
	}
	
	/**
	 * Retourne l'URI d'édition de la banque
	 * @return string
	 */
	public function editUri()
	{
		return Route::retrieve('banks.edit')->uri([
			'bankId' => $this->id,
		]);
	}
	
	/****************************************************************************************************************************/
	
}