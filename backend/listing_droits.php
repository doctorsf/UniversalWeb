<?php
//--------------------------------------------------------------------------
// LISTINGS DROITS D'ADMINISTRATION
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8		
// version : 23.10.2017
// 06.02.2018 : Petite correction bug
//--------------------------------------------------------------------------
require_once('libs/common.inc.php');

//sauvegarde de la page de retour
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
$cols['fonction'] = SimpleListingHelper::createCol(array('name' => getLib('FONCTIONNALITE'), 'size' => 34));
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
			$texte = '<a href="'._URL_ACTIONS_DROITS_.'?operation=renfoncid&do='.$fonctionnalite['id_fonctionnalite'].'">';
				$texte.= '<span data-toggle="tooltip" title="'.getLib('DROITS_MODIF_ID_FONC').'">'.$fonctionnalite['id_fonctionnalite'].'</span>';
			$texte.= '</a>';

			$texte.= ' - ';

			$texte.= '<a href="'._URL_ACTIONS_DROITS_.'?operation=renfonc&do='.$fonctionnalite['id_fonctionnalite'].'">';
				$texte.= '<span data-toggle="tooltip" title="'.getLib('DROITS_MODIF_LIB_FONC').'">'.$fonctionnalite['libelle'].'</span>';
			$texte.= '</a>';

			$texte.= '&nbsp;';

			$texte.= '(<a href="'._URL_ACTIONS_DROITS_.'?operation=renfonccode&do='.$fonctionnalite['id_fonctionnalite'].'">';
				$texte.= '<span data-toggle="tooltip" title="'.getLib('DROITS_MODIF_CODE_FONC').'">'.$fonctionnalite['code'].'</span>';
			$texte.= '</a>)';
			echo $texte;
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
		if ($_SESSION[_APP_DROITS_]->accesAutorise($codeFonctionnalite, $profil)) {
			(($id == 1) && ($profil == 1)) ? $classe='admin' : $classe='valide';
			echo '<a class="'.$classe.'" href="'._URL_ACTIONS_DROITS_.'?operation=droits&amp;do='.$id.'_'.$profil.'_0">';
			echo '<span class="text-success fas fa-check"></span>';
			echo '</a>';
		}
		else {
			echo '<a href="'._URL_ACTIONS_DROITS_.'?operation=droits&amp;do='.$id.'_'.$profil.'_1"><span class="text-danger fas fa-ban"></a>'; 
		}
	}
	else echo '<a href="'._URL_ACTIONS_DROITS_.'?operation=droits&amp;do='.$id.'_'.$profil.'_1"><span class="fas fa-question"></span></a>'; 
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
//On va travailler sur les fonctionnalités de l'application
$listing = $_SESSION[_APP_DROITS_]->fonctionnalite();
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

				//titre du listing et bouton d'actions
				//--------------------------------------
				echo '<div class="d-flex flex-row align-items-center">';
					echo '<h1 class="display-4">'.getLib('DROITS').'</h1>';
					echo '<span class="ml-auto align-items-center">';
					//affichage d'un boutons d'opérations possibles
					echo '<div class="dropdown">';
						echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="operations" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
							echo getLib('OPERATIONS');
						echo '</button>';
						echo '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="operations">';
							if (_RUN_MODE_ == _DEVELOPPEMENT_) {
								echo '<a class="dropdown-item text-right" href="'._URL_ACTIONS_DROITS_.'?operation=addniv">'.getLib('AJOUTER_UN_PROFIL').'</a>';
								echo '<a class="dropdown-item text-right" href="'._URL_ACTIONS_DROITS_.'?operation=addfonc">'.getLib('AJOUTER_UNE_FONC').'</a>';
								echo '<div class="dropdown-divider"></div>';
							}
							echo '<a class="dropdown-item text-right" href="'._URL_ACTIONS_DROITS_.'?operation=export">'.getLib('EXPORTER_LES_DROITS').'</a>';
							echo '<a class="dropdown-item text-right" href="'._URL_ACTIONS_DROITS_.'?operation=import">'.getLib('IMPORTER_LES_DROITS').'</a>';
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
					//affichage du corps du tableau. Ici on utilise pas l'objet "SimpleListingHelper" car le type de données est différent (pas issu d'une requete SQL)
					echo '</tbody>';
					foreach($listing as $key => $ligne)	{
						($ligne['id_fonctionnalite'] == 1) ? $couleur = ' class="table-info"' : $couleur = '';
						echo '<tr'.$couleur.'>';
						foreach($cols as $indiceCol => $colonne) {
							echo '<td width="'.$colonne['size'].'%" align="'.$colonne['align'].'" title="'.$colonne['title'].'">';
								$fonction = 'Col_'.$indiceCol;
								if (function_exists($fonction)) {
									call_user_func($fonction, $key, $ligne);
								}
								else Col_($indiceCol, $key);
							echo '</td>';
						}
						echo '</tr>';
					}
					ligneDelete();
					echo '</tbody>';
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

	echo '</div>';		//container
echo '</body>';
echo '</html>';