<?php
//--------------------------------------------------------------------------
// Actions diverses à mener ne faisant pas appel à affichage
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
//--------------------------------------------------------------------------
require_once('libs/common.inc.php');

$operation = grantAcces() or die();

//------------------------------------------
// ACTION A MENER
//------------------------------------------
switch($operation)
{
	//==================================================
	// Supprimer un media
	// -> path : reçoit le code du chemin d'accès à l'image (par sécurité à la place du chemin en clair)
	// -> id : MD5 du nom du fichier à supprimer (histoire de ne pas passer n'importe quoi sur la ligne de commande)
	//==================================================
	case 'delmedia':
	{
		(isset($_GET['path'])) ? $path = MySQLDataProtect($_GET['path']) : $path = 'aucun';
		(isset($_GET['id'])) ? $md5file = MySQLDataProtect($_GET['id']) : $md5file = 'aucun';

		if ((in_array($path, array_keys(_PATHS_MEDIA_AUTORISES_))) && ($md5file != 'aucun')) {
			//lecture des fichiers du repertoire correspondant au code contenu dans $path
			$dummy = litRepertoire(_PATHS_MEDIA_AUTORISES_[$path], $lesFichiers);
			//recherche du MD5 du fichier qui correspond à celui passé dans $id
			foreach($lesFichiers as $fichier) {
				if (md5($fichier) == $md5file) {
					$fichierASupprimer = _PATHS_MEDIA_AUTORISES_[$path].$fichier;
					deleteFile($fichierASupprimer);
					break;
				}
			}
		}
		//retour
		goReferer();
		break;
	}

	//==================================================
	// COMMANDES NON RECONNUES
	//==================================================
	default:
	{	
		riseErrorMessage(getLib('ERREUR_COMMANDE'));
		goPageBack();
		break;
	}
}