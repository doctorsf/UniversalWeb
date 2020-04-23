<?php
//-----------------------------------------------------------
// SQUELETTE FORMULAIRE																	
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//gère l'accès au script
$operation = grantAcces() or die();

//recuperation des parametres URL
$id = -1;
if (isset($_GET['id'])) {
	$id = MySQLDataProtect($_GET['id']);
	//test cohérence id
	if (!preg_match(IDREGEX, $id)) {		
		$operation = 'erreur';
	}
}

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

		echo '<div class="row">';
			echo '<div class="col">';

				//on interdit toute modification ou suppression de l'id système (ou inférieur d'ailleurs)
				if ((($operation == 'modifier') || ($operation == 'supprimer') || ($operation == 'retirer')) && ($id <= _ID_SYSTEM_)) $operation = 'erreur';

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
								riseErrorMessage(getLib('XXX_AJOUTE_ECHEC'));
							}
							else {
								riseMessage(getLib('XXX_AJOUTE_SUCCES'));
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
					case 'retirer': 
					case 'modifier':
					case 'supprimer': {
						$res = $table->get($id, $tuple);
						if ($res !== false) {
							$frm->charger($id, $tuple);
							echo $frm->afficher();
						}
						else {
							riseErrorMessage(getLib('ERREUR_COMMANDE'));
							//branchement vers la page d'appel
							goPageBack();
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
					// Valide retirer
					//--------------------------------------
					case 'valid_retirer': {
						$res = $table->retire($frm->getIdTravail());
						if ($res === false) {
							riseErrorMessage(getLib('XXX_RETIRE_ECHEC'));
						}
						else {
							riseMessage(getLib('XXX_RETIRE_SUCCES'));
						}
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
							//modification effective des données : la clé unique du tuple à modifier
							//est disponible dans $frm->getIdTravail()
							$res = $table->update($frm->getIdTravail(), $donnees);
							if ($res === false) {
								riseErrorMessage(getLib('XXX_MODIFIE_ECHEC'));
							}
							else {
								//test si id à changé
								if ($donnees['cle'] != $frm->getIdTravail()) {
									//modifier id sur les tables maitres
									$dummy = SqlSimple::updateChamp('tableReferente', 'cle_tableReferente', $donnees['cle'], 'cle_tableReferente', $frm->getIdTravail());
								}
								riseMessage(getLib('XXX_MODIFIE_SUCCES'));
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
						if (!$nb = SqlSimple::existValeur('tableReferente', 'cle_tableReferente', $frm->getIdTravail())) {
							//suppression effective des donnees
							if (!$table->delete($frm->getIdTravail())) {
								riseErrorMessage(getLib('XXX_SUPPRIMER_ECHEC'));
							}
							else {
								riseMessage(getLib('XXX_SUPPRIMER_SUCCES'));
							}
						}
						else {
							riseErrorMessage(getLib('XXX_SUPPRIMER_IMPOSSIBLE', 'quoi', getLibNbRefsTrouvees($nb)));
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
						die();
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