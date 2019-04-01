<?php
//-----------------------------------------------------------
// Formulaire User																	
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//gère l'accès au script
$operation = grantAcces() or die();

//----------------------------------------------------------------------------
// construction du code Jquery pour affichage d'un datetimepicker simple
//http://xdsoft.net/jqplugins/datetimepicker/
//----------------------------------------------------------------------------
function makeDateTimePicker($idChamp, $defaut='') {
	$fJquery = '$("#'.$idChamp.'").datetimepicker({';
	$fJquery.= '	lang:"'._LG_.'",';
	//$fJquery.= '	datepicker:false,';						//pas de selection date
	//$fJquery.= '	timepicker:false,';						//pas de selection time
	$fJquery.= '	format:"'._FORMAT_DATE_TIME_.'",';		//format d'affichage
	$fJquery.= '	formatDate:"'._FORMAT_DATE_.'",';		//format d'affichage maxDate / minDate
	$fJquery.= '	formatTime:"H:i:s",';					//format d'affichage maxTime / minTime
	//$fJquery.= '	allowTimes:["12:00", "13:00"],';
	$fJquery.= '	step:1,';
	if ($defaut != '') {
		$fJquery.= '	defaultDate: "'.$defaut.'",';
		$fJquery.= '	defaultTime: "'.date('H:i:s').'",';
	}
	//$fJquery.= '	yearStart: 2015,';
	//$fJquery.= '	yearEnd: 2030,';
	$fJquery.= '	validateOnBlur:false,';
	$fJquery.= '	mask:false';								//affichage masque de saisie
	$fJquery.= '});';
	return $fJquery;
}

//----------------------------------------------------------------------------
// construction du code Jquery pour affichage d'un datetimepicker simple
//http://xdsoft.net/jqplugins/datetimepicker/
//----------------------------------------------------------------------------
function makeDatePicker($idChamp, $defaut='') {
	$fJquery = '$("#'.$idChamp.'").datetimepicker({';
	$fJquery.= '	lang:"'._LG_.'",';
	//$fJquery.= '	datepicker:false,';						//pas de selection date
	$fJquery.= '	timepicker:false,';						//pas de selection time
	$fJquery.= '	format:"'._FORMAT_DATE_.'",';			//format d'affichage
	$fJquery.= '	formatDate:"'._FORMAT_DATE_.'",';		//format d'affichage maxDate / minDate
	$fJquery.= '	formatTime:"H:i:s",';					//format d'affichage maxTime / minTime
	//$fJquery.= '	allowTimes:["12:00", "13:00"],';
	$fJquery.= '	step:1,';
	if ($defaut != '') {
		$fJquery.= '	defaultDate: "'.$defaut.'",';
		$fJquery.= '	defaultTime: "'.date('H:i:s').'",';
	}
	//$fJquery.= '	yearStart: 2015,';
	//$fJquery.= '	yearEnd: 2030,';
	$fJquery.= '	validateOnBlur:false,';
	$fJquery.= '	mask:false';								//affichage masque de saisie
	$fJquery.= '});';
	return $fJquery;
}

$titrePage		= _APP_TITLE_;
$description	= '';
$motsCle		= '';
$canonical		= '';
$scriptSup	    = '';
$fJquery	    = makeDateTimePicker('idDateCreation');    //datetimepicker date de création
echo writeHTMLHeader($titrePage, $description, $motsCle, $canonical);

echo '<body>';
	echo '<div class="container-fluid">';

	/*--------------------------------------*/
	/* HEADER								*/
	/*--------------------------------------*/
	include_once(_BRIQUE_HEADER_);

	/*--------------------------------------*/
	/* CORPS								*/
	/*--------------------------------------*/
	echo '<section>';
	echo '<article>';

		echo '<div class="row">';
			echo '<div class="col">';

				$frm = new Form_user($operation, 1);
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
						$donnees = $frm->getData();
						//DEBUG_('donnees', $donnees);
						if (($donnees['bouton'] == 'notposted') && ($donnees['recherche'] != '')) {
							//recherche de l'utilisateur
							$user = newUser();
							$res = $user->userExistsInAnnuaire($donnees['recherche'], $infos);
							if ($res) {
								$frm->initWithLdapInfos($infos);
							}
							else {
								$frm->setRechercheLdapErreur(getLib('UTILISATEUR_INCONNU_LDAP'));
							}
							echo $frm->afficher();
						}
						else {
							if (!$frm->tester()) {
								echo $frm->afficher();
							}
							else {
								//ok ajouter
								$donnees = $frm->getData();
								if (!sqlUsers_addUser($donnees)) {
									riseErrorMessage(getLib('ERREUR_CREATION_USER'));
								}
								else {
									riseMessage(getLib('UTILISATEUR_AJOUTE', $donnees['prenom'], $donnees['nom']));
								}
								//branchement vers la page d'appel
								goPageBack();
							}
						}
						break;
					}
					//--------------------------------------
					// consulter / modifier / supprimer
					//--------------------------------------
					case 'consulter':
					case 'modifier':
					case 'supprimer': {
						//recuperation de l'id de l'utilisateur à charger
						//si on est pas administrateur on ne peux pas acceder aux informations d'un autre user
						if (!accesAutorise('FONC_ADM_APP')) {
							$id = $_SESSION[_APP_LOGIN_]->getId();
						}
						else {
							(isset($_GET['id'])) ? $id = mySqlDataProtect($_GET['id']) : $id = 0;
						}
						$frm->charger($id);
						echo $frm->afficher();
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
						$donnees = $frm->getData();
						//DEBUG_('donnees', $donnees);
						if (($donnees['bouton'] == 'notposted') && ($donnees['recherche'] != '')) {
							//recherche de l'abonné
							$abonne = newUser();
							$res = $abonne->userExistsInAnnuaire($donnees['recherche'], $infos);
							if ($res) {
								$frm->initWithLdapInfos($infos);
							}
							else {
								$frm->setRechercheLdapErreur(getLib('UTILISATEUR_INCONNU_LDAP'));
							}
							echo $frm->afficher();
						}
						elseif ($donnees['bouton'] == 'Modifier') {
							if (!$frm->tester()) {
								echo $frm->afficher();
							}
							else {
								$donnees = $frm->getData();
								if ($frm->getIdTravail() == $_SESSION[_APP_LOGIN_]->getId()) {
									//lorsque les modifications concernent le compte de l'utilisateur connecté on limite les champs modifiables pour eviter les erreurs sensibles (auto désactivation et modification de profil)
									$res = sqlUsers_updateUser($frm->getIdTravail(), $donnees);
									//recharge les données de son propre compte
									$_SESSION[_APP_LOGIN_]->chargeUser($donnees['id_user']);
								}
								else {
									//les modification d'un autre compte peuvent affecter tous les champs
									$res = sqlUsers_updateUserGlobal($frm->getIdTravail(), $donnees);
								}
								if (!$res) {
									riseErrorMessage(getLib('ERREUR_MODIF_USER_X', $frm->getIdTravail()));
								}
								else {
									riseMessage(getLib('UTILISATEUR_MODIFIE', $donnees['prenom'], $donnees['nom']));
								}
								//branchement vers la page d'appel
								goPageBack();
							}
						}
						else {
							echo $frm->afficher();
						}
						break;
					}
					//--------------------------------------
					// Valide supprimer
					//--------------------------------------
					case 'valid_supprimer': {
						//test si l'on ne cherche pas à s'auto-supprimer
						if ($frm->getIdTravail() != $_SESSION[_APP_LOGIN_]->getId()) {
							//test si l'utilisateur n'est pas responsable d'un serveur
							if (!sqlUsers_deleteUser($frm->getIdTravail())) {
								riseErrorMessage(getLib('ERREUR_SUPPR_USER_X', $frm->getIdTravail()));
							}
							else {
								riseMessage(getLib('UTILISATEUR_SUPPRIME'));
							}
						}
						else {
							riseErrorMessage(getLib('ERREUR_SUICIDE'));
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

	echo '</article>';
	echo '</section>';

	//--------------------------------------
	// FOOTER
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);

	echo '</div>';		//container
echo '</body>';
echo '</html>';