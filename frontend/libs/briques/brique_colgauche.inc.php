<?php
//------------------------------------------------------------------
// Brique Colonne de gauche
//------------------------------------------------------------------
// éè : pour enregistrement UTF-8
//------------------------------------------------------------------

//colonne de gauche
//lg et xl -> taille 2 colonnes
//en dessous de lg n'affiche pas
echo '<div class="col-lg-2 d-none d-lg-block">';

	echo '<aside class="bg-light px-3">';
		echo '<div class="row">';
			echo '<div class="col">';
				echo '<h4>Navigation</h4>';
				echo '<ol class="list-unstyled">';
					echo '<li><span class="fas fa-globe"></span>&nbsp;lien 1</li>';
					echo '<li><span class="fas fa-globe"></span>&nbsp;lien 2</li>';
					echo '<li><span class="fas fa-globe"></span>&nbsp;lien 3</li>';
				echo '</ol>';
			echo '</div>';
		echo '</div>';
	echo '</aside>';

	echo '<aside class="bg-light px-3">';
		echo '<div class="row">';
			echo '<div class="col">';
				echo '<h4>Menu</h4>';
				echo '<ol class="list-unstyled">';
					echo '<li>Menu 1</li>';
					echo '<li>Menu 2</li>';
					echo '<li>Menu 3</li>';
				echo '</ol>';
			echo '</div>';
		echo '</div>';
	echo '</aside>';

echo '</div>';