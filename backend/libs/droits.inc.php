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
			return $operation;		//tout le monde est autorisé
			break;
		}

		//-------------------------------------------------
		case _URL_ACTIONS_DROITS_ : {
			if (_RUN_MODE_ == _DEVELOPPEMENT_) {
				$droitATester	= 'FONC_ADM_APP';
				$opAutorises	= array('droits', 'delniv', 'addniv', 'addfonc', 'delfonc', 'renniv', 'valid_renniv', 'rennivcode', 'valid_rennivcode', 
										'rennivid', 'valid_rennivid', 'renfonc', 'valid_renfonc', 'renfonccode', 'valid_renfonccode', 'renfoncid', 'valid_renfoncid', 
										'rengrp', 'valid_rengrp', 'addgrp', 'delgrp');
				if (in_array($operation, $opAutorises)) break;
				$droitATester	= 'dev';
				break;
			}

			$droitATester	= 'FONC_ADM_APP';
			$opAutorises	= array('droits');
			if (in_array($operation, $opAutorises)) break;

			$droitATester	= 'erreur';
			break;
		}

		//-------------------------------------------------
		case _URL_ACTIONS_DIVERS_ : {
			$droitATester	= 'FONC_ADM_APP';
			$opAutorises	= array('delmedia');
			if (in_array($operation, $opAutorises)) break;

			$droitATester	= 'erreur';
			break;
		}

		//-------------------------------------------------
		case _URL_MAINTENANCE_ : {
			$droitATester	= 'FONC_ADM_APP';
			$opAutorises	= array('purgelog', 'epurelog', 'savedb', 'loaddb', 'gorestoredb', 'deldb', 'hash', 'hashfrontend', 'reseterrors', 'reseterrorsfrontend', 'dbsign');
			if (in_array($operation, $opAutorises)) break;

			$droitATester	= 'erreur';
			break;
		}

		//-------------------------------------------------
		case _URL_INFOS_SYSTEME_ :
		case _URL_LISTING_USERS_ :
		case _URL_LISTING_DROITS_ : 
		case _URL_VERSIONNING_ : 
		case _URL_LISTING_LOGS_	: 
		case _URL_LISTING_PARAMS_ : 
		case _URL_PARAM_: 
		case _URL_MEDIA_ : 
		case _URL_REGLAGES_ : {
			$droitATester	= 'FONC_ADM_APP';
			break;
		}

		//-------------------------------------------------
		case _URL_USER_ : {
			if ((isset($_GET['id'])) && ($_SESSION[_APP_LOGIN_]->getId() == mySqlDataProtect($_GET['id']))) {
				//on cherche à acceder à son propre compte -> c'est autorisé
				$opAutorises = array('consulter', 'modifier');
				if (in_array($operation, $opAutorises)) return $operation;
			}

			$droitATester	= 'FONC_ADM_APP';
			$opAutorises	= array('consulter', 'ajouter', 'modifier', 'supprimer');
			if (in_array($operation, $opAutorises)) break;

			$droitATester	= 'erreur';
			break;
		}

		//-------------------------------------------------
		case _URL_PARAM_ : {
			$droitATester	= 'FONC_ADM_APP';
			$opAutorises	= array('consulter', 'ajouter', 'modifier', 'supprimer');
			if (in_array($operation, $opAutorises)) break;

			$droitATester	= 'erreur';
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
		$leMessage = getLib('ERREUR_DROITS');
		include_once(_BRIQUE_ERREUR_);
		return false;
	}

	//droits à développer actuellement inexistant
	if ($droitATester == 'dev') {
		$leMessage = 'Accès interdit';
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