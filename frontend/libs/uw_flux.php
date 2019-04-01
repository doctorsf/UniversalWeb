<?php
//--------------------------------------------------------------------------
// routines_flux.php
// Ensemble de routines orientées flux externes (CSV, RSS, CURL, logs)
// Comprend aussi les fonctions de sauvegarde / restoration de bases de données
//--------------------------------------------------------------------------
// 15.01.2016
//		- Correction bug saveDatabase -> meilleure prise en charge des erreurs de retour
//		- Amélioration saveDatabase -> possibilité de choisir les tables à include dans la sauvegarde
//		- Correction bug restoreDatabase -> meilleure prise en charge des erreurs de retour
// 02.04.2016
//		- Modification des fonctions restoreDatabase() et saveDatabase() pour utiliser les chemins d'accès aux binaires 
//			mySQL définis dans des variables globales (dans le fichier db.inc.php)
// 16.05.2017
//		- Amélioration fonction loadCSV. Elle n'importait pas correctement le texte excel en UTF-8
//			J'ai donc remplacé les fonctions "utf8_encode" par "convert_utf8".
// 13.02.2018
//		- Modification de la fonction saveCSV() pour meilleur encodage
// 17.05.2018
//		- Modification fonctions restoreDatabase() et saveDatabase() pour obliger à renseigner les binaires "mysql" et "mysqldump"
// 29.05.2018
//		- Ajout de la fonction writeLog() qui écrit une ligne de log standardisée dans un fichier log
// 01.06.2018
//		- Modification saveDataBase() et restoreDatabase() : les noms de fichier de sauvegardes prennent maintenant en compte un paramètre de version
//			de l'application en cours (ex : v.1.2) pour renseigner sur la version sauvegardée
//		- Le paramètre $system se transforme en $mode pour plus de choix dans le nom des fichier 
//				_SAVE_DB_			-> il s'agit d'une sauvegarde classique de la base de données AVEC nom de fichier daté ($nomDb.'_'.$versionApp.'_'.date('Y-m-d_H-i-s').'.sql')
//				_SAVE_DB_SYSTEM_	-> il s'agit d'une sauvegarde système de la base de données SANS nom de fichier daté ($nomDb.'_'.$versionApp.'_'.date('Y-m-d_H-i-s').'.system')
//				_SAVE_DB_NODATE_	-> il s'agit d'une sauvegarde particulière de la base de données SANS nom de fichier (daté $nomDb.'_'.$versionApp.'_no_date.sql')
// 15.06.2018
//		- Modification saveDataBase() : supprime le fichier de la sauvegarde si celle-ci ne s'est pas bien passé (on avait des fichiers de sauvegarde vide !)
// 26.11.2018
//		- Renommé la fonction writeLog() en writeStdLog()
//--------------------------------------------------------------------------

//----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Date : 07.08.2009
// Attention fonction valable pour PHP5
//----------------------------------------------------------------------
// loadCSV
// Charge un fichier CSV dans une Array
// Auteur : Fabrice Labrousse (c)2009
//
// Entrée :
//		$fichier_destination : nom du fichier csv à lire
//		$flag : choix de présentation du tableau résultat
//			si $flag = LOADCSV_INDICE alors le tableau resultat est
//				classé par indice [0]
//			si $flag = LOADCSV_COLONNE alors le tableau resultat est
//				classé par nom de colonne ["nom"]. On assume que la
//				première ligne du CSV contient les noms de colonnes
// Sortie :
//		Une array
//
// Exemple :
// $csv = loadCSV($leFichierCSV, LOADCSV_COLONNE);
// foreach ($csv as $indice => $ligne) {
//		echo '<pre>';
//		print_r($ligne);
//		echo '</pre>';
// }
//----------------------------------------------------------------------
defined('LOADCSV_INDICE') || define('LOADCSV_INDICE', 0);
defined('LOADCSV_COLONNE') || define('LOADCSV_COLONNE', 1);

//----------------------------------------------------------------------
// Chargement du fichier CSV
// par défaut le codage est ANSI (CODAGE_ANSI) mais on peut demander
// un codage CODAGE_UTF8
//----------------------------------------------------------------------
function loadCSV($inLeFichier, $flag, $codage = CODAGE_ANSI)
{
	if (($inLeFichier != null) && (file_exists($inLeFichier))) 
	{
		$tabRes = Array();
		$fp = fopen($inLeFichier, 'r');
		//lecture de la première ligne contenant intitulé des colonnes : séparateur ';'
		$tabHeader = fgetcsv($fp, 3000, ';');
		if ($codage == CODAGE_UTF8) {
			//on transforme les intitulés de colonne en UTF-8
			foreach ($tabHeader as $indice => $header) {
				$tabHeader[$indice] = convert_utf8($tabHeader[$indice]);
			}
		}
//		print_r($tabHeader);			
		//lecture des autres lignes et remplissage d'une Array
		while ($tableau = fgetcsv($fp, 3000, ';'))
		{		
			foreach ($tableau as $indice => $valeur)
			{
				//selon le $flag, on passe le nom de la colonne ou son indice
				if ($flag == LOADCSV_INDICE) {
					if ($codage == CODAGE_UTF8) {
						$tabInterne[$indice] = convert_utf8($valeur);
					} 
					else $tabInterne[$indice] = $valeur;
				}
				else {
					if ($codage == CODAGE_UTF8) {
						$tabInterne[$tabHeader[$indice]] = convert_utf8($valeur);
					}
					else $tabInterne[$tabHeader[$indice]] = $valeur;					
				}
			}
			$tabRes[] = $tabInterne;
		}
	}
	return $tabRes;
}

//----------------------------------------------------------------------
// Procédure de création d'un fichier CSV
// Entrée :
//		$inLeFichier : le nom du fichier csv a générer
//		$lesDonnees : le tableau de données à exporter
//		$codage : codage ANSI ou UTF-8 du fichier de sortie. Par défaut
//			le fichier de sortie sera en ANSI (CODAGE_ANSI)
//			si $codage = 0 -> CODAGE_ANSI
//			si $codage = 1 -> CODAGE_UTF8
//----------------------------------------------------------------------
// Par défaut l'encodage de l'application est UTF8 car les sources sont 
// enregistrés en UTF8. C'est pour ceci que l'on "décode" de l'utf8 pour 
// avoir de l'ANSI.
// NB : Pour tout ce qui est fichier CSV, préférer un encodage ANSI
//----------------------------------------------------------------------
function saveCSV($inLeFichier, $lesDonnees, $codage=CODAGE_ANSI)
{
	if ($inLeFichier != null) {
		//creation du fichier csv
		$fp = fopen($inLeFichier, 'w');
		
		//écriture de l'entête
		$entete = @array_keys($lesDonnees[0]);
		foreach($entete as $colonne) {
			if ($codage == CODAGE_ANSI) {
				fwrite($fp, utf8_decode($colonne).';');
			}
			else {
				fwrite($fp, $colonne.';');
			}
		}
		fwrite($fp, "\r\n");
		
		//ecriture des données
		foreach ($lesDonnees as $ligne) {
			for ($index = 0; $index < count($entete); $index++) {
				$valeur = $ligne[$entete[$index]];
				if ($codage == CODAGE_ANSI) {
					fwrite($fp, utf8_decode($valeur).';');
				}
				else {
					fwrite($fp, $valeur.';');
				}
			}
			fwrite($fp, "\r\n");
		}

		//fermeture du fichier csv
		fclose($fp);					
	}
}

//----------------------------------------------------------------------
// Lecture d'un fichier flux RSS (XML)
// entree :
//		$fichier : le fichier XML
//		$objets : La liste des objets à récupérer (en pratique un tableau).
//			exemple : array('title','link','description','pubDate')
//		$maximum : le nombre de ligne maximum à ramener
//----------------------------------------------------------------------
function lit_rss($fichier, $objets, $maximum=1000)
{
	//compteur de ligne
	$nb_ligne = 0;
	// on lit tout le fichier
	if($chaine = @implode("",@file($fichier))) {
		// on découpe la chaine obtenue en items
		$tmp = preg_split("/<\/?"."item".">/",$chaine);
		// pour chaque item
		for($i=1; $i<sizeof($tmp)-1; $i+=2) {
			// on lit chaque objet de l'item
			foreach($objets as $objet) {
				// on découpe la chaine pour obtenir le contenu de l'objet
				$tmp2 = preg_split("/<\/?".$objet.">/",$tmp[$i]);
				// on ajoute le contenu de l'objet au tableau resultat
				$resultat[$i-1][] = @$tmp2[1];
			}
			$nb_ligne++;
			if ($nb_ligne >= $maximum) break;
		}
		// on retourne le tableau resultat
		return $resultat;
	}
}

//----------------------------------------------------------------------
// CHARGE LE CONTENU D'UNE URL EXTERNE.
// On passe par la bibliothèque cUrl qui permet d'éviter de passer outre
// les directives du serveur PHP "allow_url_fopen" et "allow_url_include"
// qui sont pour la plupart désactivées sur les serveurs de production.
// de fait, un appel à cette fonction remplace la fonction php
// file_get_contents(url) pour laquelle les directives citées ci-dessus
// doivent obligatoirement passée sur ON.
// ENTREE : url à interroger
// retour : le contenu de l'url passée ne paramètre
//----------------------------------------------------------------------
function chargeUrlExterne($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	$xml = curl_exec($ch);
	curl_close($ch);
	return $xml;
}

//----------------------------------------------------------------------
// Sauvegarde d'une base de données MySql
// Entree :	
//		$nomDB : nom de la base de donnée (participe jsute à la génération
//				du nom de la sauvegarde). Le fichier de sauvegarde aura pour
//				nom : $nomDB_datetime.sql
//				Cette chaine de caractère ne doit pas contenir d'espace
//		$versionApp : version de l'application en cours (ex : v.1.2) pour popinter quelle version d'application a créé la sauvegarde
//		$repertoire_sauvegardes : chemin où enregistrer la sauvegarde
//		$listeDesTables : tableau de la liste des tables à sauvegarder
//		$mode : spécifie le type de sauvegarde (qui influe sur le nom du fichier) : 
//				_SAVE_DB_			-> il s'agit d'une sauvegarde classique de la base de données AVEC nom de fichier daté ($nomDb.'_'.$versionApp.'_'.date('Y-m-d_H-i-s').'.sql')
//				_SAVE_DB_SYSTEM_	-> il s'agit d'une sauvegarde système de la base de données SANS nom de fichier daté ($nomDb.'_'.$versionApp.'_'.date('Y-m-d_H-i-s').'.system')
//				_SAVE_DB_NODATE_	-> il s'agit d'une sauvegarde particulière de la base de données SANS nom de fichier (daté $nomDb.'_'.$versionApp.'_no_date.sql')
// Retour :
//		true si la sauvegarde s'est bien passée
//		false si un espace dans le nom de la base
//		false si impossible de créer le répertoire pour la sauvegarde
//		false si erreur SQL
//----------------------------------------------------------------------
// Quel que soit le nom de fichier, il est pimoprtant qu'il possède 3 et (seulement 3) underscore pour être accepté par la restoration
//----------------------------------------------------------------------
defined('_SAVE_DB_')			|| define('_SAVE_DB_', 0);
defined('_SAVE_DB_SYSTEM_')		|| define('_SAVE_DB_SYSTEM_', 1);
defined('_SAVE_DB_NODATE_')		|| define('_SAVE_DB_NODATE_', 2);

function saveDatabase($nomDb, $versionApp, $repertoire_sauvegardes, $listeDesTables=null, $mode=_SAVE_DB_)
{
	global $dbServer;
	global $dbLogin;
	global $dbPassword;
	global $dbDatabase;
	global $dbMysqldump;

	if ($dbMysqldump == '') die('Vous devez d\'abord spécifier le chemin d\'accès au binaire "mysqldump" dans "db.inc.php" pour pouvoir utiliser cette fonction');

	if (strpos($nomDb, ' ') !== false) {
		return false;
	}

	//remplacer les underscore par des tiret si nécessaire
	$nomDb = str_replace('_', '-', $nomDb);

	// Nom du fichier de sauvegarde (ex : nomdelabasededonnee_versionapp_2010-03-01_01-01-01.sql)
	if		($mode == _SAVE_DB_)		$nom_fichier = $nomDb.'_'.$versionApp.'_'.date('Y-m-d_H-i-s').'.sql';
	elseif	($mode == _SAVE_DB_SYSTEM_) $nom_fichier = $nomDb.'_'.$versionApp.'_'.date('Y-m-d_H-i-s').'.system';
	elseif 	($mode == _SAVE_DB_NODATE_) $nom_fichier = $nomDb.'_'.$versionApp.'_no_date.sql';

	//Vérification et création dossier sauvegarde
	if (is_dir($repertoire_sauvegardes) === false) {
		if (mkdir($repertoire_sauvegardes, 0700) === FALSE) {
			return false;
		}
	}

	//construction de la liste des tables
	if ($listeDesTables == null) {
		//on sauvegarde toutes les tables de la base de données
		$result = executeQuery("SHOW TABLES", $numrow);
		if ($result !== false) {
			$listeTables = '';
			foreach($result as $indice => $table) {
				$listeTables.= $table['Tables_in_'.$dbDatabase];
				if ($indice < count($result) - 1) $listeTables.= ' ';
			}
		}
		else return $result;
	}
	else {
		//on sauvegarde que les tables selectionnées de la base de données
		$listeTables = implode(' ', $listeDesTables);
	}

	//construction de la ligne de commande mysqldump.exe
	$ligne = $dbMysqldump.' --host='.$dbServer.' --user='.$dbLogin.' --password='.$dbPassword.' '.$dbDatabase.' '.$listeTables.' > '.$repertoire_sauvegardes.$nom_fichier; 
	//si ajout de " 2>&1" à la fin de la ligne de commande, alors les erreurs (STDERR) seront écrites dans le fichier de sortie (STDOUT)
	//$ligne.= " 2>&1"; 

	//lancement de la requete systeme à mysqldump.exe
	system($ligne, $return_var);
	//si la sauvegarde n'a pas réussi on efface le fichier de sauvegarde qui a pu être quand même créé
	if ($return_var != 0) unlink($repertoire_sauvegardes.$nom_fichier);
	return ($return_var == 0);
}

//----------------------------------------------------------------------
// Restauration de la base de données MySql
// Entree :	
//		$repertoire_sauvegardes : chemin où enregistrer la sauvegarde
//		$fichier : fichier à restaurer
// Retour :
//		true si la restauration s'est bien passée
//		false si un le fichier n'existe pas
//----------------------------------------------------------------------
function restoreDatabase($repertoire_sauvegardes, $fichier)
{
	global $dbServer;
	global $dbLogin;
	global $dbPassword;
	global $dbDatabase;
	global $dbMysql;

	if ($dbMysql == '') die('Vous devez d\'abord spécifier le chemin d\'accès au binaire "mysql" dans "db.inc.php" pour pouvoir utiliser cette fonction');

	$sauvegarde = $repertoire_sauvegardes.$fichier.'.sql'; 

	//vérification existence fichier
	if (file_exists($sauvegarde) === false) {
		return false;
	}

	// Nom du fichier de sauvegarde doit être de la forme ( ex : nomdelabasededonnee_versionapp_2010-03-01_01-01-01.sql )
	$parts = explode('_', $fichier);
	if (count($parts) != 4) {
		return false;
	}

	//construction de la ligne de commande mysql
	$chaine = $dbMysql.' --host='.$dbServer.' --user='.$dbLogin.' --password='.$dbPassword.' '.$dbDatabase.' < '.$sauvegarde;

	//lancement de la requete mysql
	system($chaine, $return_var);
	return ($return_var == 0);
}

//----------------------------------------------------------------------
// Ecriture d'une ligne de log standard dans fichier avec date au format ISO 8601
// Entrée : 
//		$file : chemin complet du fichier de log
//		$msg : message à logger
//		$keepAccents : true (conserve les éventuels accents), false (supprime les accents dans le message) 
// Retour
//		rien
//----------------------------------------------------------------------
function writeStdLog($file, $msg, $keepAccents=false) 
{
	$logfile = fopen($file, 'a'); 
	if (!$keepAccents) $msg = oteAccents($msg);
	fputs($logfile, date('c').'-'.$msg."\r\n"); 
	fclose($logfile);
}

//----------------------------------------------------------------------
// Surcharge de la fonction fwrite pour écrire en ANSI ou UTF-8
// Par défaut c'est de l'UTF-8 parce que le code (.php) est en UTF-8
//----------------------------------------------------------------------
function myFwrite($pf, $codage, $texte)
{
	$CR = chr(13).chr(10);
	if ($codage == CODAGE_ANSI)
//		fwrite($pf, iconv("UTF-8", "ISO-8859-1//TRANSLIT", $texte);
		fwrite($pf, iconv("UTF-8", "Windows-1252", $texte).$CR);
	else
		fwrite($pf, $texte.$CR);
}

//----------------------------------------------------------------------
// Ecriture d'une entrée de fichier sitemap
//----------------------------------------------------------------------
function writeSitemapEntry($pf, $chaine, $periodicite, $priorite, $codage=CODAGE_UTF8)
{
    myFwrite($pf, $codage, '<url>');
    myFwrite($pf, $codage, '<loc>'.$chaine.'</loc>');
//    myFwrite($pf, $codage, '<priority>'.$priorite.'</priority>');
//    myFwrite($pf, $codage, '<lastmod>'.Date('Y-m-d').'</lastmod>');
//    if ($periodicite == 'w') myFwrite($pf, $codage, '<changefreq>weekly</changefreq>');
//    else if ($periodicite == 'd') myFwrite($pf, $codage, '<changefreq>daily</changefreq>');
//    else if ($periodicite == 'm') myFwrite($pf, $codage, '<changefreq>monthly</changefreq>');
//    else if ($periodicite == 'y') myFwrite($pf, $codage, '<changefreq>yearly</changefreq>');
//    else if ($periodicite == 'a') myFwrite($pf, $codage, '<changefreq>always</changefreq>');
//    else if ($periodicite == 'h') myFwrite($pf, $codage, '<changefreq>hourly</changefreq>');
//    else if ($periodicite == 'n') myFwrite($pf, $codage, '<changefreq>never</changefreq>');
    myFwrite($pf, $codage, '</url>');
}

//----------------------------------------------------------------------
// Ecriture d'une entrée de fichier sitemap image
// $pf : pointeur sur le fichier sitemap
// $urlpage : url de la page htmp ou se trouve l'image
// $urlimage : url de l'image
// $titre : titre de l'image (optionnel)
// $legende : légende de l'image (optionnel)
// $codage : codage de l'info (optionnel, par défaut UTF-8)
//----------------------------------------------------------------------
function writeSitemapImageEntry($pf, $urlpage, $urlimage, $titre='', $legende='', $codage=CODAGE_UTF8)
{
    myFwrite($pf, $codage, '<url>');
    myFwrite($pf, $codage, '<loc>'.$urlpage.'</loc>');
    myFwrite($pf, $codage, '<image:image>');
    myFwrite($pf, $codage, '<image:loc>'.$urlimage.'</image:loc>');
	if($titre != '') myFwrite($pf, $codage, '<image:title>'.$titre.'</image:title>');
	if($legende != '') myFwrite($pf, $codage, '<image:caption>'.$legende.'</image:caption>');
    myFwrite($pf, $codage, '</image:image>');
	myFwrite($pf, $codage, '</url>');
}