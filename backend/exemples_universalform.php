<?php
//-----------------------------------------------------------
// EXEMPLES UNIVERSALFORM
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8							
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//---------------------------------------------------------
// Fonctions de callback
//---------------------------------------------------------
function fillSelect($value)
{
	$html = '';
	($value == '') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="Indéfini"'.$defaut.'>Choose a genre</option>';
	($value == 'action') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="action"'.$defaut.'>Action</option>';
	($value == 'comedy') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="comedy"'.$defaut.'>Comedy</option>';
	($value == 'horror') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="horror"'.$defaut.'>Horror</option>';
	($value == 'romance') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="romance"'.$defaut.'>Romance</option>';
	return $html;
}

function fillSelect2($value)
{
	$html = '';
	(in_array('', $value)) ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value=""'.$defaut.'>Choose a genre</option>';
	(in_array('action', $value)) ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="action"'.$defaut.'>Action</option>';
	(in_array('comedy', $value)) ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="comedy"'.$defaut.'>Comedy</option>';
	(in_array('horror', $value)) ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="horror"'.$defaut.'>Horror</option>';
	(in_array('romance', $value)) ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="romance"'.$defaut.'>Romance</option>';
	return $html;
}

//---------------------------------------------------------
// head
//---------------------------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
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

	//---------------------------------------------------------
	// Corps APPLI
	//---------------------------------------------------------

	echo '<div class="row mt-3">';

		//colonne centrale
		echo '<div class="col-8 mr-auto ml-auto">';

			//panel de l'application
			echo '<div class="row">';
				echo '<div class="col-12">';
					echo '<div class="sidebar souligne_epais">';
						echo '<p class="h1">Exemples Classes UniversalForm</p>';
						echo '<p class="lead">UniversalForm '.UniversalForm::VERSION.'</p>';
					echo '</div>';
				echo '</div>';
			echo '</div>';

			//code propre à la page
			echo '<div class="row mt-3">';
				echo '<div class="col-12">';

				$frm = new Form_exemples_universalform('ajouter', 1);
				$action = $frm->getAction();

				switch($action) {
					case 'ajouter': {
						$frm->init();
						echo $frm->afficher();
						break;
					}
					case 'valid_ajouter': {
						if (!$frm->tester()) {
//								DEBUG_TAB_($_POST);
							echo $frm->afficher();
						}
						else {
							$donnees = $frm->getData();
//								DEBUG_TAB_($_POST);
							DEBUG_TAB_($donnees);
						}
						break;
					}
				}

				echo '</div>';
			echo '</div>';

		echo '</div>';  //fin colonne centrale

	echo '</div>';

	//--------------------------------------
	// FOOTER
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);

	echo '</div>';		//container
echo '</body>';
echo '</html>';