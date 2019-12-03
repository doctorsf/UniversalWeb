<?php
//--------------------------------------------------------------------------
// LISTINGS DROITS D'ADMINISTRATION
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8		
// version : 23.10.2017
// 06.02.2018 : 
//		Petite correction bug
// 28.05.2019
//		Affichage des droits par groupes (accordion), drag & drop, et modification des droit en javascript
//--------------------------------------------------------------------------
require_once('libs/common.inc.php');

//sauvegarde de la page de retour
setPageBack();

//gère l'accès au script
$operation = grantAcces() or die();

//récupération du groupe de fonctionnalités à afficher déployé (1 par défaut)
if (!isset($_SESSION[_APP_DROITS_GROUPE_DEPLOYE_])) {
	$_SESSION[_APP_DROITS_GROUPE_DEPLOYE_] = 1;
}
$groupeDeploye = $_SESSION[_APP_DROITS_GROUPE_DEPLOYE_];

//-------------------------------------
// Construction des colonnes du tableau
// Champs possibles : 
//		'name' (titre du champ)
//		'size' (taille en %)
//		'align' (alignement dans la cellule : left, right, center, justify)
//		'tri' (champ SQL sur lequel le listing doit être trié)
//		'sens' (sens du tri ASC ou DESC)
//		'title' (Description "title" de la colonne
//-------------------------------------
$cols['fonction'] = SimpleListingHelper::createCol(array('name' => getLib('FONCTIONNALITE'), 'size' => 34, 'css' => 'px-2'));
//colonnes des droits
foreach($_SESSION[_APP_DROITS_]->profils() as $key => $profil) {
	if (_RUN_MODE_ == _DEVELOPPEMENT_) {
		$libProfil = '<a href="'._URL_ACTIONS_DROITS_.'?operation=renniv&do='.$profil['id_profil'].'">';
			$libProfil.= '<span data-toggle="tooltip" title="'.getLib('DROITS_MODIF_LIB_PROFIL').'">'.$profil['libelle'].'</span>';
		$libProfil.= '</a><br />';

		$libProfil.= '<a href="'._URL_ACTIONS_DROITS_.'?operation=rennivcode&do='.$profil['id_profil'].'">';
			$libProfil.= '<span data-toggle="tooltip" title="'.getLib('DROITS_MODIF_CODE_PROFIL').'">'.$profil['code'].'</span>';
		$libProfil.= '</a><br />';

		$libProfil.= '(<a href="'._URL_ACTIONS_DROITS_.'?operation=rennivid&do='.$profil['id_profil'].'">';
			$libProfil.= '<span data-toggle="tooltip" title="'.getLib('DROITS_MODIF_ID_PROFIL').'">'.$profil['id_profil'].'</span>';
		$libProfil.= '</a>)';
	}
	else {
		$libProfil = $profil['libelle'];
	}
	$cols[$profil['id_profil']]  = SimpleListingHelper::createCol(array('name' => $libProfil, 'size' => 6,	'align' => 'center'));
}
//colonne de suppression des fonctionnalité uniquement affichée en mode developpement
if (_RUN_MODE_ == _DEVELOPPEMENT_) {
	$cols['sup'] = SimpleListingHelper::createCol(array('name' => 'Suppr.', 'size' => 2, 'align' => 'center'));
}

//-------------------------------------
// Ensemble des fonctions d'affichage du contenu des colonnes
//-------------------------------------
function Col_fonction($codeFonctionnalite, $fonctionnalite) {
	if (isset($fonctionnalite['libelle'])) {
		if (_RUN_MODE_ == _DEVELOPPEMENT_) {
			echo '<a href="'._URL_ACTIONS_DROITS_.'?operation=renfoncid&do='.$fonctionnalite['id_fonctionnalite'].'">';
				echo '<span data-toggle="tooltip" title="'.getLib('DROITS_MODIF_ID_FONC').'" draggable="true" id-fonc="'.$fonctionnalite['id_fonctionnalite'].'">'.$fonctionnalite['id_fonctionnalite'].'</span>';
			echo '</a>';

			echo ' - ';

			echo '<a href="'._URL_ACTIONS_DROITS_.'?operation=renfonc&do='.$fonctionnalite['id_fonctionnalite'].'">';
				echo '<span data-toggle="tooltip" title="'.getLib('DROITS_MODIF_LIB_FONC').'" draggable="true" id-fonc="'.$fonctionnalite['id_fonctionnalite'].'">'.$fonctionnalite['libelle'].'</span>';
			echo '</a>';

			echo '&nbsp;';

			echo '(<a href="'._URL_ACTIONS_DROITS_.'?operation=renfonccode&do='.$fonctionnalite['id_fonctionnalite'].'">';
				echo '<span data-toggle="tooltip" title="'.getLib('DROITS_MODIF_CODE_FONC').'" draggable="true" id-fonc="'.$fonctionnalite['id_fonctionnalite'].'">'.$fonctionnalite['code'].'</span>';
			echo '</a>)';
		}
		else {
			echo $fonctionnalite['libelle'];
		}
	}
	else {
		echo $fonctionnalite['id_fonctionnalite'].' - fonctionnalité à créer';
	}
}

//affichage des cellules de droits
function Col_($profil, $codeFonctionnalite) {
	$id = $_SESSION[_APP_DROITS_]->getIdFonctionnalite($codeFonctionnalite);
	if ($_SESSION[_APP_DROITS_]->droitExiste($codeFonctionnalite, $profil)) {
		echo '<div class="droit pointer" id="'.$id.'_'.$profil.'">';
			if ($_SESSION[_APP_DROITS_]->accesAutorise($codeFonctionnalite, $profil)) {
				echo '<span class="text-success fas fa-check"></span>';
			}
			else {
				echo '<span class="text-danger fas fa-ban">';
			}
		echo '</div>';
	}
}

//affichage des poubelles de suppression fonctionalité (dernière colonne)
function Col_sup($codeFonctionnalite, $fonctionnalite) {
	if ($codeFonctionnalite != 'FONC_ADM_APP') {
		$javascript = 'onclick="return confirm(\''.addslashes(getLib('SUPPRIMER_CETTE_FONC_CERTAIN')).'\');"';
		echo '<a href="'._URL_ACTIONS_DROITS_.'?operation=delfonc&amp;do='.$fonctionnalite['id_fonctionnalite'].'" '.$javascript.'>'; 
		echo '<span class="fas fa-trash" data-toggle="tooltip" title="'.getLib('SUPPRIMER_CETTE_FONC').'"></span>';
		echo '</a>';
	}
}

//affichage d'une ligne supplémentaire en dernier pour suppression possible des profils
//uniquement affichée pour les concepteurs de l'application
function ligneDelete() {
	if (_RUN_MODE_ == _DEVELOPPEMENT_) {
		$javascript = 'onclick="return confirm(\''.addslashes(getLib('SUPPRIMER_CE_PROFIL_CERTAIN')).'\');"';
		echo '<tr>';
		echo '<td>&nbsp;</td>';
		//pour chaque profil
		foreach($_SESSION[_APP_DROITS_]->profils() as $profil) {
			echo '<td align="center">';
			if ($profil['code'] != 'PROFIL_ADMIN') {		//pour ne pas proposer la suppression du profil admin
				echo '<a href="'._URL_ACTIONS_DROITS_.'?operation=delniv&amp;do='.$profil['id_profil'].'" '.$javascript.'>'; 
				echo '<span class="fas fa-trash" data-toggle="tooltip" title="'.getLib('SUPPRIMER_CE_PROFIL').'"></span>';
				echo '</a>';
			}
			echo '</td>';
		}
		echo '<td>&nbsp;</td>';
		echo '</tr>';
	}
}

//-------------------------------------
// Gestion du listing
// Le premier paramètre de la méthode statique getParams() est un tableau contenant 
//	les champs SQL sur lesquels on peut trier les colonnes
//-------------------------------------
SimpleListingHelper::getParams('fonction', $cols, $page, $tri, $sens);

//-------------------------------------
// Récupération des données à afficher
//-------------------------------------
//On va travailler sur les fonctionnalités de l'application et ramener les informations de groupes
//même ci ceux-ci sont vides
sqlDroits_getInfosListing($listing, $_SESSION[_APP_DROITS_]->getNotionGroupes());
$nombreLignes = $_SESSION[_APP_DROITS_]->countFonctionnalite();
//construction de la barre de navigation
$pn = new PageNavigator($nombreLignes, 30, 20, $page);
$debut = $pn->getItemDebut();
$fin = $pn->getItemFin();
$pn->setPageOff();
$navigation = $pn->draw();

//-------------------------------------
// HTML
//-------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
if ((_RUN_MODE_ == _DEVELOPPEMENT_) && ($_SESSION[_APP_DROITS_]->getNotionGroupes())) {
	$scriptSup.= '<script>';
	$scriptSup.= 'var dragImg = new Image();';
	$scriptSup.= 'dragImg.src = \''._IMAGES_COMMUN_.'file.png\';';
	$scriptSup.= '';
	$scriptSup.= 'var draggers = document.querySelectorAll(\'*[draggable="true"]\');';	//définition des draggers (objets que l'on peut déplacer (DRAG))
	$scriptSup.= 'var droppers = document.querySelectorAll(\'.dropper\');';				//définition des droppers (objets sur lesquels on peut poser (DROP))

	//position des évènements écoutés sur les draggers
	$scriptSup.= '[].forEach.call(draggers, function(dragger) {';							
	$scriptSup.= '  dragger.addEventListener(\'dragstart\', draggerStart, false);';
	$scriptSup.= '  dragger.addEventListener(\'dragend\', draggerEnd, false);';
	$scriptSup.= '});';

	//débute le DRAG d'une fonctionnalité ou d'un groupe (les 2 sont draggable)
	$scriptSup.= 'function draggerStart(e) {';
	//$scriptSup.= '	console.log(e);';
	$scriptSup.= '	var id_fonc = this.getAttribute(\'id-fonc\');';
	$scriptSup.= '  this.style.opacity = \'0.4\';';
	$scriptSup.= '  e.dataTransfer.setDragImage(dragImg, 20, 20);';
	$scriptSup.= '  e.dataTransfer.setData(\'text/plain\', \'fct|\'+id_fonc);';			// renvoie "fct|num"  (fct permet de savoir que l'on demande une migration de groupe pour une fonctionnalité)
	$scriptSup.= '}';

	//termine le DRAG d'une fonctionnalité ou d'un groupe (les 2 sont draggable)
	$scriptSup.= 'function draggerEnd(e) {';
	$scriptSup.= '  this.style.opacity = \'1\';';
	$scriptSup.= '}';

	//définition des evenements possible pour les droppers
	$scriptSup.= '[].forEach.call(droppers, function(dropper) {';
	$scriptSup.= '	dropper.addEventListener(\'dragstart\', dropperStart, false);';		//un dropper que l'on peut dragger est forcement un "groupe de fonctionnalités"
	$scriptSup.= '	dropper.addEventListener(\'dragover\', dropperOver, false);';
	$scriptSup.= '  dropper.addEventListener(\'dragenter\', dropperEnter, false);';
	$scriptSup.= '  dropper.addEventListener(\'dragleave\', dropperLeave, false);';
	$scriptSup.= '  dropper.addEventListener(\'dragend\', dropperEnd, false);';
	$scriptSup.= '  dropper.addEventListener(\'drop\', dropperDrop, false);';
	$scriptSup.= '});';

	//commence le DRAG d'un groupe
	$scriptSup.= 'function dropperStart(e) {';
	$scriptSup.= '	var id_groupe = this.getAttribute(\'id-groupe\');';
	$scriptSup.= '  this.style.opacity = \'0.4\';';
	$scriptSup.= '  e.dataTransfer.setDragImage(dragImg, 24, 20);';
	$scriptSup.= '  e.dataTransfer.setData(\'text/plain\', \'grp|\'+id_groupe);';		// renvoie "grp|num" (grp permet de savoir que l'on demande un arrangement (tri) de groupes)
	$scriptSup.= '};';

	//déplacement au-dessus d'un dropper
	$scriptSup.= 'function dropperOver(e) {';
	$scriptSup.= '	e.preventDefault();';
	$scriptSup.= '  this.classList.remove(\'bg-white\');';
	$scriptSup.= '  this.classList.add(\'bg-info\');';
	$scriptSup.= '}';

	//entrée dans la zone d'un dropper
	$scriptSup.= 'function dropperEnter(e) {';
	$scriptSup.= '	e.preventDefault();';
	$scriptSup.= '  this.classList.remove(\'bg-white\');';
	$scriptSup.= '  this.classList.add(\'bg-info\');';
	$scriptSup.= '}';

	//sortie de la zone d'un dropper
	$scriptSup.= 'function dropperLeave(e) {';
	$scriptSup.= '	e.preventDefault();';
	$scriptSup.= '  this.classList.remove(\'bg-info\');';
	$scriptSup.= '  this.classList.add(\'bg-white\');';
	$scriptSup.= '}';

	//termine le DRAG d'un dropper (donc un groupe de fonctionnalités)
	$scriptSup.= 'function dropperEnd(e) {';
	$scriptSup.= '  this.style.opacity = \'1\';';
	$scriptSup.= '}';

	//la fonction de DROP va devoir faire la différence si le dragger est une fonctionnalité ou un groupe de fonction
	//- si le dragger est une fonctionnalité alors on cherche à changer la fonctionnalté de groupe
	//- si le dragger est un groupe de fonctions alors on cherche à changer l'ordre d'affichage (tri) des groupes de fonctionnalités
	$scriptSup.= 'function dropperDrop(e) {';
	$scriptSup.= '	e.preventDefault();';
	$scriptSup.= '  this.classList.remove(\'bg-info\');';
	$scriptSup.= '  this.classList.add(\'bg-white\');';
	$scriptSup.= '	var id_groupe = this.getAttribute(\'id-groupe\');';			//groupe cible
	$scriptSup.= '  var data = e.dataTransfer.getData(\'text/plain\');';		//recupération de l'envoi de draggerStart ou de dropperStart (on recupere "source[fct/grp]|id")
	$scriptSup.= '  var res = explode(\'|\', data);';							//détermination de l'action à mener par lecture du contenu getData (soit "fct|numsource" ou "grp|numsource")
	$scriptSup.= '	if (res[0] === \'fct\') {';
	$scriptSup.= '		dummy = uw_MoveFoncToGroupe(res[1], id_groupe);';		//déplacement de la fonctionalié res[1] vers le groupe id_groupe
	$scriptSup.= '  }';
	$scriptSup.= '	else {';
	$scriptSup.= '		dummy = uw_MoveGroupe(res[1], id_groupe);';				//déplacement du groupe res[1] après groupe id_groupe
	$scriptSup.= '	}';
	$scriptSup.= '}';

	$scriptSup.= '</script>';
}

$fJquery = '';

if ($_SESSION[_APP_DROITS_]->getNotionGroupes()) {
	//fonction qui à chaque déploiement de groupe de droits récupere l'id du groupe déployé pour la passer dans
	//la variable de session _APP_DROITS_GROUPE_DEPLOYE_ via ajax
	$fJquery.= '$(\'#accordionDroits\').on(\'shown.bs.collapse\', function () {';
	$fJquery.= '  var groupe = this.querySelector(\'[aria-expanded="true"]\').getAttribute(\'id-groupe\');';
	$fJquery.= '    $.ajax({';											//on utilise ici l'objet ajax de Jquery
	$fJquery.= '		url : \'reponses-ajax.php\',';					//la ressource ciblée
	$fJquery.= '		type : \'GET\',';								//le type de la requête HTTP.
	$fJquery.= '		data : \'f=uw_SetGroupeDroitsDeploye&id=\' + groupe';	//paramètres de la ressource ciblée (le groupe déployé)
	$fJquery.= '	});';
	$fJquery.= '});';

	//fonction qui chaque fois que se referme un groupe de l'accordion, calcule le nombre de groupes resté souverts (0 ou 1).
	//si 0, le dit à la variable de session _APP_DROITS_GROUPE_DEPLOYE_ via ajax pour lui envoyer 0
	$fJquery.= '$(\'#accordionDroits\').on(\'hidden.bs.collapse\', function () {';
	$fJquery.= '  nbOuverts = $(\'[aria-expanded="true"]\').length;';
	$fJquery.= '  if (nbOuverts == 0) {';
	$fJquery.= '    $.ajax({';											//on utilise ici l'objet ajax de Jquery
	$fJquery.= '		url : \'reponses-ajax.php\',';					//la ressource ciblée
	$fJquery.= '		type : \'GET\',';								//le type de la requête HTTP.
	$fJquery.= '		data : \'f=uw_SetGroupeDroitsDeploye&id=0\'';	//paramètres de la ressource ciblée (le groupe déployé : ici aucun)
	$fJquery.= '	});';
	$fJquery.= '  };';
	$fJquery.= '});';

	//lorsque on clique sur un titre de groupe pour en changer le libellé (ou la poubelle de 
	//suppression de groupe) il faut stopper la propagation du click pour empécher l'accordion de se déployer
	//on stoppe donc la propagation sur toutes les classes "forget"
	$fJquery.= '$(\'.forget\').on(\'click\', function(e){';
	$fJquery.= '  e.stopPropagation();';
	$fJquery.= '});';
}

//swap des droits
$fJquery.= '$(\'.droit\').on(\'click\', function() {';
$fJquery.= '  var droit = this.id;';
$fJquery.= '  var save = $("#" + droit).html();';					//sauvegarde du html actuel pour le droit
$fJquery.= '  $.ajax({';											//on utilise ici l'objet ajax de Jquery
$fJquery.= '	url : \'reponses-ajax.php\',';						//la ressource ciblée
$fJquery.= '	type : \'GET\',';									//le type de la requête HTTP.
$fJquery.= '	data : \'f=uw_SetDroit&id=\'+droit,';				//paramètres de la ressource ciblée (le droit cliqué)
$fJquery.= '	success : function(retour, statut){';				//callback fait en cas de succès
$fJquery.= '		if (retour == \'-1\') {';						//erreur -> réaffichage de l'icone sauvegardée
$fJquery.= '			$("#" + droit).html(save);';
$fJquery.= '		}';
$fJquery.= '		else if (retour == \'-2\') {';					//erreur -> affichage d'un message d'alerte + réaffichage de l'icone sauvegardée
$fJquery.= '			message = \'<div class="row">\';';
$fJquery.= '			message+= \'<div class="col">\';';
$fJquery.= '			message+= \'<div class="alert alert-warning alert-dismissible" role="alert">\';';
$fJquery.= '			message+= \'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\';';
$fJquery.= '			message+= \''.getLib('MODIF_ADMIN_INTERDITE').'\';';
$fJquery.= '			message+= \'</div>\';';
$fJquery.= '			message+= \'</div>\';';
$fJquery.= '			message+= \'</div>\';';
$fJquery.= '			$("#section_messsage").html(message);';
$fJquery.= '			$("#" + droit).html(save);';				//retour erroné on réaffiche le html sauvegardé
$fJquery.= '		}';
$fJquery.= '		else {';										//Pas d'erreur -> affichage du nouveau html
$fJquery.= '			$("#" + droit).html(retour);';
$fJquery.= '		}';
$fJquery.= '	},';
$fJquery.= '	beforeSend : function(resultat, statut){';			//callback fait avant l'appel ajax (affichage du spinner)	
$fJquery.= '		$("#" + droit).html(\'<span class="fas fa-spinner fa-spin"></span>\');';
$fJquery.= '	}';
$fJquery.= '  });';
$fJquery.= '})';

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

				//titre du listing et bouton d'actions
				//--------------------------------------
				echo '<div class="d-flex flex-row align-items-center">';
					echo '<h1>'.getLib('DROITS').'</h1>';
					echo '<span class="ml-auto align-items-center">';
					//affichage d'un boutons d'opérations possibles
					if (_RUN_MODE_ == _DEVELOPPEMENT_) {
						echo '<div class="dropdown">';
							echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="operations" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
								echo getLib('OPERATIONS');
							echo '</button>';
							echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="operations">';
								echo '<a class="dropdown-item text-right" href="'._URL_ACTIONS_DROITS_.'?operation=addniv">'.getLib('AJOUTER_UN_PROFIL').'</a>';
								echo '<a class="dropdown-item text-right" href="'._URL_ACTIONS_DROITS_.'?operation=addfonc">'.getLib('AJOUTER_UNE_FONC').'</a>';
								if ($_SESSION[_APP_DROITS_]->getNotionGroupes()) {
									echo '<a class="dropdown-item text-right" href="'._URL_ACTIONS_DROITS_.'?operation=addgrp">'.getLib('GROUPE_AJOUTER').'</a>';
								}
							echo '</div>';
						echo '</div>';
					}
					echo '</span>';
				echo '</div>';

				//affichage du nombre de lignes trouvés
				//--------------------------------------
				SimpleListingHelper::drawTotal($nombreLignes);

				//affichage barre de navigation
				//--------------------------------------
				//echo $navigation;

				//affichage du tableau
				//--------------------------------------
				if ($_SESSION[_APP_DROITS_]->getNotionGroupes()) {
					$idPrecedent = 0;			//id groupe fonctionnalité précédent
					$expanded = 'false';		//paramètre expanded
					$show = '';					//parametre show
					$header = true;				//afficher entête de tableau ?
					$footer = false;			//afficher pied de tableau ?
					$javascript = 'onclick="return confirm(\''.addslashes(getLib('SUPPRIMER_CE_PROFIL_CERTAIN')).'\');"';

					//affichage de l'entete
					echo '<table width="100%">';
						echo '<thead class="align-bottom">';
						foreach($cols as $key => $colonne) {

							echo '<th scope="col" class="text-'.$colonne['align'].' '.$colonne['css'].'" width="'.$colonne['size'].'%">';
							
							if ($colonne['title'] != '') {
								echo '<span data-toggle="tooltip" title="'.$colonne['title'].'">';
							}

							if (_RUN_MODE_ == _DEVELOPPEMENT_) {
								//affichage des poubelles de suppression des profils
								if ((is_numeric($key)) && ($key != $_SESSION[_APP_DROITS_]->getIdProfil('PROFIL_ADMIN')))  {
									//proposition suppression des profils sauf celui d'ADMIN
									echo '<a href="'._URL_ACTIONS_DROITS_.'?operation=delniv&amp;do='.$key.'" '.$javascript.'>'; 
										echo '<span class="fas fa-trash pl-1" data-toggle="tooltip" title="'.getLib('SUPPRIMER_CE_PROFIL').'"></span>';
									echo '</a>';
									echo '<br />';
								}
							}

							echo $colonne['name'];

							if ($colonne['title'] != '') {
								echo '</span>';
							}

							echo '</th>';
						}
						echo '</thead>';
					echo '</table>';

					echo '<div class="accordion" id="accordionDroits">';

					//DEBUG_('listing', $listing);

					foreach($listing as $ligne) {
						if (($ligne['id_groupe_fonctionnalite'] == 1) && ($ligne['id_fonctionnalite'] == '')) {
							//il n'y a aucune fonctionnalité dans le groupe 1 "non classée" ... on passe au groupe suivant sans afficher
							continue;
						}
						//calcul du changement de front
						if ($ligne['id_groupe_fonctionnalite'] != $idPrecedent) {
							$header = true;
							$footer = ($idPrecedent != 0);
							$idPrecedent = $ligne['id_groupe_fonctionnalite'];
						}
						//affichage pied de tableau
						if ($footer) {
							echo '</tbody></table></div></div></div>';
							$footer = false;
						}
						//affichage entete de tableau
						if ($header) {
							if ($groupeDeploye == $ligne['id_groupe_fonctionnalite']) {
								$expanded = 'true';		//paramètre expanded
								$show = ' show';		//parametre show
							}
							//les groupes de fonctionnalités sont déplacables uniquement en mode developpement
							$draggable = (_RUN_MODE_ == _DEVELOPPEMENT_) ? ' draggable="true"' : '';

							echo '<div class="card border-0">';

								echo '<div class="dropper card-header border-top border-secondary pl-2 bg-white" id-groupe="'.$ligne['id_groupe_fonctionnalite'].'"'.$draggable.' data-toggle="collapse" data-target="#collapse'.$ligne['id_groupe_fonctionnalite'].'" aria-expanded="'.$expanded.'" aria-controls="collapse'.$ligne['id_groupe_fonctionnalite'].'" role="button">';
									if ((_RUN_MODE_ == _DEVELOPPEMENT_) && ($ligne['id_groupe_fonctionnalite'] != 1)) {
										//permettre la modification du libellé du groupe de fonctionnalités
										echo '<div class="d-flex justify-content-between">';
											echo '<a class="h4 forget" href="'._URL_ACTIONS_DROITS_.'?operation=rengrp&amp;do='.$ligne['id_groupe_fonctionnalite'].'">';
												echo $ligne['groupe'];
											echo '</a>';
											if ($ligne['id_groupe_fonctionnalite'] != 2) {
												//ajout de la poubelle de suppression sauf pour groupe "Administration" (id 2)
												$javascript = 'onclick="return confirm(\''.addslashes(getLib('GROUPE_SUPPR_CERTAIN')).'\');"';
												echo '<a class="forget" href="'._URL_ACTIONS_DROITS_.'?operation=delgrp&amp;do='.$ligne['id_groupe_fonctionnalite'].'" '.$javascript.'>';
													echo '<span class="fas fa-trash mr-3" data-toggle="tooltip" title="'.getLib('GROUPE_SUPPR').'"></span>';
												echo '</a>';
											}
										echo '</div>';
									}
									else {
										echo '<h4>'.$ligne['groupe'].'</h4>';
									}
								echo '</div>';

								echo '<div id="collapse'.$ligne['id_groupe_fonctionnalite'].'" class="collapse'.$show.'" aria-labelledby="heading'.$ligne['id_groupe_fonctionnalite'].'" data-parent="#accordionDroits">';
									echo '<div class="card-body px-0 py-0">';
										echo '<table class="table table-hover table-responsive-sm table-striped">';	//table-responsive table-striped table-sm 
											echo '<tbody>';
							//reinit données de gestion
							$expanded = 'false';
							$show = '';
							$header = false;
						}
						//affichage contenu de tableau
						if ($ligne['id_fonctionnalite'] != '') {
							($ligne['id_fonctionnalite'] == 1) ? $couleur = ' class="table-info"' : $couleur = '';
							echo '<tr'.$couleur.'>';
							foreach($cols as $indiceCol => $colonne) {
								echo '<td width="'.$colonne['size'].'%" align="'.$colonne['align'].'" title="'.$colonne['title'].'">';
									$fonction = 'Col_'.$indiceCol;
									if (function_exists($fonction)) {
										call_user_func($fonction, $ligne['code'], $ligne);
									}
									else Col_($indiceCol, $ligne['code']);
								echo '</td>';
							}
							echo '</tr>';
						}
						else {
							echo '<tr><td>'.getLib('GROUPE_FONCTIONNALITE_AUCUNE').'</td></tr>';
						}
					}
					//fermeture du dernier tableau
					if (count($listing) > 0) echo '</tbody></table></div></div></div>';

					echo '</div>';		//accordion
				}
				else {
					//affichage du tableau
					//ci-dessous le code pour afficher toutes les fonctionnalités sans notions de groupe dans un seul et unique tableau
					//--------------------------------------
					
					echo '<table class="table table-hover table-responsive-sm table-striped">';	//table-responsive table-striped table-sm 
						//affichage de l'entete
						SimpleListingHelper::drawHead($cols, $tri, $sens);
						//affichage du corps du tableau. Ici on utilise pas l'objet "SimpleListingHelper" car le type de données est différent (pas issu d'une requete SQL)
						echo '</tbody>';
						foreach($listing as $key => $ligne)	{
							if ($ligne['id_fonctionnalite'] != '') {
								($ligne['id_fonctionnalite'] == 1) ? $couleur = ' class="table-info"' : $couleur = '';
								echo '<tr'.$couleur.'>';
								foreach($cols as $indiceCol => $colonne) {
									echo '<td width="'.$colonne['size'].'%" align="'.$colonne['align'].'" title="'.$colonne['title'].'">';
										$fonction = 'Col_'.$indiceCol;
										if (function_exists($fonction)) {
											call_user_func($fonction, $ligne['code'], $ligne);
										}
										else Col_($indiceCol, $ligne['code']);
									echo '</td>';
								}
								echo '</tr>';
							}
						}
						ligneDelete();
						echo '</tbody>';
					echo'</table>';

					//affichage barre de navigation
					//--------------------------------------
					//echo $navigation;
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