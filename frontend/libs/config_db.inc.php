<?php
//----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
//----------------------------------------------------------------------
// Configuration de la base de données
// éè : UTF-8
//----------------------------------------------------------------------

$dbServer = '';					//ne pas utiliser localhost qui ralentit Xampp
$dbDatabase = '';				//nom de la base de données
$dbLogin = '';					//login
$dbPassword = '';				//mot de passe

//préfixe des tables
defined('_PREFIXE_TABLES_')	|| define('_PREFIXE_TABLES_', 'uw_');