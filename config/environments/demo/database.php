<?php defined('INITIALIZED') OR die('Vous n\'êtes pas autorisé à accéder à ce fichier.');

/**
 * Configuration de la base de données de démonstration
 */

return [
	'default'	=> [
		'connection'	=> [
			'dns'			=> 'mysql:host=localhost;dbname=banks_demo',
			'username'		=> 'root',
			'password'		=> NULL,
		],
	],
];