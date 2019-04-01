<?php
//--------------------------------------------------------------------------
// LISTING DES LOGS
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
//--------------------------------------------------------------------------
require_once('libs/common.inc.php');

//gère l'accès au script
$operation = grantAcces() or die();

//sauvegarde de la page de retour
setPageBack();

//-------------------------------------
//Id unique du listing
//-------------------------------------
defined('_ID_LISTING_') || define('_ID_LISTING_', 'flogs');

//-------------------------------------
// Construction du listing
// unset($_SESSION[_ID_LISTING_]);
//-------------------------------------
if  ((!isset($_SESSION[_ID_LISTING_])) || ($_SESSION[_ID_LISTING_] == null)) {
	$_SESSION[_ID_LISTING_] = new Listing_logs();
	$_SESSION[_ID_LISTING_]->setNbLinesParPage(25);
	$_SESSION[_ID_LISTING_]->showFormButtons(false);
	//par défaut on filtre sur la date de la plus récente à la plus ancienne
	$_SESSION[_ID_LISTING_]->setTriEncours('quand');
	$_SESSION[_ID_LISTING_]->setTriSensEncours('DESC');
}

//-------------------------------------
// Gestion du listing
//-------------------------------------
$_SESSION[_ID_LISTING_]->getParams();

//-------------------------------------
// Récupération des données à afficher
//-------------------------------------
//recherche du nombre de lignes à ramener
$totalLignes = $_SESSION[_ID_LISTING_]->getData($listing);

//construction de la barre de navigation
$pn = new PageNavigator($totalLignes, $_SESSION[_ID_LISTING_]->getNbLinesParPage(), 10, $_SESSION[_ID_LISTING_]->getPageEncours());
$pn->setPageOff();
$navigation = $pn->draw();

//-------------------------------------
// HTML
//-------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
$fJquery = '';
echo writeHtmlHeader($titrePage, '', '');

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
	echo '<article>';

		//--------------------------------------
		// TABLEAU
		//--------------------------------------
		echo '<div class="row">';
			echo '<div class="col">';

				//titre du listing et bouton d'actions
				//--------------------------------------
				echo '<div class="d-flex flex-row align-items-center">';
					echo '<h1 class="display-4">'.getLib('LOGS').'</h1>';
					echo '<span class="ml-auto align-items-center">';
					//affichage d'un boutons d'opérations possibles
					echo '<div class="dropdown">';
						echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="operations" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
							echo getLib('OPERATIONS');
						echo '</button>';
						echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="operations">';
							echo '<a class="dropdown-item text-right" href="'._URL_MAINTENANCE_.'?operation=epurelog">'.getLib('LOGS_PURGER').'</a>';
							echo '<a class="dropdown-item text-right" href="'._URL_MAINTENANCE_.'?operation=purgelog">'.getLib('LOGS_VIDER').'</a>';
						echo '</div>';
					echo '</div>';
					echo '</span>';
				echo '</div>';

				//affichage du nombre de lignes trouvés
				//--------------------------------------
				SimpleListingHelper::drawTotal($totalLignes);
				echo ' / '.$_SESSION[_ID_LISTING_]->getTriSensEncoursLibelle().' '.$_SESSION[_ID_LISTING_]->getTriEncoursLibelle();
		
				//affichage barre de navigation
				//--------------------------------------
				echo $navigation;

				//affichage du tableau
				//--------------------------------------
				echo '<table class="table table-hover">';			//table-responsive table-bordered table-striped table-sm  
					//affichage des filtres en entête du tableau
					$_SESSION[_ID_LISTING_]->drawFiltresColonnes();
					//affichage de l'entête du tableau
					$_SESSION[_ID_LISTING_]->drawHead();
					//affichage du corps du tableau : donnees
					$_SESSION[_ID_LISTING_]->drawBody($listing);
				echo '</table>';

				//réaffichage de la navigation en bas de page
				//--------------------------------------
				echo $navigation;
				
			echo '</div>'; //col
		echo '</div>';  //row tableau

	echo '</article>';
	echo '</section>';

	//--------------------------------------
	// FOOTER
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);

	echo '</div>';  //container-fluid
echo '</body>';
echo '</html>';