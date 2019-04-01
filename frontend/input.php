<?php
/*--------------------------------------------------------------------------*/
/* Formulaire de saisie simple réutilisable 								*/
/*--------------------------------------------------------------------------*/
/* ééàç : pour sauvegarde du fichier en utf-8								*/
/*--------------------------------------------------------------------------*/
require_once('libs/common.inc.php');

$choixOperation = array('saisie');
(isset($_GET['operation']))	? $operation = MySQLDataProtect($_GET['operation'])	: $operation = 'saisie';
if (!in_array($operation, $choixOperation)) $operation = 'saisie';

//--------------------- H T M L -----------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
$fJquery = '';
echo writeHTMLHeader($titrePage, '', '');

echo '<body onload="document.getElementById(\'idChamp\').focus()">';
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
		//exceptionnellement, on ajoute un nouveau container simple pour centtrer le petit tableau résultat
		//on supprime les paddings supplémentaires ajouté par le container pour un meilleur rendu sur mobile
		echo '<div class="container no-horizontal-padding">';

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
					//		$_SESSION[_APP_INPUT_]['callback'] = _URL_ACTIONS_CHECKS_.'?operation=valid_'.$operation.'&amp;do='.$do;
					//	-	construction du formulaire. Pour chaque champ : 
					//		$_SESSION[_APP_INPUT_]['champs']['nom'] = array(							
					//						'type' => 'text',							//type de formulaire (correspoind à un objet UniversalForm, ici 'text')
					//						'label' => 'Nom de la sélection',			//libellé du champ
					//						'labelHelp' => 'Aide sur le libellé',		//aide sur le libellé du champ
					//						'testMatches' => array('REQUIRED'),			//batterie des tests
					//						'value' => 'Test sélection');				//valeur à afficher par défaut
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
						//formulaire constitué d'1 seul cham 'text' nommé 'nom'
						//Array
						//(
						//	[nom] => saisie													//sasie du champ 'nom'
						//	[callback] => actions_checks.php?operation=valid_newsel&do=5	//url de callback
						//	[form_title] => Nom de la sélection								//titre du formulaire
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