<?php

/**
 * Classe utilitaire pour le site
 */

namespace App;

use Root\Config;
use Root\HTML;
use Root\Request;

class Site {
    
    /**
     * Nom du site
     * @var string
     */
    private static $_name = NULL;
    
    /**
     * Titre de la page
     * @var string
     */
    private static $_title = NULL;
    
    /**
     * Description de la page
     * @var string 
     */
    private static $_description = NULL;
    
    /********************************************************************************/
    
    /* BALISE TITLE / META */
    
    /**
     * Retourne le nom du site
     * @return string
     */
    public static function name() : ?string
    {
        if(self::$_name === NULL)
        {
            self::$_name = Config::load('site.name');
        }
        return self::$_name;
    }
    
    /**
     * Retourne le nom de la page actuelle
     * @param string $title Si renseigné, le titre de la page
     * @return string
     */
    public static function title($title = NULL) : ?string
    {
        if(self::$_title === NULL)
        {
            if($title === NULL)
            {
                $title = self::name();
            }
            self::$_title = $title;
        }
        return self::$_title;
    }
    
    /**
     * Retourne la description du site
     * @param string $description Si renseigné, la description à affecter 
     * @return string
     */
    public static function description(?string $description = NULL) : ?string
    {
        if(self::$_description === NULL)
        {
            if($description === NULL)
            {
                $description = Config::load('site.description');
            }
            self::$_description = $description;
        }
        return self::$_description;
    }
    
    /**
     * Retourne les mots clés du site, si on en transmet en paramètres on fusionne un tableau
     * @param array $keywords Tableau des mots clés de la page actuelle
     * @return array
     */
    public static function keywords(array $keywords = []) : string
    {
        if($siteName = static::name())
        {
            $keywords[] = $siteName;
        }
        return implode(', ', $keywords);
    }
    
    /********************************************************************************/
    
    /* BALISES SCRIPTS */
    
    /**
     * Retourne les balises JavaScripts su site
     * @param bool $actived Vrai si le JavaScript est actif
     * @return string
     */
    public static function scripts($actived = FALSE) : ?string
    {
        if(! $actived)
        {
            return NULL;
        }
        
        $scripts = ''; 
        if($configScripts = Config::load('static.scripts'))
        {
            foreach($configScripts as $script)
            {
            	$url = 'files/scripts/' . $script;
                $scripts .= HTML::script($url);
            }
        }
        return $scripts;
    }
    
    /**
     * Retourne la configuration du JavaScript
     * @return array
     */
    public static function javascriptSettings() : array
    {
    	$request = Request::current();
    	
    	$routeName = $request->route()->name();
    	$controllerClass = strtr(ucwords(strtr(strtolower($routeName), [ '.' => ' '])), [ ' ' => '' ])  . 'Controller';
    
    	return [
    		/*'route' => [
    			'name' => $routeName,
    		],*/
    		'controller' => [
    			'className' => $controllerClass,
    		],
    	];
    }
    
    /****************************************************************************************************************************/
    
    /* BALISES STYLES */
    
    /**
     * Retourne les balises de styles du site
     * @return string
     */
    public static function styles() : string
    {
        $styles = '';
         
        if($configStyles = Config::load('static.styles.files'))
        {
            foreach($configStyles as $style)
            {
                $url = 'files/styles/' . $style;
                $styles .= HTML::style($url);
            }
        }
        $styles .= HTML::style('files/styles/print.css', [
            'media' => 'print',
        ]);
        return $styles;
    }
    
    /****************************************************************************************************************************/
    
    
}