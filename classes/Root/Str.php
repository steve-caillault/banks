<?php

/**
 * Méthode utilitaire sur les chaines de caractères
 */

namespace Root;

final class Str {
    
    /**
     * Transforme la chaine de caractères en paramètre en camelCase
     * @param string $value 
     * @return string
     */
    public static function camelCase(string $value) : string
    {
        return strtr(ucwords(strtr(strtolower($value), [ '_' => ' '])), [ ' ' => '' ]);
    }
    
}