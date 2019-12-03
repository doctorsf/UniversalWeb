<?php
//-----------------------------------------------------------
// SQUELETTE FORMULAIRE
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//gère l'accès au script
$operation = grantAcces() or die();

//creation de l'objet table Application
$table = new table();

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
		//exceptionnellement, on ajoute un nouveau container simple pour centtrer le petit tableau résultat
		//on supprime les paddings supplémentaires ajouté par le container pour un meilleur rendu sur mobile
		echo '<div class="container no-horizontal-padding">';

		echo '<div class="row">';
			echo '<div class="col">';

				$frm = new Form_squelette($operation, 1);
				$action = $frm->getAction();

				switch($action)	{
					//--------------------------------------
					// Ajouter
					//--------------------------------------
					case 'ajouter': {
						$frm->init();
						echo $frm->afficher();
						break;
					}
					//--------------------------------------
					// Valide ajouter
					//--------------------------------------
					case 'valid_ajouter': {
						if (!$frm->tester()) {
							echo $frm->afficher();
						}
						else {
							//ok ajouter
							$donnees = $frm->getData();
							//DEBUG_('donnees', $donnees);
							if (!$table->add($donnees)) {
								riseErrorMessage(getLib('ERREUR'));
							}
							//branchement vers la page d'appel
							goPageBack();
						}
						break;
					}
					//--------------------------------------
					// consulter / modifier / supprimer
					//--------------------------------------
					case 'consulter':
					case 'modifier':
					case 'supprimer': {
						//recuperation de l'id de l'item à charger
						(isset($_GET['id'])) ? $id = mySqlDataProtect($_GET['id']) : $id = 0;
						$res = $table->get($id, $tuple);
						if ($res !== false) {
							$frm->charger($id, $tuple);
							echo $frm->afficher();
						}
						break;
					}
					//--------------------------------------
					// Valide consulter
					//--------------------------------------
					case 'valid_consulter': {
						//branchement vers la page d'appel
						goPageBack();
						break;
					}
					//--------------------------------------
					// Valide modifier
					//--------------------------------------
					case 'valid_modifier': {
						if (!$frm->tester()) {
							echo $frm->afficher();
						}
						else {
							$donnees = $frm->getData();
							//DEBUG_('donnees', $donnees);
							//modification effective des données : la clé unique du tuple à modifier*
							//est disponible dans $frm->getIdTravail()
							$res = $table->update($frm->getIdTravail(), $donnees);
							if ($res === false) {
								riseErrorMessage(getLib('ERREUR'));
							}
							//branchement vers la page d'appel
							goPageBack();
						}
						break;
					}
					//--------------------------------------
					// Valide supprimer
					//--------------------------------------
					case 'valid_supprimer': {
						//test si la suppression n'impacte pas une autre table
						if (!$nb = autreTable_existValeur(indexAutreTable, $frm->getIdTravail())) {
							//suppression effective des donnees
							if (!$table->delete($frm->getIdTravail())) {
								riseErrorMessage(getLib('ERREUR'));
							}
							else {
								riseMessage('Donnée supprimé');
							}
						}
						else {
							riseErrorMessage('Il est impossible de supprimer cette donnée. Celle-ci est utilisée par '.$nb. ' données(s)');
						}
						//branchement vers la page d'appel
						goPageBack();
						break;
					}
					//--------------------------------------
					// Commandes non reconnues
					//--------------------------------------
					default: {
						riseErrorMessage(getLib('ERREUR_COMMANDE'));
						goPageBack();
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