<?php
//--------------------------------------------------------------------------
// Formulaire de saisie simple réutilisable
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
// 24.05.2019
//		correction bug mise de la pose du focus sur le champ
//--------------------------------------------------------------------------
require_once('libs/common.inc.php');

$choixOperation = array('saisie');
(isset($_GET['operation']))	? $operation = MySQLDataProtect($_GET['operation'])	: $operation = 'saisie';
if (!in_array($operation, $choixOperation)) $operation = 'saisie';

//--------------------- H T M L -----------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
$fJquery = '';
echo writeHTMLHeader($titrePage, '', '');

echo '<body>';
	echo '<div class="container-fluid">';
	//--------------------------------------
	// HEADER								
	//--------------------------------------
	include_once(_BRIQUE_HEADER_);

	//--------------------------------------
	// CORPS								
	//--------------------------------------
	echo '<section>';
	echo '<article>';

		echo '<div class="row">';
			echo '<div class="col">';

			//--------------------------------------
			// CORPS								
			//--------------------------------------

			$frm = new Form_input($operation, 1);
			$action = $frm->getAction();

			//--------------------------------------
			// ACTION A MENER					
			//--------------------------------------
			switch($action)
			{
				//--------------------------------------
				// DEMANDE LOGIN			                              
				//--------------------------------------
				case 'saisie': {
					//l'initialisation du formulaire de saisie (y compris les paramètres de sa création) est contenu 
					//dans la variable de session $_SESSION[_APP_INPUT_] avant l'appel à ce script. Exemple :
					//	- titre affiché sur le formulaire
					//		$_SESSION[_APP_INPUT_]['form_title'] = 'Nom de la sélection';
					//	- url de retour après validation du formulaire
					//		$_SESSION[_APP_INPUT_]['callback'] = _URL_ACTIONS_CHECKS_.'?operation=valid_checks&amp;do=14;
					//	-	construction du formulaire. Pour chaque champ : 
					//		$_SESSION[_APP_INPUT_]['champs']['nom'] = array(							
					//						'type' => 'text',							//type de formulaire (correspond à un objet UniversalForm, ici 'text')
					//						'label' => 'Nom de la sélection',			//libellé du champ
					//						'labelHelp' => 'Aide sur le libellé',		//aide sur le libellé du champ
					//						'testMatches' => array('REQUIRED'),			//batterie des tests
					//						'value' => 'Test sélection');				//valeur à afficher par défaut
					//		$_SESSION[_APP_INPUT_]['champs']['liste'] = array(			//exemple : autre champ de type 'select'
					//						'type' => 'select',						
					//						'label' => 'Nom de la sélection', 
					//						'labelHelp' => 'Choisir la sélection de Checks à laquelle ajouter ce Check', 
					//						'complement' => 'sqlSelectionsChecks_fillSelectForStrategie',
					//						'value' => '');
					//		$_SESSION[_APP_INPUT_]['champs']['id'] = array(				//exemple : autre champ de type 'hidden'
					//						'type' => 'hidden', 'value' => '24');
					$frm->init();
					echo $frm->afficher();
					break;
				}
				//--------------------------------------
				// VALIDATION LOGIN
				//--------------------------------------
				case 'valid_saisie': {
					if (!$frm->tester()) {
						echo $frm->afficher();
					}
					else {
						//la saisie est ok. recuperation des infos de la saisie (tableau). Exemple de récupération pour un 
						//formulaire constitué des informations citées en exemple dans 'saisie' ci-dessus :
						//Array
						//(
						//	[form_title] => Nom de la sélection								//titre du formulaire
						//	[callback] => actions_checks.php?operation=valid_newsel&do=5	//url de callback
						//	[nom] => saisie													//saisie du champ 'nom'
						//	[liste] => 4													//résultat de la sélection de la liste déroulante 'liste'
						//	[id] => 24														//contenu du champ caché 'id'
						//)
						//Les données saisies sont déposées dans la variable de session $_SESSION[_APP_INPUT_]['values']
						$_SESSION[_APP_INPUT_]['values'] = $frm->getData();
						//appel de l'url de callback
						header('Location: '._URL_BASE_SITE_.$_SESSION[_APP_INPUT_]['callback']);
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

	echo '</article>';
	echo '</section>';

	//--------------------------------------
	// FOOTER								
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);

	echo '</div>';
echo '</body>';
echo '</html>';