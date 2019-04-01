<?php
//------------------------------------------------------------------
// Brique panel de l'application
//------------------------------------------------------------------
// éè : pour enregistrement UTF-8
//------------------------------------------------------------------
echo '<section class="bg-light px-3 souligne">';
	echo '<div class="row">';
		//marque
		echo '<div class="col-12 col-md-10">';
			echo '<p class="h1">'._APP_TITLE_.'</p>';
			echo '<p><span class="lead">'._APP_SLOGAN_.'</span></p>';
		echo '</div>';
		//logo
		echo '<div class="col-md-2 text-right d-none d-lg-block">';
			echo '<span class="fas fa-globe fa-5x" aria-hidden="true"></span>';
		echo '</div>';
	echo '</div>';
echo '</section>';