<?php

/**
 * Contrôleur de contenu d'une page HTML de base
 * @author Stève Caillault
 */

namespace App\Controllers\HTML;

use App\{ User };
use App\Site\Breadcrumb;
use App\HTML\Menu\{ HeaderHTML, MenuHTML };
/***/
use Root\{ Redirect, View, HTML };

abstract class ContentController extends BodyController
{
    /**
     * Titre de la page (pour affichage)
     * @var string
     */
    protected $_page_title = NULL;
    
    /**
     * Object permettant de gérer un fil d'ariane
     * @var Breadcrumb
     */
    protected $_site_breadcrumb	= NULL;
    
    /**
     * Liste des menus à afficher
     * @var array
     */
    protected $_menus = [
    	MenuHTML::TYPE_SECONDARY => NULL,
    	MenuHTML::TYPE_TERTIARY => NULL,
	];
    
    /**
     * Contenu principal de la page
     * @var View|string
     */
    protected $_main_content = NULL;
    
    /**
     * Vrai si la page demande un utilisateur connecté
     * @var bool
     */
    protected $_required_user = TRUE;
    
    /****************************************************************************************************************************/
    
    public function before() : void
    {
    	// Si un utilisateur doit être connecté, on redirige vers la page de connexion
    	if($this->_required_user AND User::current() === NULL)
    	{
    		Redirect::process(User::loginUri());
    	}
    	
    	parent::before();
        
        // Initialisation du fil d'ariane
        $this->_site_breadcrumb = Breadcrumb::instance();
        
    }
    
    public function after() : void
    {
        // Menus
        $menus = NULL;
        if($this->_menus[MenuHTML::TYPE_SECONDARY] !== NULL OR $this->_menus[MenuHTML::TYPE_TERTIARY] !== NULL)
        {
            $menus = View::factory('site/menu/submenu', [
            	'menus' => $this->_menus,
            ]);
        }
     
        // Contenu principal
        $mainContent = View::factory('html/content/main', [
        	'breadcrumb' => $this->_site_breadcrumb,
        	'pageTitle' => $this->_page_title,
        	'content' => $this->_main_content,
        ]);
        
        $content = $mainContent;
        
        $attributes = [
        	'id' => 'main-content',
        	'class' => ($menus === NULL) ? 'one-column' : 'two-columns',
        ];
        
        // Deux colonnes
        if($menus !== NULL)
        {
        	$content = View::factory('html/content/two-columns', [
        		'menus' => $menus,
        		'content' => $mainContent,
        	]);
        }
        
        $this->_content = View::factory('html/content', [
        	'header' => HeaderHTML::render(),
        	'content' => $content,
        	'attributes' => HTML::attributes($attributes),
        ]);
     
        parent::after();
    }
}
