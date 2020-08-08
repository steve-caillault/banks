<?php

/**
 * Contrôleur de base du site
 * @author Stève Caillault
 */

namespace App\Controllers\HTML;

use App\Site;
use Root\Controllers\HTMLController as HTMLController;
use App\Site\Meta;
use Root\HTML;
use Root\View;

abstract class BodyController extends HTMLController
{
    /**
     * Vue de base à utiliser
     * @var string
     */
    protected string $_template_path = 'html/body';
   
    /**
     * Titre de la balise title
     * @var string
     */
    protected $_head_title			= NULL;
    
    /**
     * Objet permetant de gérer une liste de balise meta
     * @var Meta
     */
    protected $_site_meta			= NULL;
    
    /**
     * Description de la balise meta description
     * @var string
     */
    protected $_meta_description	= NULL;
    
    /**
     * Tableau des mots clé de la balise meta keywords
     * @var array
     */
    protected $_meta_keywords		= [];
    
    /**
     * Contenu principal de la page
     * @var View|string
     */
    protected $_content				= NULL;
    
    /**
     * Vrai si on charge les fichiers JavaScript
     * @var bool
     */
    protected $_active_javascript	= FALSE;
    
    /****************************************************************************************************************************/
    
    public function before() : void
    {
        parent::before();
        // Initialisation des balises metas
        $this->_site_meta = Meta::factory([
            'name' => 'robots',
            'content' => 'noindex,nofollow',
        ])->set([
            'charset' => 'utf-8',
        ]);
    }
    
    public function after() : void
    {
        // Balise meta description
        $metaDescription = Site::description($this->_meta_description);
        if($this->_head_title != $this->_meta_description)
        {
            $this->_site_meta->set([
                'name'		=> 'description',
                'content'	=> $metaDescription,
            ]);
        }
        
        // Balise meta keywords
        $this->_site_meta->set([
            'name'			=> 'keywords',
            'content'		=> Site::keywords($this->_meta_keywords),
    	]);
        $siteMeta = $this->_site_meta->render();
        // Titres
        $headTitle = Site::title($this->_head_title);
        // Contenu
        $content = $this->_content;
        // Scripts Javascript
        $scripts = Site::scripts($this->_active_javascript);
        // Styles CSS
        $styles = Site::styles();
        // Favicon
        $favicon = HTML::link('file/images/favicon.ico', [
            'rel'	=> 'shortcut icon',
        ]);
        
     	// Configuration du JavaScript
        $configJavascript = ($this->_active_javascript) ? json_encode(Site::javascriptSettings()) : NULL;
        
        // On transmet les variables à la vue
        $this->_template->setVars([
            'head_title'   	 	=> $headTitle,
            'metas'         	=> $siteMeta,
            'styles'       		=> $styles,
            'scripts'       	=> $scripts,
            'favicon'      	 	=> $favicon,
            'content'       	=> $content,
        	'config_javascript' => $configJavascript,
        ]);
        
        // Pour enregistrer les requêtes dans un fichier
        /*$queries = \Root\DB::queries();
        if(count($queries) > 0)
        {
        	logMessage(implode(PHP_EOL, $queries));
        }*/
        
        parent::after();
    }
}