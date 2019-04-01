<?php
//-----------------------------------------------------------
// EXEMPLES UNIVERSALFORM
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8							
//-----------------------------------------------------------
require_once('libs/common.inc.php');

/*

$site = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$dummy = array_pop($site);
$site = implode('/', $site);

defined('_URL_BASE_SITE_') || define('_URL_BASE_SITE_',	$_SERVER['REQUEST_SCHEME'].'://'.$site.'/');
defined('_URL_FRONT_END_') || define('_URL_FRONT_END_',	$_SERVER['REQUEST_SCHEME'].'://'.$site.'/');

//chemins absolus (http://) vers les images
defined('_IMAGES_FRONT_END_')		|| define('_IMAGES_FRONT_END_',	_URL_FRONT_END_.'images/');

//Infos propriétaire du site
defined('_APP_BLOWFISH_')		|| define('_APP_BLOWFISH_',		'exemples');
defined('_APP_ID_')				|| define('_APP_ID_',			md5(_APP_BLOWFISH_));			//id de l'application
defined('_APP_TITLE_')			|| define('_APP_TITLE_',		'Exemples UniversalForm');
defined('_EMAIL_WEBMASTER_')	|| define('_EMAIL_WEBMASTER_',	'fabrice.labrousse@intradef.gouv.fr');
defined('_AUTEUR_')				|| define('_AUTEUR_',			'Fabrice Labrousse');
defined('_COPYRIGHT_')			|| define('_COPYRIGHT_',		'Auteur : Fabrice Labrousse - 2016');
defined('_VERSION_APP_')		|| define('_VERSION_APP_',		'v0.0.0');
defined('_LG_')					|| define('_LG_',				'fr');
defined('_IP_DEVELOPPEMENT_')	|| define('_IP_DEVELOPPEMENT_',	array('20.46.72.1'));

//chemins
defined('_IMAGES_')					|| define('_IMAGES_',					'images/');
defined('_IMAGES_COMMUN_')			|| define('_IMAGES_COMMUN_',			_IMAGES_.'common/');
defined('_CSS_')					|| define('_CSS_',						'css/');
defined('_JAVASCRIPT_')				|| define('_JAVASCRIPT_',				'js/');
defined('_BOOTSTRAP_')				|| define('_BOOTSTRAP_',				'bootstrap-4.1.3-dist/');
defined('_FONT_AWSOME_CSS_')		|| define('_FONT_AWSOME_CSS_',			'fontawesome-free-5.0.10/css/');
defined('_LIBS_')					|| define('_LIBS_',						'libs/');
defined('_CLASSES_')				|| define('_CLASSES_',					_LIBS_.'classes/');

//versions de logiciels tiers
defined('_JQUERY_VERSION_')			|| define('_JQUERY_VERSION_',			'3.3.1');
defined('_JQUERY_')					|| define('_JQUERY_',					'jquery-3.3.1.min.js');			//jQuery
defined('_BOOTSTRAP_VERSION_')		|| define('_BOOTSTRAP_VERSION_',		'4.1.3');
defined('_BOOTSTRAP_JS_')			|| define('_BOOTSTRAP_JS_',				_BOOTSTRAP_.'js/bootstrap.bundle.min.js');
defined('_BOOTSTRAP_CSS_')			|| define('_BOOTSTRAP_CSS_',			_BOOTSTRAP_.'css/bootstrap.min.css');

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

//compression des pages
//ob_start('ob_gzhandler');

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
	
	//CSS (respecter l'ordre)
	$chaine.= '<link rel="stylesheet" href="'._JAVASCRIPT_.'datetimepicker-master/jquery.datetimepicker.css" />';
	$chaine.= '<link rel="stylesheet" href="'._FONT_AWSOME_CSS_.'fontawesome-all.min.css" />';
	$chaine.= '<link rel="stylesheet" href="'._BOOTSTRAP_CSS_.'" />';

	$chaine.= '<style>';
	$chaine.= '.rouge {';
	$chaine.= '		color:red;';
	$chaine.= '}';
	$chaine.= '.bleu {';
	$chaine.= '		color: blue;';
	$chaine.= '}';
	$chaine.= '.vert {';
	$chaine.= '		color:green;';
	$chaine.= '}';
	$chaine.= '.souligne {';
	$chaine.= '		font-decoration:underline;';
	$chaine.= '}';
	$chaine.= '.gras {';
	$chaine.= '		font-weight:bold;';
	$chaine.= '}';
	$chaine.= '.border-simple {';
	$chaine.= '		border: 1px dotted silver';
	$chaine.= '}';
	$chaine.= '.filtre {';
	$chaine.= '		width: 2.25em;';
	$chaine.= '		height: 1.75em;';
	$chaine.= '		border: 0;';
	$chaine.= '		border-radius: .25rem;';
	$chaine.= '		margin: 0;';
	$chaine.= '		padding: 0;';
	$chaine.= '		color: #fff;';
	$chaine.= '		font-weight: bold;';
	$chaine.= '		cursor: pointer;';
	$chaine.= '}';
	$chaine.= '.filtre:hover {';
	$chaine.= '		background:#bcbcbc;';
	$chaine.= '}';
	$chaine.= '.sidebar {';
	$chaine.= '		background-color: #f5f5f5;';
	$chaine.= '		border-radius: .25rem;';
	$chaine.= '		padding: 0.5rem;';
	$chaine.= '}';
	$chaine.= '.souligne_epais {';
	$chaine.= '		border-bottom: 0.5rem solid #5c76a9;';
	$chaine.= '}';
	$chaine.= '.surligne {';
	$chaine.= '		border-top : 0.1rem solid #5c76a9;';
	$chaine.= '}';
	$chaine.= '</style>';

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
//		$fonctionsJquery : script javascript de finctions jquery (entrée facultative)
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
*/
//---------------------------------------------------------
// Fonctions de callback
//---------------------------------------------------------
function fillSelect($value)
{
	$html = '';
	($value == '') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="Indéfini"'.$defaut.'>Choose a genre</option>';
	($value == 'action') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="action"'.$defaut.'>Action</option>';
	($value == 'comedy') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="comedy"'.$defaut.'>Comedy</option>';
	($value == 'horror') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="horror"'.$defaut.'>Horror</option>';
	($value == 'romance') ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="romance"'.$defaut.'>Romance</option>';
	return $html;
}

function fillSelect2($value)
{
	$html = '';
	(in_array('', $value)) ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value=""'.$defaut.'>Choose a genre</option>';
	(in_array('action', $value)) ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="action"'.$defaut.'>Action</option>';
	(in_array('comedy', $value)) ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="comedy"'.$defaut.'>Comedy</option>';
	(in_array('horror', $value)) ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="horror"'.$defaut.'>Horror</option>';
	(in_array('romance', $value)) ? $defaut = ' selected' : $defaut = '';
	$html.= '<option value="romance"'.$defaut.'>Romance</option>';
	return $html;
}

//---------------------------------------------------------
// head
//---------------------------------------------------------
$titrePage = _APP_TITLE_;
$scriptSup = '';
$fJquery = '';
echo writeHTMLHeader($titrePage, '', '');

//---------------------------------------------------------
// body
//---------------------------------------------------------
echo '<body>';
	echo '<div class="container-fluid">';

	//--------------------------------------
	// HEADER
	//--------------------------------------
	include_once(_BRIQUE_HEADER_);

	//---------------------------------------------------------
	// Corps APPLI
	//---------------------------------------------------------

	echo '<div class="row mt-3">';

		//colonne centrale
		echo '<div class="col-8 mr-auto ml-auto">';

			//panel de l'application
			echo '<div class="row">';
				echo '<div class="col-12">';
					echo '<div class="sidebar souligne_epais">';
						echo '<p class="h1">Exemples Classes UniversalForm</p>';
						echo '<p class="lead">UniversalForm '.UniversalForm::VERSION.'</p>';
					echo '</div>';
				echo '</div>';
			echo '</div>';

			//code propre à la page
			echo '<div class="row mt-3">';
				echo '<div class="col-12">';

				$frm = new Form_exemples_universalform('ajouter', 1);
				$action = $frm->getAction();

				switch($action) {
					case 'ajouter': {
						$frm->init();
						echo $frm->afficher();
						break;
					}
					case 'valid_ajouter': {
						if (!$frm->tester()) {
//								DEBUG_TAB_($_POST);
							echo $frm->afficher();
						}
						else {
							$donnees = $frm->getData();
//								DEBUG_TAB_($_POST);
							DEBUG_TAB_($donnees);
						}
						break;
					}
				}

				echo '</div>';
			echo '</div>';

		echo '</div>';  //fin colonne centrale

	echo '</div>';

	//--------------------------------------
	// FOOTER
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);

	echo '</div>';		//container
echo '</body>';
echo '</html>';