<?php
//--------------------------------------------------------------------------
// Droits de l'application
//-------------------------------------------------------------------------
// 26.11.2018 : Création du fichier (extrait du fichier fonctions.inc.php)
//--------------------------------------------------------------------------

//--------------------------------------------------------------
// Cette fonction gere l'accès aux scripts selons les droits accordés.
// Si le droit n'est pas accordé, le script est détourné en message d'erreur avec
// explication du droit dénié ou bien de l'operation erronée
// L'opération est lue sur la ligne de commande GET et se nomme systématiquement 'operation'
// Entree
//		Rien
// Retour
//		l'opération demandée sur le script si autorisé / false sinon
//--------------------------------------------------------------
function grantAcces()
{
	(isset($_GET['operation']))	? $operation = mySqlDataProtect($_GET['operation'])	: $operation = 'all';

	//on recupere le nom du script appelant
	switch (basename($_SERVER['SCRIPT_NAME'])) {

		//-------------------------------------------------
		case 'squelette.php' : {
			$droitATester	= 'FONC_ADM_APP';
			break;
		}

		//-------------------------------------------------
		case _URL_INDEX_ : {
			$droitATester	= 'FONC_ADM_APP';
			break;
		}

		//-------------------------------------------------
		default: {	
			$droitATester	= 'erreur';
			break;
		}
	}

	//erreur de commande
	if ($droitATester == 'erreur') {
		$leMessage = getLib('ERREUR_COMMANDE');
		include_once(_BRIQUE_ERREUR_);
		return false;
	}
	
	//test de l'acces
	if (!accesAutorise($droitATester)) {
		$leMessage = getLib('DROITS_INSUFFISANTS');
		$leMessage2 = '"'.$_SESSION[_APP_DROITS_]->getLibelleFonctionnalite($droitATester).'"';
		include_once(_BRIQUE_ERREUR_);
		return false;
	}
	
	return $operation;

}