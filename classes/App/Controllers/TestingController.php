<?php

/**
 * Page de test
 */

namespace App\Controllers;

use Root\{ Controller };

class TestingController extends Controller {
	
	public function index()
	{
		/*$uri = 'owners/{some}/(/page-{page}-{other})'; // 'owners/{ownerId}/budgets(/{year})';
		$searchParams = [];
		
		
		
		preg_match_all('/{[^{}.]+}/', $uri, $searchParams);
		
		\({page}\)*/
		
		
		//$pattern = '/[\(^(\({other}).]+/';
		
		// '/\([^(\({other}).]+[{other}]+[^(\){other}).]\)+/'
		
		// /\([^(\({bla}).]*[{bla}]+[^(\){bla}).]\)*/
		
		//'/\([^({page}).]*[{page}]+/';
		//$found = preg_match($pattern, $uri, $data);
		
		//debug($data, TRUE);
		
		//echo debug($searchParams, TRUE);
		
		/*$uriParams = Arr::get($searchParams, 0, []);
		
		//echo debug($searchParams);
		
		foreach($uriParams as $tag)
		{*/
			/*$pattern = strtr('/\([{page}]+\)/', [
				':tag' => $tag,
			]);
			
			$found = preg_match($pattern, $uri);
			
			echo $tag . var_dump($found);*/
			
			//echo $pattern . '<br />';
			
			/*$isOptional = strtr('/\(:tag)\/', [
				':tag' => 
			]);*/
			
			//preg_match('/^\(.+\)$/D', $tag);
			/*if($isOptional)
			{
				//$tag = trim($tag, '()');
			}
			var_dump($isOptional);
			$paramKey = trim($tag, '{}');
			
			echo $tag . '<br />';*/
		/*}
		
		
		debug($searchParams, TRUE);*/
		
		// logMessage('Message de log ' . date('d/m/Y H:i:s'));
		/*$dateBegin = Date::instance('2012-01-01');
		$dateEnd = Date::instance('2018-01-01');
		
		$dates = Date::between(Date::PERIOD_YEAR, $dateBegin, $dateEnd);
		
		echo debug($dates);*/
		
		/*$account = Account::factory(1);
		$type = AccountStatistic::TYPE_YEAR;
		$timestamp = Date::instance('2018-01-01')->getTimestamp();
		
		$latest = AccountStatistic::latest($account, $type, $timestamp);
		
		var_dump($latest);*/
		
		/*$value = 'value2';
		
		$response = DB::insert('testing', [ 
			'id', 'text',
		])->addValues([
			'id' => 1,
			'text' => $value,
		])->onDuplicateUpdate([
			'text' => $value,
		])->execute();
		
		echo Debug::show($response);*/
		
		/*$user = User::factory([
			'first_name' => 'Stève',
			'last_name' => 'Caillault',
			'password_hashed' => User::passwordCrypted('FkJ9mq4Q'),
		]);
		
		$user->save();
		
		echo \Root\Debug::show($user);*/
		
		/*$password = 'dsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflgdsgfdfqg,kdflgkdfjglf bdfmbldfgdflg';
		echo password_hash($password, PASSWORD_ARGON2I);*/
		
		//$valid = password_verify($password, '$argon2i$v=19$m=1024,t=2,p=2$RUZER1ZzR1dMMUdmUmpxdQ$lnb29K5XiT9Qz6QeD9wmS1FEIpR2TJ6w7mhOoPk1vzo');
		
		//var_dump($valid);
		
		/*$keySession = 'add_session';
		$value = Arr::get($this->request()->inputs(), $keySession);
		
		$session = session();
		// $session->delete($keySession);
		
		if($value)
		{
			$session->change($keySession, $value);
		}
		
		\Root\Debug::show($_SESSION);
		\Root\Debug::show($session);*/
		
	}
	
}