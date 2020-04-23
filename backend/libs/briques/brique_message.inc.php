<?php
//------------------------------------------------------------------
// BRIQUE_MESSAGE
//------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
//------------------------------------------------------------------
// 28.05.2019
//		- Affichage systématique de la section (même vide si pas de message), permet d'envoyer des messages via javascript
// 01.04.2020
//		- Transformation en tableau de messages, indicés selon l'ordre de choix de l'affichage (tri dans l'ordre choisi avant affichage)
//			Array (
//				[10] => Array (
//						[message] => message 1
//						[color] => alert-success
//					)
//				[5] => Array (
//						[message] => message 2
//						[color] => alert-success
//					)
//				[8] => Array (
//						[message] => message 3
//						[color] => alert-success
//					)
//			)
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
				//tri des message pour les afficher dans l'ordre décidé par le developpeur
				ksort($_SESSION[_APP_ID_.'MESSAGE_APPLICATION']);
				foreach($_SESSION[_APP_ID_.'MESSAGE_APPLICATION'] as $indice => $message) {
					echo '<div class="alert '.$_SESSION[_APP_ID_.'MESSAGE_APPLICATION'][$indice]['color'].' alert-dismissible mb-1" role="alert">';
						echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
						echo $_SESSION[_APP_ID_.'MESSAGE_APPLICATION'][$indice]['message'];
					echo '</div>';
				}
				//on vide le buffer de messages
				unset($_SESSION[_APP_ID_.'MESSAGE_APPLICATION']);
			echo '</div>';
		echo '</div>';
}
echo '</section>';