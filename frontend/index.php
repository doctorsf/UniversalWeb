<?php
//-----------------------------------------------------------
// INDEX																	
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//$_SESSION[_APP_LOGIN_]->logout();
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

	//---------------------------------------------------------
	// Corps APPLI
	//---------------------------------------------------------
	echo '<div class="container-fluid">';

		echo '<div class="row" style="min-height:65rem">';

		//--------- colonne de gauche -----------------
		include_once(_BRIQUE_COL_GAUCHE_);

		//------------ colonne centrale ---------------
		//lg et xl -> taille 10 colonnes
		//en dessous prend toute la largeur de la fenetre (12 colonnes)
		echo '<div class="col-12 col-lg-8">';

			//panel de l'application
			include_once(_BRIQUE_PANEL_APPLI_);

			//code propre à la page
			echo '<article style="margin-bottom:3rem;">';
				echo '<div class="row">';
					echo '<div class="col-12">';
						if ($_SESSION[_APP_LOGIN_]->isLogged()) {
							//DEBUG_('login', $_SESSION[_APP_LOGIN_]);
							echo '<p>'.getLib('BIENVENUE').'&hellip;</p>';
							echo '<p><b>Nom et prénom de l\'utilisateur connecté</b> : '.$_SESSION[_APP_LOGIN_]->getPrenomNom().'</p>';
						}
						else {
							echo '<p>Merci de vous connecter pour commencer&hellip;</p>';
						}
					echo '</div>';  //col
				echo '</div>';	//row
			echo '</article>';

		echo '</div>';  
		//------ fin colonne centrale -----------

		//------ colonne de droite ------------
		include_once(_BRIQUE_COL_DROITE_);

	echo '</div>'; //row

	//---------------------------------------------------------
	// Footer APPLI
	//---------------------------------------------------------

	include_once(_BRIQUE_FOOTER_);

	echo '</div>';  //container

echo '</body>';
echo '</html>';