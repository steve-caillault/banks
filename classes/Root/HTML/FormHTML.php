<?php

/**
 * Gestion du HTML des formulaires
 */

namespace Root\HTML;

use Root\HTML;

class FormHTML {
	
	/**
	 * Retourne une balise label
	 * @param string $field Nom du chemin du label
	 * @param string $text Texte du label
	 * @param array $attributes
	 * @return string
	 */
	public static function label(string $name, string $text, array $attributes = []) : string
	{
		$attributes = HTML::attributes(array_merge([
			'for' => $name,
		], $attributes));
		
		return strtr('<label :attributes>:text</label>', [
			':attributes' => $attributes,
			':text' => $text,
		]);
	}
	
	/**
	 * Retourne une balise input
	 * @param string $type
	 * @param string $field
	 * @param array $attributes
	 * @return string
	 */
	public static function input(string $type, ?string $field = NULL, ?string $value = NULL, array $attributes = []) : string
	{
		$prepareAttributes = array_merge([
			'type' 	=> $type,
			'name'	=> $field,
			'value'	=> $value,
		], $attributes); 
		
		return strtr('<input :attributes />', [
			':attributes' => HTML::attributes($prepareAttributes),
		]);
	}
	
	/**
	 * Retourne une balise input de type texte
	 * @param string $field Nom du champs
	 * @param string $value Valeur du champs
	 * @param array $attributes
	 * @return string
	 */
	public static function text(string $field, ?string $value = NULL, array $attributes = []) : string
	{
		return static::input('text', $field, $value, $attributes);
	}
	
	/**
	 * Retourne une balise input de type nombre 
	 * @param string $field Nom du champs
	 * @param string $value Valeur du champs
	 * @param array $attributes
	 * @return string
	 */
	public static function number(string $field, ?string $value = NULL, array $attributes = []) : string
	{
		return static::input('number', $field, $value, $attributes);
	}
	
	/**
	 * Retourne une balise textarea
	 * @param string $field Nom du champs
	 * @param string $value Valeur du champs
	 * @param array $attributes
	 * @return string
	 */
	public static function textarea(string $field, ?string $value = NULL, array $attributes = []) : string
	{
		$prepareAttributes = array_merge([
			'name' => $field,
		], $attributes);
		
		return strtr('<textarea :attributes>:value</textarea>', [
			':value' => $value,
			':attributes' => HTML::attributes($prepareAttributes),
		]);
	}
	
	/**
	 * Retourne une balide de select 
	 * @param string $field Nom du champs
	 * @param string $value Valeur du champs sélectionné
	 * @param array $options Tableau des options sélectionnable
	 * @param array $attributes
	 * @return string
	 */
	public static function select(string $field, ?string $value = NULL, array $options = [], array $attributes = []) : string
	{
		$optionsHTML = '';
		foreach($options as $optionValue => $optionText)
		{
			$optionAttributes = [
				'value' => $optionValue,
			];
			if($value == $optionValue)
			{
				$optionAttributes['selected'] = 'selected';
			}
			
			$optionsHTML .= strtr('<option :attributes>:value</option>', [
				':attributes' => HTML::attributes($optionAttributes),
				':value' => $optionText,
			]);
		}
		
		$prepareAttributes = array_merge([
			'name' => $field,
		], $attributes);
		
		return strtr('<select :attributes>:options</select>', [
			':attributes' => HTML::attributes($prepareAttributes),
			':options' => $optionsHTML,
		]);
	}
	
	/**
	 * Retourne une balide de champs caché
	 * @param string $field Nom du champs
	 * @param string $value Valeur du champs
	 * @return string
	 */
	public static function hidden(string $field, ?string $value, array $attributes = []) : string
	{
		return static::input('hidden', $field, $value, $attributes);
	}
	
	/**
	 * Retourne une balise de champs file
	 * @param string $field Nom du champs
	 * @param array $attributes
	 * @return string
	 */
	public static function file(string $field, array $attributes = []) : string
	{
		return static::input('file', $field, NULL, $attributes);
	}
	
	/**
	 * Retourne une balise input submit
	 * @param string $text
	 * @param array $attributes
	 * @return string
	 */
	public static function submit(?string $text, array $attributes = []) : string
	{
		return static::input('submit', NULL, $text, $attributes);
	}
	
}