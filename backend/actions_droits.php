<?php
//--------------------------------------------------------------------------
// Actions à mener sur les droits ne faisant pas appel à affichage
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
// version : 01.06.2018
// 27.11.2018
//		- Standardisation noms de fichier de sauvegarde
// 28.05.2019
//		- Ajout de actions sur les groupes de fonctionnalités
//		- Ajout de controles sur la saisie
//--------------------------------------------------------------------------
require_once('libs/common.inc.php');

$operation = grantAcces() or die();

//------------------------------------------
// ACTION A MENER
//------------------------------------------
switch($operation)
{
	//------------------------------------------
	// Exporter les droits sous forme de fichier .sql
	//------------------------------------------
	case 'export':
	{
		$listeDesTables = array(_PREFIXE_TABLES_.'profils', _PREFIXE_TABLES_.'fonctionnalites', _PREFIXE_TABLES_.'droits');
		$res = saveDatabase('droits', _APP_VERSION_, _ARMOIRE_, $listeDesTables, _SAVE_DB_NODATE_);
		if ($res) {
			riseMessage(getLib('EXPORT_DROITS_OK')); 
		}
		else {
			riseErrorMessage(getLib('EXPORT_DROITS_KO')); 
		}
		goReferer();
		break;
	}

	//------------------------------------------
	// Importer les droits sous forme de fichier .sql
	//------------------------------------------
	case 'import':
	{
		$fichier = 'droits_'._APP_VERSION_.'_no_date';
		$res = restoreDatabase(_ARMOIRE_, $fichier);
		if ($res) {
			riseMessage(getLib('IMPORT_DROITS_OK')); 
			header('Location: '._URL_LOGOUT_);
			die();
		}
		else {
			riseErrorMessage(getLib('IMPORT_DROITS_KO', $fichier));
		}
		goReferer();
		break;
	}

	//------------------------------------------
	// Modification des droits d'un couple fonctionnalite/profil
	// Cette fonction n'est plus utilisée depuis la gestion des droits via javascript 
	//------------------------------------------
	case 'droits':
	{
		(isset($_GET['do'])) ? $do = MySQLDataProtect($_GET['do']) : $do = 'aucun';
		if ($do != 'aucun') {
			$codes = explode('_', $do);
			if (count($codes) == 3) {
				//vérification si on essaye pas de s'auto-oter les droits d'administration
				if (!(($codes[0] == '1') && ($codes[1] == '1'))) {
					//ok, on récupère bien 3 infos (numFonctionalite, profil d'acces, valeur à déposer)
					$res = sqlDroits_updateAutorisationProfil($codes[0], $codes[1], $codes[2]);
					//retrouver le code de la fonctionnalité "$codes[0]"
					$codeFonctionnalite = $_SESSION[_APP_DROITS_]->retreiveCodeFonctionnaliteFromId($codes[0]);
					//appel de la fonction de callback si elle existe correspondant à cette fonctionnalité
					if (function_exists('callback_fonctionnalite_'.$codeFonctionnalite)) {
						call_user_func('callback_fonctionnalite_'.$codeFonctionnalite);
					}
					//suppression de l'objet Droit (il sera recréé au chargement de la page)
					$_SESSION[_APP_DROITS_] = null;
				}
				else {
					riseWarningMessage(getLib('MODIFICATION_DROIT_INTERDITE'));
				}
			}
		}
		//retour
		goPageBack();
		break;
	}

	//------------------------------------------
	// Ajout d'un profil avec les options par défaut
	// Voir sqlDroits_addProfil()
	//------------------------------------------
	case 'addniv':
	{
		$res = sqlDroits_addProfil();
		//on force le déploiement du groupe "Non classée"
		$_SESSION[_APP_DROITS_GROUPE_DEPLOYE_] = 1;
		//suppression de l'objet Droit (il sera recréé au chargement de la page)
		$_SESSION[_APP_DROITS_] = null;
		//retour
		goPageBack();
		break;
	}

	//------------------------------------------
	// Suppression d'un profil
	//------------------------------------------
	case 'delniv':
	{
		(isset($_GET['do'])) ? $do = MySQLDataProtect($_GET['do']) : $do = 'aucun';
		if (($do != 'aucun') && ($do != 1)) {
			//ok, on supprime si ce n'est pas le profil administration
			$res = sqlDroits_deleteProfil($do);
			//suppression de l'objet Droit (il sera recréé au chargement de la page)
			$_SESSION[_APP_DROITS_] = null;
		}
		//retour au listing des droits
		goPageBack();
		break;
	}

	//------------------------------------------
	// Ajout d'une fonctionalite avec les options par défaut
	// Voir sqlDroits_addFonctionnalite()
	//------------------------------------------
	case 'addfonc':
	{
		$res = sqlDroits_addFonctionnalite();
		//on force le déploiement du groupe "Non classée"
		$_SESSION[_APP_DROITS_GROUPE_DEPLOYE_] = 1;
		//suppression de l'objet Droit (il sera recréé au chargement de la page)
		$_SESSION[_APP_DROITS_] = null;
		//retour
		goPageBack();
		break;
	}

	//------------------------------------------
	// Supprimer une fonctionalite
	//------------------------------------------
	case 'delfonc':
	{
		(isset($_GET['do'])) ? $do = MySQLDataProtect($_GET['do']) : $do = 'aucun';
		if (($do != 'aucun') && ($do != 1)) {
			//ok, on supprime si ce n'est pas la fonctionnalité administration
			$res = sqlDroits_deleteFonctionnalite($do);
			//suppression de l'objet Droit (il sera recréé au chargement de la page)
			$_SESSION[_APP_DROITS_] = null;
		}
		//retour
		goPageBack();
		break;
	}

	//------------------------------------------
	// Ajout d'un groupe de fonctionnalités
	// Voir sqlDroits_addGroupeFonctionnalites()
	//------------------------------------------
	case 'addgrp':
	{
		$res = sqlDroits_addGroupeFonctionnalites();
		//suppression de l'objet Droit (il sera recréé au chargement de la page)
		$_SESSION[_APP_DROITS_] = null;
		//retour
		goPageBack();
		break;
	}

	//------------------------------------------
	// Suppression d'un groupe de fonctionnalités
	// On peut supprimer seulement les groupes vides 
	// On ne peut pas supprimer les groupes "Non classée" et "Administration"
	//------------------------------------------
	case 'delgrp':
	{
		(isset($_GET['do'])) ? $do = MySQLDataProtect($_GET['do']) : $do = 'aucun';
		if ($do != 'aucun') {
			if ($do > 2) {
				//ok, on supprime si ce n'est pas un des groupes "Non classée" et "Administration"
				//il n'est pas nécessaire de recrééer les droits puisque les droits se moquent des groupes
				if (SqlSimple::existValeur(_PREFIXE_TABLES_.'fonctionnalites', 'id_groupe_fonctionnalite', $do) == 0) {
					$res = sqlDroits_deleteGroupeFonctionnalites($do);
				}
				else {
					riseWarningMessage(getLib('GROUPE_SUPPR_IMPOSSIBLE'));
				}
			}
			else {
				riseWarningMessage(getLib('GROUPE_SUPPR_INTERDIT'));
			}
		}
		//retour au listing des droits
		goPageBack();
		break;
	}

	//------------------------------------------
	// Demande de renommage d'un profil, fonctionnalité ou groupe
	// renniv		: renomme le libellé du profil
	// rennivcode	: renomme le code PHP du profil
	// rennivid		: renomme l'id du profil
	// renfonc		: renomme le libellé d'une fonctionnalité
	// renfonccode	: renomme le code PHP d'une fonctionnalité
	// renfoncid	: renomme l'id d'une fonctionnalité
	// rengrp		: renomme le libellé d'un groupe de fonctionnalité
	//------------------------------------------
	case 'renniv':
	case 'rennivcode':
	case 'rennivid':
	case 'renfonc':
	case 'renfonccode':
	case 'renfoncid':
	case 'rengrp':
	{
		//on récupere l'id du profil ou de la fonctionnalite dont on veut modifier un champ
		(isset($_GET['do'])) ? $do = MySQLDataProtect($_GET['do']) : $do = 0;
		//on interdit toute modification de code 1 (administration)
		if ($do != 1) {
			//création de l'adresse de callback, c'est à dire la page qui sera appelée après validation de la saisie
			unset($_SESSION[_APP_INPUT_]);
			$_SESSION[_APP_INPUT_]['form_title'] = 'Nouvelle saisie';
			$_SESSION[_APP_INPUT_]['callback'] = _URL_ACTIONS_DROITS_.'?operation=valid_'.$operation;
			$_SESSION[_APP_INPUT_]['champs']['saisie'] = array('type' => 'text', 
															'label' => 'Saisie', 
															'testMatches' => array('REQUIRED'),
															'value' => '');
			if (($operation == 'rennivid') || ($operation == 'renfoncid')) {
				$_SESSION[_APP_INPUT_]['champs']['saisie']['testMatches'][] = 'CHECK_UNSIGNED_INTEGER';
				$_SESSION[_APP_INPUT_]['champs']['saisie']['testMatches'][] = 'NOT_ZERO';
			}
			if (($operation == 'rennivcode') || ($operation == 'renfonccode')) {
				$_SESSION[_APP_INPUT_]['champs']['saisie']['testMatches'][] = 'CHECK_ALPHA_CODE';
				$_SESSION[_APP_INPUT_]['champs']['saisie']['testMatches'][] = 'UPPERCASE';
			}
			//DEBUG_('_APP_INPUT_', $_SESSION[_APP_INPUT_]['champs']['saisie']);
			//on passe l'id du tuple à modifier en champ caché dans le formulaire de saisie pour pouvoir le récupérer à la validation
			$_SESSION[_APP_INPUT_]['champs']['id'] = array('type' => 'hidden', 'value' => $do);
			//on lance le formulaire de saisie
			header('Location: input.php');
		}
		else {
			//retour à la page d'appel
			riseWarningMessage(getLib('MODIF_ADMIN_INTERDITE'));
			goPageBack();
		}
		break;
	}

	//------------------------------------------
	// Validation du renommage / modification de profil, de 
	// fonctionnalité ou de groupe
	//------------------------------------------
	case 'valid_renniv':
	case 'valid_rennivcode':
	case 'valid_rennivid':
	case 'valid_renfonc':
	case 'valid_renfonccode':
	case 'valid_renfoncid':
	case 'valid_rengrp':
	{
		//le retour de la saisie se trouve dans le tableau $_SESSION[_APP_INPUT_]['values']
		$saisie = $_SESSION[_APP_INPUT_]['values']['saisie'];		//valeur saisie
		$id = $_SESSION[_APP_INPUT_]['values']['id'];				//id du tuple à modifier

		//renommage / modification effective
		//libelle profil
		if ($operation == 'valid_renniv') {
			$res = sqlDroits_renameProfil($id, $saisie);
			if (!$res) {
				riseErrorMessage(getLib('ERREUR_REN_LIB_PROFIL'));
			}
		}
		//code profil
		else if ($operation == 'valid_rennivcode') {
			if (SqlSimple::existValeur(_PREFIXE_TABLES_.'profils', 'code', $saisie) == 0) {
				$res = sqlDroits_renameCodeProfil($id, $saisie);
				if (!$res) {
					riseErrorMessage(getLib('ERREUR_REN_CODE_PROFIL'));
				}
			}
			else {
				riseWarningMessage(getLib('PROFIL_REN_CODE_EXISTE_DEJA'));
			}
		}	
		//id profil
		else if ($operation == 'valid_rennivid') {
			if (SqlSimple::existValeur(_PREFIXE_TABLES_.'profils', 'id_profil', $saisie) == 0) {
				$res = sqlDroits_renameIdProfil($id, $saisie);
				if (!$res) {
					riseErrorMessage(getLib('ERREUR_REN_ID_PROFIL'));
				}
			}
			else {
				riseWarningMessage(getLib('PROFIL_REN_ID_EXISTE_DEJA'));
			}
		}
		//libelle fonctionnalite
		else if ($operation == 'valid_renfonc') {
			$res = sqlDroits_renameFonctionnalite($id, $saisie);
			if (!$res) {
				riseErrorMessage(getLib('ERREUR_REN_LIB_FONC'));
			}
		}
		//code fonctionnalite
		else if ($operation == 'valid_renfonccode') {
			if (SqlSimple::existValeur(_PREFIXE_TABLES_.'fonctionnalites', 'code', $saisie) == 0) {
				$res = sqlDroits_renameCodeFonctionnalite($id, $saisie);
				if (!$res) {
					riseErrorMessage(getLib('ERREUR_REN_CODE_FONC'));
				}
			}
			else {
				riseWarningMessage(getLib('FONC_REN_CODE_EXISTE_DEJA'));
			}
		}
		//id fonctionnalite
		else if ($operation == 'valid_renfoncid') {
			if (SqlSimple::existValeur(_PREFIXE_TABLES_.'fonctionnalites', 'id_fonctionnalite', $saisie) == 0) {
				$res = sqlDroits_renameIdFonctionnalite($id, $saisie);
				if (!$res) {
					riseErrorMessage(getLib('ERREUR_REN_ID_FONC'));
				}
			}
			else {
				riseWarningMessage(getLib('FONC_REN_ID_EXISTE_DEJA'));
			}
		}
		//libellé groupe de fonctionnalité
		else if ($operation == 'valid_rengrp') {
			$res = sqlDroits_renameGroupeFonctionnalites($id, $saisie);
			if (!$res) {
				riseErrorMessage(getLib('GROUPE_REN_ERREUR'));
			}
		}

		//suppression de l'objet Droit (il sera recréé au chargement de la page)
		$_SESSION[_APP_DROITS_] = null;
		//retour à la page d'appel
		goPageBack();
		break;
	}

	//------------------------------------------
	// COMMANDES NON RECONNUES
	//------------------------------------------
	default:
	{	
		riseErrorMessage(getLib('ERREUR_COMMANDE'));
		goPageBack();
		break;
	}
}