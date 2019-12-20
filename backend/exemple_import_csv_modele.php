<?php
//-----------------------------------------------------------
// AFFICHAGE DU MODELE D'IMPORTATION DU FICHIER
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//on vérifie que ce script ne soit lancé (inclus) QUE par le script exemple_import_csv.php
if (getScriptName() != 'exemple_import_csv.php') {
	$leMessage = 'Erreur commande&hellip;';
	include_once(_BRIQUE_ERREUR_);
	die();
}

function getListe($tri, $sens, &$laListe) {
	$csv = new Exemple_csvimport();
	$csv->buildModele();
	$laListe = $csv->getModele();
	return count($laListe);
}

$cols['colonne'] = SimpleListingHelper::createCol(array('name' => 'N° Colonne', 'size' => 10, 'align' => 'center'));
$cols['champ'] = SimpleListingHelper::createCol(array('name' => 'Information', 'size' => 20));
$cols['attendu'] = SimpleListingHelper::createCol(array('name' => 'Saisie attendue', 'size' => 70));

//-------------------------------------
// Ensemble des fonctions d'affichage du contenu des colonnes
//-------------------------------------
function Col_colonne($donnee, $page) {
	echo $donnee['colonne'];
}
function Col_champ($donnee, $page) {
	echo $donnee['libelle'];
}
function Col_attendu($donnee, $page) {
	if ($donnee['css'] != '') {
		echo '<span class="'.$donnee['css'].'">'.$donnee['commentaire'].'</span>';
	}
	else echo $donnee['commentaire'];
}

//-------------------------------------
// Gestion du listing
// Le premier paramètre de la méthode statique getParams() est un tableau contenant 
// les colonnes sur lesquelles on peut trier les données.
// La colonne triée par défaut est ici 'titre' puisque c'est la première colonne de ce tableau
//-------------------------------------
SimpleListingHelper::getParams('colonne', $cols, $page, $tri, $sens);

//-------------------------------------
// Récupération des données à afficher
//-------------------------------------
$totalLignes = getListe($tri, $sens, $donnees) ;

//affichage du nombre de lignes trouvés
//--------------------------------------
echo '<div class="container-lg px-0 mt-5">';
echo '<p class="lead">Structure du fichier d\'import CSV attendue</p>';

//affichage du tableau
//--------------------------------------
echo '<table class="table table-hover table-striped table-responsive">';	// table-striped table-sm 
	//affichage de l'entete
	SimpleListingHelper::drawHead($cols, $tri, $sens);
	//affichage du corps du tableau : donnees
	SimpleListingHelper::drawBody($cols, $donnees, $page);
echo '</table>';
echo '</div>';