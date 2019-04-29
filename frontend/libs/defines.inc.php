<?php
//----------------------------------------------------------------------------------
//								DEFINITIONS								
//----------------------------------------------------------------------------------

//paramètres utilisés pour le mode de fonctionnement de l'application (dev / prod)
defined('_DEVELOPPEMENT_')			|| define('_DEVELOPPEMENT_',			'1');
defined('_PRODUCTION_')				|| define('_PRODUCTION_',				'0');

//type de shéma d'url
defined('_SCHEMA_NATUREL_')			|| define('_SCHEMA_NATUREL_',			'0');
defined('_SCHEMA_DOMAINE_')			|| define('_SCHEMA_DOMAINE_',			'1');

//type d'authentification possibles
defined('_ANNUAIRE_INTERNE_')		|| define('_ANNUAIRE_INTERNE_',			'interne');

//constantes pour la fonction executeQuery
defined('SQL_MODE_NORMAL')			|| define('SQL_MODE_NORMAL',			0);
defined('SQL_MODE_SILENT')			|| define('SQL_MODE_SILENT',			1);
defined('SQL_MODE_DEBUG')			|| define('SQL_MODE_DEBUG',				2);

//configuration particulière du site
include_once('libs/config.inc.php');
include_once('libs/config_db.inc.php');

//définitions des chemins frontend et backend
if (_APP_SCHEMA_ == _SCHEMA_NATUREL_) {
	//definition inline
	$site = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	$dummy = array_pop($site);		//enleve le nom du script
	$dummy = array_pop($site);		//enleve 'frontend' ou 'backend'
	$site = implode('/', $site);
	defined('_URL_SERVEUR_')	|| define('_URL_SERVEUR_',		$_SERVER['REQUEST_SCHEME'].'://'.$site.'/');
	defined('_URL_BASE_SITE_')	|| define('_URL_BASE_SITE_',	$_SERVER['REQUEST_SCHEME'].'://'.$site.'/frontend/');
	defined('_URL_FRONT_END_')	|| define('_URL_FRONT_END_',	$_SERVER['REQUEST_SCHEME'].'://'.$site.'/frontend/');
	defined('_URL_BACK_END_')	|| define('_URL_BACK_END_',		$_SERVER['REQUEST_SCHEME'].'://'.$site.'/backend/');
}
elseif (_APP_SCHEMA_ == _SCHEMA_DOMAINE_) {
	//definition par domaine
	defined('_URL_SERVEUR_')	|| define('_URL_SERVEUR_',		$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/');
	defined('_URL_BASE_SITE_')	|| define('_URL_BASE_SITE_',	$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/');
	defined('_URL_FRONT_END_')	|| define('_URL_FRONT_END_',	$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/');
	//determination du backend
	if (strpos(_URL_FRONT_END_, 'localhost') !== false) {
		//localhost (ex : "http://extranet.cftcdefense.localhost")
		defined('_URL_BACK_END_') || define('_URL_BACK_END_', $_SERVER['REQUEST_SCHEME'].'://'.'extranet.'.$_SERVER['HTTP_HOST'].'/');
	}
	else {
		//web (ex : "www.cftcdefense.fr" => "extranet.cftcdefense.fr")
		defined('_URL_BACK_END_') || define('_URL_BACK_END_', str_replace('www.', 'extranet.', _URL_FRONT_END_));
	}
}
else die('Schema d\'url non renseigné');

//chemins absolus (http://) vers les images
defined('_IMAGES_FRONT_END_')		|| define('_IMAGES_FRONT_END_',			_URL_FRONT_END_.'images/');

//chemins
defined('_IMAGES_')					|| define('_IMAGES_',					'images/');
defined('_IMAGES_COMMUN_')			|| define('_IMAGES_COMMUN_',			_IMAGES_.'commun/');
defined('_IMAGES_DRAPEAUX_')		|| define('_IMAGES_DRAPEAUX_',			_IMAGES_.'drapeaux/');
defined('_CSS_')					|| define('_CSS_',						'css/');
defined('_JAVASCRIPT_')				|| define('_JAVASCRIPT_',				'js/');
defined('_BOOTSTRAP_')				|| define('_BOOTSTRAP_',				'bootstrap-4.3.1-dist/');
defined('_FONT_AWSOME_')			|| define('_FONT_AWSOME_',				'fontawesome-free-5.7.2-web/');
defined('_LIBS_')					|| define('_LIBS_',						'libs/');
defined('_CLASSES_')				|| define('_CLASSES_',					_LIBS_.'classes/');
defined('_BRIQUES_')				|| define('_BRIQUES_',					_LIBS_.'briques/');
defined('_LANGUES_')				|| define('_LANGUES_',					_LIBS_.'langues/');
defined('_SQL_')					|| define('_SQL_',						_LIBS_.'sql/');
defined('_ARMOIRE_')				|| define('_ARMOIRE_',					_LIBS_.'armoire/');

//paramètres utilisés pour rendre l'application unique
defined('_APP_ID_')					|| define('_APP_ID_',					md5(_APP_BLOWFISH_));			//id de l'application
defined('_APP_DROITS_')				|| define('_APP_DROITS_',				_APP_ID_.'_droits');			//var. session contient les droits de l'appli
defined('_APP_LOGIN_')				|| define('_APP_LOGIN_',				_APP_ID_.'_login');				//var. session contient les infos sur l'utilisateur loggué
defined('_APP_LANGUE_ENCOURS_')		|| define('_APP_LANGUE_ENCOURS_',		_APP_ID_.'_langue_en_cours');	//var. session contient la langue en cours de l'appli
defined('_APP_LANGUE_CHANGEE_')		|| define('_APP_LANGUE_CHANGEE_',		_APP_ID_.'_langue_changee');	//var. session contient la langue changée de l'appli
defined('_APP_INPUT_')				|| define('_APP_INPUT_',				_APP_ID_.'_input');				//var. session contient l'entrée saisie du formulaire de classe Form_input

//fichiers
defined('_PHP_FILE_ERRORS_')		|| define('_PHP_FILE_ERRORS_',			_ARMOIRE_.'errors.txt');

//type de logs possibles
defined('_LOG_CONNEXION_')			|| define('_LOG_CONNEXION_',			'1');
defined('_LOG_ERREUR_')				|| define('_LOG_ERREUR_',				'2');

//versions de logiciels tiers
defined('_JQUERY_VERSION_')			|| define('_JQUERY_VERSION_',			'3.3.1');
defined('_JQUERY_')					|| define('_JQUERY_',					'jquery-3.3.1.min.js');			//jQuery
defined('_BOOTSTRAP_VERSION_')		|| define('_BOOTSTRAP_VERSION_',		'4.3.1');
defined('_BOOTSTRAP_JS_')			|| define('_BOOTSTRAP_JS_',				_BOOTSTRAP_.'js/bootstrap.bundle.min.js');
defined('_BOOTSTRAP_CSS_')			|| define('_BOOTSTRAP_CSS_',			_BOOTSTRAP_.'css/bootstrap.min.css');
defined('_FONTAWESOME_VERSION_')	|| define('_FONTAWESOME_VERSION_',		'5.7.2');
defined('_FONT_AWSOME_CSS_')		|| define('_FONT_AWSOME_CSS_',			_FONT_AWSOME_.'css/all.min.css');

//briques et scripts
defined('_BRIQUE_FOOTER_')			|| define('_BRIQUE_FOOTER_',			_BRIQUES_.'brique_footer.inc.php');
defined('_BRIQUE_MESSAGE_')			|| define('_BRIQUE_MESSAGE_',			_BRIQUES_.'brique_message.inc.php');
defined('_BRIQUE_ERREUR_')			|| define('_BRIQUE_ERREUR_',			_BRIQUES_.'brique_erreur.inc.php');
defined('_BRIQUE_DEBUG_')			|| define('_BRIQUE_DEBUG_',				_BRIQUES_.'brique_debug.inc.php');
defined('_BRIQUE_COL_GAUCHE_')		|| define('_BRIQUE_COL_GAUCHE_',		_BRIQUES_.'brique_colgauche.inc.php');
defined('_BRIQUE_PANEL_APPLI_')		|| define('_BRIQUE_PANEL_APPLI_',		_BRIQUES_.'brique_panelappli.inc.php');
defined('_BRIQUE_COL_DROITE_')		|| define('_BRIQUE_COL_DROITE_',		_BRIQUES_.'brique_coldroite.inc.php');

defined('_URL_INDEX_')				|| define('_URL_INDEX_',				'index.php');
defined('_URL_LOGOUT_')				|| define('_URL_LOGOUT_',				'logout.php');
defined('_URL_AUTHENTIFICATION_')	|| define('_URL_AUTHENTIFICATION_',		'authentification.php');

//drapeaux
defined('_DRAPEAU_FR_')				|| define('_DRAPEAU_FR_',				_IMAGES_DRAPEAUX_.'fr.jpg');
defined('_DRAPEAU_US_')				|| define('_DRAPEAU_US_',				_IMAGES_DRAPEAUX_.'uk.jpg');

//regex
defined('PAGEREGEX')				|| define('PAGEREGEX',					'#^[0-9]{0,}$#');				//0..n chiffres ou rien