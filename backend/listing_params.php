<?php
//--------------------------------------------------------------------------
// LISTING DES PARAMETRES APPLI
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
//--------------------------------------------------------------------------
require_once('libs/common.inc.php');

//gère l'accès au script
$operation = grantAcces() or die();

//réinitialise la page de retour
setPageBack();

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
$cols[1] = SimpleListingHelper::createCol(array('name' => getLib('ID'), 'size' => 5, 'align' => 'center', 'tri' => 'ordre'));			//noter : ici tri = 'ordre' !!!
//$cols[2] = SimpleListingHelper::createCol(array('name' => getLib('ORDRE'), 'size' => 0, 'align' => 'center', 'tri' => 'ordre'));
$cols[3] = SimpleListingHelper::createCol(array('name' => getLib('LIBELLE'), 'size' => 25));
$cols[4] = SimpleListingHelper::createCol(array('name' => getLib('VALEUR'), 'size' => 32));
$cols[5] = SimpleListingHelper::createCol(array('name' => getLib('COMMENTAIRE'), 'size' => 32));
$cols[6] = SimpleListingHelper::createCol(array('name' => '<span class="fas fa-user-cog"></span>', 'size' => 3, 'title' => getLib('PARAMETRE_REGLAGE')));
$cols[7] = SimpleListingHelper::createCol(array('name' => getLib('SUPPR'), 'size' => 3, 'align' => 'center'));

//-------------------------------------
// Ensemble des fonctions d'affichage du contenu des colonnes
//-------------------------------------

function Col_1($ligne, $page) {
	echo '<a href="'._URL_PARAM_.'?operation=consulter&amp;id='.$ligne['id'].'&amp;page='.$page.'">';
		echo '<span class="far fa-edit" data-toggle="tooltip" title="'.getLib('MODIFIER_PARAMETRE').'"></span>';
	echo '</a>';
}

function Col_2($ligne, $page) {
	echo $ligne['ordre'];
}

function Col_3($ligne, $page) {
	echo $ligne['parametre'];
}

function Col_4($ligne, $page) {
	if (($ligne['type'] == 'boolean') && ($ligne['min'] == '') && ($ligne['max'] == '')) {
		echo $ligne['valeur'];
		if ((bool)$ligne['valeur'] === true) echo ' (true)';
		elseif ((bool)$ligne['valeur'] === false) echo ' (false)';
	}
	else echo $ligne['valeur'];
}

function Col_5($ligne, $page) {
	if (existeLib($ligne['comment'])) {
		echo getLib($ligne['comment']);
	}
	else {
		echo $ligne['comment'];
	}
}

function Col_6($ligne, $page) {
	if ($ligne['reglable']) echo '<span class="fas fa-user-cog"></span>';
}

function Col_7($ligne, $page) {
	echo '<a href="'._URL_PARAM_.'?operation=supprimer&amp;id='.$ligne['id'].'&amp;page='.$page.'">';
		echo '<span class="fas fa-trash" data-toggle="tooltip" title="'.getLib('SUPPRIMER_PARAMETRE').'"></span>';
	echo '</a>';
}

//-------------------------------------
// Gestion du listing
// Le premier paramètre de la méthode statique getParams() est un tableau contenant 
//	les champs SQL sur lesquels on peut trier les colonnes
//-------------------------------------
SimpleListingHelper::getParams('1', $cols, $page, $tri, $sens);

//-------------------------------------
// Récupération des données à afficher
//-------------------------------------
$table = new SqlParams();
$totalLignes = $table->getListeNombre();
//construction de la barre de navigation
$pn = new PageNavigator($totalLignes, 30, 20, $page);
$debut = $pn->getItemDebut();
$fin = $pn->getItemFin();
$pn->setPageOff();
$navigation = $pn->draw();
//lecture des articles
$nombreLignes = $table->getListe($tri, $sens, $debut, 30, $listing);

//-------------------------------------
// Ajout d'informations permettant le Drag & Drop des lignes
//-------------------------------------
foreach($listing as $indice => $ligne) {
	$listing[$indice]['line-droppable'] = true;
	$listing[$indice]['line-draggable'] = true;
	$listing[$indice]['line-info'] = $indice + 1;
}

//-------------------------------------
// HTML
//-------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
$scriptSup.= '<script>';
$scriptSup.= 'var dragImg = new Image();';
$scriptSup.= 'dragImg.src = "'._IMAGES_COMMUN_.'cog.png";';
$scriptSup.= '';
$scriptSup.= 'var draggers = document.querySelectorAll(\'*[draggable="true"]\');';	//définition des draggers (objets que l'on peut déplacer (DRAG))
$scriptSup.= 'var droppers = document.querySelectorAll(\'*[dropper="true"]\');';	//définition des droppers (objets sur lesquels on peut poser (DROP))

//position des évènements écoutés sur les draggers
$scriptSup.= '[].forEach.call(draggers, function(dragger) {';							
$scriptSup.= '  dragger.addEventListener("dragstart", draggerStart, false);';
$scriptSup.= '  dragger.addEventListener("dragend", draggerEnd, false);';
$scriptSup.= '});';

//débute le DRAG
$scriptSup.= 'function draggerStart(e) {';
//$scriptSup.= '	console.log(e);';
$scriptSup.= '	var source = this.getAttribute("info");';					//recupere l'attribut info qui contient l'ordre actuel de la ligne de paramètre draggée
$scriptSup.= '  this.style.opacity = "0.4";';
$scriptSup.= '  e.dataTransfer.setDragImage(dragImg, 20, 20);';
$scriptSup.= '  e.dataTransfer.setData("text/plain", source);';				//envoi source dans un format text
$scriptSup.= '}';

//termine le DRAG
$scriptSup.= 'function draggerEnd(e) {';
$scriptSup.= '  this.style.opacity = "1";';
$scriptSup.= '}';

//définition des evenements possible pour les droppers
$scriptSup.= '[].forEach.call(droppers, function(dropper) {';
$scriptSup.= '	dropper.addEventListener("dragover", dropperOver, false);';
$scriptSup.= '  dropper.addEventListener("dragenter", dropperEnter, false);';
$scriptSup.= '  dropper.addEventListener("dragleave", dropperLeave, false);';
$scriptSup.= '  dropper.addEventListener("drop", dropperDrop, false);';
$scriptSup.= '});';

//déplacement au-dessus d'un dropper
$scriptSup.= 'function dropperOver(e) {';
$scriptSup.= '	e.preventDefault();';
$scriptSup.= '  this.classList.remove("bg-white");';
$scriptSup.= '  this.classList.add("bg-info");';
$scriptSup.= '}';

//entrée dans la zone d'un dropper
$scriptSup.= 'function dropperEnter(e) {';
$scriptSup.= '	e.preventDefault();';
$scriptSup.= '  this.classList.remove("bg-white");';
$scriptSup.= '  this.classList.add("bg-info");';
$scriptSup.= '}';

//sortie de la zone d'un dropper
$scriptSup.= 'function dropperLeave(e) {';
$scriptSup.= '	e.preventDefault();';
$scriptSup.= '  this.classList.remove("bg-info");';
$scriptSup.= '  this.classList.add("bg-white");';
$scriptSup.= '}';

//DROP
$scriptSup.= 'function dropperDrop(e) {';
$scriptSup.= '	e.preventDefault();';
$scriptSup.= '  this.classList.remove("bg-info");';
$scriptSup.= '  this.classList.add("bg-white");';
$scriptSup.= '	var cible = this.getAttribute("info");';					//récupere l'ordre cible (droppé)
$scriptSup.= '  var source = e.dataTransfer.getData("text/plain");';		//recupération de la source du draggerStart
$scriptSup.= '	if ((cible != source) && (cible != (source - 1))) {';		//si source = cible ou si source juste en dessous de la cible c'est déjà la cas (on fait rien)
$scriptSup.= '		dummy = uw_paramSort(source, cible);';					//action AJAX
$scriptSup.= '	}';
$scriptSup.= '}';

$scriptSup.= '</script>';
$fJquery = '';
echo writeHTMLHeader($titrePage, '', '');

echo '<body>';
	echo '<div class="container-fluid">';

	//-------------------------------------
	// HEADER
	//-------------------------------------
	include_once(_BRIQUE_HEADER_);

	//-------------------------------------
	// CORPS
	//-------------------------------------
	echo '<section>';
	echo '<article class="container-xl">';

		echo '<div class="row">';
			echo '<div class="col p-0">';

				//titre du listing et bouton d'actions
				//--------------------------------------
				echo '<div class="d-flex flex-row align-items-center">';
					echo '<h1>'.getLib('PARAMETRES').'</h1>';
					echo '<span class="ml-auto align-items-center">';
					//affichage d'un boutons d'opérations possibles
					echo '<div class="dropdown">';
						echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="operations" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
							echo getLib('OPERATIONS');
						echo '</button>';
						echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="operations">';
							echo '<a class="dropdown-item text-right" href="'._URL_PARAM_.'?operation=ajouter&amp;page='.$page.'">'.getLib('AJOUTER_PARAMETRE').'</a>';
						echo '</div>';
					echo '</div>';
					echo '</span>';
				echo '</div>';

				//affichage du nombre de lignes trouvés
				//--------------------------------------
				SimpleListingHelper::drawTotal($nombreLignes);

				//affichage barre de navigation
				//--------------------------------------
				echo $navigation;

				//affichage du tableau
				//--------------------------------------
				echo '<table class="table table-responsive-lg table-hover table-striped">';	//table-responsive table-striped table-sm 
					//affichage de l'entete
					SimpleListingHelper::drawHead($cols, $tri, $sens);
					//affichage du corps du tableau : donnees
					SimpleListingHelper::drawBody($cols, $listing, $page);
				echo'</table>';

				//affichage barre de navigation
				//--------------------------------------
				echo $navigation;

			echo '</div>';
		echo '</div>';

	echo '</article>';
	echo '</section>';

	//--------------------------------------
	// FOOTER
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);

	echo '</div>';
echo '</body>';
echo '</html>';