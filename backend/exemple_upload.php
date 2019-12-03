<?php
//-----------------------------------------------------------
// UPLOAD DE FICHIERS
//-----------------------------------------------------------
// Cet exemple propose le téléversement de fichiers de types .txt et .csv 
// de 2 manières possibles : soit pas selecteur de fichier, soit par glissé / posé
// sur une zone de téléversement.
// Le principe : Il s'agit d'un objet texte de type "file" avec selection multiple possible
// On va utiliser la propriété naturelle de ce type de champ : si l'on drop un fichier dessus, 
// l'évement est identique à la sélection via boite de dialogue.
// Nous allons donc cacher l'objet (opacité 0) sous un objet DIV et ajouter 2 zones d'affichage 
// des fichiers téléversés.
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//gère l'accès au script
//$operation = grantAcces() or die();

//sauvegarde de la page de retour
//setPageBack();

//-----------------------------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
$scriptSup.= '<script>';
$scriptSup.= '"use strict";';

$scriptSup.= 'var idForm	= document.getElementById("idForm");';
$scriptSup.= 'var idDiv		= document.getElementById("ufbchamp_deb");';
$scriptSup.= 'var idFile	= document.getElementById("idFile");';
$scriptSup.= 'var idUpload	= document.getElementById("idUpload");';

$scriptSup.= 'if (idForm) {';

	//modifie le style de "idFile" : entre autre le rend non visible (pas invisible !)
	$scriptSup.= 'idFile.style = "line-height:5rem;min-height:5rem;opacity:.0";';

	//modifie le style "idDiv" pour cohérence visuelle avec le formulaire
	$scriptSup.= 'idDiv.parentNode.classList.add("mx-0");';
	$scriptSup.= 'document.getElementById("ufbchamp_file").classList.remove("mb-3");';
	$scriptSup.= 'document.getElementById("ufbchamp_sep").classList.remove("mb-3");';
	$scriptSup.= 'idDiv.style = "border:2px dotted silver;";';

	//gestion des évènements drag & drop sur "idDiv"
	$scriptSup.= 'idDiv.ondragenter = function(e) {';
	$scriptSup.= '	e.stopPropagation();';
	$scriptSup.= '	this.classList.add("bg-light");';
	$scriptSup.= '};';
	$scriptSup.= 'idDiv.ondragover = function(e) {';
	$scriptSup.= '	e.stopPropagation();';
	$scriptSup.= '	this.classList.add("bg-light");';
	$scriptSup.= '};';
	$scriptSup.= 'idDiv.ondragleave = function(e) {';
	$scriptSup.= '	e.stopPropagation();';
	$scriptSup.= '	this.classList.remove("bg-light");';
	$scriptSup.= '};';
	$scriptSup.= 'idDiv.addEventListener("drop", drop);';
	$scriptSup.= 'function drop(e) {';
	$scriptSup.= '	e.stopPropagation();';
	$scriptSup.= '	this.classList.remove("bg-light");';
	$scriptSup.= '	showFiles(e.dataTransfer.files);';
	$scriptSup.= '};';

	//recopie des saisies (en selection ou en drop)
	$scriptSup.= 'idFile.addEventListener("change", recopie);';
	$scriptSup.= 'function recopie() {';
	$scriptSup.= '	showFiles(idFile.files);';
	$scriptSup.= '}';

	//affichage saisies
	$scriptSup.= 'function showFiles(lesFichiers) {';
	$scriptSup.= '	var dropShow = document.getElementById("drop-show");';
	$scriptSup.= '	dropShow.innerHTML = "";';
	$scriptSup.= '	var droppedItem;';
	$scriptSup.= '	var image;';
	$scriptSup.= '	var liste = "";';
	$scriptSup.= '	for (var i = 0; i < lesFichiers.length; i++) {';
	//$scriptSup.= '		console.log(lesFichiers[i]);';
	$scriptSup.= '		liste += lesFichiers[i].name + "\n";';
	$scriptSup.= '		droppedItem = document.createElement("p");';
	$scriptSup.= '		droppedItem.className = "mb-0";';
	$scriptSup.= '		droppedItem.innerHTML = getIcon(lesFichiers[i].type) + " " + lesFichiers[i].name + " (" + lesFichiers[i].size + " Kb)";';
	$scriptSup.= '		dropShow.appendChild(droppedItem);';
	$scriptSup.= '		if (lesFichiers[i].type == "image/jpeg") {';
	$scriptSup.= '			image = document.createElement("img");';
	$scriptSup.= '			image.src = window.URL.createObjectURL(lesFichiers[i]);';
	$scriptSup.= '			dropShow.appendChild(image);';
	$scriptSup.= '		};';
	$scriptSup.= '	};';
	$scriptSup.= '	idUpload.value = liste;';
	$scriptSup.= '}';

	//obtenir une icone font-awesome en fonction du type de fichier
	//a compléter avec autres types de fichiers mime
	$scriptSup.= 'function getIcon(type) {';
	$scriptSup.= '	var icone = "far fa-file";';
	$scriptSup.= '	if (type.substring(0,5) == "image") type = "image";';
	$scriptSup.= '	switch(type) {';
	$scriptSup.= '		case "image":';
	$scriptSup.= '			icone = "far fa-file-image";';
	$scriptSup.= '			break;';
	$scriptSup.= '		case "application/vnd.ms-excel":';
	$scriptSup.= '			icone = "far fa-file-excel";';
	$scriptSup.= '			break;';
	$scriptSup.= '		case "application/msword":';
	$scriptSup.= '			icone = "far fa-file-word";';
	$scriptSup.= '			break;';
	$scriptSup.= '		case "application/x-zip-compressed":';
	$scriptSup.= '			icone = "far fa-file-archive";';
	$scriptSup.= '			break;';
	$scriptSup.= '		case "application/pdf":';
	$scriptSup.= '			icone = "far fa-file-pdf";';
	$scriptSup.= '			break;';
	$scriptSup.= '		default:';
	$scriptSup.= '			icone = "far fa-file";';
	$scriptSup.= '	}';
	$scriptSup.= '	return "<span class=\"" + icone + "\"></span>";';
	$scriptSup.= '}';

$scriptSup.= '};';

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
		echo '<div class="container px-0">';

		echo '<div class="row">';
			echo '<div class="col">';

				$frm = new Form_upload('upload', 1);
				$action = $frm->getAction();

				switch($action)	{
					//--------------------------------------
					// upload
					//--------------------------------------
					case 'upload': {
						//sauvegarde de la page de retour
						$frm->init();
						echo $frm->afficher();
						break;
					}
					//--------------------------------------
					// Valide upload
					//--------------------------------------
					case 'valid_upload': {
						if (!$frm->tester()) {
							$frm->recopieErreurs();
							echo $frm->afficher();
						}
						else {
							//ok uploader
							$donnees = $frm->getData();
							//DEBUG_('donnees', $donnees);
							//upoad des fichiers vers le dossier _ARMOIRE_
							$files = $donnees['file']['files'];
							//[file] => Array {
							//		[nbFiles] => 2
							//		[files] => Array (
							//				[0] => Array (
							//						[name] => A LIRE.txt
							//						[type] => text/plain
							//						[tmp_name] => D:\xampp-7.2.0\tmp\php3130.tmp
							//						[error] => 0
							//						[size] => 10716
							//					)
							//				[1] => Array (
							//						[name] => exemple_import.csv
							//						[type] => application/vnd.ms-excel
							//						[tmp_name] => D:\xampp-7.2.0\tmp\php3131.tmp
							//						[error] => 0
							//						[size] => 521
							//					)
							//			)
							//	)
							foreach ($files as $key => $file) {
								//on ignore les champs non renseignés ou en erreur
								if (($file['name'] == '') || ($file['error'] != 0)) continue;
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
									echo '<p class="lead text-success">fichier "'.$file['name'].'" uploadé avec succès!</p>';
								}
								else {
									echo '<p class="lead text-danger">Impossible d\'uploader le fichier "'.$file['name'].'" !</p>';
								}
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