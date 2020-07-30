<?php

/**
 * Gestion d'un objet instanciable
 * @author Stève Caillault
 */

namespace Root;

abstract class Instanciable
{
    
    /**
     * Instanciation
     * @return self
     */
    public static function factory($params = NULL)
    {
        return new static($params);
    }
   
}