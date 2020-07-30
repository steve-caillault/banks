<?php

/**
 * Gestion de l'en-tête du site
 */

namespace App\HTML\Menu;

use Root\{ Route, View, HTML };
/***/
use App\{ User, Site };

class HeaderHTML {
	
	/**
	 * Retourne la vue de l'en-tête du site
	 * @return View
	 */
	public static function render() : View
	{
		$siteTitle = Site::name();
		$homeUri = Route::retrieve('home')->uri();
		$homeAnchor = HTML::anchor($homeUri, $siteTitle, [
			'title' => 'Revenir à la racine du site.',
			'class' => 'home',
		]);
		
		$content = View::factory('site/header', [
			'homeAnchor' => $homeAnchor,
			'menus' => [
				'main' => self::mainMenu(),
				'user' => self::userMenu(),
			],
		]);
		
		return $content;
	}
	
	/**
	 * Retourne le menu principal du site
	 * @return MainMenuHTML
	 */
	public static function mainMenu() : ?MainMenuHTML
	{
		if(User::current() === NULL)
		{
			return NULL;
		}
		return MainMenuHTML::factory();
	}
	
	/**
	 * Retourne le menu de l'utilisateur connecté
	 * @return View
	 */
	public static function userMenu() : ?View
	{
		$user = User::current();
		if($user === NULL)
		{
			return NULL;
		}
		
		$data = [
			'name' => $user->fullName(),
			'anchors' => [
				'disconnect' => HTML::anchor($user->logoutUri(), 'Se déconnecter', [
					'title' => 'Se déconnecter du site.',
				]),
			],
		];
		
		return View::factory('site/menu/user', $data);
	}
}