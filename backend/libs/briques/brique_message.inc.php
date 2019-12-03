<?php
//------------------------------------------------------------------
// BRIQUE_MESSAGE
//------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
//------------------------------------------------------------------
// 28.05.2019
//		- Affichage systématique dela section (même vide si pas de message), permet d'envoyer des messages via javascript
//------------------------------------------------------------------

//------------------------------------------------------------------
// Affichage des messages
// Fonctionnement : la variable de session dédiées est relue et affichée
// puis elle est supprimée (pour que le message soit affiché qu'une fois)
//------------------------------------------------------------------

echo '<section id="section_messsage">';
if (!empty($_SESSION[_APP_ID_.'MESSAGE_APPLICATION'])) {
		echo '<div class="row">';
			echo '<div class="col">';
				echo '<div class="alert '.$_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['color'].' alert-dismissible" role="alert">';
					echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
					echo $_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['message'];
				echo '</div>';
				unset($_SESSION[_APP_ID_.'MESSAGE_APPLICATION']);
			echo '</div>';
		echo '</div>';
}
echo '</section>';