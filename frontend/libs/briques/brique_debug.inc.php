<?php
/*------------------------------------------------------------------*/
/* Brique DEBUG														*/
/*------------------------------------------------------------------*/
/* éè : pour enregistrement UTF-8									*/
/*------------------------------------------------------------------*/

//section de debug
if (in_array($_SERVER['REMOTE_ADDR'], array_merge(array('127.0.0.1', '::1'), _IP_DEVELOPPEMENT_))) {
	if (!empty($_SESSION[_APP_ID_.'DEBUG_PHP'])) {
		echo '<section>';
			echo '<div class="row">';
				echo '<div class="col bg-light">';
					echo '<h1>>> FENETRE DE DEBUGGE <<</h1>';
						DEBUG_PRINT_();
					echo '</div>';
			echo '</div>';
		echo '</section>';
	}
}