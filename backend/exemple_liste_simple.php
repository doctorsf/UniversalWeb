<?php
//-----------------------------------------------------------
// LISTING SIMPLE MONOPAGE
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
require_once('libs/common.inc.php');

function getListe($tri, $sens, &$laListe) {
	$laListe = array();
	$requete = "SELECT titre, annee, realisateur, visuel, genre ";
	$requete.= "FROM films ";
	$requete.= "ORDER BY ".$tri." ".$sens." ";
	$laListe = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($laListe !== false) {
		return $nombre;
	}
	$laListe = null;
	return false;
}


$cols['titre'] = SimpleListingHelper::createCol(array('name' => 'Titre', 'size' => 40, 'tri' => 'titre', 'header' => true));
$cols['annee'] = SimpleListingHelper::createCol(array('name' => 'Année', 'size' => 10, 'align' => 'center', 'tri' => 'annee'));
$cols['real'] = SimpleListingHelper::createCol(array('name' => 'Réalisateur', 'size' => 20,	'title' => 'Nom du réalisateur'));
$cols['visuel'] = SimpleListingHelper::createCol(array('name' => '<span class="fas fa-tv"></span>', 'size' => 10, 'align' => 'center'));
$cols['genre'] = SimpleListingHelper::createCol(array('name' => 'Genre', 'size' => 20, 'tri' => 'genre'));

//-------------------------------------
// Ensemble des fonctions d'affichage du contenu des colonnes
//-------------------------------------
function Col_titre($donnee, $page) {
	echo $donnee['titre'];
}
function Col_annee($donnee, $page) {
	echo $donnee['annee'];
}
function Col_real($donnee, $page) {
	echo $donnee['realisateur'];
}
function Col_visuel($donnee, $page) {
	echo $donnee['visuel'];
}
function Col_genre($donnee, $page) {
	echo $donnee['genre'];
}

//-------------------------------------
// Gestion du listing
// Le premier paramètre de la méthode statique getParams() est un tableau contenant 
// les colonnes sur lesquelles on peut trier les données.
// La colonne triée par défaut est ici 'titre' puisque c'est la première colonne de ce tableau
//-------------------------------------
SimpleListingHelper::getParams('titre', $cols, $page, $tri, $sens);

//-------------------------------------
// Récupération des données à afficher
//-------------------------------------
$totalLignes = getListe($tri, $sens, $donnees) ;

//---------------------------------------------------------
// head
//---------------------------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
$scriptSup.= '<script>';
//$scriptSup.= 'var table = document.getElementsByTagName(\'table\')[0];';
$scriptSup.= 'var table = document.getElementById(\'tableId\');';
$scriptSup.= 'resizableGrid(table);';
$scriptSup.= '</script>';
$fJquery = '';
echo writeHTMLHeader($titrePage, '', '');

//---------------------------------------------------------
// body
//---------------------------------------------------------
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
			echo '<div class="col-12">';
				echo '<p class="h1">Liste simple</p>';

				//affichage du nombre de lignes trouvés
				//--------------------------------------
				SimpleListingHelper::drawTotal($totalLignes);

				//affichage du tableau
				//--------------------------------------
				echo '<table id="tableId" class="table table-hover table-striped">';	//table-responsive table-striped table-sm 
					//affichage de l'entete
					SimpleListingHelper::drawHead($cols, $tri, $sens, 'bg-light');
					//affichage du corps du tableau : donnees
					SimpleListingHelper::drawBody($cols, $donnees, $page);
				echo'</table>';

			echo '</div>';  //col
		echo '</div>';	//row

	echo '</article>';
	echo '</section>';

	//--------------------------------------
	// FOOTER
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);

	echo '</div>';		//container
echo '</body>';
echo '</html>';