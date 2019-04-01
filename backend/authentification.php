<?php
//--------------------------------------------------------------------------
// Formulaire d'authentification
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
//--------------------------------------------------------------------------
require_once('libs/common.inc.php');

//exécution d'une mise en veille programmée (éventuellement suite à une tentative d'usurpation d'identité)
if(isset($_SESSION['sleep'])) {
	sleep($_SESSION['sleep']);
	unset($_SESSION['sleep']);
}

$choixOperation = array('login');
(isset($_GET['operation']))	? $operation = MySQLDataProtect($_GET['operation'])	: $operation = 'login';
if (!in_array($operation, $choixOperation)) $operation = 'login';

//--------------------------------------------------------------------------
// Debut de code HTML
//--------------------------------------------------------------------------
$titrePage = getLib('IDENTIFIEZ-VOUS');
$scriptSup = '';
$fJquery = '';
echo writeHTMLHeader($titrePage, '', '');

echo '<body>';
	echo '<div class="container-fluid">';

		echo '<div class="row mt-5">';
			//colonne centrale
			echo '<div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-4 ml-sm-auto mr-sm-auto">';

				//--------------------------------------
				// CORPS								
				//--------------------------------------

				$frm = new Form_login($operation, 1);
				$action = $frm->getAction();

				//--------------------------------------
				// ACTION A MENER					
				//--------------------------------------
				switch($action)
				{
					//--------------------------------------
					// DEMANDE LOGIN			                              
					//--------------------------------------
					case 'login':
					{
						$frm->init();
						echo $frm->afficher();
						break;
					}
					//--------------------------------------
					// VALIDATION LOGIN
					//--------------------------------------
					case 'valid_login':
					{
						if (!$frm->tester()) {
							echo $frm->afficher();
						}
						else {
							//la saisie est ok. recuperation des infos user
							$donnees = $frm->getData();
							//DEBUG_TAB_($donnees);
							//on loggue l'utilisateur
							$retour = $_SESSION[_APP_LOGIN_]->login($donnees['login']);
							if ($retour === true) {
								//DEBUG_('login', $_SESSION[_APP_LOGIN_]);
								//renvoie à la page d'index
								header('Location: '._URL_BASE_SITE_._URL_INDEX_);
							}
							else {
								echo 'Erreur de login n°'.$retour.'. Classe '.get_class($_SESSION[_APP_LOGIN_]);
							}
						}
						break;
					}
					//--------------------------------------
					// COMMANDES NON RECONNUES
					//--------------------------------------
					default: {	
						break;
					}
				}

			echo '</div>';
		echo '</div>';
	echo '</div>';

	//	writeHtmlFooter();
	//--------------------------------------
	// FOOTER								
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);


echo '</body>';
echo '</html>';