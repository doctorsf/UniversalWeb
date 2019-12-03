<?php
//--------------------------------------------------------------------------
// Fonctions de l'application
//--------------------------------------------------------------------------
// Dernière maj : 25.10.2017
// 15.01.2018 : Ajout de la fonction newUser()
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
// Lance le script javascript de recherche de la résolution de l'écran 
// du client. Le script est lancé seulement si l'info n'est pas déja présente 
// dans les variables de session 'screen_width' et 'screen_height'
// ENTREE : Rien
// SORTIE : le code javascript si besoin
//--------------------------------------------------------------------------
function getScreenResolution() {
	//unset ($_SESSION['screen_width']);
	if (!isset($_SESSION['screen_width'])) {
		$chaine = '<script>';
		$chaine.= 'getScreenResolution();';
		$chaine.= '</script>';
		return $chaine;
	}
}

//-- RENVOIE LES INFORMATIONS DE HEADER DES PAGES HTML ---------------------
// ENTREE :
//		$titre : titre de la fenetre
//		$description : description de la page
//		$motsCle : mots clé de la page (si $motsCle contient 'NOINDEX', la
//			page ne sera pas indexée par les moteurs de recherche
//		$canonical : url canonique si nécessaire
// SORTIE :
//		le code de l'entête HTML
//--------------------------------------------------------------------------
function writeHtmlHeader($titre, $description, $motsCle, $canonical='')
{
	$chaine = '<!doctype html>';
	$chaine.= '<html lang="'._LG_.'">';
	$chaine.= '<head>';
	
	//META TAGS : toujours les déclarer en premier
	$chaine.= '<meta charset="utf-8" />';
	$chaine.= '<meta name="description" content="'.$description.'" />';
	if($motsCle != '') {
		if($motsCle == 'NOINDEX') {
			$chaine.= '<meta name="robots" content="noindex, nofollow">';
		}
		else {
			$chaine.= '<meta name="robots" content="index, follow" />';
			$chaine.= '<meta name="keywords" content="'.$motsCle.'" />';
		}
	}
	$chaine.= '<meta name="author" content="'._AUTEUR_.'" />';
	$chaine.= '<meta name="copyright" content="'._COPYRIGHT_.'" />';
	$chaine.= '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />';
	$chaine.= '<meta http-equiv="x-ua-compatible" content="ie=edge" />';

	//chemin de base de l'application
	$chaine.= '<base href="'._URL_FRONT_END_.'" />';
	
	//icone de l'application
	$chaine.= '<link rel="shortcut icon" type="image/x-icon" href="'._IMAGES_COMMUN_.'site.ico" />';

	//CSS (respecter l'ordre)
	$chaine.= '<link rel="stylesheet" href="'._JAVASCRIPT_.'datetimepicker-master/jquery.datetimepicker.css" />';
	$chaine.= '<link rel="stylesheet" href="'._FONT_AWSOME_CSS_.'" />';
	$chaine.= '<link rel="stylesheet" href="'._BOOTSTRAP_CSS_.'" />';
	//la CSS de l'appli a la priorité sur toutes les autres CSS
	$chaine.= '<link rel="stylesheet" href="'._CSS_.'cookieDisclaimer.min.css">';
	$chaine.= '<link rel="stylesheet" href="'._CSS_.'styles.css" />';

	//Chemin canonique du script
	if ($canonical != '') 
		$chaine.= '<link rel="canonical" href="'.$canonical.'" />';
	$chaine.= '<link rel="top" href="index.php" title="'._APP_TITLE_.'" />';

	//titre de l'application
	$chaine.= '<title>'.$titre.'</title>';

	$chaine.= '</head>';
	return($chaine);
}

//-- RENVOIE LES INFORMATIONS DE FOOTER DES PAGES HTML ---------------------
// ENTREE :
//		$scriptSup : script javascript supplémentaire (entrée facultative)
//		$fonctionsJquery : script javascript de fonctions jquery (entrée facultative)
// SORTIE :
//		le code de l'entête HTML
//--------------------------------------------------------------------------
function writeHtmlFooter($scriptSup='', $fonctionsJquery='')
{
	$chaine = '';

	//chargement jQuery
	//-----------------
	$chaine.= '<script src="'._JAVASCRIPT_._JQUERY_.'"></script>';

	//chargement Bootstrap
	//-----------------
	$chaine.= '<script src="'._BOOTSTRAP_JS_.'"></script>';

	//chargement fonctions internes supplémentaires basées sur jQuery
	//-----------------
	$chaine.= '<script>';
	$chaine.= '$(document).ready(function () {';
	//activation tooltip bootstrap (avec initialisation 800ms a l'affichage et 100 à la disparition)
	$chaine.= '$("[data-toggle=\'tooltip\']").tooltip({ delay: { show: 800, hide: 100 } });';
	//code Cookie Disclaimer
	$chaine.= '$(\'body\').cookieDisclaimer({';
	$chaine.= 'layout: "bar",';
	$chaine.= 'position: "top",';
	$chaine.= 'style: "dark",';
	$chaine.= 'title: "Cookies",';
	$chaine.= 'text: "'.getLib('COOKIE_DISCLAIMER').'",';
	$chaine.= 'cssPosition: "fixed",';
	$chaine.= 'onAccepted: "",';
	$chaine.= 'acceptBtn: {';
	$chaine.= '		text: "'.getLib('JACCEPTE').'",';
	$chaine.= '		cssClass: "cdbtn cookie",';
	$chaine.= '		cssId: "cookieAcceptBtn",';
	$chaine.= '		onAfter: ""'; 
	$chaine.= '},';
	$chaine.= 'policyBtn: {';
	$chaine.= '		active: false,';
	$chaine.= '		text: "Read More",';
	$chaine.= '		link: "#",'; 
	$chaine.= '		linkTarget: "_blank",';
	$chaine.= '		cssClass: "cdbtn privacy",';
	$chaine.= '		cssId: "policyPageBtn"';
	$chaine.= '},';
	$chaine.= 'cookie: {';
	$chaine.= '		name: "cookieDisclaimer",';
	$chaine.= '		val: "confirmed",';
	$chaine.= '		path: "/",';
	$chaine.= '		expire: 365';
	$chaine.= '}'; 
	$chaine.= '});';
	$chaine.= $fonctionsJquery;
	$chaine.= '});';
	$chaine.= '</script>';

	//chargement des librairies javascript supplémentaires
	//-----------------
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'datetimepicker-master/jquery.datetimepicker.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'jquery-cookie/jquery.cookie.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'jquery.cookieDisclaimer.min.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'oXHR.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'scripts.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'php.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'universalform.min.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'resizable.min.js"></script>';
	$chaine.= $scriptSup;
	//lancement du code de recherche des la résolution écran du client
	$chaine.= getScreenResolution();

	return $chaine;
}

//- FONCTIONS D'AFFICHAGE DE MESSAGES --------------------------------------
// Positionnement d'un message (écrit en vert)
// Entree : 
//		$leMessage : chaine représentant le message à afficher
// Sortie : 
//		Rien
//--------------------------------------------------------------------------
// Fonctionnement : la chaine est stockée dans une variable de session qui
// sera relue ultérieurement
//--------------------------------------------------------------------------
function riseMessage($leMessage)
{
	$_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['message'] = $leMessage;
	$_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['color'] = 'alert-success';
}

//- FONCTIONS D'AFFICHAGE DE MESSAGES --------------------------------------
// Positionnement d'un message d'erreur (écrit en rouge)
// Entree : 
//		$leMessage : chaine représentant le message à afficher
// Sortie : 
//		Rien
//--------------------------------------------------------------------------
// Fonctionnement : la chaine est stockée dans une variable de session qui
// sera relue ultérieurement
//--------------------------------------------------------------------------
function riseErrorMessage($leMessage)
{
	$_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['message'] = $leMessage;
	$_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['color'] = 'alert-danger';
}

function riseWarningMessage($leMessage)
{
	$_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['message'] = $leMessage;
	$_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['color'] = 'alert-warning';
}

function riseInfoMessage($leMessage)
{
	$_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['message'] = $leMessage;
	$_SESSION[_APP_ID_.'MESSAGE_APPLICATION']['color'] = 'alert-info';
}

//- AFFICHAGE DE MESSAGES D'ALERTE -----------------------------------------
// Entree : 
//		$leMessage : tableau du message à afficher. Il a plusieurs rubriques :
//			title : titre du message
//			text : texte du message
//			align : alignement du texte (left, center, right)
//			size : taille de la boite d'alerte (25/50/75/100 ou personnalisé)
//			footer : texte en footer du message
//			color : couleur bootsrap (success, info, warning, danger)
//			dismiss : booleen (true, on peut fermer l'alerte)(false par defaut)
// Sortie : 
//		le code html bootstrap à afficher
//--------------------------------------------------------------------------
// https://v4-alpha.getbootstrap.com/components/alerts/
//--------------------------------------------------------------------------
function bootstrapAlert($leMessage)
{
	$chaine = '';
	if (empty($leMessage['dismiss'])) $leMessage['dismiss'] = false;
	if (!empty($leMessage['size'])) $leMessage['size'] = ' w-'.$leMessage['size']; else $leMessage['size'] = '';
	if (!empty($leMessage['align'])) $leMessage['align'] = ' text-'.$leMessage['align']; else $leMessage['align'] = '';
	if (empty($leMessage['text'])) $leMessage['text'] = 'Texte manquant&hellip;'; 

	if ($leMessage['dismiss']) {
		//on ajoute un bouton de fermeture de l'alerte
		$chaine.= '<div class="alert alert-'.$leMessage['color'].$leMessage['align'].$leMessage['size'].' alert-dismissible fade show" role="alert">';
			$chaine.= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
				$chaine.= '<span aria-hidden="true">&times;</span>';
			$chaine.= '</button>';
	}
	else {
		$chaine.= '<div class="w-50 alert alert-'.$leMessage['color'].$leMessage['align'].$leMessage['size'].'" role="alert">';
	}
	//titre
	if (!empty($leMessage['title'])) $chaine.= '<h4 class="alert-heading">'.$leMessage['title'].'</h4>';
	//texte
	$chaine.= '<p>'.$leMessage['text'].'</p>';
	//footer
	if (!empty($leMessage['footer'])) $chaine.= '<p class="mb-0">'.$leMessage['footer'].'</p>';
	$chaine.= '</div>';

	return $chaine;
}

//--------------------------------------------------------------------------
// Envoi un mail au webmaster du site
// Entree
//		$titre : objet du message
//		$informations : tableau contenant le corps du message
// Retour :
//		code d'erreur (négatif) ou 1 si ok
//--------------------------------------------------------------------------
function sendMailToAdmin($titre, $informations)
{
	//envoi d'un email au client avec rappel des identifiants
	$mail = new SilentMail();
	$mail->setMode('plain');
	$mail->setFrom(_EMAIL_WEBMASTER_);
	$mail->addTo(_EMAIL_WEBMASTER_);
	$mail->setSubject(_APP_TITLE_.' - '.$titre);
	$mail->setBody($informations);
	return $mail->send();
}