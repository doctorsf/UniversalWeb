<?php
//--------------------------------------------------------------------------
// LISTING DES UTILISATEURS
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
//--------------------------------------------------------------------------
require_once('libs/common.inc.php');

//positionne la page de retour
setPageBack();

//gère l'accès au script
$operation = grantAcces() or die();

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
$cols[0] = SimpleListingHelper::createCol(array('name' => getLib('PROFIL'),			'size' => 10,	'tri' => 'profil'));
//$cols[1] = SimpleListingHelper::createCol(array('name' => getLib('ID'),				'size' => 15,	'tri' => 'id_user'));
$cols[2] = SimpleListingHelper::createCol(array('name' => getLib('NOM_PRENOM'),		'size' => 24,	'tri' => 'nom,prenom'));
$cols[3] = SimpleListingHelper::createCol(array('name' => getLib('EMAIL'),			'size' => 24,	'tri' => 'email'));
$cols[4] = SimpleListingHelper::createCol(array('name' => getLib('DATE_CREATION'),	'size' => 20,	'tri' => 'date_creation'));
$cols[5] = SimpleListingHelper::createCol(array('name' => getLib('DERNIER_ACCES'),	'size' => 20));
$cols[8] = SimpleListingHelper::createCol(array('name' => getLib('SUPPR'),			'size' => 2,	'align' => 'center'));

//-------------------------------------
// Ensemble des fonctions d'affichage du contenu des colonnes
//-------------------------------------
//fonctions d'affichage du contenu des colonnes
function Col_0($ligne, $page) {
	echo $ligne['libelle_profil'];
}
function Col_1($ligne, $page) {
	echo '<a href="'._URL_USER_.'?operation=consulter&amp;id='.$ligne['id_user'].'&amp;page='.$page.'">'.$ligne['id_user'].'</a>';
}
function Col_2($ligne, $page) {
	echo '<a href="'._URL_USER_.'?operation=consulter&amp;id='.$ligne['id_user'].'&amp;page='.$page.'">'.ucwords(utf8_strtolower($ligne['nom'].' '.$ligne['prenom'])).'</a>';
}
function Col_3($ligne, $page) {
	echo '<a href="mailto:'.$ligne['email'].'">'.$ligne['email'].'</a>';
}
function Col_4($ligne, $page) {
	echo $ligne['date_creation'];
}
function Col_5($ligne, $page) {
	echo $ligne['dernier_acces'];
}
function Col_8($ligne, $page) {
	if ($ligne['id_user'] != $_SESSION[_APP_LOGIN_]->getId()) {
		echo '<a href="'._URL_USER_.'?operation=supprimer&amp;id='.$ligne['id_user'].'&amp;page='.$page.'">';
		echo '<span class="fas fa-trash" title="'.getLib('SUPPRIMER_CET_UTILISATEUR').'"></span>';
		echo '</a>';
	}
}

//-------------------------------------
// Gestion du listing
// Le premier paramètre de la méthode statique getParams() est un tableau contenant 
//	les champs SQL sur lesquels on peut trier les colonnes
//-------------------------------------
SimpleListingHelper::getParams('4', $cols, $page, $tri, $sens);

//-------------------------------------
// Récupération des données à afficher
//-------------------------------------
$totalLignes = sqlUsers_getListingNombre();
//construction de la barre de navigation
$pn = new PageNavigator($totalLignes, 30, 20, $page);
$debut = $pn->getItemDebut();
$fin = $pn->getItemFin();
$pn->setPageOff();
$navigation = $pn->draw();
//lecture des articles
$nombreLignes = sqlUsers_getListing($tri, $sens, $debut, 30, _LG_, $listing);

//-------------------------------------
// Colorisation particulière des lignes à afficher
// On ajoute le champ 'line-color' aux données à afficher. Il contient la classe background-color de la ligne
//-------------------------------------
foreach($listing as $indice => $ligne)	{
	(!$ligne['active']) ? $listing[$indice]['line-color'] = 'table-info' : $listing[$indice]['line-color'] = '';
}

//-------------------------------------
// HTML
//-------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
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
	echo '<article class="container-xl">';

		echo '<div class="row">';
			echo '<div class="col p-0">';

				//titre du listing et bouton d'actions
				//--------------------------------------
				echo '<div class="d-flex flex-row align-items-center">';
					echo '<h1>'.getLib('UTILISATEURS').'</h1>';
					echo '<span class="ml-auto align-items-center">';
					//affichage d'un boutons d'opérations possibles
					echo '<div class="dropdown">';
						echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="operations" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
							echo getLib('OPERATIONS');
						echo '</button>';
						echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="operations">';
							echo '<a class="dropdown-item text-right" href="'._URL_USER_.'?operation=ajouter&amp;page='.$page.'">'.getLib('AJOUTER_UN_UTILISATEUR').'</a>';
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
				echo '<table class="table table-hover table-responsive table-striped">';	//table-responsive table-striped table-sm 
					//affichage de l'entete
					SimpleListingHelper::drawHead($cols, $tri, $sens);
					//affichage du corps du tableau : donnees
					SimpleListingHelper::drawBody($cols, $listing, $page);
				echo'</table>';

				//affichage légende
				//--------------------------------------
				echo '<div class="d-flex justify-content-between">';
					echo '<div>';
						echo '<p span class="mb-0 text-muted small">'.getLib('LEGENDE').' : </span>';
						echo '<span class="badge badge-unactived mr-1">'.getLib('COMPTE_DESACTIVE').'</span>';
					echo '</div>';
					if (_RUN_MODE_ == _DEVELOPPEMENT_) {
						echo '<p class="mb-0 text-right text-muted"><small>'.getLib('TAILLE_LISTING').' : '.SimpleListingHelper::getSize($cols).'%</small></p>';
					}
				echo '</div>';

				//affichage barre de navigation
				//--------------------------------------
				//echo $navigation;

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