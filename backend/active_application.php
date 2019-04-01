<?php
/*-----------------------------------------------------------------------
Auteur : Fabrice Labrousse
Date : 12.01.2017
éè : UTF-8
-------------------------------------------------------------------------
CREATION DE LA BASE DE DONNEES DE L'APPLICATION
IMPORTANT : Ce script doit être obligatoirement supprimé après utilisation
-------------------------------------------------------------------------
16.04.2018
	- Création des tables de Logs et exemple "films"
23.07.2018
	- Remaniement pour UniverslWeb
27.11.2018
	-- Mise en conformité pour fonctionnemùent UniversalWeb 3.8.1.0
-------------------------------------------------------------------------*/
require_once('libs/defines.inc.php');
defined('_SQL_MODE_') || define('_SQL_MODE_', SQL_MODE_DEBUG);

//bibliothèques
require_once(_LIBS_.'db.connexion.pdo.php');		//module de gestion de la base de données
require_once(_LIBS_.'routines.inc.php');			//routines generale inter-applications
require_once(_CLASSES_.'SqlSimple.class.php');		//classe SQL simple

//requetes SQL
require_once(_SQL_.'sql_droits.inc.php');			//appels SQL orientés droits
require_once(_SQL_.'sql_users.inc.php');			//appels SQL orientés utilisateurs
require_once(_SQL_.'sql_logs.inc.php');				//appels SQL orientés Logs
require_once(_SQL_.'sql_divers.inc.php');			//appels SQL orientés Divers

//version PHP minimum requis : 5.6.0 (a cause des constantes de classes tableau MENU MENU_IGNORE) 
if (version_compare(PHP_VERSION, '5.6.0') < 0) {
	die('Votre version de PHP est insuffisante : V5.6.0 minimum requis');
}

//classes
require_once(_CLASSES_.'UniversalForm.class.php');	//chargement de la classe UniversalList
require_once(_CLASSES_.'UniversalList.class.php');	//chargement de la classe UniversalList

$_LDAP_LOGIN_ADMIN = 'admin';

$chaine = '<!doctype html>'."\n";
$chaine.= '<html lang="fr">';
$chaine.= '<head>'."\n";
$chaine.= '<meta charset="utf-8" />'."\n";
$chaine.= '<title>Premier démarrage</title>'."\n";
$chaine.= '</head>'."\n";
echo $chaine;

echo '<body>';

//----------------------------------------------------------------------
// Creation des tables de bases (droits)
//----------------------------------------------------------------------

//création de la table profils
//--------------------------------
$res = sqlDroits_createTableProfils();
if ($res) {
	echo 'Création table Profils -> OK<br />';
}
else die('Erreur lors de la création de la table Profils');

//création de la table fonctionnalités
//--------------------------------
$res = sqlDroits_createTableFonctionnalites();
if ($res) {
	echo 'Création table Fonctionnalites -> OK<br />';
}
else die('Erreur lors de la création de la table Fonctionnalites');

//création de la table droits
//--------------------------------
$res = sqlDroits_createTableDroits();
if ($res) {
	echo 'Création table Droits -> OK<br />';
}
else die('Erreur lors de la création de la table Droits');

//création de la table listings
//--------------------------------
$res = UniversalList::createTable();
if ($res) {
	echo 'Création table Listings -> OK<br />';
}
else die('Erreur lors de la création de la table Listings');

//création de la table des utilisateurs
//--------------------------------
$res = sqlUsers_createTableUsers();
if ($res) {
	echo 'Création table Users -> OK<br />';
}
else die('Erreur lors de la création de la table Users');

//création de la table des logs
//--------------------------------
$res = sqlLogs_createTableLogs();
if ($res) {
	echo 'Création table Logs -> OK<br />';
}
else die('Erreur lors de la création de la table Logs');

//création de la table des types de logs
//--------------------------------
$res = sqlLogs_createTableLogsTypes();
if ($res) {
	echo 'Création table Logs_types -> OK<br />';
}
else die('Erreur lors de la création de la table Logs Types');

//création de la table d'exemples "films"
//--------------------------------
$res = sqlDivers_createTableExemplesFilms();
if ($res) {
	echo 'Création table d\'exemple "films" -> OK<br />';
}
else die('Erreur lors de la création de la table d\'exemples "films"');

if (_ANNUAIRE_ == _ANNUAIRE_INTERNE_) {
	//creation d'un administrateur local
	$res = sqlUsers_createLocalAdmin();
	if ($res) {
		echo 'Création compte administrateur -> OK<br />';
	}
	else die('Erreur lors de la création du compte administrateur de la table Users');
}
else {
	//creation d'un administrateur local authentifié sur un annuaire externe
	$res = sqlUsers_createLdapAdmin($_LDAP_LOGIN_ADMIN);
	if ($res) {
		echo 'Création compte administrateur LDAP -> OK<br />';
	}
	else die('Erreur lors de la création du compte LDAP administrateur de la table Users');
}

echo '<br />Création des tables de base terminée<br />';
echo 'Vous pouvez maintenant suppprimer ce script...<br />';

echo '</body>';
echo '</html>';