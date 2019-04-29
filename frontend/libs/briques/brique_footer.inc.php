<?php
//------------------------------------------------------------------
// Brique FOOTER
//------------------------------------------------------------------
// éè : pour enregistrement UTF-8
//------------------------------------------------------------------

//debug
include_once(_BRIQUE_DEBUG_);

//Footer
echo '<footer>';

	//version
	echo '<div class="row bg-light fixed-bottom" id="pannel-signature">';
		echo '<div class="col">';
			echo '<p class="lead small text-center" style="margin-bottom:0">'._COPYRIGHT_.' - '._APP_VERSION_.'</p>';
			if (in_array($_SERVER['REMOTE_ADDR'], array_merge(array('127.0.0.1', '::1'), _IP_DEVELOPPEMENT_))) {
				echo '<p class="text-center" style="margin-bottom:0">UniversalForm '.UniversalForm::VERSION.' - jQuery '._JQUERY_VERSION_.' - Bootstrap '._BOOTSTRAP_VERSION_.' - FontAwesome '._FONTAWESOME_VERSION_.'</p>';
			}
		echo '</div>';
	echo '</div>';

echo '</footer>';

// Scripts Javascripts placés à la fin pour accélérer le chargement des pages
echo writeHtmlFooter($scriptSup, $fJquery);