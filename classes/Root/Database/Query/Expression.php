<?php

/**
 * Gestion d'une expression protégée d'une requête 
 */

namespace Root\Database\Query;

use Root\Instanciable;

class Expression extends Instanciable {
    
    /**
     * Valeur de l'expression
     * @var string
     */
    private string $_value;
    
    /********************************************************************************/
    
    /* CONSTRUCTEUR / INSTANCIATION */
    
    /**
     * Constructeur
     * @param string $value Valeur de l'expression
     */
    protected function __construct(string $value)
    {
        $this->_value = $value;
    }
    
    /********************************************************************************/
    
    /* GET */
    
    /**
     * Retourne la valeur de l'expression
     * @return string
     */
    public function getValue() : string
    {
        return $this->_value;
    }
    
    /********************************************************************************/
    
}