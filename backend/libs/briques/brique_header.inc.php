<?php
//------------------------------------------------------------------
// Brique HEADER
//------------------------------------------------------------------
// éè : UTF-8
//------------------------------------------------------------------

echo '<header>';

	//entete
	echo '<div class="row">';
		echo '<div class="col">';

			echo '<div class="row">';

				//--------------------------------------
				// Bloc LOGO
				//--------------------------------------
				echo '<div class="col-sm-12 col-md-6">';
					echo '<h2 class="d-inline mb-0">'._APP_TITLE_.' '._APP_VERSION_._APP_RELEASE_;
					if (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false) echo ' <span class="text-warning">LOCALHOST</span>';
					echo '</h2>';
					echo '<p class="small mb-1">'.getLib('TODAY_IS', MySQLToDateClair(date('Y-m-d'), _LG_)).'</p>';
				echo '</div>';

				//--------------------------------------
				// Bloc BIENVENUE + DRAPEAUX
				//--------------------------------------
				echo '<div class="col-sm-12 col-md-6 text-right">';
					if ($_SESSION[_APP_LOGIN_]->isLogged()) {
						echo '<p class="lead d-inline">';
						echo ucfirst(getLib('BIENVENUE'));
						echo '&nbsp;<a href="'._URL_USER_.'?operation=consulter&amp;id='.$_SESSION[_APP_LOGIN_]->getId().'" data-toggle="tooltip" title="'.getLib('ACCEDER_MON_COMPTE').'" rel="nofollow">'.$_SESSION[_APP_LOGIN_]->getPrenom().'</a>';
						echo '&nbsp;|&nbsp;';
						echo '<a href="'._URL_LOGOUT_.'" data-toggle="tooltip" title="'.getLib('DECONNECTEZ_MOI').'" rel="nofollow">';
						echo '<span class="fas fa-sign-out-alt"></span>&nbsp;'.getLib('LOGOUT').'</a>';
						echo '</p>';
					}
					echo '<span class="ml-3"></span>';
					echo '<a href="'._URL_BASE_SITE_._URL_LANGUE_.'?langue=fr">';
						echo '<img class="mb-1"src="'._DRAPEAU_FR_.'" alt="" title="" />';
					echo '</a>';
					echo '&nbsp;|&nbsp;';
					echo '<a href="'._URL_BASE_SITE_._URL_LANGUE_.'?langue=en">';
						echo '<img class="mb-1" src="'._DRAPEAU_US_.'" alt="" title="" />';
					echo '</a>';
				echo '</div>';

			echo '</div>';

			//--------------------------------------
			// Bloc MENU
			// "row" est ici remplacé par "nav"
			//--------------------------------------
			echo '<nav class="navbar navbar-expand-md navbar-dark bg-info rounded mb-1">';

				//marque
				echo '<a class="navbar-brand">'._APP_TITLE_.'</a>';
				
				//bouton de menu minimal
				echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">';
					echo '<span class="navbar-toggler-icon"></span>';
				echo '</button>';

				//barre de navigation
				echo '<div class="collapse navbar-collapse" id="navbarNavDropdown">';

					echo '<ul class="navbar-nav">';

						//mise en place du visuel "entrée de menu active"
						$active = '';
						if (in_array($scriptName, array(_URL_INDEX_))) $active = ' active';
						echo '<li class="nav-item'.$active.'">';
							echo '<a class="nav-link" href="#">';
							echo '<span class="fas fa-home"></span>&nbsp;'.getLib('ACCUEIL').'&nbsp;</a>';
						echo '</li>';
								
						//mise en place du visuel "entrée de menu active"
						$active = '';
						if (in_array($scriptName, array())) $active = ' active';
						echo '<li class="nav-item dropdown'.$active.'">';
							echo '<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="navbarDropdownMenu" role="button" aria-haspopup="true" aria-expanded="false">';
								echo '<span class="fas fa-bars"></span>&nbsp;Menu&nbsp;';
							echo '</a>';
							echo '<div class="dropdown-menu" aria-labelledby="navbarDropdownMenu">';
								echo '<a class="dropdown-item" href="#">'.getLib('SOUS-MENU').' 1</a>';
								echo '<a class="dropdown-item" href="#">'.getLib('SOUS-MENU').' 2</a>';
								echo '<div class="dropdown-divider"></div>';
								echo '<a class="dropdown-item disabled" href="#">'.getLib('SOUS-MENU').' 3</a>';
							echo '</div>';
						echo '</li>';

						//mise en place du visuel "entrée de menu active"
						$active = '';
						if (in_array($scriptName, array())) $active = ' active';
						echo '<li class="nav-item dropdown'.$active.'">';
							echo '<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="navbarDropdownReferenciel" role="button" aria-haspopup="true" aria-expanded="false">';
								echo '<span class="fas fa-table"></span>&nbsp;'.getLib('REFERENCIEL').'&nbsp;';
							echo '</a>';
							echo '<div class="dropdown-menu" aria-labelledby="navbarDropdownReferenciel">';
								echo '<a class="dropdown-item" href="#">Table 1</a>';
								echo '<a class="dropdown-item" href="#">Table 2</a>';
								echo '<div class="dropdown-divider"></div>';
								echo '<a class="dropdown-item disabled" href="#">Table 3</a>';
							echo '</div>';
						echo '</li>';

						//mise en place du visuel "entrée de menu active"
						$active = '';
						if (in_array($scriptName, array('exemple_liste_simple.php', 'exemple_liste_simple_pages.php', 'exemple_liste_complexe.php'))) $active = ' active';
						echo '<li class="nav-item dropdown'.$active.'">';
							echo '<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="navbarDropdownExamples" role="button" aria-haspopup="true" aria-expanded="false">';
								echo '<span class="fas fa-coffee"></span>&nbsp;'.getLib('EXAMPLES').'&nbsp;';
							echo '</a>';
							echo '<div class="dropdown-menu" aria-labelledby="navbarDropdownExamples">';
								echo '<a class="dropdown-item" href="exemple_liste_simple.php">Liste simple</a>';
								echo '<a class="dropdown-item" href="exemple_liste_simple_pages.php">Liste simple paginée</a>';
								echo '<a class="dropdown-item" href="exemple_liste_complexe.php">Liste complexe</a>';
								echo '<div class="dropdown-divider"></div>';
								echo '<a class="dropdown-item" href="exemples_universalform.php">UniversalForm</a>';
								echo '<div class="dropdown-divider"></div>';
								echo '<a class="dropdown-item" href="exemple_import_csv.php">'.getLib('IMPORT_CSV').'</a>';
								echo '<div class="dropdown-divider"></div>';
								echo '<a class="dropdown-item" href="exemple_upload.php">'.getLib('UPLOADER').'</a>';
							echo '</div>';
						echo '</li>';

						//mise en place du visuel "entrée de menu active"
						$active = '';
						if (in_array($scriptName, array(_URL_LISTING_DROITS_, _URL_ACTIONS_DROITS_, _URL_LISTING_USERS_, _URL_USER_, _URL_LISTING_PARAMS_, _URL_REGLAGES_, _URL_MEDIA_, _URL_INFOS_SYSTEME_, _URL_MAINTENANCE_, _URL_LISTING_LOGS_, _URL_VERSIONNING_))) $active = ' active';
						echo '<li class="nav-item dropdown'.$active.'">';
							echo '<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="navbarDropdownAdmin" role="button" aria-haspopup="true" aria-expanded="false">';
								echo '<span class="fas fa-cog"></span>&nbsp;Administration&nbsp;';
							echo '</a>';
							echo '<div class="dropdown-menu" aria-labelledby="navbarDropdownAdmin">';
								echo '<a class="dropdown-item" href="'._URL_LISTING_DROITS_.'"><span class="fas fa-balance-scale-right mr-2"></span>'.getLib('GESTION_DES_DROITS').'</a>';
								echo '<a class="dropdown-item" href="'._URL_LISTING_USERS_.'"><span class="fas fa-users-cog mr-2"></span>'.getLib('LISTE_UTILISATEURS').'</a>';
								echo '<div class="dropdown-divider"></div>';
								echo '<a class="dropdown-item" href="'._URL_LISTING_PARAMS_.'"><span class="fas fa-cog mr-2"></span>'.getLib('PARAMETRES').'</a>';
								echo '<a class="dropdown-item" href="'._URL_REGLAGES_.'"><span class="fas fa-sliders-h mr-2"></span>'.getLib('REGLAGES_APPLICATION').'</a>';
								echo '<div class="dropdown-divider"></div>';
								echo '<a class="dropdown-item" href="'._URL_MEDIA_.'"><span class="fas fa-photo-video mr-2"></span>'.getLib('MEDIA').'</a>';
								echo '<div class="dropdown-divider"></div>';
								echo '<a class="dropdown-item" href="'._URL_INFOS_SYSTEME_.'"><span class="fas fa-desktop mr-2"></span>'.getLib('INFORMATIONS_SYSTEME').'</a>';
								echo '<a class="dropdown-item" href="'._URL_MAINTENANCE_.'?operation=dbsign"><span class="fas fa-signature mr-2"></span>'.getLib('SIGNATURE_BASE').'</a>';
								echo '<a class="dropdown-item" href="'._URL_MAINTENANCE_.'?operation=hash"><span class="fas fa-signature mr-2"></span>'.getLib('SIGNATURE_CODE').'</a>';
								echo '<a class="dropdown-item" href="'._URL_MAINTENANCE_.'?operation=hashfrontend"><span class="fas fa-signature mr-2"></span>'.getLib('SIGNATURE_CODE').' (frontend)</a>';
								echo '<div class="dropdown-divider"></div>';
								echo '<a class="dropdown-item" href="'._URL_MAINTENANCE_.'?operation=savedb"><span class="fas fa-database mr-2"></span>'.getLib('SAUVEGARDE_BD').'</a>';
								echo '<a class="dropdown-item" href="'._URL_MAINTENANCE_.'?operation=loaddb"><span class="fas fa-database mr-2"></span>'.getLib('RESTORATION_BD').'</a>';
								echo '<div class="dropdown-divider"></div>';
								echo '<a class="dropdown-item" href="'._URL_LISTING_LOGS_.'"><span class="far fa-list-alt mr-2"></span>'.getLib('LOGS').'</a>';
								if (_RUN_MODE_ == _DEVELOPPEMENT_) {
									echo '<div class="dropdown-divider"></div>';
									echo '<a class="dropdown-item d-flex" href="'._URL_VERSIONNING_.'">';
										echo '<span class="fas fa-code mr-2"></span><div class="mr-auto">Versionning&nbsp;</div>';
										echo '<div class="badge badge-success ml-auto h-25 mt-1">DEV</div>';
									echo '</a>';
								}
							echo '</div>';
						echo '</li>';

					echo '</ul>';

				echo '</div>';

			echo '</nav>';

		echo '</div>';
	echo '</div>';

echo '</header>';

include_once(_BRIQUE_MESSAGE_);