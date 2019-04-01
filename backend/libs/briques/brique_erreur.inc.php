<?php
//--------------------------------------------------------------------------
// BRIQUE_ERREUR
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
//--------------------------------------------------------------------------

//prise en compte du script en cours
$scriptName = explode('/', $_SERVER['SCRIPT_NAME']);
$scriptName = end($scriptName);

$titrePage = 'Extranet '._APP_TITLE_.' - '.getLib('ERREUR');
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
			echo '<div class="alert alert-danger" role="alert">';
				echo '<h1>'.getLib('ERREUR').'</h1>';
				echo $leMessage;
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '</section>';
	echo '</article>';

	//--------------------------------------
	// FOOTER
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);

	echo '</div>';		//container
echo '</body>';
echo '</html>';