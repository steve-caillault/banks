# Banks

Il s'agit d'un site permettant de gérer des comptes bancaires. Il permet de :
* gérer les opérations
* calculer les soldes
* consulter l'évolution
* comparer les soldes des dernières années
* définir un budget

## Installation

PHP 7.4 est requis pour faire fonctionner ce projet. Ce projet est compatible avec MySQL 8 ou MariaDB 10.1.

Après avoir récupéré le projet, exécuter la commande suivante à la racine :

````bash
php cli environment demo
````

Cela permet de déclarer l'environnement à exécuter.

## Initialisation des bases de données

Vous pouvez exécuter les requêtes du projet présent dans le fichier /database/initialize-demo.sql.
La base de données banks_demo sera créée.

## Configuration

Dans le fichier /config/environments/demo/database.php, adaptez la connexion à la base de données pour votre installation.

## Création d'un compte utilisateur

En ligne de commande, vous pouvez exécuter la commande du type :

````bash
php cli create-user firstName lastName password
````

*firstName* et *lastName* seront utilisés pour déterminer l'identifiant de l'utilisateur. Par exemple, si le prénom de l'utilisateur est George et le nom Washington, le nom d'utilsateur sera george-washington.

## Présentation

Après vous être identifié, commencer par ajouter une banque. Vous pouvez ensuite ajouter des propriétaires de comptes.
Seul l'utilisateur qui crée des propriétaires peut consulter leurs comptes. En cliquant sur le nom d'un propriéraire, vous pouvez le modifier.
Pour gérer la liste des comptes d'un propriétaire, vous pouvez cliquer sur le nombre de comptes en face du propriétaire.

Pour la gestion des comptes, utilisez des points pour les valeurs décimales ; par exemple  1.5 au lieu de 1,5.
Lorsque vous ajoutez une opération, vous n'avez pas besoin d'ajouter un signe négatif pour les débits. Les soldes seront calculés en fonction des types d'opérations que
vous renseignez. Pour mettre à jour le solde d'un compte, cliquez sur le lien *Calculer* en face du nom sur la liste des comptes. 

La page évolution d'un compte affiche l'évolution du solde sur les 5 dernières années, sur l'année et le mois en cours.
La page d'évolution des comptes (accessible depuis le menu) affiche la même chose, mais pour tous les comptes du propriétaire.
Sur la page de *comparaison des années*, vous avez la progression du solde des comptes mois par mois pour les dernières années.
La colonne de l'année affiche l'évolution par rapport au mois précédent ; la colonne *progression* affiche l'évolution sur l'année.

Enfin la page de gestion des budgets vous permet de définir un budget à suivre pour une année. Le principe est sensiblement le même que pour
l'ajout d'opération, mais lieu d'entrer une date, vous donnez une fréquence pour l'opération.