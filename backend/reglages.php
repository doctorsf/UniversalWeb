<?php
//-----------------------------------------------------------
// SQUELETTE FORMULAIRE																	
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

//creation de l'objet table Application
$table = new SqlParams();

//--------------------- H T M L -----------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
$fJquery = '';
// on active le calendrier pour chaque reglage de type date
$res = $table->getSome('reglable', 1, 'ordre', $liste);
if ($res !== false) {
	foreach($liste as $reg) {
		if ($reg['type'] == 'date') 
			$fJquery.= makeDatePicker('id'.$reg['id']);    //datetimepicker date de création
		elseif ($reg['type'] == 'datetime') 
			$fJquery.= makeDateTimePicker('id'.$reg['id']);    //datetimepicker date de création
	}
}
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

				$frm = new Form_reglages('modifier', 1);
				$action = $frm->getAction();

				switch($action)	{
					//--------------------------------------
					// modifier
					//--------------------------------------
					case 'modifier': {
						$res = $table->getSome('reglable', 1, 'ordre', $_SESSION['listeReglages']);
						if ($res !== false) {
							$frm->charger($_SESSION['listeReglages']);
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
					// Valide modifier
					// Les données en retour sont un tableau contenant
					// les valeurs de chaques réglage indexé par l'id unique du réglage
					// Array (
					//    [8] => 12
					//    [9] => mon url
					//    [10] => oui
					//    [11] => 1970-01-01 01:00:00
					//    [12] => 1970-01-01
					//)
					//--------------------------------------
					case 'valid_modifier': {
						if (!$frm->tester()) {
							echo $frm->afficher();
						}
						else {
							$donnees = $frm->getData();
							//DEBUG_('donnees', $donnees);
							//pour chaque reglage
							foreach($donnees as $idReglage => $nouvelleValeur) {
								SqlSimple::updateChamp(_PT_.'params', 'valeur', $nouvelleValeur, 'id', $idReglage);
							}
							riseMessage(getLib('REGLAGES_OK'));
							//on detruit la variable de session qui contient les champs de réglages
							unset($_SESSION['listeReglages']);
							//on raffraichit la page
							goReferer();
						}
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