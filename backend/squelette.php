<?php
//-----------------------------------------------------------
// SQUELETTE																
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//gère l'accès au script
$operation = grantAcces() or die();

//sauvegarde de la page de retour
setPageBack();

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
				echo '<h1>'._APP_TITLE_.'</h1>';
				echo '<p class="lead text-center">'._APP_TITLE_.'</p>';
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