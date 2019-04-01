<?php
/*--------------------------------------------------------------------------*/
/* BRIQUE_MESSAGE															*/
/*--------------------------------------------------------------------------*/
/* ééàç : pour sauvegarde du fichier en utf-8								*/
/*--------------------------------------------------------------------------*/

//--------------------------------------------------------------------------
// Affichage des messages
// Fonctionnement : la variable de session dédiées est relue et affichée
// puis elle est supprimée (pour que le message soit affiché qu'une fois)
//--------------------------------------------------------------------------
if (!empty($_SESSION[_APP_ID_.'MESSAGE_APPLICATION'])) {
	echo '<div class="alert '.$_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['color'].' alert-dismissible" role="alert">';
		echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
		echo $_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['message'];
	echo '</div>';
	unset($_SESSION[_APP_ID_.'MESSAGE_APPLICATION']);
}