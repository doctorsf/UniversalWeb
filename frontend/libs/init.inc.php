<?php
//--------------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Date : 27.11.2018
//--------------------------------------------------------------------------
// init.inc.php
//--------------------------------------------------------------------------

//Définit le décalage horaire par défaut de toutes les fonctions date/heure
date_default_timezone_set('Europe/Paris');

//comptabilisation du nombre de requetes SQL : remise à zéro
$_NB_QUERY = 0;

//----------------------------------------------------------------------
// Chargement des paramètres de l'application
//----------------------------------------------------------------------
Params::load();

//----------------------------------------------------------------------
// Mode d'execution des requetes SQL en fonction du _RUN_MODE_
// SQL_MODE_SILENT => aucune info affichée (prod)
// SQL_MODE_NORMAL => infos ligne, code erreur SQL
// SQL_MODE_DEBUG  => infos complete + rappel requete
//----------------------------------------------------------------------
if (_RUN_MODE_ == _DEVELOPPEMENT_) {
	//execution des requetes SQL en mode debug
	defined('_SQL_MODE_') || define('_SQL_MODE_', SQL_MODE_DEBUG);
	//rapporte et affiche toutes les erreurs PHP
	ini_set('display_startup_errors', 'On');
	ini_set('display_errors', 'stdout');
	error_reporting(-1);
}
else {
	//execution des requete en mode silencieux
	defined('_SQL_MODE_') || define('_SQL_MODE_', SQL_MODE_SILENT);
	//Désactiver le rapport d'erreurs et n'affiche aucune erreur
	ini_set('display_startup_errors', 'Off');
	ini_set('display_errors', 'stderr');
	error_reporting(0); 
	//Spécifie la fonction utilisateur "userErrorHandler" comme gestionnaire d'erreurs
	$old_error_handler = set_error_handler("userErrorHandler");
}

//----------------------------------------------------------------------
// Creation de l'objet 'droits' qui gere les droits d'accès à l'application
// stockés dans la base de données
//----------------------------------------------------------------------
//$_SESSION[_APP_DROITS_] = null;
if  ((!isset($_SESSION[_APP_DROITS_])) || ($_SESSION[_APP_DROITS_] == null)) {
	$_SESSION[_APP_DROITS_] = new Droits();
}

//----------------------------------------------------------------------
// Creation de l'objet _APP_LOGIN_ qui contient les infos de l'utilisateur loggué
// et prise en compte de la langue en cours de l'utilisateur
//----------------------------------------------------------------------
//$_SESSION[_APP_LOGIN_] = null;
if  ((!isset($_SESSION[_APP_LOGIN_])) || ($_SESSION[_APP_LOGIN_] == null)) {
	if (_ANNUAIRE_ == _ANNUAIRE_INTERNE_) $_SESSION[_APP_LOGIN_] = new Login();
	//incrire d'autres éventuels annuaires ici
	//...
	$_SESSION[_APP_LANGUE_ENCOURS_] = $_SESSION[_APP_LOGIN_]->getLanguePref();
}

//----------------------------------------------------------------------
// Choix de la langue
//----------------------------------------------------------------------
//langue par défaut 'fr'
if (empty($_SESSION[_APP_LANGUE_ENCOURS_])) {
	$_SESSION[_APP_LANGUE_ENCOURS_] = 'fr';
}
if ((isset($_SESSION[_APP_LANGUE_CHANGEE_])) && ($_SESSION[_APP_LANGUE_CHANGEE_] != $_SESSION[_APP_LANGUE_ENCOURS_])) {
	//modification de la langue choisie par l'utilisateur
	$_SESSION[_APP_LOGIN_]->changeLangue($_SESSION[_APP_LANGUE_CHANGEE_]);
	//nouvelle langue en cours prise en compte
	$_SESSION[_APP_LANGUE_ENCOURS_] = $_SESSION[_APP_LANGUE_CHANGEE_];
	//reset de l'info de changement de langue
	unset($_SESSION[_APP_LANGUE_CHANGEE_]);
}
//chargement de la langue adéquate
if ($_SESSION[_APP_LANGUE_ENCOURS_] == 'fr') {
	require_once(_LANGUES_.'langue_fr.inc.php');
}
elseif ($_SESSION[_APP_LANGUE_ENCOURS_] == 'en') {
	require_once(_LANGUES_.'langue_us.inc.php');
}

//----------------------------------------------------------------------
// Positionnement de l'image de remplacement des images inexistentes (utilisé par la fonction getThumb())
//----------------------------------------------------------------------
setThumbReplacement(_IMAGES_LANGUE_.'small_affiche_nondisponible.jpg');

//prise en compte du script en cours
$scriptName = explode('/', $_SERVER['SCRIPT_NAME']);
$scriptName = end($scriptName);
//DEBUG_('scriptName', $scriptName);

//DEBUG_('_APP_DROITS_', $_SESSION[_APP_DROITS_]);
//DEBUG_(_APP_LOGIN_, $_SESSION[_APP_LOGIN_]);