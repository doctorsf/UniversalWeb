<?php
/*------------------------------------------------------------------*/
/* DECONNEXION														*/
/*------------------------------------------------------------------*/
/* éè : pour enregistrement UTF-8									*/
/*------------------------------------------------------------------*/
//session_start();
require_once('libs/common.inc.php');
//on detruit l'objet login
unset($_SESSION[_APP_LOGIN_]);
//on detruit l'objet droits
unset($_SESSION[_APP_DROITS_]);
//on vide les variables de session
session_destroy();
//rechargement page d'accueil
header('Location: '._URL_BASE_SITE_);