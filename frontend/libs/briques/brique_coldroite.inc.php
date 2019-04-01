<?php
//------------------------------------------------------------------
// Brique colonne de droite
//------------------------------------------------------------------
// éè : pour enregistrement UTF-8
//------------------------------------------------------------------

//--------------------------------------
// Formulaire de Login
//--------------------------------------

echo '<div class="col-lg-2">';

	echo '<aside class="bg-light px-3">';
		echo '<div class="row">';
			echo '<div class="col">';

				//colonne de droite
				if ($_SESSION[_APP_LOGIN_]->isLogged()) {
					echo '<p>Bonjour '.$_SESSION[_APP_LOGIN_]->getNomPrenom().'</p>';
					echo '<a href="'._URL_LOGOUT_.'" title="'.getLib('DECONNECTEZ_MOI').'" rel="nofollow">Déconnexion</a>';
				}

				else {

					$frmLogin = new Form_login('login', 1);
					$loginAction = $frmLogin->getAction();

					//--------------------------------------
					// ACTION A MENER					
					//--------------------------------------
					switch($loginAction)
					{
						//--------------------------------------
						// DEMANDE LOGIN			                              
						//--------------------------------------
						case 'login': {
							$frmLogin->init();
							echo '<h4>Connexion utilisateur</h4>';
							echo $frmLogin->afficher();
							echo '<a href="" title="Demander un nouveau mot de passe par e-mail."><p>Demander un nouveau mot de passe</p></a>';
							break;
						}
						//--------------------------------------
						// VALIDATION LOGIN
						//--------------------------------------
						case 'valid_login': {
							if (!$frmLogin->tester()) {
								echo '<h4>Connexion utilisateur</h4>';
								echo $frmLogin->afficher();
								echo '<a href="" title="Demander un nouveau mot de passe par e-mail."><p>Demander un nouveau mot de passe</p></a>';
							}
							else {
								//la saisie est ok. recuperation des infos user
								$donneesLogin = $frmLogin->getData();
								//on loggue l'utilisateur
								$retour = $_SESSION[_APP_LOGIN_]->login($donneesLogin['login']);
								if ($retour === true) {
									//renvoie à la page d'index
									header('Location: '._URL_FRONT_END_._URL_INDEX_);
								}
								else {
									echo '<p class="error">'.getLib('ECHEC_LOGIN', $donneesLogin['login']).'</p>';
								}
								break;
							}
						}
						//--------------------------------------
						// COMMANDE NON RECONNUE
						//--------------------------------------
						default: {	
							break;
						}
					}
				}

			echo '</div>';
		echo '</div>';
	echo '</aside>';
echo '</div>';