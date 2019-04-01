<?php
//----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
//----------------------------------------------------------------------
// Personnalisation du site
// éè : UTF-8
//----------------------------------------------------------------------

defined('_APP_SCHEMA_')				|| define('_APP_SCHEMA_',				_SCHEMA_NATUREL_);		//_SCHEMA_NATUREL_ (url inline) / _SCHEMA_DOMAINE_ (url par domaine)
defined('_APP_TITLE_')				|| define('_APP_TITLE_',				'Frontend');
defined('_APP_SLOGAN_')				|| define('_APP_SLOGAN_',				'Slogan de l\'application');
defined('_AUTEUR_')					|| define('_AUTEUR_',					'Auteur');
defined('_COPYRIGHT_')				|| define('_COPYRIGHT_',				'&copy;');
defined('_VERSION_APP_')			|| define('_VERSION_APP_',				'v1.0.0');
defined('_EMAIL_WEBMASTER_')		|| define('_EMAIL_WEBMASTER_',			'');
defined('_IP_DEVELOPPEMENT_')		|| define('_IP_DEVELOPPEMENT_',			array('xxx.xxx.xxx.xxx', 'xxx.xxx.xxx.xxx'));	//ip développeurs
defined('_APP_BLOWFISH_')			|| define('_APP_BLOWFISH_',				'unechainedecaractereunique');
defined('_RUN_MODE_')				|| define('_RUN_MODE_',					_DEVELOPPEMENT_);		//_DEVELOPPEMENT_ / _PRODUCTION_

//----------------------------------------------------------------------
// Choix de l'annuaire utilisé pour l'authentification : 
//	_ANNUAIRE_INTERNE_			=> gestion des utilisateur entièrement géré par l'application (base de données)
// S'il faut changer d'annuaire, d'abord détruire la variable de session _APP_LOGIN_
//----------------------------------------------------------------------
defined('_ANNUAIRE_') || define('_ANNUAIRE_', _ANNUAIRE_INTERNE_);