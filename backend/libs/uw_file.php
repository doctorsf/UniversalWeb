<?php
//--------------------------------------------------------------------------
// routines_file.php
// Ensemble de routines orientées gestion de fichiers
//--------------------------------------------------------------------------
// 24.11.2015
//		- ajout de la fonction etatDuRepertoire($repertoire) qui renseigne sur l'état d'un répertoire
// 04.02.2016
//		- Ajout de la fonction delTree($dir) qui supprime récursivement un dossier et tous les fichiers et dossiers qu'il contient
// 03.05.2018
//		- Simplification de la fonction etatDuRepertoire()
//		- Création de la fonction litRepertoire()
// 14.06.2019
//		- ajout fonction downloadUrl() qui télécharge une url distante (hors site) vers un fichier cible
//--------------------------------------------------------------------------

//----------------------------------------------------------------------
// Lecture d'une ligne dans une fichier texte
// Encodage sortie en UTF-8 car les fichiers texte sont en ascii
// Windows-1252
//----------------------------------------------------------------------
function lectureLigne($fdata)
{
	$ligne = fgets($fdata);
	$ligne = stripslashes($ligne);
    // on enlève les caractères vide de fin de ligne qui foutent la merde
	$ligne = RTrim($ligne);
	$ligne = iconv("Windows-1252", "UTF-8//TRANSLIT", $ligne);
	return $ligne;
}

//--------------------------------------------------------------------------
// Suppression d'un fichier
//--------------------------------------------------------------------------
function deleteFile($fichier)
{
	if(file_exists($fichier)) unlink($fichier);
}

//----------------------------------------------------------------------
// creation de répertoire de manière récursive
// Entree : chemin à créer : ex : /images/produits/o/thumbs
//		  : le mode d'écriture : ex '0644' / '0777' (en octal)
// RETOUR : renvoie TRUE si existe ou créé, FALSE si echec
//----------------------------------------------------------------------
function mkdir_recursive($pathname, $mode)
{
    is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname), $mode);
    return is_dir($pathname) || @mkdir($pathname, $mode);
}

//----------------------------------------------------------------------
// renseigne sur l'état d'un répertoire
// Entree
//		$repertoire : le répertoire sur lequel se renseigner
// Retour: 
//		-1 : le répertoire n'existe pas
//		0  : le repertoir existe mais il est vide
//		1  : le repertoire existe et contient des fichiers 
//----------------------------------------------------------------------
function etatDuRepertoire($repertoire)
{
	$fichierTrouve = false;
	if (is_dir($repertoire)) {
		if ($handle = opendir($repertoire)) {
		    while ((false !== ($file = readdir($handle))) && ($fichierTrouve === false)) {
				if (($file != '.') && ($file != '..')) $fichierTrouve = true;
			}
			closedir($handle);
		}
	}
	else return -1;
	if ($fichierTrouve === false) return 0;
	return 1;
}

//----------------------------------------------------------------------
// lit le contenu d'un répertoire
// Entree
//		$repertoire : le répertoire sur lequel fouiner
//		$fichiers : les fichiers du repertoires en retour
//		$regex : une regex qui permet de sélectionne le type de fichier recherché
//				(par défaut tous les fichiers)
// Retour: 
//		-1 : le répertoire n'existe pas
//		0  : le repertoire existe mais il est vide
//		1  : le repertoire existe et contient des fichiers 
//----------------------------------------------------------------------
function litRepertoire($repertoire, &$fichiers, $regex='##')
{
	$fichiers = array();
	if (is_dir($repertoire)) {
		if ($handle = opendir($repertoire)) {
		    while (false !== ($file = readdir($handle))) {
				if (($file != '.') && ($file != '..')) {
					if (preg_match($regex, $file)) $fichiers[] = $file;
				}
			}
			closedir($handle);
		}
	}
	else return -1;
	return count($fichiers);
}

//----------------------------------------------------------------------
// Crée ou supprime un fichier vide. 
// Ceci est utilisé à la façon d'un cookie pour vérifier son existence.
// Par exemple, si le fichier est présent on est en maintenance ...
// Entrée :
//		$fichier_maintenance : nom du fichier
//		$etat : true (défaut)(fichier créé) / false (fichier détruit)
// Retour :
//		true (fichier présent) / false (fichier absent)
//----------------------------------------------------------------------
function setMaintenance($fichier_maintenance, $etat=true) {
	if ($etat == true) {
		$fp = fopen($fichier_maintenance, 'w');
		fclose($fp);		
	}
	if ($etat == false) {
		deleteFile($fichier_maintenance);
	}
	return $etat;
}

function getMaintenance($fichier_maintenance) {
	return file_exists($fichier_maintenance);
}

//----------------------------------------------------------------------
// Récupere l'extention d'un fichier
//----------------------------------------------------------------------
function getExtension($nom) 
{
    $nom = explode('.', $nom);
    $nb = count($nom);
    return strtolower($nom[$nb-1]);
}

//----------------------------------------------------------------------
// Supprimer récursivement un dossier et tous les fichiers qui sont dedans
// Entree
//		$dir : le répertoire à supprimer
// Retour
//		true (succès) / false sinon
//----------------------------------------------------------------------
function delTree($dir)
{ 
	if (is_dir($dir)) {
		$files = scandir($dir);
		if ($files !== false) {
			$files = array_diff($files, array('.', '..')); 
			foreach ($files as $file) { 
				if (is_dir($dir.'/'.$file)) {
					//dossier trouvé -> on relance la fonction
					delTree($dir.'/'.$file);
				}
				else {
					//on supprime le fichier
					unlink($dir.'/'.$file);
				}
			} 
			//suppression du dossier
			return rmdir($dir); 
		}
	}
	return false;
}

//----------------------------------------------------------------------
// Télécharge une url distante $urlSource et pose son contenu dans le 
// fichier $fichierCible. 
// Entree
//		$urlSource : url complète de la ressource à copier
//		$fichierCible : fichier dans lequel sera copié la ressource externe
// Retour
//		true (succès) / (string) erreur en clair sinon
//----------------------------------------------------------------------
function downloadUrl($urlSource, $fichierCible)
{
	$fp = fopen($fichierCible, 'w');
	//création ressource curl et transfert url cible
	$ch = curl_init($urlSource); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_FILE, $fp); 
	curl_exec($ch); 
	$curl_errno = curl_errno($ch);
	$curl_error = curl_error($ch);
	curl_close($ch); 
	//fermeture du fichier cible (doit être réalisée immédiatement après le curl_close())
	fclose($fp);
	if ($curl_errno > 0) {
		return 'Erreur cUrl n°'.$curl_errno.' : '.$curl_error;
	}
	else {
		return true;
	}
}