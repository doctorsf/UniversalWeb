<?php
//----------------------------------------------------------------------------------
//								LANGUE ANGLAIS
//----------------------------------------------------------------------------------
// Fichier de langue ANGLAIS
//----------------------------------------------------------------------------------

defined('_LG_')						|| define('_LG_', 'en');
defined('_FR_')						|| define('_FR_', 'fr');
defined('_EN_')						|| define('_EN_', 'en');
defined('_INT_')					|| define('_INT_', 'int');		//international (=toutes les langues)
defined('_FORMAT_DATE_TIME_SQL_')	|| define('_FORMAT_DATE_TIME_SQL_', 'Y-m-d H:i:s');
defined('_FORMAT_DATE_SQL_')		|| define('_FORMAT_DATE_SQL_',		'Y-m-d');
defined('_FORMAT_DATE_')			|| define('_FORMAT_DATE_',			'm/d/Y');
defined('_FORMAT_DATE_TIME_')		|| define('_FORMAT_DATE_TIME_',		'm/d/Y H:i:s');
defined('_FORMAT_DATE_TIME_NOS_')	|| define('_FORMAT_DATE_TIME_NOS_', 'm/d/Y H:i');	//format datetime sans les secondes
defined('_IMAGES_LANGUE_')			|| define('_IMAGES_LANGUE_', _IMAGES_.'en/');

//on pose la variable de session 'langue_en_cours' utilisée dans les cas particuliers ou l'information de langue n'est pas dans l'url comme par exemple l'appel à des fonctions solitaires (ex : reponses-ajax.php)
$_SESSION[_APP_LANGUE_ENCOURS_] = _LG_;

$_LIBELLES = array(

	//------------------------------------------
	// Libellés des classes UniversalField (UFC)
	//------------------------------------------
	'UFC_ERREURS_A_CORRIGER'		=> 'Errors to correct before import',
	'UFC_CHAMP_REQUIS'				=> 'This field is required',
	'UFC_CHAMP_NUMERIQUE'			=> 'This field must be numeric',
	'UFC_CHOIX_INVALIDE'			=> 'Not a valid choice',
	'UFC_INTEGER_1OU2'				=> 'Integer expected (2 numbers max)',
	'UFC_FLOAT'						=> 'Float number expected (option decimals)',
	'UFC_FLOAT_2DEC'				=> 'Float number expected (option 2 decimals)',
	'UFC_INTEGER_X'					=> '%d digits expected',
	'UFC_INTEGER'					=> 'Integer expected',
	'UFC_DOLLARS'					=> '$ expected',
	'UFC_MONNAIE'					=> 'Currency expected',
	'UFC_INTEGER_SPACED'			=> 'Integer expected (spaces welcome)',
	'UFC_UNSIGNED_INTEGER'			=> 'Unsigned integer expected',
	'UFC_SIGNED_INTEGER'			=> 'Signed integer expected',
	'UFC_BOOLEAN'					=> '1 or 0 expected',
	'UFC_DATETIME'					=> 'Datetime expected : yyyy-mm-jj hh:mm:ss',
	'UFC_EMAIL'						=> 'eMail expected',
	'UFC_EMAIL_APOSTROPHE'			=> 'eMail expected (apostrophe welcome)',
	'UFC_ALPHA_CODE'				=> 'Coding characters awaited',
	'UFC_ALPHA_SIMPLE'				=> 'Simple text expected',
	'UFC_ALPHA_NOMS'				=> 'Simple alphanum with emphasis expected',
	'UFC_FILE_NAME'					=> 'File name characters expected only',
	'UFC_URL'						=> 'url expected',
	'UFC_IPV4'						=> 'IP V4 adress expected',
	'UFC_MAC'						=> 'Mac adresse expected',
	'UFC_SHA1'						=> '40 hex chars awaited',
	'UFC_MAJ_VERROUILLEES'			=> 'Caps Lock is on',
	'UFC_FICHIER_INEXISTANT'		=> 'This file does not exist&hellip;',
	'UFC_TYPE_FICHIER_NON_AUTORISE'	=> 'File type not allowed. Are allowed: ',
	'UFC_NOM_FICHIER_NON_VALIDE'	=> 'Invalid filename.',
	'UFC_POIDS_FICHIER_MOINS_DE'	=> 'Your file must not exceed ',
	'UFC_UPLOAD_MAX_FILE_SIZE_INI'	=> 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
	'UFC_UPLOAD_MAX_FILE_SIZE_FORM'	=> 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
	'UFC_IMAGE_PARTIEL_TELECHARGEE'	=> 'The uploaded file was only partially uploaded',
	'UFC_AUCUN_FICHIER_TELECHARGE'	=> 'No file was uploaded',
	'UFC_DOSSIER_TEMP_MANQUANT'		=> 'Missing a temporary folder',
	'UFC_ECHEC_ECRITURE_DISQUE'		=> 'Failed to write file to disk',
	'UFC_ECHEC'						=> 'Failure&hellip;',
	'UFC_MATCH_INVALIDE'			=> 'Unknown testMatches property!',
	'UFC_MAX_LENGTH'				=> 'length > %d chars (%d)',
	'UFC_MIN_LENGTH'				=> 'length < %d chars (%d)',

	//------------------------------------------
	// ERREURS
	//------------------------------------------
	'ERREUR'						=> 'Error',
	'ERREUR_AUCUNE'					=> 'No Error',
	'ERREUR_SQL'					=> 'SQL error&hellip;',
	'ERREUR_COMMANDE'				=> 'Choice error&hellip;',
	'ERREUR_ANNUAIRE'				=> 'LDAP Connexion Error',
	'ERREUR_DROITS'					=> 'Rights must be precised&hellip;',
	'ECHEC_LOGIN'					=> 'Login failure for %s. Contact the administrator !',
	'ERREUR_CREATION_USER'			=> 'Error while creating a user',
	'ERREUR_MODIF_USER_X'			=> 'Error while updating user %s',
	'ERREUR_SUPPR_USER_X'			=> 'Error while deleting user %s',
	'ERREUR_SUICIDE'				=> 'It is impossible to delete your own account. Ask another administrator to do this for you...',
	'ERREUR_FICHIER_INEXISTANT'		=> 'File not found',
	'PHP_ERROR_FILE_VIDER'			=> 'Empty PHP Errors File',
	'PHP_ERROR_FILE_DELETED'		=> 'PHP Errors File deleted with success&hellip;',
	'PHP_ERROR_FILE_ERROR'			=> 'Error while deleting PHP Errors File!',
	'ERREURS_BACKEND'				=> 'Backend Errors',
	'ERREURS_FRONTEND'				=> 'Frontend Errors',

	//------------------------------------------
	// MOTS / VERBES
	//------------------------------------------
	'FICHIER'						=> 'File',
	'VERSION'						=> 'Version',
	'ACCUEIL'						=> 'Home',
	'BIENVENUE'						=> 'Welcome',
	'LOGIN'							=> 'Login',
	'LOGOUT'						=> 'Log out',
	'DECONNECTEZ_MOI'				=> 'Log me out',
	'RECHERCHE'						=> 'Search',
	'ADMINISTRATION'				=> 'Administration',
	'SAISIE'						=> 'Input',
	'AJOUTER'						=> 'Add',
	'EDITER'						=> 'Edit',
	'MODIFIER'						=> 'Update',
	'MODIF'							=> 'Update',
	'DUPLIQUER'						=> 'Duplicate',
	'SUPPRIMER'						=> 'Delete',
	'ANNULER'						=> 'Cancel',
	'RETOUR'						=> 'Back',
	'LEGENDE'						=> 'Caption',
	'PROFIL'						=> 'Profile',
	'ID'							=> 'Id',
	'EMAIL'							=> 'eMail',
	'LANGUE'						=> 'Language',
	'ACTION'						=> 'Action',
	'SUPPR'							=> 'Del.',
	'OPERATIONS'					=> 'What To Do',
	'AUTOLOG'						=> 'Autolog',
	'FRANCAIS'						=> 'french',
	'ANGLAIS'						=> 'english',
	'MATRICULE'						=> 'Number',
	'POSTE'							=> 'Tel',
	'SERVICE'						=> 'Service',
	'FONCTION'						=> 'Function',
	'PIECE'							=> 'Room',
	'TOUS'							=> 'all',
	'PERSONNEL'						=> 'People',
	'AUTRE'							=> 'Other',
	'DEFAUT'						=> 'Default',
	'LETTRE'						=> 'Letter',
	'CHIFFRE'						=> 'Number',
	'STATUT'						=> 'Position',
	'PHOTO'							=> 'Photo',
	'LIBELLE'						=> 'Caption',
	'IP'							=> 'IP Adress',
	'FAMILLE'						=> 'Family',
	'DROITS'						=> 'Policies',
	'UTILISATEUR'					=> 'User',
	'REFERENCIEL'					=> 'Filing',
	'SOUS-MENU'						=> 'Submenu',
	'EXAMPLES'						=> 'Examples',
	'MANQUANT'						=> 'Missing',
	'COMPOSANTS'					=> 'Components',
	'CONFIGURATION'					=> 'Configuration',

	//------------------------------------------
	// LOGS
	//------------------------------------------
	'LOGS'							=> 'Logs',
	'LOG_LOGIN'						=> 'Login %s',
	'LOG_LOGOUT'					=> 'Logout %s',
	'LOG_ERREUR'					=> 'Login error for  %s',
	'LOG_LOGIN_USURPATION'			=> 'Usurpation %s login attempt by %s sAMAccountName',
	'LOG_ERREUR_ANNUAIRE'			=> 'Directory connexion error for %s',
	'LOG_ANNUAIRE_USER_UNKNOWN'		=> 'Unknown user %s in directory: access denied',
	'LOG_USER_UNKNOWN'				=> 'User %s in unknown in the database: access denied',
	'LOG_USER_BAD_PASSWORD'			=> 'Wrong password for %s',
	'LOG_RETIRE_AUCUN'				=> 'No log deleted&hellip;',
	'LOG_RETIRE_X'					=> '%d logs successfull deleted&hellip;',
	'LOG_PURGE'						=> 'Log purge successful&hellip;',
	'LOGS_PURGER'					=> 'Purge logs',
	'LOGS_VIDER'					=> 'Delete logs',
	'LOG_TYPE_DATE'					=> 'by type of log, then by date',
	'LOG_OPERATION_DATE'			=> 'by opération, then by date',
	'LOG_USER_DATE'					=> 'by user, then by date',
	'LOG_FILTRE_TYPE'				=> 'Log type filter',
	'LOG_FILTRE_LIBELLE'			=> 'Log libelle filter',
	'LOG_FILTRE_USER'				=> 'User filter',
	'LOG_DATE'						=> 'Log date',

	//------------------------------------------
	// DATES
	//------------------------------------------
	'TODAY_IS'						=> 'Today is %s',
	'DATE_CREATION'					=> 'Creation Date',
	'DERNIER_ACCES'					=> 'Last Access',
	'DERNIERE_IP'					=> 'Last known IP',

	//------------------------------------------
	// COMPTE
	//------------------------------------------
	'ACCEDER_MON_COMPTE'			=> 'Access my account',
	'COMPTE_ACTIF'					=> 'active account',
	'COMPTE_DESACTIVE'				=> 'unactivated account',
	'TESTEUR'						=> 'Testing user',
	'ACTION_DEMANDEE'				=> 'Action Request',
	'CODE_VALIDATION'				=> 'Validation Code',

	//------------------------------------------
	// DIALOGUES
	//------------------------------------------
	'CHAMP_REQUIS'					=> 'Required field',
	'LECTURE_SEULE'					=> 'Read Only',
	'TAILLE_LISTING'				=> 'Listing size',

	//------------------------------------------
	// IDENTIFIANT / UTILISATEUR
	//------------------------------------------
	'IDENTIFIANT'					=> 'Id',
	'IDENTIFIEZ-VOUS'				=> 'Identify yourself',
	'IDENTIFIANT_UTILISATEUR'		=> 'User id',
	'UTILISATEUR_INCONNU'			=> 'Unknown user',
	'UTILISATEUR_INCONNU_LDAP'		=> 'Unknown user',
	'AJOUTER_UN_UTILISATEUR'		=> 'Add a user', 
	'UTILISATEUR_AJOUTE'			=> 'User %s %s has been added&hellip;',
	'EDITER_CET_UTILISATEUR'		=> 'Edit this user',
	'EDITER_MON_COMPTE'				=> 'Edit my own account',
	'UTILISATEUR_EXISTE_DEJA'		=> 'This user already exists. Duplicated information !',
	'ANNULER_EDITION'				=> 'Cancel edit',
	'UTILISATEUR_MODIFIE'			=> 'User %s %s has been updated&hellip;',
	'SUPPRIMER_CET_UTILISATEUR'		=> 'Delete this user',
	'CERTAIN_SUPPRIMER_USER'		=> 'Do you really want to delete this user ?',
	'UTILISATEUR_SUPPRIME'			=> 'User deleted successfully&hellip;',
	'UTILISATEURS'					=> 'Users',
	'LISTE_UTILISATEURS'			=> 'Users List',
	'LISTE_ADMINISTRATEURS'			=> 'Admins List',
	'TOUS_LES_UTILISATEURS'			=> 'All users',
	'NOM'							=> 'Name',
	'PRENOM'						=> 'First Name',
	'NOM_PRENOM'					=> 'Name Firstname',
	'NOTES_PRIVEES'					=> 'Private memo',

	//------------------------------------------
	// DROITS
	//------------------------------------------
	'ACCES_NON_AUTORISE'			=> 'Access not granted. Contact the administrator !',
	'GESTION_DES_DROITS'			=> 'Rights Policies',
	'DROITS_INSUFFISANTS'			=> 'You need to have specific rights to access this functionality.',
	'FONCTIONNALITE'				=> 'Functionality',
	'DROITS_MODIF_LIB_PROFIL'		=> 'Click to change this profile label',
	'DROITS_MODIF_CODE_PROFIL'		=> 'Click to change this profile code',
	'DROITS_MODIF_ID_PROFIL'		=> 'Click to change this profile id',
	'ERREUR_REN_LIB_PROFIL'			=> 'SQL error while renaming profile label',
	'ERREUR_REN_CODE_PROFIL'		=> 'SQL error while renaming profile code',
	'ERREUR_REN_ID_PROFIL'			=> 'SQL error while changing profile id',
	'DROITS_MODIF_LIB_FONC'			=> 'Click to change this functionality label',
	'DROITS_MODIF_CODE_FONC'		=> 'Click to change this functionality code',
	'DROITS_MODIF_ID_FONC'			=> 'Click to change this functionality id',
	'ERREUR_REN_LIB_FONC'			=> 'SQL error while renaming functionality label',
	'ERREUR_REN_CODE_FONC'			=> 'SQL error while renaming functionality code',
	'ERREUR_REN_ID_FONC'			=> 'SQL error while changing functionality id',
	'SUPPRIMER_CETTE_FONC'			=> 'Delete this functionality',
	'SUPPRIMER_CETTE_FONC_CERTAIN'	=> 'Do you really want to definitively delete this functionality ?',
	'SUPPRIMER_CE_PROFIL'			=> 'Delete this profile',
	'SUPPRIMER_CE_PROFIL_CERTAIN'	=> 'Do you really want to definitively delete this profile ?',
	'AJOUTER_UN_PROFIL'				=> 'Add a profile',
	'AJOUTER_UNE_FONC'				=> 'Add a functionality',
	'EXPORTER_LES_DROITS'			=> 'Export Rights Policies',
	'IMPORTER_LES_DROITS'			=> 'Import Rights Policies',
	'EXPORT_DROITS_OK'				=> 'Rights policies export was successfull&hellip;',
	'EXPORT_DROITS_KO'				=> 'Rights policies export failed!',
	'IMPORT_DROITS_OK'				=> 'Right policies import was successfull&hellip;',
	'IMPORT_DROITS_KO'				=> 'Right policies import failed! Check if file "%s" exists',
	'MODIFICATION_DROIT_INTERDITE'	=> 'Updating this policie is prohibited&hellip;',
	'NOUVELLE_SAISIE'				=> 'New input',
	'GROUPE_AJOUTER'				=> 'Add a group of functionalities',
	'MODIF_ADMIN_INTERDITE'			=> 'Administrative change is denied&hellip;',
	'GROUPE_FONCTIONNALITE_AUCUNE'	=> 'There is no functionality in this group',
	'GROUPE_SUPPR'					=> 'Delete this group of functionalities',
	'GROUPE_SUPPR_INTERDIT'			=> 'Deleting this group of functionalities is forbidden&hellip;',
	'GROUPE_SUPPR_IMPOSSIBLE'		=> 'It is not possible to delete a group that still contains functionalities&hellip;',
	'GROUPE_SUPPR_CERTAIN'			=> 'Do you really want to definitively delete this group of functionalities ?',
	'GROUPE_REN_ERREUR'				=> 'SQL error while renaming the label of this group of functionalities',
	'FONC_REN_ID_EXISTE_DEJA'		=> 'This functionality Id already exists&hellip;',
	'FONC_REN_CODE_EXISTE_DEJA'		=> 'This functionality code already exists&hellip;',
	'PROFIL_REN_ID_EXISTE_DEJA'		=> 'This profile Id already exists&hellip;',
	'PROFIL_REN_CODE_EXISTE_DEJA'	=> 'This profile code already exists&hellip;',

	//------------------------------------------
	// BASE DE DONNEES
	//------------------------------------------
	'SAUVEGARDE_BD'					=> 'Save Database',
	'RESTORATION_BD'				=> 'Restore Database',
	'SAUVEGARDES_LISTE'				=> 'Available backup list',
	'RESTAURATION_CERTAIN'			=> 'Do you really want to restore this database?',
	'SAUVEGARDE_SUPPRIMER_CERTAIN'	=> 'Do you really want to delete this backup?',
	'SAUVEGARDE_VERSION'			=> 'Backup of %s at %s (%s)',
	'SAUVEGARDE_OK'					=> 'Backup success',
	'SAUVEGARDE_KO'					=> 'Backup failed',
	'RESTAURATION_OK'				=> 'Backup restore success',
	'RESTAURATION_KO'				=> 'Backup restore failed!',
	'SAUVEGARDE_SECURITE_KO'		=> 'Security backup failed!',
	'NON_CONSEILLE'					=> 'not safe',
	'FORMAT_IGNORE'					=> 'Unknown file format',
	'SUPPRIMER_LA_SAUVEGARDE'		=> 'Delete this backup',
	'SAUVEGARDE_SUPPRIMEE_OK'		=> 'Backup deleted&hellip;',
	'SAUVEGARDE_SUPPRIMEE_KO'		=> 'Deleting the backup failed!',
	'SAUVEGARDE_INEXISTANTE'		=> 'This backup does not exist&hellip;',
	'PHP_ERROR_FILE_VIDER'			=> 'Empty the PHP errors file',
	'PHP_ERROR_FILE_DELETED'		=> 'PHP error file has been emptied&hellip;',
	'PHP_ERROR_FILE_ERROR'			=> 'Error while emptying PHP error file!',

	//------------------------------------------
	// MOT DE PASSE
	//------------------------------------------
	'MOT_DE_PASSE'					=> 'Password',
	'MOT_DE_PASSE_RETAPER'			=> 'Re-enter the password',
	'MOT_DE_PASSE_ERRONE'			=> 'Invalid password',
	'PASSWORD_SAISIE_DIFFERENTE'	=> 'Passwords are differents',

	//------------------------------------------
	// SYSTEME
	//------------------------------------------
	'SYSTEME'						=> 'System',
	'INFORMATIONS_SYSTEME'			=> 'System Information',
	'SIGNATURE_CODE'				=> 'Code Hash',
	'SIGNATURE_FINALE'				=> 'Final hash',
	'VERSION_PHP'					=> 'PHP Version',
	'INFOS_PHP'						=> 'PHP Infos',
	'MEM_REELLE_PHP'				=> 'Actual memory allocated to PHP',
	'MEM_USED_PHP'					=> 'Memory used by PHP',
	'ACCEDER_PHPMYADMIN'			=> 'Run phpMyAdmin',
	'VERSION_MYSQL'					=> 'MySQL Version',
	'VERSION_INNODB'				=> 'Innodb Version',
	'VERSION_PROTOCOLE'				=> 'Protocol Version',
	'VERSION_APACHE'				=> 'Apache Version',
	'MODULE_AUTHENTIFICATION'		=> 'Authentication module',
	'PAS_EXPEDITEUR'				=> 'Sender unknown',
	'EMAIL_EXPEDITEUR_KO'			=> 'Bad sender eMail',
	'PAS_DESTINATAIRE'				=> 'No recipient',
	'TROP_DESTINATAIRES'			=> 'Too much recipient! Maximum 3',
	'PAS_OBJET'						=> 'No subject',
	'EMAIL_DESTINATAIRE_INVALIDE'	=> 'Bad email for a recipient',
	'EMAIL_ENVOI_ERREUR'			=> 'Error sending email',
	'ERREURS_BACKEND'				=> 'Backend Errors',
	'ERREURS_FRONTEND'				=> 'Frontend Errors',
	'VOTRE_IP'						=> 'Your IP',

	//------------------------------------------
	// AUTRE
	//------------------------------------------
	'CLASSEMENT_ASCENDANT'			=> 'Ascending sort',
	'CLASSEMENT_DESCENDANT'			=> 'Descending sort',
	'AUCUNE_REF_TROUVEE'			=> 'No result found',
	'1_REF_TROUVEE'					=> '1 result found',
	'X_REF_TROUVEE'					=> '%d results found',
	'NOM_COLONNE'					=> 'Column Name?',
	'ACTIONS'						=> '<span class="fas fa-child fa-lg"></span>'
);

$_MOIS_EN_CLAIR	= array('', 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'); 
$_CIVILITE = array('', 'Mr.', 'Ms.', 'Miss', 'Unknown');

//------------------------------------------------------------------
// Construction d'une chaine avec des paramètres passés en option
// Entrée :
//		$indice : indice du template de la chaine dans $_LIBELLE
//		$param1 : paramètre 1 de remplacement (facultatif)
//		$param2 : paramètre 2 de remplacement (facultatif)
//		$param3 : paramètre 3 de remplacement (facultatif)
//		$param4 : paramètre 4 de remplacement (facultatif)
// Sortie :
//		La chaine formattée
// Exemple d'appel :
//		getLib('PREF_FILMS_ADD', 30)
//		donne: Ajoutez ce titre à la liste de vos 30 films préférés
//------------------------------------------------------------------
function getLib($indice, $param1='', $param2='', $param3='', $param4='', $param5='')
{
	global $_LIBELLES;
	$chaine = sprintf($_LIBELLES[$indice], $param1, $param2, $param3, $param4, $param5);
	if ($chaine) return $chaine; else return 'MISSING TEXT';
}
function getLibUpper($indice, $param1='', $param2='', $param3='', $param4='', $param5='')
{
	return '<span class="text-uppercase">'.getLib($indice, $param1, $param2, $param3, $param4, $param5).'</span>';
}