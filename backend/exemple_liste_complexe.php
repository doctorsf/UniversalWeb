<?php
//--------------------------------------------------------------------------
// LISTING COMPLEXE DES FILMS
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
//--------------------------------------------------------------------------
require_once('libs/common.inc.php');

/*
//----------------------------------------
// fonction permettant de récupérer le nombre de lignes
// ramenée par la liste au regard des filtres aposés par l'utilisateur.
// Bien que ce soit possible, il est toutefois préférable que ces
// fonctionnalités soient confiées à la classe qui hérite de UniversalField, 
// soit les méthodes getListe() et getListeNombre(). Voir la documentation à ce titre.
// Entrée :
//		$id_listing : id de notre liste
// Retour :
//		le nombre de lignes
//----------------------------------------
function getListeNombre($id_listing) {
	$requete = "SELECT count(*) nombre ";
	$requete.= "FROM films ";
	$requete.= "WHERE 1 ";
	$requete.= $_SESSION[$id_listing]->buildFiltres();
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		if ($nombre != 0) {
			return $res[0]['nombre'];
		}
		else return $nombre;
	}
	return false;
}

//----------------------------------------
// fonction permettant de récupérer les lignes
// ramenée par la liste au regard des filtres aposés par l'utilisateur.
// Bien que ce soit possible, il est toutefois préférable que ces
// fonctionnalités soient confiées à la classe qui hérite de UniversalField, 
// soit les méthodes getListe() et getListeNombre(). Voir la documentation à ce titre.
// Entrée :
//		$id_listing : id de notre liste
//		$start : position de notre première ligne de données à afficher en haut de la page
//		$nb_lignes : nombre de lignes à afficher sur la page
//		$laListe : liste en retour des tuples récupérés
// Retour :
//		le nombre de lignes à afficher
//----------------------------------------
function getListe($id_listing, $start, $nb_lignes, &$laListe) {
	$requete = "SELECT titre, annee, realisateur, genre ";
	$requete.= "FROM films ";
	$requete.= "WHERE 1 ";
	$requete.= $_SESSION[$id_listing]->buildFiltres();
	$requete.= "ORDER BY ".$_SESSION[$id_listing]->buildTris()." ";
	if ($nb_lignes != 0)
		$requete.= "LIMIT ".$start.", ".$nb_lignes;
	$laListe = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($laListe !== false) {
		return $nombre;
	}
	$laListe = null;
	return false;
}
*/

//-------------------------------------
//Id unique du listing
//-------------------------------------
defined('_ID_LISTING_') || define('_ID_LISTING_', 'lstFilms');

//-------------------------------------
// Construction du listing
// unset($_SESSION[_ID_LISTING_]);
//-------------------------------------
if  ((!isset($_SESSION[_ID_LISTING_])) || ($_SESSION[_ID_LISTING_] == null)) {
	$_SESSION[_ID_LISTING_] = new Exemple_listing_films();
	$_SESSION[_ID_LISTING_]->setNbLinesParPage(5);
	//on cache les boutons OK et RAZ du formulaire
	$_SESSION[_ID_LISTING_]->showFormButtons(false);
	//on souhaite trier par défaut sur la colonne année de la plus grande à la plus petite
	//$_SESSION[_ID_LISTING_]->setTriEncours('annee');
	//$_SESSION[_ID_LISTING_]->setTriSensEncours('DESC');
	//modification de la classe CSS pour l'entete de la liste
	$_SESSION[_ID_LISTING_]->setHeadClass('bg-warning table-sm');		//table-bordered
	//modification de la classe CSS pour le bandeau de filtres de la liste
	$_SESSION[_ID_LISTING_]->setFiltresClass('bg-warning');				//table-bordered
}

//-------------------------------------
// Sauvegarde ou chargement du listing
//-------------------------------------
//$retour = $_SESSION[_ID_LISTING_]->saveList('Sauve la liste', 'fabrice', 'lstFilms');

//-------------------------------------
// Gestion du listing
//-------------------------------------
//DEBUG_('nombre de pages', $_SESSION[_ID_LISTING_]->getNbPages());
$_SESSION[_ID_LISTING_]->getParams();

// Traitement spécifique pour l'abécédaire
//-------------------------------------
(isset($_GET['do'])) ? $do = MySQLDataProtect($_GET['do']) : $do = 'aucun';
if ($do != 'aucun') {
	if ($do == 'tous') {
		//commence par un chiffre
		$_SESSION[_ID_LISTING_]->getFiltreExterne('alpha')->setFiltreRange(UniversalListColonne::TOUT);
		$_SESSION[_ID_LISTING_]->getFiltreExterne('alpha')->setFiltreValue($do);
	}
	elseif ($do == '0') {
		//commence par un chiffre
		$_SESSION[_ID_LISTING_]->getFiltreExterne('alpha')->setFiltreRange(UniversalListColonne::COMMENCENUM);
		$_SESSION[_ID_LISTING_]->getFiltreExterne('alpha')->setFiltreValue($do);
	}
	else {
		//commence par une lettre
		$_SESSION[_ID_LISTING_]->getFiltreExterne('alpha')->setFiltreRange(UniversalListColonne::COMMENCE);
		$_SESSION[_ID_LISTING_]->getFiltreExterne('alpha')->setFiltreValue($do);
	}
}

//-------------------------------------
// Récupération des données à afficher
//-------------------------------------
//lecture du nombre total de lignes pour le listing
$totalLignes = $_SESSION[_ID_LISTING_]->getData($listing);
//ou
//$totalLignes = getListeNombre(_ID_LISTING_);

//construction de la barre de navigation
$pn = new PageNavigator($totalLignes, $_SESSION[_ID_LISTING_]->getNbLinesParPage(), 2, $_SESSION[_ID_LISTING_]->getPageEncours());
//$debut = $pn->getItemDebut();
//$fin = $pn->getItemFin();
$pn->setPageOff();
$navigation = $pn->draw();

//DEBUG_('listing', $listing);

//et
///$nbLignes = getListe(_ID_LISTING_, $debut, $_SESSION[_ID_LISTING_]->getNbLinesParPage(), $listing);

//-------------------------------------
// Colorisation particulière des lignes à afficher
// On ajoute le champ 'line-color' aux données à afficher. Il contient la classe background-color de la ligne
//-------------------------------------
//foreach($listing as $indice => $ligne)	{
//	($ligne['deleted']) ? $listing[$indice]['line-color'] = 'table-danger' : $listing[$indice]['line-color'] = '';
//}

//---------------------------------------------------------
// head
// Script resizable-table : https://www.brainbell.com/javascript/making-resizable-table-js.html
//---------------------------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
$scriptSup.= '<script>';
$scriptSup.= 'var table = document.getElementsByTagName(\'table\')[0];';
//$scriptSup.= 'var table = document.getElementById(\'tableId\');';
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
	echo '<article class="mt-5">';

		echo '<div class="row">';
			echo '<div class="col">';
				echo '<p class="h1">Liste complexe</p>';

				echo '<div class="row">';

					//-----------------------------------------
					//Code personnalisé de la page commence ICI
					//-----------------------------------------

					//--------------------------------------
					// Bloc ABCDAIRE
					//--------------------------------------
					echo '<div class="col-12">';

						echo '<p class="h3">';
						//affichage menu
						//recupération de la valeur du filtre 'alpha' en cours
						$lettre = $_SESSION[_ID_LISTING_]->getFiltreExterne('alpha')->getValue();

						//affichage
						//TOUS
						($lettre == 'tous') ? $chaine='<span class="font-weight-bold bg-primary text-white mr-1 px-1">TOUS</span>' : $chaine = '<span class="mr-1 px-1">TOUS</span>';
						echo '<a class="d-inline-block" href="'.$_SERVER['PHP_SELF'].'?operation=filtreAlpha&do=tous">'.$chaine.'</a>';
						//LETTRES ALPHABET
						for ($i='A'; $i!='AA'; $i++) {
							($lettre == $i) ? $chaine='<span class="font-weight-bold bg-primary text-white mr-1 px-1">'.$i.'</span>' : $chaine = '<span class="mr-1 px-1">'.$i.'</span>';
							echo '<a class="d-inline-block" href="'.$_SERVER['PHP_SELF'].'?operation=filtreAlpha&do='.$i.'">'.$chaine.'</a>';
						}
						//CHIFFRES
						($lettre == '0') ? $chaine='<span class="font-weight-bold bg-primary text-white px-1">#</span>' : $chaine = '<span class="px-1">#</span>';
						echo '<a class="d-inline-block" href="'.$_SERVER['PHP_SELF'].'?operation=filtreAlpha&do=0">'.$chaine.'</a>';
						echo '</p>';

					echo '</div>';

					//--------------------------------------
					// Filtre externe recherche multiple
					//--------------------------------------
					echo '<div class="col-12 col-sm-4">';
						echo $_SESSION[_ID_LISTING_]->getFiltreExterne('recherche')->afficher();
					echo '</div>';

					//--------------------------------------
					// Filtre checkbox
					//--------------------------------------
					echo '<div class="col-12 col-sm-4">';
						echo $_SESSION[_ID_LISTING_]->getFiltreExterne('datation')->afficher();
					echo '</div>';

					//--------------------------------------
					// Filtre externe recherche simple
					//--------------------------------------
					echo '<div class="col-12 col-sm-4">';
						echo $_SESSION[_ID_LISTING_]->getFiltreExterne('simple')->afficher();
					echo '</div>';

					//--------------------------------------
					// Affichage de la liste
					//--------------------------------------
					echo '<div class="col-12">';

						//affichage du nombre de lignes trouvés
						//--------------------------------------
						SimpleListingHelper::drawTotal($totalLignes);
						echo ' / '.$_SESSION[_ID_LISTING_]->getTriSensEncoursLibelle().' '.$_SESSION[_ID_LISTING_]->getTriEncoursLibelle();
				
						//affichage barre de navigation
						//--------------------------------------
						echo $navigation;

						//affichage du tableau
						//--------------------------------------
						echo '<table id="tableId" class="table table-hover table-striped">';	//table-responsive table-striped table-sm 
							//affichage des filtres en entête du tableau
							$_SESSION[_ID_LISTING_]->drawFiltresColonnes();
							//affichage de l'entête du tableau
							$_SESSION[_ID_LISTING_]->drawHead();
							//affichage du corps du tableau : donnees
							$_SESSION[_ID_LISTING_]->drawBody($listing);
						echo '</table>';

						//affichage de la taille totale des colonnes visibles en %
						echo '<p class="small text-right">Cols: '.$_SESSION[_ID_LISTING_]->getDisplayedSize().'/100</p>';

					echo '</div>';  //col

				echo '</div>';	//row

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