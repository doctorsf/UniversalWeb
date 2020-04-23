<?php
//----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Date : 12.01.2017
// 26.11.2018 : ajout require fichiers droits.inc.php
//----------------------------------------------------------------------
// COMMON
// éè : UTF-8
//----------------------------------------------------------------------
//chargement des définitions
require_once('libs/defines.inc.php');

//Auto-chargement des classes (obligatoire avant le session_start() pour pouvoir passer des objets dans des variables de session)
function chargerClasses($classe) {
	require_once(_CLASSES_.$classe.'.class.php');
}
spl_autoload_register('chargerClasses');

//session
session_name(_APP_BLOWFISH_);						//nommage de la session pour qu'elle soit propre à l'application
session_start();									//demarrage session
session_regenerate_id(false);						//regenère un ID pour sécuriser l'application

//compression des pages
ob_start('ob_gzhandler');

//librairies généralistes
require_once(_LIBS_.'db.connexion.pdo.php');		//module de gestion de la base de données MySql
//require_once(_LIBS_.'db.connexion.pdo.oracle.php');	//module de gestion de la base de données Oracle
require_once(_LIBS_.'routines.inc.php');			//routines generale inter-applications

//requetes SQL
require_once(_SQL_.'sql_droits.inc.php');			//appels SQL orientés droits
require_once(_SQL_.'sql_users.inc.php');			//appels SQL orientés users
require_once(_SQL_.'sql_logs.inc.php');				//appels SQL logs
require_once(_SQL_.'sql_divers.inc.php');			//appels SQL divers
require_once(_SQL_.'sql_params.inc.php');			//appels SQL parametres

//code propre à l'application
require_once(_LIBS_.'droits.inc.php');				//droits propres à l'application
require_once(_LIBS_.'fonctions.inc.php');			//fonctions propres à l'application
require_once(_LIBS_.'init.inc.php');				//initialisations