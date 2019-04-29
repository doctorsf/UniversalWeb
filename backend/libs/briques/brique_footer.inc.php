<?php
/*------------------------------------------------------------------*/
/* Brique FOOTER													*/
/*------------------------------------------------------------------*/
/* éè : pour enregistrement UTF-8									*/
/*------------------------------------------------------------------*/

//debug
include_once(_BRIQUE_DEBUG_);

echo '<footer>';
/*
	//pied de page
	echo '<div class="row">';
		echo '<div class="col bg-gris">';

			echo '<div class="row">';
				//premier tiers
				echo '<div class="col-12 col-md-4">';
					echo '<h2>Info 1</h2>';
				echo '</div>';

				//deuxième tiers
				echo '<div class="col-12 col-md-4">';
					echo '<h2>Info 2</h2>';
				echo '</div>';

				//troisieme tiers
				echo '<div class="col-12 col-md-4">';
					echo '<h2>DEBUG</h2>';
					echo '<ul>';
						echo '<li>Votre IP : '.$_SERVER['REMOTE_ADDR'].'</li>';
						if (isset($_SESSION[_APP_ID_.'PageRetour']))	echo '<li>Page de retour : '.$_SESSION[_APP_ID_.'PageRetour'].'</li>'; else echo '<li>Page de retour : aucune</li>';
					echo '</ul>';		
				echo '</div>';
			echo '</div>';

		echo '</div>';
	echo '</div>';
*/
	//Affichage de la version uniquement sur la page d'index
	if (getScriptName() == _URL_INDEX_) {
		echo '<div class="row bg-gris fixed-bottom" id="pannel-signature">';
			echo '<div class="col bg-gris">';
				echo '<p class="lead text-center" style="margin-bottom:0">'._COPYRIGHT_.' - '._APP_VERSION_.'</p>';
				if (in_array($_SERVER['REMOTE_ADDR'], array_merge(array('127.0.0.1', '::1'), _IP_DEVELOPPEMENT_))) {
					echo '<p class="text-center" style="margin-bottom:0">UniversalForm '.UniversalForm::VERSION.' - jQuery '._JQUERY_VERSION_.' - Bootstrap '._BOOTSTRAP_VERSION_.' - FontAwesome '._FONTAWESOME_VERSION_.'</p>';
				}
			echo '</div>';
		echo '</div>';
	}

echo '</footer>';

// Scripts Javascripts placés à la fin pour accélérer le chargement des pages
echo writeHtmlFooter($scriptSup, $fJquery);