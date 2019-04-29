<?php
//-----------------------------------------------------------
// EXEMPLE VIDEOTHEQUE																	
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
//librairies généralistes
require_once('libs/routines.inc.php');			//routines generale inter-applications

$site = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$dummy = array_pop($site);
$site = implode('/', $site);

defined('_URL_BASE_SITE_') || define('_URL_BASE_SITE_',	$_SERVER['REQUEST_SCHEME'].'://'.$site.'/');
defined('_URL_FRONT_END_') || define('_URL_FRONT_END_',	$_SERVER['REQUEST_SCHEME'].'://'.$site.'/');

//chemins absolus (http://) vers les images
defined('_IMAGES_FRONT_END_')	|| define('_IMAGES_FRONT_END_',	_URL_FRONT_END_.'images/');

//chemins du backend
defined('_IMAGES_')				|| define('_IMAGES_',			'images/');
defined('_IMAGES_COMMUN_')		|| define('_IMAGES_COMMUN_',	_IMAGES_.'commun/');
defined('_CSS_')				|| define('_CSS_',				'css/');
defined('_JAVASCRIPT_')			|| define('_JAVASCRIPT_',		'js/');
defined('_BOOTSTRAP_JS_')		|| define('_BOOTSTRAP_JS_',		'bootstrap-4.3.1-dist/js/');
defined('_BOOTSTRAP_CSS_')		|| define('_BOOTSTRAP_CSS_',	'bootstrap-4.3.1-dist/css/');
defined('_FONT_AWSOME_CSS_')	|| define('_FONT_AWSOME_CSS_',	'fontawesome-free-5.7.2/css/');
defined('_LIBS_')				|| define('_LIBS_',				'libs/');
defined('_CLASSES_')			|| define('_CLASSES_',			_LIBS_.'classes/');

//Infos propriétaire du site
defined('_APP_BLOWFISH_')		|| define('_APP_BLOWFISH_',		'videotheque');
defined('_APP_ID_')				|| define('_APP_ID_',			md5(_APP_BLOWFISH_));			//id de l'application
defined('_APP_TITLE_')			|| define('_APP_TITLE_',		'Saisie vidéothèque');
defined('_EMAIL_WEBMASTER_')	|| define('_EMAIL_WEBMASTER_',	'webmaster@yoursite.fr');
defined('_AUTEUR_')				|| define('_AUTEUR_',			'Auteur');
defined('_COPYRIGHT_')			|| define('_COPYRIGHT_',		'Auteur - 2016');
defined('_APP_VERSION_')		|| define('_APP_VERSION_',		'v0.0.0');
defined('_LG_')					|| define('_LG_',				'fr');
defined('_IP_DEVELOPPEMENT_')	|| define('_IP_DEVELOPPEMENT_',	array('120.146.72.10'));

defined('_JQUERY_')				|| define('_JQUERY_',			'jquery-3.3.1.min.js');
defined('_BOOTSTRAP_')			|| define('_BOOTSTRAP_',		'bootstrap.min.js');

//Auto-chargement des classes (obligatoire avant le session_start() pour pouvoir passer des objets dans des variables de session)
function chargerClasses($classe) {
	require_once(_CLASSES_.$classe.'.class.php');
}
spl_autoload_register('chargerClasses');

defined('_APP_SLOGAN_')			|| define('_APP_SLOGAN_',		'Version UniversalForm : '.UniversalForm::VERSION);

//session
session_name(_APP_BLOWFISH_);						//nommage de la session pour qu'elle soit propre à l'application
session_start();									//demarrage session
session_regenerate_id(false);						//regenère un ID pour sécuriser l'application

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
			$chaine.= '<meta name="robots" content="noindex, nofollow" />';
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
	$chaine.= '<link rel="stylesheet" href="'._JAVASCRIPT_.'datetimepicker-master/jquery.datetimepicker.css"/ >';
	$chaine.= '<link rel="stylesheet" href="'._FONT_AWSOME_CSS_.'fontawesome-all.min.css" />';
	$chaine.= '<link rel="stylesheet" href="'._BOOTSTRAP_CSS_.'bootstrap.min.css" />';

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
	$chaine.= '<script src="'._BOOTSTRAP_JS_._BOOTSTRAP_.'"></script>';

	//chargement fonctions internes supplémentaires basées sur jQuery
	//-----------------
	$chaine.= '<script>';
	$chaine.= '$(document).ready(function () {';
	$chaine.= '$("[data-toggle=\'tooltip\']").tooltip();';		//activation tooltip bootstrap
	$chaine.= $fonctionsJquery;
	$chaine.= '});';
	$chaine.= '</script>';

	//chargement des librairies javascript supplémentaires
	//-----------------
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'datetimepicker-master/jquery.datetimepicker.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'oXHR.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'scripts.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'php.js"></script>';
	$chaine.= '<script type="text/javascript" src="'._JAVASCRIPT_.'universalform.min.js"></script>';
	$chaine.= $scriptSup;

	return $chaine;
}

//---------------------------------------------------------
// Fonctions de callback
//---------------------------------------------------------
function fillSelect($value)
{
	$html = '';
	($value == '') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value=""'.$defaut.'>Choisissez un genre</option>';
	($value == 'action') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="action"'.$defaut.'>Action</option>';
	($value == 'comedie') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="comedie"'.$defaut.'>Comédie</option>';
	($value == 'horreur') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="horreur"'.$defaut.'>Horreur</option>';
	($value == 'romance') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="romance"'.$defaut.'>Romance</option>';
	return $html;
}

//---------------------------------------------------------
// header
//---------------------------------------------------------
echo writeHtmlHeader(_APP_TITLE_, '', '');

//---------------------------------------------------------
// body
//---------------------------------------------------------
echo '<body>';

	//---------------------------------------------------------
	// Corps APPLI
	//---------------------------------------------------------
	echo '<div class="container-fluid">';

		echo '<div class="row mt-3">';
			echo '<div class="col-6 ml-auto mr-auto">';

				//panel de l'application
				echo '<div class="row p-3">';
					echo '<div class="col-12 bg-light ">';
						echo '<p class="h1">'._APP_TITLE_.'</p>';
						echo '<p class="lead">'._APP_SLOGAN_.'</p>';
					echo '</div>';
				echo '</div>';

				//code propre à la page
				echo '<div class="row mt-3">';
					echo '<div class="col-12">';

					$frm = new Form_exemple_videotheque('ajouter', 1);
					$action = $frm->getAction();

					switch($action) {
						case 'ajouter': {
							$frm->init();
							echo $frm->afficher();
							break;
						}
						case 'valid_ajouter': {
							if (!$frm->tester()) {
//								DEBUG_('POST', $_POST);
								echo $frm->afficher();
							}
							else {
								$donnees = $frm->getData();
								DEBUG_TAB_($donnees);
//								DEBUG_('POST', $_POST);
//								DEBUG_('donnees', $donnees);
							}
							break;
						}
					}

					echo '</div>';
				echo '</div>';

			echo '</div>';  //fin colonne centrale

		echo '</div>';

	echo '</div>';

	//---------------------------------------------------------
	// Footer APPLI
	//---------------------------------------------------------

	//Fenetre de DEBUG
	if (in_array($_SERVER['REMOTE_ADDR'], array_merge(array('127.0.0.1', '::1'), _IP_DEVELOPPEMENT_))) {
		if (!empty($_SESSION[_APP_ID_.'DEBUG_PHP'])) {
			echo '<div class="jumbotron jumbotron-fluid">';
				echo '<div class="container-fluid">';
					echo '<h1>>> DEBUGGAGE <<</h1>';
					DEBUG_PRINT_();
				echo '</div>';
			echo '</div>';
		}
	}

	// Scripts Javascripts placés à la fin pour accélérer le chargement des pages
	echo writeHtmlFooter();

echo '</body>';
echo '</html>';