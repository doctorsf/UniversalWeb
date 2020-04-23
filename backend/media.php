<?php
//-----------------------------------------------------------
// MEDIATHEQUE
//-----------------------------------------------------------
// Affichage et gestion des medias de l'application
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//gère l'accès au script
$operation = grantAcces() or die();

//-----------------------------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
//ajout de la librairie MD5
$scriptSup.= '<script type="text/javascript" src="'._JAVASCRIPT_.'md5.min.js"></script>';

$scriptSup.= '<script>';
$scriptSup.= '"use strict";';

$scriptSup.= 'var idForm	= document.getElementById("idForm");';
$scriptSup.= 'var idDiv		= document.getElementById("ufbchamp_deb");';
$scriptSup.= 'var idFile	= document.getElementById("idFile");';
$scriptSup.= 'var idUpload	= document.getElementById("idUpload");';
$scriptSup.= 'var idSubmit  = document.getElementById("idSubmit");';

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

	//supprimer eventuelle erreur précédentes
	$scriptSup.= 'idFile.addEventListener("click", annuleErreur);';
	$scriptSup.= 'function annuleErreur() {';
	$scriptSup.= '	idUpload.classList.remove("is-invalid");';
	$scriptSup.= '	idUpload.innerText = "";';
	$scriptSup.= '	var ufztitre_upload = document.getElementById("ufztitre_upload");';
	$scriptSup.= '	ufztitre_upload.querySelector("label").classList.remove("danger-color");';
	$scriptSup.= '	var ufzchamp_upload = document.getElementById("ufzchamp_upload");';
	$scriptSup.= '	ufzchamp_upload.querySelector("p.form_error").innerText = "";';
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
	$scriptSup.= '		if (lesFichiers[i].type.substring(0,5) == "image") {';
	$scriptSup.= '			image = document.createElement("img");';
	$scriptSup.= '			image.src = window.URL.createObjectURL(lesFichiers[i]);';
	$scriptSup.= '			image.classList.add("img-fluid");';
	$scriptSup.= '			dropShow.appendChild(image);';
	$scriptSup.= '		};';
	$scriptSup.= '	};';
	$scriptSup.= '	idUpload.value = liste;';
	$scriptSup.= '	idSubmit.disabled = false';
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

$scriptSup.= '<script>';
//cette fonction permet de positionner l'image active du carousel	
$scriptSup.= 'function activate(num, total) {';
$scriptSup.= '	/* supprime toutes les classes "active" */';
$scriptSup.= '	for(var i = 0; i < total;  i++) {';
$scriptSup.= '		document.getElementById("carimg" + i).classList.remove("active");';
$scriptSup.= '	}';
$scriptSup.= '	/* ajoute la classe "active" a l\'image cliquée */';
$scriptSup.= '	var idActive = document.getElementById("carimg" + num);';
$scriptSup.= '	idActive.classList.add("active");';
$scriptSup.= '	/* construit et ajoute l\'url au bouton "supprimer" de la boite modale qui contient le Carousel */';
$scriptSup.= '	var image = idActive.getAttribute("data-image");';
$scriptSup.= '	var path = idActive.getAttribute("data-path");';
$scriptSup.= '	var btDelete = document.getElementById("btDelete");';
$scriptSup.= '	btDelete.href = "'._URL_ACTIONS_DIVERS_.'?operation=delmedia&path=" + path + "&id=" + md5(image);';
$scriptSup.= '}';
$scriptSup.= '</script>';

$fJquery   = '';
//recalcule le lien de suppression de l'image (affecté au bouton de suppression) lors de chaque changement de slide du carousel
$fJquery  .= '$("#myGallery").on("slid.bs.carousel", function(event) {';
$fJquery  .= '  var idStr = event.relatedTarget.id;';
$fJquery  .= '	var idActive = document.getElementById(idStr);';
$fJquery  .= '	var image = idActive.getAttribute("data-image");';
$fJquery  .= '	var path = idActive.getAttribute("data-path");';
$fJquery  .= '	var btDelete = document.getElementById("btDelete");';
$fJquery  .= '	btDelete.href = "'._URL_ACTIONS_DIVERS_.'?operation=delmedia&path=" + path + "&id=" + md5(image);';
$fJquery  .= '});';

//demande de confirmation de la suppression de l'image
$fJquery  .= '$("#btDelete").on("click", function(e){';
$fJquery  .= '  return confirm(\''.addslashes(getLib('MEDIA_SUPPRIMER_CERTAIN')).'\');';
$fJquery  .= '});';

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

				$frm = new Form_media('upload', 1);
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
					// Validationi du formulaire
					//--------------------------------------
					case 'valid_upload': {
						$donnees = $frm->getData();
						//DEBUG_('donnees', $donnees);
						if ($donnees['btPath'] == 'OK') {
							//sauvegarde du nouveau path media regardé
							$_SESSION['mediaPath'] = $donnees['path'];
							//changement du path et affichage formulaire
							$frm->changePath($donnees['path']);
							echo $frm->afficher();
							break;
						}
						if (!$frm->tester()) {
							$frm->recopieErreurs();
							echo $frm->afficher();
						}
						else {
							//ok uploader
							$files = $donnees['file']['files'];
							//DEBUG_('files', $files);
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
							if ($donnees['file']['nbFiles'] > 0) {
								foreach ($files as $key => $file) {
									//on ignore les champs non renseignés ou en erreur
									if (($file['name'] == '') || ($file['error'] != 0)) continue;
									//faire ici les vérifications fupplémentaires si besoin...
									//list($width_orig, $height_orig) = getimagesize($file['tmp_name']);
									//if (($width_orig != 800) && ($height_orig != 800)) {
									//	riseErrorMessage(getLib('IMAGE_NEEDS_800_PIXELS', $file['name']));
									//	//recharge la page
									//	header('Location: '.$_SERVER['REQUEST_URI']);
									//	die();
									//}
								}
								//tous les media proposés sont acceptés => transfert
								foreach ($files as $key => $file) {
									//on ignore les champs non renseignés ou en erreur
									if (($file['name'] == '') || ($file['error'] != 0)) continue;
									//création du nom de fichier cible
									$dest_fichier = $donnees['realpath'].$file['name'];
									//copie du fichier
									if (move_uploaded_file($file['tmp_name'], $dest_fichier)) {
										riseMessage(getLib('MEDIA_UPLOAD_SUCCES', $file['name']), $key);
									}
									else {
										riseErrorMessage(getLib('MEDIA_UPLOAD_ERREUR', $file['name']), $key);
									}
								}
							}
							//recharge la page
							header('Location: '.$_SERVER['REQUEST_URI']);
							die();
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