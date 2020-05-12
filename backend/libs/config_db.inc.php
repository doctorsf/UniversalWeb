<?php
//----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
//----------------------------------------------------------------------
// Configuration de la base de données
// éè : UTF-8
//----------------------------------------------------------------------

$dbServer = '';					//ne pas utiliser localhost qui ralentit Xampp
$dbLogin = '';					//login
$dbPassword = '';				//mot de passe
$dbDatabase = '';				//nom de la base de données
$dbMysql = '';					//chemin complet vers mysql.exe
$dbMysqldump = '';				//chemin complet vers mysqldump.exe

//LOCALHOST MAISON
$dbServer = '127.0.0.1';		//ne pas utilise localhost, ceci ralenti énormément les requetes et donc le site
$dbDatabase = 'universalweb';
$dbLogin = 'root';
$dbPassword = '';
$dbMysql = 'H:\\ServeursWeb\\xampp_5_6_11\\mysql\\bin\\mysql';
$dbMysqldump = 'H:\\ServeursWeb\\xampp_5_6_11\\mysql\\bin\\mysqldump';

//préfixe des tables
defined('_PREFIXE_TABLES_')	|| define('_PREFIXE_TABLES_', 'uw_');
defined('_PT_')	|| define('_PT_', 'uw_');