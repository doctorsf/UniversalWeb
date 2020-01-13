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
defined('_FORMAT_DATE_')			|| define('_FORMAT_DATE_', 'm/d/Y');
defined('_FORMAT_DATE_TIME_')		|| define('_FORMAT_DATE_TIME_', 'm/d/Y H:i:s');
defined('_FORMAT_DATE_TIME_NOS_')	|| define('_FORMAT_DATE_TIME_NOS_', 'm/d/Y H:i');	//format datetime sans les secondes
defined('_IMAGES_LANGUE_')			|| define('_IMAGES_LANGUE_', _IMAGES_.'en/');

//on pose la variable de session 'langue_en_cours' utilisée dans les cas particuliers ou l'information de langue n'est pas dans l'url comme par exemple l'appel à des fonctions solitaires (ex : reponses-ajax.php)
$_SESSION[_APP_LANGUE_ENCOURS_] = _LG_;

$_LIBELLES = array(

	//------------------------------------------
	// Libellés des classes UniversalField (UFC)
	//------------------------------------------
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
	'UFC_IPV4'						=> 'IP V4 expected',
	'UFC_MAC'						=> 'Mac address expected',
	'UFC_SHA1'						=> '40 hex characters expected',
	'UFC_MAJ_VERROUILLEES'			=> 'Caps Lock is on',
	'UFC_FICHIER_INEXISTANT'		=> 'This file does not exist&hellip;',
	'UFC_TYPE_FICHIER_NON_AUTORISE'	=> 'This file type is not allowed. Are allowed: ',
	'UFC_NOM_FICHIER_NON_VALIDE'	=> 'Invalid filename.',
	'UFC_POIDS_FICHIER_MOINS_DE'	=> 'Your file must not exceed ',
	'UFC_UPLOAD_MAX_FILE_SIZE_INI'	=> 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
	'UFC_UPLOAD_MAX_FILE_SIZE_FORM'	=> 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
	'UFC_IMAGE_PARTIEL_TELECHARGEE'	=> 'The uploaded file was only partially uploaded',
	'UFC_AUCUN_FICHIER_TELECHARGE'	=> 'No file was uploaded',
	'UFC_DOSSIER_TEMP_MANQUANT'		=> 'Missing a temporary folder',
	'UFC_ECHEC_ECRITURE_DISQUE'		=> 'Failed to write file to disk',
	'UFC_ECHEC'						=> 'Failure&hellip;',
	'UFC_MATCH_INVALIDE'			=> 'Match property unknown!',
	'UFC_MAX_LENGTH'				=> 'length > %d chars (%d)',
	'UFC_MIN_LENGTH'				=> 'length < %d chars (%d)',

	'PAS_EXPEDITEUR'				=> 'Sender unknown',
	'EMAIL_EXPEDITEUR_KO'			=> 'Bad sender eMail',
	'PAS_DESTINATAIRE'				=> 'No recipient',
	'TROP_DESTINATAIRES'			=> 'Too much recipient! Maximum 3',
	'PAS_OBJET'						=> 'No subject',
	'EMAIL_DESTINATAIRE_INVALIDE'	=> 'Bad email for a recipient',
	'EMAIL_ENVOI_ERREUR'			=> 'Error sending email',

	//------------------------------------------
	// ERREURS
	//------------------------------------------
	'ERREUR'						=> 'Error',
	'ERREUR_COMMANDE'				=> 'Choice error&hellip;',
	'ERREUR_ANNUAIRE'				=> 'LDAP Connexion Error',
	'ECHEC_LOGIN'					=> 'Login failure for %s. Contact the administrator !',
	'ERREUR_CREATION_USER'			=> 'Error while creating a user',
	'ERREUR_MODIF_USER_X'			=> 'Error while updating user %s',
	'ERREUR_SUPPR_USER_X'			=> 'Error while deleting user %s',
	'ERREUR_SUICIDE'				=> 'It is impossible to delete your own account. Ask another administrator to do this for you...',

	//------------------------------------------
	// MOTS / VERBES
	//------------------------------------------
	'BIENVENUE'						=> 'Welcome',
	'LOGIN'							=> 'Login',
	'LOGOUT'						=> 'Log out',
	'DECONNECTEZ_MOI'				=> 'Log me out',
	'RECHERCHE'						=> 'Search',
	'ADMINISTRATION'				=> 'Administration',
	'SAISIE'						=> 'Input',
	'AJOUTER'						=> 'Add',
	'MODIFIER'						=> 'Update',
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

	//------------------------------------------
	// LOGS
	//------------------------------------------
	'LOG_LOGIN'						=> 'Login %s',
	'LOG_LOGOUT'					=> 'Logout %s',
	'LOG_ERREUR'					=> 'Login error for %s',
	'LOG_LOGIN_USURPATION'			=> 'Hack attempt for login %s by sAMAccountName %s',
	'LOG_ERREUR_ANNUAIRE'			=> 'Directory login error for %s',
	'LOG_ANNUAIRE_USER_UNKNOWN'		=> 'Unknown user %s in directory : access denied',
	'LOG_USER_UNKNOWN'				=> 'Unknow user %s in database : access denied',
	'LOG_USER_BAD_PASSWORD'			=> 'Bad password for %s',

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

	//------------------------------------------
	// IDENTIFIANT / UTILISATEUR
	//------------------------------------------
	'IDENTIFIANT'					=> 'Id',
	'IDENTIFIEZ-VOUS'				=> 'Identify yourself',
	'IDENTIFIANT_UTILISATEUR'		=> 'User id',
	'UTILISATEUR_INCONNU'			=> 'unknown user',
	'AJOUTER_UN_UTILISATEUR'		=> 'Add a user', 
	'EDITER_CET_UTILISATEUR'		=> 'Edit this user',
	'UTILISATEUR_EXISTE_DEJA'		=> 'This user already exists. Duplicated information !',
	'ANNULER_EDITION'				=> 'Cancel edit',
	'SUPPRIMER_CET_UTILISATEUR'		=> 'Delete this user',
	'CERTAIN_SUPPRIMER_USER'		=> 'Do you really want to delete this user ?',
	'UTILISATEURS'					=> 'Users',
	'LISTE_UTILISATEURS'			=> 'Users List',
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

	//------------------------------------------
	// MOT DE PASSE
	//------------------------------------------
	'MOT_DE_PASSE'					=> 'Password',
	'MOT_DE_PASSE_RETAPER'			=> 'Re-enter the password',
	'MOT_DE_PASSE_ERRONE'			=> 'Invalid password',

	//------------------------------------------
	// AUTRE
	//------------------------------------------
	'AUCUNE_REF_TROUVEE'			=> 'No result found',
	'1_REF_TROUVEE'					=> '1 result found',
	'X_REF_TROUVEE'					=> '%d results found',
	'COOKIE_DISCLAIMER'				=> 'This website uses cookies to ensure you get the best experience on our website&hellip;',
	'JACCEPTE'						=> 'J\'accepte'
);

$_MOIS_EN_CLAIR	= array('', 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'); 
$_CIVILITE = array('', 'Mr.', 'Ms.', 'Miss', 'Unknown');

//------------------------------------------------------------------
// Construction d'une chaine avec des paramètres passés en option
// Entrée :
//		$indice : indice du template de la chaine dans $_LIBELLES
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
function getLib($indice, $param1='', $param2='', $param3='', $param4='', $param5='') {
	global $_LIBELLES;
	$chaine = sprintf($_LIBELLES[$indice], $param1, $param2, $param3, $param4, $param5);
	if ($chaine) return $chaine; else return 'MISSING TEXT';
}

function getLibUpper($indice, $param1='', $param2='', $param3='', $param4='', $param5='') {
	return '<span class="text-uppercase">'.getLib($indice, $param1, $param2, $param3, $param4, $param5).'</span>';
}

function getLibLower($indice, $param1='', $param2='', $param3='', $param4='', $param5='') {
	return '<span class="text-lowercase">'.getLib($indice, $param1, $param2, $param3, $param4, $param5).'</span>';
}

function getLLib($indice, $param1='', $param2='', $param3='', $param4='', $param5='') {
	$param1 = utf8_strtolower($param1);
	$param2 = utf8_strtolower($param2);
	$param3 = utf8_strtolower($param3);
	$param4 = utf8_strtolower($param4);
	$param5 = utf8_strtolower($param5);
	return getLib($indice, $param1, $param2, $param3, $param4, $param5);
}

function getULib($indice, $param1='', $param2='', $param3='', $param4='', $param5='') {
	$param1 = utf8_strtoupper($param1);
	$param2 = utf8_strtoupper($param2);
	$param3 = utf8_strtoupper($param3);
	$param4 = utf8_strtoupper($param4);
	$param5 = utf8_strtoupper($param5);
	return getLib($indice, $param1, $param2, $param3, $param4, $param5);
}

//------------------------------------------------------------------
// Renseigne si un libellé existe
//------------------------------------------------------------------
// Entrée 
//		$indice : indice du template de la chaine dans $_LIBELLES
// Retour
//		true / false
//------------------------------------------------------------------
function existeLib($indice) {
	global $_LIBELLES;
	return isset($_LIBELLES[$indice]);
}