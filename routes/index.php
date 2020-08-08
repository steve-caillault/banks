<?php defined('INITIALIZED') OR die('Vous n\'êtes pas autorisé à accéder à ce fichier.');

/**
 * Définition des routes
 */

use Root\Route;

/*********************************************************************************/

/* GESTION DES PROPRIETAIRES */

/**
 * Liste des propriétaires
 */
Route::add('owners.list', 'owners(/page-{page})', 'Owners\CollectionController@index')->where([
	'page' => '[0-9]+',
])->defaults([
	'page' => 1,
]);

/**
 * Ajout d'un propriétaire
 */
Route::add('owners.add', 'owners/add', 'Owners\CreateOrEditController@index');

/**
 * Edition d'un propriétaire
 */
Route::add('owners.edit', 'owners/{ownerId}/edit', 'Owners\CreateOrEditController@index')->where([
	'ownerId' => '[0-9]+',
]);

/**
 * Evolution des comptes bancaires du propriétaire
 */
Route::add('owners.evolution', 'owner/{ownerId}/evolution', 'Owners\EvolutionController@index')->where([
	'ownerId' => '[0-9]+',
]);

/**
 * Comparaison des années des comptes bancaires du propriétaire
 */
Route::add('owners.comparison', 'owner/{ownerId}/comparison', 'Owners\ComparisonController@index')->where([
	'ownerId' => '[0-9]+',
]);

/*********************************************************************************/

/* GESTION DES COMPTES BANCAIRES */

/**
 * Liste des comptes d'un propriétaire
 */
Route::add('owners.accounts.list', 'owners/{ownerId}/accounts', 'Owners\Accounts\CollectionController@index')->where([
	'ownerId' => '[0-9]+',
]);

/**
 * Remise à 0 des comptes du propriétaire
 */
Route::add('owners.accounts.reset', 'owners/{ownerId}/accounts/reset', 'Owners\Accounts\ResetController@index')->where([
	'ownerId' => '[0-9]+',
]);

/**
 * Ajouter un compte à un propriétaire
 */
Route::add('owners.accounts.add', 'owners/{ownerId}/accounts/add', 'Owners\Accounts\CreateOrEditController@index')->where([
	'ownerId' => '[0-9]+',
]);

/**
 * Edition du compte d'un propriétaire
 */
Route::add('owners.accounts.edit', 'owners/{ownerId}/accounts/{accountId}/edit', 'Owners\Accounts\CreateOrEditController@index')->where([
	'ownerId' => '[0-9]+',
	'accountId' => '[0-9]+',
]);

/*********************************************************************************/

/* OPERATIONS BANCAIRES */

/**
 * Liste des opérations d'un compte
 */
Route::add('owners.accounts.operations.list', 'owners/{ownerId}/accounts/{accountId}/operations(/page-{page})', 'Owners\Accounts\Operations\CollectionController@index')->where([
	'ownerId' => '[0-9]+',
	'accountId' => '[0-9]+',
	'page' => '[0-9]+',
])->defaults([
	'page' => 1,
]);

/**
 * Calcul de la valeur du compte
 */
Route::add('owners.accounts.operations.compute', 'owners/{ownerId}/accounts/{accountId}/operations/compute', 'Owners\Accounts\Operations\ComputeController@index')->where([
	'ownerId' => '[0-9]+',
	'accountId' => '[0-9]+',
]);

/**
 * Ajout d'une opération bancaire à un compte
 */
Route::add('owners.accounts.operations.add', 'owners/{ownerId}/accounts/{accountId}/operations/add', 'Owners\Accounts\Operations\CreateOrEditController@index')->where([
	'ownerId' => '[0-9]+',
	'accountId' => '[0-9]+',
]);

/**
 * Evolution de la valeur d'un compte bancaire
 */
Route::add('owners.accounts.evolution', 'owners/{ownerId}/accounts/{accountId}/evolution', 'Owners\Accounts\EvolutionController@index')->where([
	'ownerId' => '[0-9]+',
	'accountId' => '[0-9]+',
]);

/**
 * Modification d'une opération bancaire d'un compte
 */
Route::add('owners.accounts.operations.edit', 'owners/{ownerId}/accounts/{accountId}/operations/{operationId}/edit', 'Owners\Accounts\Operations\CreateOrEditController@index')->where([
	'ownerId' => '[0-9]+',
	'accountId' => '[0-9]+',
	'operationId' => '[0-9]+',
]);

/*********************************************************************************/

/* GESTION DE BUDGETS */

/**
 * Liste des budgets
 */
Route::add('owners.budgets.list', 'owners/{ownerId}/budgets', 'Owners\Budgets\CollectionController@index')->where([
	'ownerId' => '[0-9]+',
]);

/**
 * Budget d'une année
 */
Route::add('owners.budgets.year', 'owners/{ownerId}/budgets/{year}', 'Owners\Budgets\BudgetController@index')->where([
	'ownerId' => '[0-9]+',
	'year' => '[0-9]{4}',
]);

/**
 * Initialiser un budget
 */
Route::add('owners.budgets.init', 'owners/{ownerId}/budgets/init', 'Owners\Budgets\CreateOrEditController@index')->where([
	'ownerId' => '[0-9]+',
])->defaults([
	'year' => NULL,
]);
 

/**
 * Ajout d'un budget
 */
Route::add('owners.budgets.add', 'owners/{ownerId}/budgets/{year}/add', 'Owners\Budgets\CreateOrEditController@index')->where([
	'ownerId' => '[0-9]+',
	'year' => '[0-9]{4}',
]);

/*Route::add('owners.budgets.add', 'owners/{ownerId}/budgets/{year}/add', 'Owners\Budgets\CreateOrEditController@index')->where([
	'ownerId' => '[0-9]+',
	'year' => '(0-9]{4}',
])->defaults([
	'year' => NULL,
]);*/

/**
 * Modification d'un budget
 */
Route::add('owners.budget.edit', 'owners/{ownerId}/budgets/{year}/{budgetId}', 'Owners\Budgets\CreateOrEditController@index')->where([
	'ownerId' => '[0-9]+',
	'year' => '[0-9]{4}',
	'budgetId' => '[0-9]+',
]);

/*********************************************************************************/

/* GESTION DES BANQUES */

/**
 * Liste des banques
 */
Route::add('banks.list', 'banks(/page-{page})', 'Banks\CollectionController@index')->where([
	'page' => '[0-9]+',
])->defaults([
	'page' => 1,
]);

/**
 * Ajout d'une banque
 */
Route::add('banks.add', 'banks/add', 'Banks\CreateOrEditController@index');

/**
 * Edition d'une banque
 */
Route::add('banks.edit', 'banks/edit/{bankId}', 'Banks\CreateOrEditController@index')->where([
	'bankId' => '[0-9]+',
]);

/*********************************************************************************/

/* AUTHENTIFICATION */

/**
 * Page de connexion
 */
Route::add('login', 'login', 'Auth\LoginController@index');

/**
 * Page de déconnexion
 */
Route::add('logout', 'logout', 'Auth\LogoutController@index');

/*********************************************************************************/

/**
 * Page d'accueil
 */
Route::add('home', '', 'HomeController@index');

/**
 * Page d'erreur
 */
Route::add('error', 'error', 'ErrorController@index');

/**
 * Route de test
 */
Route::add('testing', 'testing', 'TestingController@index');

/*********************************************************************************/
