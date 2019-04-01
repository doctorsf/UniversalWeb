<?php
/*------------------------------------------------------------------*/
/* CHANGEMENT DE LANGUE												*/
/*------------------------------------------------------------------*/
/* éè : pour enregistrement UTF-8									*/
/*------------------------------------------------------------------*/
require_once('libs/common.inc.php');

if (isset($_GET['langue'])) {
	$_SESSION[_APP_LANGUE_CHANGEE_] = MySQLDataProtect($_GET['langue']);
}
//raffraichissement de la page
goReferer();