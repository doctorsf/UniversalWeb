<?php
//----------------------------------------------------------------------------------
//								LANGUE FRANCAIS
//----------------------------------------------------------------------------------
// Fichier de langue FRANCAIS
//----------------------------------------------------------------------------------

defined('_LG_')						|| define('_LG_', 'fr');
defined('_FR_')						|| define('_FR_', 'fr');
defined('_EN_')						|| define('_EN_', 'en');
defined('_INT_')					|| define('_INT_', 'int');		//international (=toutes les langues)
defined('_FORMAT_DATE_')			|| define('_FORMAT_DATE_', 'd/m/Y');
defined('_FORMAT_DATE_TIME_')		|| define('_FORMAT_DATE_TIME_', 'd/m/Y H:i:s');
defined('_FORMAT_DATE_TIME_NOS_')	|| define('_FORMAT_DATE_TIME_NOS_', 'd/m/Y H:i');		//format datetime sans les secondes
defined('_IMAGES_LANGUE_')			|| define('_IMAGES_LANGUE_', _IMAGES_.'fr/');

//on pose la variable de session 'langue_en_cours' utilisée dans les cas particuliers ou l'information de langue n'est pas dans l'url comme par exemple l'appel à des fonctions solitaires (ex : reponses-ajax.php)
$_SESSION[_APP_LANGUE_ENCOURS_] = _LG_;

$_LIBELLES = array(

	//------------------------------------------
	// Libellés des classes UniversalField (UFC)
	//------------------------------------------
	'UFC_CHAMP_REQUIS'				=> 'Ce champ ne doit pas être vide',
	'UFC_CHAMP_NUMERIQUE'			=> 'Ce champ doit être numérique',
	'UFC_CHOIX_INVALIDE'			=> 'Ce choix n\'est pas valide',
	'UFC_INTEGER_1OU2'				=> 'Entier attendu (2 chiffres max)',
	'UFC_FLOAT'						=> 'Nombre à virgule attendu (décimales optionnelles)',
	'UFC_FLOAT_2DEC'				=> 'Nombre à virgule attendu (2 décimales optionnelles)',
	'UFC_INTEGER_X'					=> '%d chiffres attendus',
	'UFC_INTEGER'					=> 'Entier attendu',
	'UFC_DOLLARS'					=> 'Dollars attendus',
	'UFC_MONNAIE'					=> 'Monnaie attendue',
	'UFC_INTEGER_SPACED'			=> 'Entier attendu (espaces autorisés)',
	'UFC_UNSIGNED_INTEGER'			=> 'Entier non signé attendu',
	'UFC_SIGNED_INTEGER'			=> 'Entier signé attendu',
	'UFC_BOOLEAN'					=> '1 ou 0 attendu',
	'UFC_DATETIME'					=> 'Format date heure attendu : yyyy-mm-jj hh:mm:ss',
	'UFC_EMAIL'						=> 'eMail attendue',
	'UFC_EMAIL_APOSTROPHE'			=> 'eMail attendue (apostrophe autorisée)',
	'UFC_ALPHA_CODE'				=> 'Caractères de codage attendus',
	'UFC_ALPHA_SIMPLE'				=> 'Caractères alphanumériques attendus',
	'UFC_ALPHA_NOMS'				=> 'Caractères alphanumériques accentués attendus',
	'UFC_FILE_NAME'					=> 'Caractères compatibles avec nomage de fichiers attendus',
	'UFC_URL'						=> 'url attendue',
	'UFC_IPV4'						=> 'adresse IP V4 attendue',
	'UFC_MAC'						=> 'adresse Mac attendue',
	'UFC_SHA1'						=> '40 caractères hexadécimaux attendus',
	'UFC_MAJ_VERROUILLEES'			=> 'Attention, les majuscules sont verrouilllées !',
	'UFC_FICHIER_INEXISTANT'		=> 'Fichier inexistant&hellip;',
	'UFC_TYPE_FICHIER_NON_AUTORISE'	=> 'Ce type de fichier n\'est pas autorisé. Sont autorisés : ',
	'UFC_NOM_FICHIER_NON_VALIDE'	=> 'Nom de fichier non valide.',
	'UFC_POIDS_FICHIER_MOINS_DE'	=> 'Votre fichier doit faire moins de ',
	'UFC_UPLOAD_MAX_FILE_SIZE_INI'	=> 'Le poids du fichier dépasse celui précisé par la directive upload_max_filesize du fichier php.ini',
	'UFC_UPLOAD_MAX_FILE_SIZE_FORM'	=> 'Le poids du fichier dépasse celui précisé par la directive MAX_FILE_SIZE du formulaire',
	'UFC_IMAGE_PARTIEL_TELECHARGEE'	=> 'L\'image n\'a été que partiellement téléchargée',
	'UFC_AUCUN_FICHIER_TELECHARGE'	=> 'Aucun fichier n\'a été téléchargé',
	'UFC_DOSSIER_TEMP_MANQUANT'		=> 'Un dossier temporaire est manquant',
	'UFC_ECHEC_ECRITURE_DISQUE'		=> 'Échec de l\'écriture du fichier sur le disque',
	'UFC_ECHEC'						=> 'Échec&hellip;',
	'UFC_MATCH_INVALIDE'			=> 'Paramètre de propriété testMatches inconnu!',
	'UFC_MAX_LENGTH'				=> 'longueur > %d caractères (%d)',
	'UFC_MIN_LENGTH'				=> 'longueur < %d caractères (%d)',
	'UFC_SAISIE_NON_CONFORME'		=> 'Saisie non conforme',

	//------------------------------------------
	// Libellés des classes UniversalWeb SilentMail (UWSM)
	//------------------------------------------
	'UWSM_TEST'						=> 'Test',
	'UWSM_SUCCESS'					=> 'Message envoyé avec succès&hellip;',
	'UWSM_NO_SENDER'				=> 'Pas d\'expéditeur déclaré',
	'UWSM_BAD_SENDER'				=> 'eMail expéditeur non valide',
	'UWSM_NO_OBJECT'				=> 'Pas d\'objet déclaré',
	'UWSM_NO_RECIPIENT'				=> 'Pas de destinaitaire déclaré',
	'UWSM_TOO_MANY_RECIPIENTS'		=> 'Trop de destinataires ! 3 maximum autorisés',
	'UWSM_BAD_RECIPIENT'			=> 'eMail d\'un destinataire non valide',
	'UWSM_SEND_ERROR'				=> 'Erreur lors de l\'envoi',
	'UWSM_ERROR_UNKNOWN'			=> 'Erreur inconnue',

	//------------------------------------------
	// ERREURS
	//------------------------------------------
	'ERREUR'						=> 'Erreur',
	'ERREUR_COMMANDE'				=> 'Erreur commande&hellip;',
	'ERREUR_ANNUAIRE'				=> 'Erreur de connexion à l\'annuaire',
	'ECHEC_LOGIN'					=> 'Echec de login utilisateur %s. Contactez l\'administrateur !',
	'ERREUR_CREATION_USER'			=> 'Erreur lors de la création d\'un utilisateur',
	'ERREUR_MODIF_USER_X'			=> 'Erreur lors de la modification de l\'utilisateur %s',
	'ERREUR_SUPPR_USER_X'			=> 'Erreur lors de la suppression de l\'utilisateur %s',
	'ERREUR_SUICIDE'				=> 'Impossible de s\'auto-supprimer. Demandez à un autre administrateur d\'effectuer cette opération pour vous...',

	//------------------------------------------
	// MOTS / VERBES
	//------------------------------------------
	'BIENVENUE'						=> 'Bienvenue',
	'LOGIN'							=> 'Connexion',
	'LOGOUT'						=> 'Déconnexion',
	'DECONNECTEZ_MOI'				=> 'Déconnectez-moi',
	'RECHERCHE'						=> 'Recherche',
	'ADMINISTRATION'				=> 'Administration',
	'SAISIE'						=> 'Saisie',
	'AJOUTER'						=> 'Ajouter',
	'MODIFIER'						=> 'Modifier',
	'DUPLIQUER'						=> 'Dupliquer',
	'SUPPRIMER'						=> 'Supprimer',
	'ANNULER'						=> 'Annuler',
	'RETOUR'						=> 'Retour',
	'LEGENDE'						=> 'Légende',
	'PROFIL'						=> 'Profil',
	'ID'							=> 'Id',
	'EMAIL'							=> 'eMail',
	'LANGUE'						=> 'Langue',
	'ACTION'						=> 'Action',
	'SUPPR'							=> 'Supp.',
	'OPERATIONS'					=> 'Opérations',
	'AUTOLOG'						=> 'Autolog',
	'FRANCAIS'						=> 'français',
	'ANGLAIS'						=> 'anglais',
	'MANQUANT'						=> 'Manquant',

	//------------------------------------------
	// LOGS
	//------------------------------------------
	'LOG_LOGIN'						=> 'Connexion %s',
	'LOG_LOGOUT'					=> 'Déconnexion %s',
	'LOG_ERREUR'					=> 'Erreur de connexion pour %s',
	'LOG_LOGIN_USURPATION'			=> 'Tentative d\'usurpation du login %s par le sAMAccountName %s',
	'LOG_ERREUR_ANNUAIRE'			=> 'Erreur de connexion à l\'annuaire pour %s',
	'LOG_ANNUAIRE_USER_UNKNOWN'		=> 'Utilisateur %s inconnu dans l\'annuaire : accès refusé',
	'LOG_USER_UNKNOWN'				=> 'Utilisateur %s inconnu dans la base de données : accès refusé',
	'LOG_USER_BAD_PASSWORD'			=> 'Mot de passe érroné pour %s',

	//------------------------------------------
	// DATES
	//------------------------------------------
	'TODAY_IS'						=> 'Nous sommes le %s',
	'DATE_CREATION'					=> 'Date création',
	'DERNIER_ACCES'					=> 'Dernier accès',
	'DERNIERE_IP'					=> 'Dernière IP connue',

	//------------------------------------------
	// COMPTE
	//------------------------------------------
	'ACCEDER_MON_COMPTE'			=> 'Accéder à son compte',
	'COMPTE_ACTIF'					=> 'compte actif',
	'COMPTE_DESACTIVE'				=> 'compte désactivé',
	'TESTEUR'						=> 'Testeur',
	'ACTION_DEMANDEE'				=> 'Action demandée',
	'CODE_VALIDATION'				=> 'Code de validation',

	//------------------------------------------
	// DIALOGUES
	//------------------------------------------
	'CHAMP_REQUIS'					=> 'Champ requis',
	'LECTURE_SEULE'					=> 'Lecture seule',

	//------------------------------------------
	// IDENTIFIANT / UTILISATEUR
	//------------------------------------------
	'IDENTIFIANT'					=> 'Identifiant',
	'IDENTIFIEZ-VOUS'				=> 'Identifiez-vous',
	'IDENTIFIANT_UTILISATEUR'		=> 'Identifiant utilisateur',
	'UTILISATEUR_INCONNU'			=> 'Utilisateur inconnu',
	'AJOUTER_UN_UTILISATEUR'		=> 'Ajouter un utilisateur', 
	'EDITER_CET_UTILISATEUR'		=> 'Editer cet utilisateur',
	'UTILISATEUR_EXISTE_DEJA'		=> 'Cet utilisateur est déjà répertorié. Saisie dupliquée !',
	'ANNULER_EDITION'				=> 'Annuler l\'édition',
	'SUPPRIMER_CET_UTILISATEUR'		=> 'Supprimer cet utilisateur',
	'CERTAIN_SUPPRIMER_USER'		=> 'Etes-vous certain de vouloir supprimer cet utilisateur ?',
	'UTILISATEURS'					=> 'Utilisateurs',
	'LISTE_UTILISATEURS'			=> 'Liste des utilisateurs',
	'TOUS_LES_UTILISATEURS'			=> 'Tous les utilisateurs',
	'NOM'							=> 'Nom',
	'PRENOM'						=> 'Prénom',
	'NOM_PRENOM'					=> 'Nom Prénom',
	'NOTES_PRIVEES'					=> 'Notes privées',

	//------------------------------------------
	// DROITS
	//------------------------------------------
	'ACCES_NON_AUTORISE'			=> 'Vous n\'êtes pas autorisé à accéder à cette application. Contactez l\'administrateur !',
	'GESTION_DES_DROITS'			=> 'Gestion des droits',
	'DROITS_INSUFFISANTS'			=> 'Vous n\'avez pas les droits nécessaires pour accéder à cette fonctionnalité.',
	'FONCTIONNALITE'				=> 'Fonctionnalité',
	'DROITS_MODIF_LIB_PROFIL'		=> 'Cliquer pour modifier le libellé de ce profil',
	'DROITS_MODIF_CODE_PROFIL'		=> 'Cliquer pour modifier le code de ce profil',
	'DROITS_MODIF_ID_PROFIL'		=> 'Cliquer pour modifier l\'id de ce profil',
	'ERREUR_REN_LIB_PROFIL'			=> 'Erreur SQL lors du renommage de libellé de profil',
	'ERREUR_REN_CODE_PROFIL'		=> 'Erreur SQL lors du renommage de code de profil',
	'ERREUR_REN_ID_PROFIL'			=> 'Erreur SQL lors de la modifiction d\'un id profil',
	'DROITS_MODIF_LIB_FONC'			=> 'Cliquer pour modifier le libellé de cette fonctionnalité',
	'DROITS_MODIF_CODE_FONC'		=> 'Cliquer pour modifier le code de cette fonctionnalité',
	'DROITS_MODIF_ID_FONC'			=> 'Cliquer pour modifier l\'id de cette fonctionnalité',
	'ERREUR_REN_LIB_FONC'			=> 'Erreur SQL lors du renommage de libellé de fonctionnalité',
	'ERREUR_REN_CODE_FONC'			=> 'Erreur SQL lors du renommage de code de fonctionnalité',
	'ERREUR_REN_ID_FONC'			=> 'Erreur SQL lors de la modifiction d\'un id fonctionnalité',
	'SUPPRIMER_CETTE_FONC'			=> 'Supprimer cette fonctionnalité',
	'SUPPRIMER_CETTE_FONC_CERTAIN'	=> 'Etes-vous certain de vouloir supprimer définitivement cette fonctionnalité ?',
	'SUPPRIMER_CE_PROFIL'			=> 'Supprimer ce profil',
	'SUPPRIMER_CE_PROFIL_CERTAIN'	=> 'Etes-vous certain de vouloir supprimer définitivement ce profil ?',
	'AJOUTER_UN_PROFIL'				=> 'Ajouter un profil',
	'AJOUTER_UNE_FONC'				=> 'Ajouter une fonctionnalité',

	//------------------------------------------
	// MOT DE PASSE
	//------------------------------------------
	'MOT_DE_PASSE'					=> 'Mot de passe',
	'MOT_DE_PASSE_RETAPER'			=> 'Retaper le mot de passe',
	'MOT_DE_PASSE_ERRONE'			=> 'Mot de passe erroné',

	//------------------------------------------
	// AUTRE
	//------------------------------------------
	'AUCUNE_REF_TROUVEE'			=> 'Aucune référence trouvée',
	'1_REF_TROUVEE'					=> '1 référence trouvée',
	'X_REF_TROUVEE'					=> '%d références trouvées',
	'COOKIE_DISCLAIMER'				=> 'En poursuivant votre navigation sur ce site, vous acceptez le dépôt de cookies aux seules fins de gestion du site&hellip;',
	'JACCEPTE'						=> 'J\'accepte'
);

$_MOIS_EN_CLAIR	= array('', 'janvier', 'février', 'mars','avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'); 
$_CIVILITE = array('', 'M.', 'Mme', 'Melle', 'Inconnue');

//--------------------------------------
// Affiche un texte "nombre de références " + verbe ou participe passé
// Entrée : $nb : nombre à transcrire
// Retour : la chaine de catactère résultante
//--------------------------------------
function getLibNbRefsTrouvees($nb) {
	if ($nb == 0)		return 'Aucune référence trouvée';
	elseif ($nb == 1)	return '1 référence trouvée';
	else				return $nb.' références trouvées';
}

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
	if ($chaine) return $chaine; else return 'TEXTE ABSENT';
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

//------------------------------------------------------------------
// Renvoie le libellé ci-celui-ci existe, l'indice sinon
//------------------------------------------------------------------
// Entrée 
//		$indice : indice du template de la chaine dans $_LIBELLES
// Retour
//		la traduction si existe / $indice sinon
//------------------------------------------------------------------
function getLibIfExists($indice) {
	if (existeLib($indice)) return getLib($indice); else return $indice;
}