<?php
//-----------------------------------------------------------
// REPONSES-AJAX
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
// regroupe toutes les fonctions utilisées par des apels ajax
// 25.02.2013 : 
//		Première version
// 27.05.2019 : 
//		Ajout des fonctions de gestion des droits via javascript 
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

	//----------------------------------------------------------------------------------
	// Utilisé par la page de gestion des droits.
	// Positionne (en le swappant) un droit pour un fonctionnalité et un profil particulier
	// Entrée : 
	//		id : chaine de caratère construitre de la sorte [id_fonctionhnalite]_[id_profil]
	// Retour : 
	//		-1 en cas d'erreur
	//		-2 en cas de tenataitve de modificiation d'un droit interdit (droit administratif)
	//		html de l'icone de remplacement en cas de succès (check ou ban)
	//----------------------------------------------------------------------------------
	case 'uw_SetDroit' : 
	{
		$retour = '-1';
		if (isset($_GET['id'])) {
			$id_droit = mySQLDataProtect($_GET['id']);
			$parts = explode('_', $id_droit);
			if (count($parts) == 2) {
				//ok, on récupère bien 2 infos (numFonctionalite, profil d'acces)
				$id_fonctionnalite = $parts[0];
				$id_profil = $parts[1];
				//vérification si on essaye pas de s'auto-oter les droits d'administration
				if (!(($id_fonctionnalite == '1') && ($id_profil == '1'))) {
					//swap du droit
					$res = sqlDroits_swapAutorisationProfil($id_fonctionnalite, $id_profil);
					//suppression et recréation de l'objet Droit
					$droitsNotionGroupes = $_SESSION[_APP_DROITS_]->getNotionGroupes();
					$_SESSION[_APP_DROITS_] = null;
					$_SESSION[_APP_DROITS_] = new Droits($droitsNotionGroupes);
					if ($_SESSION[_APP_DROITS_]->accesAutoriseByIdFonc($id_fonctionnalite, $id_profil)) {
						$retour = '<span class="text-success fas fa-check"></span>';
					}
					else {
						$retour = '<span class="text-danger fas fa-ban"></span>';
					}
				}
				else {
					//modification interdite pour ce droit
					$retour = '-2';
				}
			}
		}
		break;
	}

	//----------------------------------------------------------------------------------
	// Utilisé par la page de gestion des droits.
	// Positionne (déplace) une fonctionnalité dans un groupe de fonctionnalités. 
	// Entrée : 
	//		fonc : numéro de la fonctionnalité source
	//		groupe : groupe de fonctionnalités cible
	//----------------------------------------------------------------------------------
	case 'uw_MoveFoncToGroupe' : 
	{
		if ((isset($_GET['fonc'])) && (isset($_GET['groupe']))) {
			$id_fonctionnalite = mySQLDataProtect($_GET['fonc']);
			$id_groupe = mySQLDataProtect($_GET['groupe']);
			//migration de la fonctionnalité $id_fonctionnalite dans le groupe $id_groupe
			$res = sqlDroits_setFonctionnaliteToGroupe($id_fonctionnalite, $id_groupe);
			//destruction de la variable de session qui contient les droits, elle sera reconstuite au rechargement de la page
			$_SESSION[_APP_DROITS_] = null;
			//modification du groupe de fonctionnalités à afficher déployer (groupe cible)
			$_SESSION[_APP_DROITS_GROUPE_DEPLOYE_] = $id_groupe;
		}
		//on ne renvoie rien ici car la fonction n'appelle pas de fonction de callback
		$retour = '';
		break;
	}

	//----------------------------------------------------------------------------------
	// Utilisé par la page de gestion des droits.
	// Réarrangement (tri) de groupes. Déplace le groupe de fonctionnalités source après 
	// le 'groupe' de fonctionnalité 'after'
	// Entrée : 
	//		fonc : numéro de la fonctionnalité source
	//		groupe : groupe de fonctionnalités cible
	//----------------------------------------------------------------------------------
	case 'uw_MoveGroupe' : 
	{
		if ((isset($_GET['source'])) && (isset($_GET['after']))) {
			$id_groupe_source = mySQLDataProtect($_GET['source']);
			$id_groupe_after = mySQLDataProtect($_GET['after']);
			$dummy = sqlDroits_rearrangeGroupes($id_groupe_source, $id_groupe_after);
			//destruction de la variable de session qui contient les droits, elle sera reconstuite au rechargement de la page
			$_SESSION[_APP_DROITS_] = null;
		}
		//on ne renvoie rien ici car la fonction n'appelle pas de fonction de callback
		$retour = '';
		break;
	}

	//----------------------------------------------------------------------------------
	// Utilisé par la page de gestion des droits.
	// Positionne la variable de session _APP_DROITS_GROUPE_DEPLOYE_ avec l'id du groupe
	// de fonctionnalités déployé. Cette variable est systématiquement lue par la page de droits pour
	// redéployer le dernier groupe déployé.
	// Entrée : 
	//		fonc : numéro de la fonctionnalité source
	//		groupe : groupe de fonctionnalités cible
	//----------------------------------------------------------------------------------
	case 'uw_SetGroupeDroitsDeploye' : 
	{
		if (isset($_GET['id'])) {
			$id_groupe = mySqlDataProtect($_GET['id']);
			//modification du groupe de fonctionnalités à afficher déployer
			$_SESSION[_APP_DROITS_GROUPE_DEPLOYE_] = $id_groupe;
		}
		//on ne renvoie rien ici car la fonction n'appelle pas de fonction de callback
		$retour = '';
		break;
	}

	//----------------------------------------------------------------------------------
	// Utilisé par la liste des paramètres de l'application.
	// Les paramètres sont affichés selon la valeur "ordre" de la table "params". 
	// L'ordre d'affichage des paramètres est modifiable par drag & drop des lignes <tr> du tableau des paramètres. 
	// Chaque drag & Drop fait appel à la fonction Ajax ci-dessous qui se charge de réordonner l'affichage en intervertissant
	// source et cible
	// Entrée : 
	//		source : paramètre ayant pour ordonnancement la valeur "source"
	//		cible  : paramètre ayant pour ordonnancement la valeur "cible"
	//----------------------------------------------------------------------------------
	case 'uw_paramSort' : 
	{
		if ((isset($_GET['source'])) && (isset($_GET['cible']))) {
			$source = mySQLDataProtect($_GET['source']);
			$cible = mySQLDataProtect($_GET['cible']);
			//modifie la base de données en conséquence
			$dummy = SqlParams::paramSort($source, $cible);
		}
		//on ne renvoie rien ici car la fonction n'appelle pas de fonction de callback
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