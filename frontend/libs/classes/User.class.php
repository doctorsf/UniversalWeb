<?php
//-----------------------------------------------------------------------
// Auteur : Fabrice Labrousse											
// Classe User														
// Date : 13.02.2017													
//-----------------------------------------------------------------------
// éè : UTF-8				
// Cette classe permet de travailler sur des données utilisateurs issus de la base de données de l'application
// Elle comprend les méthodes d'authentification et d'identification d'un utilisateur
//-----------------------------------------------------------------------

class User {
	private $_id;						//ex : fun41
	private $_nom;						//nom : ex : Durant
	private $_prenom;					//prenom : ex : Pierre
	private $_email;					//email : ex : pierre.durant@free.fr
	private $_password;					//mot de passe crypté SHA1
	private $_langue;					//langue de connexion : ex : fr / en
	private $_profil;					//profil utilisateur (niveau de droits accordé)
	private $_testeur;					//compte de test (1) ou non (0)
	private $_dateCreation;				//date et heure de création du compte
	private $_dernierAcces;				//date et heure du dernier accès
	private $_actionDemandee;			//action demandée par user
	private $_codeValidation;			//code de validation pour action user
	private $_active;					//booléen dit si user actif ou non
	private $_autolog;					//auto login ? : ex : 0 / 1
	private $_ip;						//ip connue : ex : 228.62.23.210
	private $_notesPrivees;				//notes privees (commentaires non visible par user)

	const ERREUR_AUCUNE = 0;					//aucune erreur
	const ERREUR_ANNUAIRE = -1;					//erreur annuaire LDAP
	const ERREUR_ANNUAIRE_USER_UNKNOWN = -2;	//utilisateur non trouvé dans l'annuaire LDAP
	const ERREUR_USER_UNKNOWN = -3;				//utilisateur non trouvé dans la base de données de l'application
	const ERREUR_USER_BADPWD = -4;				//mauvais mot de passe saisi
	const ERREUR_USER_INACTIVE = -5;			//utilisateur inactif

	//=======================================
	// Constructeur
	//=======================================

	public function __construct() {
		//initialisation des propriétés de l'objet
		$this->_init();
	}

	//=======================================
	// Méthodes privées
	//=======================================

	//initialisations
	private function _init() {
		$this->_id = '';															//id user
		$this->_nom = '';															//nom
		$this->_prenom = '';														//prénom
		$this->_email = '';															//email
		$this->_password = '';														//mot de passe
		$this->_langue = 'fr';														//langue de préférence
		$this->_profil = $_SESSION[_APP_DROITS_]->getIdProfil('PROFIL_VISITEUR');	//profil visiteur (invité) par défaut
		$this->_testeur = 0;														//drapeau testeur application
		$this->_dateCreation = '0000-00-00 00:00:00';								//date de cration du compte
		$this->_dernierAcces = '0000-00-00 00:00:00';								//dernier accès
		$this->_actionDemandee = 0;													//action demandee
		$this->_codeValidation = '';												//code de validation pour action demandee
		$this->_active = 0;															//compte actif (1) ou inactif (0)
		$this->_autolog = 0;														//login automatique demandé par user (1) sinon (0)
		$this->_ip = '';															//dernièer IP connue
		$this->_notesPrivees = '';													//notes privées
	}

	//=======================================
	// GETTERS
	//=======================================

	public function getId()					{return $this->_id;}
	public function getNom()				{return $this->_nom;}
	public function getPrenom()				{return $this->_prenom;}
	public function getNomPrenom()			{return $this->_nom.' '.$this->_prenom;}
	public function getPrenomNom()			{return $this->_prenom.' '.$this->_nom;}
	public function getEmail()				{return $this->_email;}
	public function getPassword()			{return $this->_password;}
	public function getLangue()				{return $this->_langue;}
	public function getProfil()				{return $this->_profil;}
	public function getTesteur()			{return $this->_testeur;}
	public function getDateCreation()		{return $this->_dateCreation;}
	public function getDernierAcces()		{return $this->_dernierAcces;}
	public function getActionDemandee()		{return $this->_actionDemandee;}
	public function getCodeValidation()		{return $this->_codeValidation;}
	public function getActive()				{return $this->_active;}
	public function getAutolog()			{return $this->_autolog;}
	public function getIp()					{return $this->_ip;}
	public function getNotesPrivees()		{return $this->_notesPrivees;}

	//=======================================
	// SETTERS
	//=======================================

	public function setId($info)				{$this->_id = $info;}
	public function setNom($info)				{$this->_nom = $info;}
	public function setPrenom($info)			{$this->_prenom = $info;}
	public function setEmail($info)				{$this->_email = $info;}
	public function setPassword($info)			{$this->_password = $info;}
	public function setLangue($info)			{$this->_langue = $info;}
	public function setProfil($info)			{$this->_profil = $info;}
	public function setTesteur($info)			{$this->_testeur = $info;}
	public function setDateCreation($info)		{$this->_dateCreation = $info;}
	public function setDernierAcces($info)		{$this->_dernierAcces = $info;}
	public function setActionDemandee($info)	{$this->_actionDemandee = $info;}
	public function setCodeValidation($info)	{$this->_codeValidation = $info;}
	public function setActive($info)			{$this->_active = $info;}
	public function setAutolog($info)			{$this->_autolog = $info;}
	public function setIp($info)				{$this->_ip = $info;}
	public function setNotesPrivees($info)		{$this->_notesPrivees = $info;}

	//=======================================
	// Methodes publiques
	//=======================================

	//---------------------------------------
	// Modifie la date de dernier acces de l'utilisateur avec l'heure courante + ip courante
	// Entree : rien
	// Sortie : rien
	//---------------------------------------
	public function majDernierAcces() {				
		$res = sqlUsers_updateLastAccess($this->_id, $_SERVER['REMOTE_ADDR']);
		if ($res !== false) {
			$this->setDernierAcces($res);
			$this->setIp($_SERVER['REMOTE_ADDR']);
			return true;
		}
		return false;
	}

	//---------------------------------------
	// Chargement dans l'objet des informations d'un utilisateur. 
	// Entree : $uid (uid de l'utilisateur)
	// Retour : true (annuaire connecté, user existe) / false sinon
	//---------------------------------------
	public function chargeUser($uid) {
		$this->_init();
		$res = sqlUsers_getInfosUser($uid, $infos);
		if ($res) {
			//hydratation des propriétés de l'objet
			$this->setId($infos['id_user']);
			$this->setNom($infos['nom']);
			$this->setPrenom($infos['prenom']);
			$this->setEmail($infos['email']);
			$this->setPassword($infos['password']);
			$this->setLangue($infos['langue']);
			$this->setProfil($infos['profil']);
			$this->setTesteur($infos['testeur']);
			$this->setDateCreation($infos['date_creation']);
			$this->setDernierAcces($infos['dernier_acces']);
			$this->setActionDemandee($infos['action_demandee']);
			$this->setCodeValidation($infos['code_validation']);
			$this->setActive($infos['active']);
			$this->setAutolog($infos['autolog']);
			$this->setIp($infos['ip']);
			$this->setNotesPrivees($infos['notes_privees']);
			return true;
		}			
		return self::ERREUR_USER_UNKNOWN;
	}

	//=======================================
	// Methodes statiques
	//=======================================

	//---------------------------------------
	// Détermine si id user connu
	// Entree : $id (user id de l'utilisateur)
	// Retour : code ERREUR_USER_UNKNOWN si utilisateur inconnu ou le profil si connu
	//---------------------------------------
	public static function isValidUser($id) {
		if (sqlUsers_getInfosUser($id, $infos)) {
			//renvoie le niveau d'acces
			return $infos['profil'];
		}
		return self::ERREUR_USER_UNKNOWN;	//utilisateur inconnu dans la base de l'appli
	}

	//---------------------------------------
	// Détermine si le couple id/pwd est valide
	// et si le compte est activé
	// Entree : $id (user id de l'utilisateur)
	//			$pwd (mot de passe de l'utilisateur)
	// Retour : User::ERREUR_AUCUNE (ok) / autre erreur sinon
	//---------------------------------------
	public static function isGrantedUser($id, $pwd) {
		if (sqlUsers_userExists($id)) {
			if (sqlUsers_testUserPassword($id, $pwd)) {
				if (sqlUsers_userIsActive($id)) {
					return self::ERREUR_AUCUNE;
				}
				else return self::ERREUR_USER_INACTIVE;
			}
			else return self::ERREUR_USER_BADPWD;
		}
		else return self::ERREUR_USER_UNKNOWN;
	}

	//---------------------------------------
	// Détermine si id est un id administrateur de l'application
	// Entree : $id (user id de l'utilisateur)
	// Retour : true (id est admin) / false sinon
	//---------------------------------------
	public static function isAdmin($id) {
		if (sqlUsers_getInfosUser($id, $infos)) {
			//vérifie le niveau d'acces
			return $_SESSION[_APP_DROITS_]->accesAutorise('FONC_ADM_APP', $infos['profil']);
		}
		return false;	//utilisateur inconnu dans la base de l'appli
	}

	//---------------------------------------
	// Renvoie le prénom et le nom de l'utilisateur ou l'id passé en
	// parametre si non trouvé
	//---------------------------------------
	public static function getPrenomNomOrId($id) {
		if (sqlUsers_getInfosUser($id, $infos)) {
			//vérifie le niveau d'acces
			return $infos['prenom'].' '.$infos['nom'];
		}
		return $id;	//utilisateur inconnu dans la base de l'appli
	}

}