<?php
//-----------------------------------------------------------
// REPONSES-AJAX
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
// regroupe toutes les fonctions utilisées par des apels ajax
// ATTENTION : le retour de ces fonctions est réalisé alors que les
// entêtetes HTTP ont déjà été envoyées. Donc il ne faut pas  refaire
// de session_start. Donc il ne faut pas faire appel à common.inc.php
// 25.02.2013 : Ben non, ça marche avec sessions_start !! comprends pas!
//-----------------------------------------------------------
require_once('libs/common.inc.php');

$retour = '';

//echo $_SERVER['REQUEST_URI'];

//on recupere le nom de la fonction ajax choisie
$fonctionChoisie = (isset($_GET['f'])) ? MySQLDataProtect($_GET['f']) : NULL;
//on traite la fonction selon de le choix
switch ($fonctionChoisie)
{

	//---------------------------------------------------
	// Récupération de la résolution graphique du client
	// Si l'info est présente (envoyée par javascript en GET) on 
	// stocke les infos dans les variables de session 'screen_width' et 'screen_height'
	//---------------------------------------------------
	case 'getScreenResolution' : 
	{
		if ((isset($_GET['w'])) && (isset($_GET['h']))) {
			$_SESSION['screen_width'] = mySQLDataProtect($_GET['w']);
			$_SESSION['screen_height'] = mySQLDataProtect($_GET['h']);
			//calcul de la résolution de la grille correspondante au sense Bootstrap 4
			//voir https://v4-alpha.getbootstrap.com/layout/grid/
			if		($_SESSION['screen_width'] < 576)											$_SESSION['user_viewport'] = 'xs';
			elseif	(($_SESSION['screen_width'] >= 576) && ($_SESSION['screen_width'] < 768))	$_SESSION['user_viewport'] = 'sm';
			elseif	(($_SESSION['screen_width'] >= 768) && ($_SESSION['screen_width'] < 992))	$_SESSION['user_viewport'] = 'md';
			elseif	(($_SESSION['screen_width'] >= 992) && ($_SESSION['screen_width'] < 1200))	$_SESSION['user_viewport'] = 'lg';
			elseif	($_SESSION['screen_width'] >= 1200)											$_SESSION['user_viewport'] = 'xl';
		}
		//on ne renvoie rien ic car la fonction n'appelle pas de fonction de callback (rien à faire d'autre que de positionner 
		//les variables de session ci-dessus
		$retour = '';
		break;
	}

	//----------------------------------
	// fonctions inexistante
	//----------------------------------
	default :
		$retour = '-1';			// renvoie -1 en texte brut
		break;
}
echo $retour;