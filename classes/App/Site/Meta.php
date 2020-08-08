<?php

/**
 * Gestion des balises meta
 * @author Stève Caillault
 */

namespace App\Site;

use Root\{ HTML, Instanciable, View };

class Meta extends Instanciable
{
	/**
	 * Liste des balises meta
	 * @var array
	 */
	private array $_metas = [];
	
	/****************************************************************************************************************************/
	
	/* CONSTRUCTEUR ET INSTANCIATION */
	
	/**
	 * Constructeur d'un objet Site_Meta
	 * @param array $params (la première balise meta à affecter)
	 */
	protected function __construct(array $params = NULL)
	{
		if($params !== NULL)
		{
			$this->set($params);
		}
	}
	
	/****************************************************************************************************************************/
	
	/* AJOUT / MODITICATION */
	
	/**
	 * Ajoute / modifit une balise meta
	 * @param array $params
	 * @return self
	 */
	public function set(array $params) : self
	{
		$update = FALSE; // Si une balise a été mis à jour, on ne la crée pas
		if($name = getArray($params, 'name'))
		{
			// On parcours la liste des balises pour vérifier s'il ne faut modifier une balise
			foreach($this->_metas as $key => $meta)
			{
				if(getArray($meta, 'name') == $name)
				{
					$this->_metas[$key] = $params;
					$update = TRUE;
				}
			}
		}
		// Si on n'a pas pu mettre à jour une balise meta alors c'est qu'il faut la créer
		if(! $update)
		{
			$this->_metas[] = $params;
		}
		return $this;
	}
	
	/****************************************************************************************************************************/
	
	/* RENDUS */
	
	/**
	 * Retourne le HTML des balises meta
	 * @return View
	 */
	public function render() : ?View
	{
		$metas = [];
		
		foreach($this->_metas as $meta)
		{
			$metas[] = [
				'attributes' => HTML::attributes($meta),
			];
		}
		
		if(count($metas) == 0)
		{
			return NULL;
		}
		
		return View::factory('tools/metas', [
		    'metas'	=> $metas,
		]);
	}
	
	/****************************************************************************************************************************/
	
}

