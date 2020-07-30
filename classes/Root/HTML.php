<?php

/**
 * Classe utilitaire sur le contenu HTML
 */

namespace Root;

class HTML {
    
    /**
     * Formate la chaine des attributs d'une balise HTML
     * @param array $attributes
     * @return string  
     */
    public static function attributes(array $attributes) : string
    {
        $str = '';
     
        foreach($attributes as $key => $value)
        {
            $str .= ' ' . $key . '="' . htmlentities($value) . '"';
        }
        
        return trim($str);
    }
    
    /**
     * Retourne le HTML d'une ancre
     * @param string $uri
     * @param string $label Texte de l'ancre
     * @param array $attributes
     * @return string
     */
    public static function anchor(string $uri, string $label, array $attributes = []) : string
    {
    	$attributes = static::attributes(array_merge([
    		'href' => URL::get($uri),
    	], $attributes));
    	
    	
    	return strtr('<a :attributes>:label</a>', [
    		':attributes' 	=> $attributes,
    		':label'		=> $label,
    	]);
    }
    
    /**
     * Retourne le HTML d'une balise image
     * @param string $path Chemin ou URL de l'image
     * @param array $attributes 
     * @return string
     */
    public static function image(string $path, array $attributes = []) : string
    {
    	$prepareAttributes = array_merge([
    		'src' => URL::get($path),
    	], $attributes);
    	
    	return strtr('<img :attributes />', [
    		':attributes' => static::attributes($prepareAttributes),
    	]);
    }
    
    /**
     * Retourne le HTML d'une balise script
     * @param string $url
     * @param array $options (defer, async)
     * @return string
     */
    public static function script(string $url, array $options = []) : string
    {
        $attributes = static::attributes(array_merge([
            'type'  => 'text/javascript',
            'src'   => URL::get($url),
        ], $options));
        
        return strtr('<script :attributes></script>', [
          ':attributes' => $attributes,  
        ]);
    }
    
    /**
     * Retourne le HTML d'une balise link
     * @param string $url
     * @param array $options (type, rel...)
     * @return string
     */
    public static function link(string $url, array $options = []) : string
    {
        $attributes = static::attributes(array_merge([
            'href' => URL::get($url),
        ], $options));
        
        return strtr('<link :attributes />', [
            ':attributes' => $attributes,
        ]); 
    }
    
    /**
     * Retourne le HTML d'une balise style
     * @param string $url
     * @param array $options (media)
     * @return string
     */
    public static function style($url, array $options = []) : string
    {
        $linkOptions = array_merge([
            'rel'   => 'stylesheet',
            'type'  => 'text/css',
        ], $options);
        return static::link($url, $linkOptions);
    }
    
}