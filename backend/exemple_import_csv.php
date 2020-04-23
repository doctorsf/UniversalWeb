<?php
//-----------------------------------------------------------
// IMPORT EN MASSE D'ARTICLES DEPUIS FICHIER CSV
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//gère l'accès au script
//$operation = grantAcces() or die();

//sauvegarde de la page de retour
//setPageBack();

//-----------------------------------------------------------
// fonction d'importation des contacts
// Entree : 
//		$importFile : $fichier CSV
// Sortie : 
//-----------------------------------------------------------
function import($importFile) {

	if (file_exists($importFile)) {

		// creation de l'objet ImportCsV_exemple et chargement fichier CSV
		//-------------------------------------------
		$csv = new Exemple_csvimport();
		$csv->buildModele();
		$data = $csv->charge($importFile);

		// affinage des données avant traitement
		//-------------------------------------------
		foreach ($data as $numLine => $enreg) {
			//on transforme les champs visuel et genre en minuscules
			$data[$numLine]['visuel'] = utf8_strtolower($data[$numLine]['visuel']);
			$data[$numLine]['genre'] = utf8_strtolower($data[$numLine]['genre']);
		}

		// test de chaque colonne de l'enregistrement
		// les erreurs sont rapportées directement dans l'enregistrement
		//-------------------------------------------
		$nbErreur = 0;
		foreach ($data as $numLine => $enreg) {
			$erreur = $csv->testColonnes($data[$numLine]);
			if ($erreur) {
				$nbErreur++;
			}
		}
		//DEBUG_TAB_($data);

		// traitement postérieur si pas d'erreur
		//-------------------------------------------
		if ($nbErreur == 0) {
			foreach ($data as $numLine => $enreg) {
				//transformation couleur / noir & blanc en 1 / O pour le visuel
				$data[$numLine]['visuel'] = (($data[$numLine]['visuel']) == 'couleur') ? '1' : '0';
			}
		}

		// affichage des erreurs
		//-------------------------------------------
		else {
			//affichage des infos non valide dans un tableau
			echo '<div class="container-lg px-0">';
				echo $csv->displayRawErrors($data, $nbErreur);
			echo '</div>';
			return false;
		}

		//---------------------------------------------
		// INSERTION DANS LA BASE DE DONNEES
		//---------------------------------------------
		if ($nbErreur == 0) {

			//DEBUG_TAB_($data);
			echo '<p>Import en cours&hellip;</p>';
			$res = $csv->import($data);
			if ($res !== false) {
				echo '<p class="lead text-success">Import terminé avec succès ('.$res.' nouvelles entrées).</p>';
				return true;
			}
			else {
				echo '<p class="lead text-danger">'.getLib('ERREUR_SQL').'</p>';
				//DEBUG_('requete', $requete);
				return false;
			}
		}

	}
	else {
		$leMessage['title'] = getLib('ERREUR');
		$leMessage['text'] = getLib('FICHIER_IMPORT_X_INEXISTANT', $importFile);
		$leMessage['color'] = 'danger';
		$leMessage['dismiss'] = 'false';
		$leMessage['footer'] = '<a href="'.$_SERVER['REQUEST_URI'].'">'.getLib('CORRIGEZ_RELANCEZ').'</a>';
		echo bootstrapAlert($leMessage);
		return false;
	}
}


//-----------------------------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
$scriptSup.= '<script>';
$scriptSup.= '"use strict";';
$scriptSup.= 'var idFile = document.getElementById("idFile");';				//champ invisible de type 'file'
$scriptSup.= 'var idUpload = document.getElementById("idUpload");';			//champ 'visible' recevant le nom fichier choisi
$scriptSup.= 'var idBtUpload = document.getElementById("idBtUpload");';		//bouton d'ouverture du selecteur de fichiers
$scriptSup.= 'var idSubmit = document.getElementById("idSubmit");';			//bouton de validation du formulaire
//clic sur l'icone bouton d'upload lance le clic sur le champ 'file' invisible
$scriptSup.= 'idBtUpload.addEventListener("click", function() {';
$scriptSup.= '	idFile.click();';
$scriptSup.= '});';
//recopie du fichier choisi (champ invisible 'file') vers champ visible ('upload')
$scriptSup.= 'idFile.addEventListener("change", function() {';
$scriptSup.= '	idUpload.value = idFile.files[0].name;';
$scriptSup.= '	idSubmit.disabled = false';
$scriptSup.= '});';
//supprimer eventuelle erreur précédentes
$scriptSup.= 'idFile.addEventListener("click", function() {';
$scriptSup.= '	idUpload.classList.remove("is-invalid");';
$scriptSup.= '	idUpload.value = "";';
$scriptSup.= '	var ufztitre_upload = document.getElementById("ufztitre_upload");';
$scriptSup.= '	ufztitre_upload.querySelector("label").classList.remove("danger-color");';
$scriptSup.= '	var ufzchamp_upload = document.getElementById("ufzchamp_upload");';
$scriptSup.= '	ufzchamp_upload.querySelector("p.form_error").innerText = "";';
$scriptSup.= '});';
$scriptSup.= '</script>';
$fJquery = '';
echo writeHTMLHeader($titrePage, '', '');

echo '<body>';
	echo '<div class="container-fluid">';

	//--------------------------------------
	// HEADER
	//--------------------------------------
	include_once(_BRIQUE_HEADER_);

	//--------------------------------------
	// CORPS
	//--------------------------------------
	echo '<section>';
	echo '<article>';

		echo '<div class="row">';
			echo '<div class="col">';

				$frm = new Form_import_csv('charger', 1);
				$action = $frm->getAction();

				switch($action)	{
					//--------------------------------------
					// Ajouter
					//--------------------------------------
					case 'charger': {
						//sauvegarde de la page de retour
						$frm->init();
						echo $frm->afficher();
						//affichage de la structure du modèle d'import
						include_once('exemple_import_csv_modele.php');
						break;
					}
					//--------------------------------------
					// Valide ajouter
					//--------------------------------------
					case 'valid_charger': {
						if (!$frm->tester()) {
							$frm->recopieErreurs();
							echo $frm->afficher();
							//affichage de la structure du modèle d'import
							include_once('exemple_import_csv_modele.php');
						}
						else {
							//ok ajouter
							$donnees = $frm->getData();
							//DEBUG_('donnees', $donnees);
							//import du fichier vers le dossier navette
							$file = $donnees['file']['files'];
							//[file] => Array
							//	(
							//		[nbFiles] => 1
							//		[files] => Array
							//			(
							//				[name] => exemple_import.csv
							//				[type] => application/vnd.ms-excel
							//				[tmp_name] => D:\xampp-7.2.0\tmp\php4EEF.tmp
							//				[error] => 0
							//				[size] => 521
							//			)
							//
							//	)
							//on ignore les champs non renseignés ou en erreur (correction passage PHP 7.3 : remplacé continue par break)
							if (($file['name'] == '') || ($file['error'] != 0)) break;
							//un petit regex sur le fichier cible pour remplacer tous ce qui n'est ni chiffre ni lettre par "_"
							$dest_fichier = preg_replace('/([^._a-z0-9]+)/i', '-', $file['name']);
							//on enleve tous les accents
							$dest_fichier = strtr($dest_fichier, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
							//creation du dossier cible si celui-ci n'existe pas
							$dest_dossier = _ARMOIRE_;
							if (!is_dir($dest_dossier)) { 
								mkdir($dest_dossier, 0705); 
							}
							//copie du fichier
							if (move_uploaded_file($file['tmp_name'], $dest_dossier.$dest_fichier)) {
								//OK on procède à l'import
								$res = import($dest_dossier.$dest_fichier);
								if ($res != false) {
									riseMessage('Document importé&hellip;');
									goReferer();
								}
							}
							else {
								riseErrorMessage(getLib('FICHIER_IMPORT_X_ERROR', $file['name']));
								goReferer();
							}
						}
						break;
					}
					//--------------------------------------
					// Commandes non reconnues
					//--------------------------------------
					default: {
						riseErrorMessage('Erreur commande&hellip;');
						goPageBack();
						break;
					}
				}

			echo '</div>';
		echo '</div>';

	echo '</article>';
	echo '</section>';

	//--------------------------------------
	// FOOTER
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);

	echo '</div>';		//container
echo '</body>';
echo '</html>';