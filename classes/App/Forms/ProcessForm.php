<?php

/**
 * Gestion du traitement et du rendu d'un formulaire
 * @author Stève Caillault
 */

namespace App\Forms;

use Root\Validation;
use Root\Arr;
use Root\View;
use Root\HTML\FormHTML;
use Root\HTML;
use Root\Instanciable;

abstract class ProcessForm extends Instanciable
{
	protected const METHOD_GET 		= 'GET';
	protected const METHOD_POST 	= 'POST';
	/***/
	protected const FIELD_TEXT 		= 'text';
	protected const FIELD_PASSWORD	= 'password';
	protected const FIELD_DATE		= 'date';
	protected const FIELD_TEXTAREA 	= 'textarea';
	protected const FIELD_SELECT 	= 'select';
	protected const FIELD_FILE 		= 'file';
	protected const FIELD_HIDDEN	= 'hidden';
	/***/
	protected const DATA_FORM_NAME 	= 'form_name';
	
	/**********************************************************************************************************/
	
	/**
	 * Nom du formulaire
	 * @var string 
	 */
	public static $name	= NULL;
	
	/**
	 * Noms de champs autorisés 
	 * @var array
	 */
	protected static $_allowed_names = [];
	
	/**
	 * Listes des labels
	 * @var array
	 */
	protected static $_labels = [];
	
	/**********************************************************************************************************/

	/**
	 * Données du formulaire
	 * @var array 
	 */
	protected $_data = [];
	
	/**
	 * Objet validation 
	 * @var Validation
	 */
	protected $_validation = NULL;
	
	
	/**
	 * Vrai si le formulaire a pu être traité avec succès (reste à NULL si le formulaire n'a pas été posté)
	 * @var bool 
	 */
	private $_success = NULL;
	
	/**
	 * Erreurs de validation du formulaire
	 * @var array 
	 */
	protected $_errors = [];
	
	/**********************************************************************************************************/
	
	/**
	 * Chemin de la vue à utiliser pour le rendue
	 * @var string
	 */
	protected static $_file_view = 'forms/default';
	
	/**
	 * Méthode de transmission des données
	 * @var string
	 */
	protected static $_method = self::METHOD_POST;
	
	/**
	 * Vrai si on doit uploader des fichiers
	 * @var bool
	 */
	protected static $_must_upload_files = FALSE;
	
	/**
	 * Vrai si on doit afficher le titre du formulaire
	 * @var bool
	 */
	protected static $_with_title = TRUE;
	
	/**********************************************************************************************************/
	
	/* VALIDATION */
	
	/**
	 * Test si le nom en paramètre est un nom de champs valide
	 * @return bool
	 */
	private static function _allowedName($name) : bool
	{
		if($name == static::DATA_FORM_NAME)
		{
			return TRUE;
		}
		return array_key_exists($name, static::$_allowed_names);
	}
	
	/**
	 * Retourne l'objet validation
	 * @return Validation 
	 */
	protected function _validation() : Validation
	{
		if($this->_validation === NULL)
		{
			$this->_validation = $this->_initValidation();
		}
		return $this->_validation;
	}
	
	/**
	 * Retourne l'objet Validation initialisé avec les réglés de validation
	 * @return Validation
	 */
	abstract protected function _initValidation() : Validation;
	
	/**********************************************************************************************************/
	
	/* CONSTRUCTEUR / INSTANCIATION */
	
	/**
	 * Constructeur 
	 * @param array $params Paramètres
	 * @return array
	 */
	protected function __construct(array $params)
	{
		$this->_data(Arr::get($params, 'data', $this->_data));
	}
	
	/**********************************************************************************************************/

	/* TRAITEMENT DU FORMULAIRE */
	
	/**
	 * Processus de traitement du formulaire
	 * @return void
	 */
	public function process() : void
	{
		// On vérifit que le nom du formulaire correspont à celui qui a été soumis
		$nameSubmitted = Arr::get($this->_data, static::DATA_FORM_NAME);
		if($nameSubmitted == static::$name)
		{
			$validation = $this->_validation();
			$validation->validate();
			if($validation->success())
			{
				$this->_success = $this->_onValid();
			}
			else
			{
				$this->_errors = $this->_onErrors();
			}
		}
	}
	
	/**
	 * Méthode à exécuter si le formulaire est valide
	 * @return bool
	 */
	abstract protected function _onValid() : bool;
	
	/**
	 * Méthode à exécuter si le formulaire à des erreurs
	 * @return array Tableau des erreurs du formulaire
	 */
	protected function _onErrors() : array
	{
		$this->_success = FALSE;
		return $this->_validation()->errors();
	}
	
	/**
	 * Méthode à éxécuter si le traitement du formulaire est un succès
	 * @return void
	 */
	protected function _onSuccess() : void
	{
		// Rien pour le moment 
	}
	
	/**
	 * Retourne l'URL de redirection où rediriger en cas de succès
	 * @return string
	 */
	abstract public function redirectUrl() : ?string;
	
	/**********************************************************************************************************/
	
	/* RETOURNE MODIFIT LES DONNEES */
	
	/**
	 * Retourne / modifit les données du formulaire
	 * @return array 
	 */
	protected function _data(?array $data = NULL) : array
	{
		if($data !== NULL)
		{
			foreach($data as $key => $value)
			{
				if(static::_allowedName($key))
				{
					$value = (is_string($value)) ? trim(strip_tags($value)) : $value;
					$this->_data[$key] = $value;
				}
			}
		}
		return $this->_data;
	}
	
	/**
	 * Retourne si le formulaire a pu être traité avec succès (reste à NULL si le formulaire n'a pas été posté)
	 * @return bool
	 */
	public function success() : bool
	{
		return $this->_success;
	}
	
	/**
	 * Retourne les erreurs de validation du formulaire
	 * @return array 
	 */
	protected function _errors() : array
	{
		return $this->_errors;
	}
	
	/**********************************************************************************************************/
	
	/* RENDU */
	
	/**
	 * Retourne le titre du formulaire
	 * @return string
	 */
	abstract public function title() : string;
	
	/**
	 * Retourne la réponse du traitement du formulaire (utilisé lors d'un appel Ajax notamment)
	 * @return array
	 *		'success': <boolean>,
	 *		'errors': <array>
	 */
	public function response()
	{
		$response = array(
			'success'	=> $this->success(),
			'errors'	=> $this->_errors(),
		);
		return $response;
	}
	
	/**
	 * Retourne le tableau des champs du formulaire
	 * @return array
	 */
	protected function _inputs() : array
	{
		$inputs = [
			'fields' 	=> [],
			'hidden'	=> [],
			'files'	 	=> [],	
			'name' 		=> (static::$name === NULL) ? NULL : FormHTML::hidden(static::DATA_FORM_NAME, static::$name),
			'submit' 	=> FormHTML::submit('Envoyer'),
		];
		
		foreach(static::$_allowed_names as $name => $fieldType)
		{
			$key = ($fieldType == self::FIELD_FILE) ? 'files' : (($fieldType == self::FIELD_HIDDEN) ? 'hidden' : 'fields');
			$inputs[$key][$name] = $this->{ '_input' . ucfirst($fieldType) }($name);
		}
		
		
		
		return $inputs;
	}
	
	/**
	 * Retourne un champs texte pour le nom du champs en paramètre
	 * @param string $name Nom du champs
	 * @return string
	 */
	protected function _inputText(string $name) : string
	{
		$value = Arr::get($this->_data, $name);
		return FormHTML::text($name, $value, [
			'autocomplete' => 'off',
			'id' => $name,
		]);
	}
	
	/**
	 * Retourne un champs mot de passe pour le nom du champs en paramètre
	 * @param string $name Nom du champs
	 * @return string
	 */
	protected function _inputPassword(string $name) : string
	{
		return FormHTML::input('password', $name, NULL, [
			'id' => $name,
		]);
	}
	
	/**
	 * Retourne un champs date pour le nom du champs en paramètre
	 * @param string $name Nom du champs
	 * @return string
	 */
	protected function _inputDate(string $name) : string
	{
		$value = Arr::get($this->_data, $name);
		return FormHTML::input('date', $name, $value, [
			'autocomplete' => 'off',
			'id' => $name,
		]);
	}
	
	/**
	 * Retourne un champs textarea pour le nom du champs en paramètre
	 * @param string $name Nom du champs
	 * @return string
	 */
	protected function _inputTextarea(string $name) : string
	{
		$value = Arr::get($this->_data, $name);
		return FormHTML::textarea($name, $value, [
			'id' => $name,
		]);
	}
	
	/**
	 * Retourne un champs select pour le nom du champs en paramètre
	 * @param string $name Nom du champs
	 * @return string
	 */
	protected function _inputSelect(string $name) : string
	{
		$value = Arr::get($this->_data, $name);
		$options = $this->{ '_selectOptions' }($name);
		
		if(count($options) == 0)
		{
			exception(strtr('Aucune options pour le champs :name.', [
				':name' => $name,
			]));
		}
		
		return FormHTML::select($name, $value, $options, [
			'id' => $name,
		]);
	}
	
	/**
	 * Retourne les options du champs select dont le nom du champs est en paramètre
	 * @param string $name Nom du champs
	 * @return string
	 */
	protected function _selectOptions(string $name) : array
	{
		// A surcharger dans les classes filles
		return [];
	}
	
	/**
	 * Retourne un champs de téléchargement de fichier pour le nom du champs en paramètre
	 * @param string $name Nom du champs
	 * @return string
	 */
	protected function _inputFile(string $name) : string
	{
		return FormHTML::file($name);
	}
	
	/**
	 * Retourne un champs caché pour le nom du champs en paramètre
	 * @param string $name Nom du champs
	 * @return string
	 */
	protected function _inputHidden(string $name) : string
	{
		return FormHTML::hidden($name, Arr::get($this->_data, $name));
	}
	
	/**
	 * Retourne les labels des champs
	 * @return array
	 */
	protected function _labels() : array
	{
		$labels = [];
		
		foreach(static::$_labels as $key => $value)
		{
			$labels[$key] = FormHTML::label($key, $value);
		}
		
		return $labels;
	}
	
	/**
	 * Retourne les attributs du formulaire
	 * @return array
	 */
	protected function _attributes() : array
	{
		$attributes = [
			'method' => strtolower(static::$_method),
		];
		
		if(static::$_must_upload_files)
		{
			$attributes['enctype'] = 'multipart/form-data';
		}
		
		return $attributes;
				
	}
	
	/**
	 * Méthode de rendu du formulaire
	 * @return View 
	 */
	public function render() : View
	{
		
		$attributes = HTML::attributes($this->_attributes());
		
		return View::factory(static::$_file_view, [
			'attributes' 	=> $attributes,
			'errors' 		=> $this->_errors(),
			'title'			=> $this->title(),
			'withTitle'		=> static::$_with_title,
			'labels'		=> $this->_labels(),
			'inputs'		=> $this->_inputs(),
		]);
	}
		
	/**********************************************************************************************************/
	
}
