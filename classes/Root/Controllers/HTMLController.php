<?php

/**
 * Contrôleur HTML de base
 */

namespace Root\Controllers;

use Root\Controller;
use Root\View;

class HTMLController extends Controller {
    
    /**
     * Chemin de la vue de base à utiliser
     * @var string
     */
    protected $_templatePath = NULL;
    
    /**
     * Vue à utiliser
     * @var View
     */
    protected $_template = NULL;
    
    /********************************************************************************/
    
    /* CONTRUCTEUR / INSTANCIATION */
    
    /**
     * Constructeur
     */
    public function __construct()
    {
    	if($this->_templatePath === NULL)
        {
            exception('Le template est inconnu.');
        }
        
        $this->_template = View::factory($this->_templatePath);
        
        parent::__construct();
    }
    
    /********************************************************************************/
    
    /**
     * Retourne la réponse du contrôleur
     * @param mixed $response Si renseignée, la valeur à effecter
     * @return mixed
     */
    final public function response($response = NULL)
    {
        $this->_response = $this->_template->render();
        return $this->_response;
    }
   
    /********************************************************************************/
    
}