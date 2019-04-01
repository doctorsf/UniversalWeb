<?php
//-----------------------------------------------------------------
// Classe LDAP
// s'appuie sur les librairies LDAP de PHP
//-----------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Date : 06 janvier 2017
//-----------------------------------------------------------------
// 12.01.2018 :
//		- ajout du port dans le constructeur avec port par défaut à 389
//		- ajout de l'erreur -1 (Can't contact LDAP server)
// 12.04.2018 : 
//		- ajout de la méthode connectUsingKerberos() propre à une authentification via Kerberos
//		- ajout de l'erreur MLDAP_TICKET_KERBEROS_KO (Ticket Kerberos non valide)
// 23.07.2018
//		- amélioration de la méthode search 
//		voir https://forum.phpfrance.com/php-avance/suivre-referral-ldap-t35236.html
//		ou https://www.developpez.net/forums/d123654/php/bibliotheques-frameworks/ldap-ldap_search/
//		le referral est une définition dans OpenLDAP qui permet de dire au CLIENT d'aller voir ailleurs s'il ne trouve pas la réponse dans l'OpenLDAP directement.
//		Mettre LDAP_OPT_REFERRALS à 0 permets donc de virer l'OU lorsque l'on souhaite faire une recherche sur l'intégralité de l'AD.
//		@ldap_set_option($this->_ldapConnexionId, LDAP_OPT_REFERRALS, 0);         // NFE/2S : Patch to look beyond OU
//		@ldap_set_option($this->_ldapConnexionId, LDAP_OPT_PROTOCOL_VERSION, 3);  // NFE/2S : Patch to look beyond OU
//-----------------------------------------------------------------

class Ldap {

	private $_host;				//nom du serveur ldap à attaquer
	private $_port = 389;		//port du ldap (defaut 389)
	private $_dn;				//chaine DN (Distinguished Name)
	private $_ldapConnexionId;	//ressource de la connexion ldap : identifiant positif de ldap ou false en cas d'erreur
	private $_connected;		//booleen dit si ldap connecté ou non
	private $_erreur;			//tableau d'informations sur l'erreur rencontrée
	private $_ldapInfos;		//tableau des informations recupérées du ldap
	private $_serviceAccount;	//compte de service permettrant d'accéder à la base (pas de connexion anonyme acceptée)
	private $_servicePwd;		//mot de passe du compte de service d'accès à la base

	private	$_LDAP_ERREURS = array(
						-2 => 'Ticket Kerberos expiré', -1 => 'Impossible de se contacter le serveur LDAP', 0 => 'Aucune erreur', 1 => 'Erreur LDAP', 2 => 'Erreur de protocole', 3 => 'Time Out', 4 => 'Taille maximale dépassée. Résultats incomplets', 16 => 'Attribut inexistant', 
						19 => 'Opération impossible', 20 => 'Valeur existe déjà', 22 => 'LDAP Non connecté', 23 => 'Erreur de modification du protocole 3',
						32 => 'Cible DN non trouvée', 34 => 'Syntaxe DN incorrecte', 49 => 'Authentification incorrecte', 50 => 'Droits insuffisants', 51 => 'Serveur LDAP trop occupé',
						52 => 'Serveur LDAP non disponible', 53 => 'Requete impossible à accomplir', 54 => 'Détection d\'une boucle', 64 => 'Violation des règles de structure du LDAP', 
						65 => 'Violation des règles d\'objet LDAP', 68 => 'Attribut existe déjà', 80 => 'Erreur non référencée'
					);

	//quelques erreurs LDAP (d'autres sont possibles)
	//http://wiki.servicenow.com/index.php?title=LDAP_Error_Codes#gsc.tab=0
	const MLDAP_TICKET_KERBEROS_KO		= -2;		//Ticket Kerberos non valide
	const MLDAP_CANT_CONTACT_SERVER		= -1;		//Can't contact LDAP server
	const MLDAP_SUCCESS					= 0;		//Aucune erreur
	const MLDAP_OPERATIONS_ERROR		= 1;		//Erreur LDAP
	const MLDAP_PROTOCOL_ERROR			= 2;		//Erreur de protocole
	const MLDAP_TIMELIMIT_EXCEEDED		= 3;		//Time out
	const MLDAP_SIZELIMIT_EXCEEDED		= 4;		//Taille maximale de retour dépassée. Résultats incomplets
	const LDAP_NO_SUCH_ATTRIBUTE		= 16;		//Attribut inexistant
	const MLDAP_CONSTRAINT_VIOLATION	= 19;		//Opération impossible
	const MLDAP_TYPE_OR_VALUE_EXISTS	= 20;		//Valeur existe déjà

	//ajout persos
	const MLDAP_NOT_CONNECTED			= 22;		//LDAP Non connecté
	const MLDAP_PROTOCOL_3				= 23;		//Erreur de modification du protocole 3

	const MLDAP_NO_SUCH_OBJECT 			= 32;		//Cible non trouvée
	const MLDAP_INVALID_DN_SYNTAX		= 34;		//Syntaxe DN incorrecte
	const MLDAP_INVALID_CREDENTIALS		= 49;		//Compte / mode passe erroné
	const MLDAP_INSUFFICIENT_ACCESS		= 50;		//Droits insuffisants
	const MLDAP_BUSY					= 51;		//Serveur LDAP trop occupé
	const MLDAP_UNAVAILABLE				= 52;		//Serveur LDAP non disponible
	const MLDAP_UNWILLING_TO_PERFORM	= 53;		//Requete impossible à accomplir (du par exemple à des restrictions du serveur LDAP ou AD)
	const MLDAP_LOOP_DETECT				= 54;		//Détection d'une boucle
	const MLDAP_NAMING_VIOLATION		= 64;		//Violation des règles de structure du LDAP
	const MLDAP_OBJECT_CLASS_VIOLATION	= 65;		//Violation des règles d'objet LDAP
	const MLDAP_ALREADY_EXISTS 			= 68;		//Attribut existe déjà
	const MLDAP_OTHER					= 80;		//Erreur non référencée

	//=======================================
	// Constructeur / Destructeur
	//=======================================

	public function __construct($host, $port, $compte=null, $mdp=null) {
		$this->_host = $host;
		$this->_port = $port;
		$this->_serviceAccount = $compte;
		$this->_servicePwd = $mdp;
		$this->_connected = false;
		$this->_erreur = array('num' => self::MLDAP_SUCCESS, 'message' => 'No error', 'fr_message' => 'Aucune erreur');
		$this->_ldapConnexionId = null;
	}

	public function __destruct() {
		$this->_connected = false;
	}

	//=======================================
	// Méthodes privees
	//=======================================

	private function _readError() {
		$this->_erreur['num'] = @ldap_errno($this->_ldapConnexionId);
		$this->_erreur['message'] = @ldap_err2str(ldap_errno($this->_ldapConnexionId));
		if ($this->_erreur['num'] == '') $this->_erreur['num'] = 0;
		$this->_erreur['fr_message'] = $this->_LDAP_ERREURS[$this->_erreur['num']];
	}

	//=======================================
	// Getters
	//=======================================
	public function getHost()				{return $this->_host;}
	public function getConnexionId()		{return $this->_ldapConnexionId;}
	public function getErreur()				{return $this->_erreur['num'];}
	public function getErreurNum()			{return $this->_erreur['num'];}
	public function getErreurMessage()		{return $this->_erreur['message'];}
	public function getErreurMessageFr()	{return $this->_erreur['fr_message'];}
	public function getDn()					{return $this->_dn;}
	public function isConnected()			{return ($this->_connected == true);}

	//=======================================
	// Setters
	//=======================================
	public function setDn($dn)				{$this->_dn = $dn;}
	public function setHost($host)			{$this->_host = $host;}

	//=======================================
	// Méthodes publiques
	//=======================================

	//---------------------------------------
	// Connexion LDAP 
	// Entree : rien
	// Retour : true / false (erreurs disponibles via les getters)
	//---------------------------------------
	public function connect() {
		//connexion au serveur
		//retourne un identifiant positif de ldap ou false en cas d'erreur
		$this->_ldapConnexionId = ldap_connect($this->_host, $this->_port);
		$this->_readError();
		if ($this->_ldapConnexionId === false) {
			return false;
		}
		//DEBUG_TAB_($this->_erreur);
		//modification du protocole LDAP à 3
		$res = ldap_set_option($this->_ldapConnexionId, LDAP_OPT_PROTOCOL_VERSION, 3);
		$this->_readError();
		if ($res === false) {
			$this->_erreur = array('num' => self::MLDAP_PROTOCOL_3, 'message' => 'Protocol 3 error', 'fr_message' => $this->_LDAP_ERREURS[self::MLDAP_PROTOCOL_3]);
			return false;
		}
		//connexion authentifiée au serveur
		$this->_connected = @ldap_bind($this->_ldapConnexionId, $this->_serviceAccount, $this->_servicePwd);
		$this->_readError();
		return $this->_connected;
	}

	//---------------------------------------
	// Connexion LDAP en utilisant l'authentification KERBEROS
	// Entree : $binduser (utilisateur qui fait le bind)
	// Retour : true / false (erreurs disponibles via les getters)
	//---------------------------------------
	public function connectUsingKerberos($binduser) {
		//connexion au serveur
		//retourne un identifiant positif de ldap ou false en cas d'erreur
		$this->_ldapConnexionId = ldap_connect($this->_host, $this->_port);
		$this->_readError();
		if ($this->_ldapConnexionId === false) {
			return false;
		}
		//modification du protocole LDAP à 3
		$res = ldap_set_option($this->_ldapConnexionId, LDAP_OPT_PROTOCOL_VERSION, 3);
		$this->_readError();
		if ($res === false) {
			$this->_erreur = array('num' => self::MLDAP_PROTOCOL_3, 'message' => 'Protocol 3 error', 'fr_message' => $this->_LDAP_ERREURS[self::MLDAP_PROTOCOL_3]);
			return false;
		}
		//connexion authentifiée au serveur
		$this->_connected = @ldap_sasl_bind($this->_ldapConnexionId, null, null, "GSSAPI", null, $binduser);
		$this->_readError();
		//DEBUG_TAB_($this->_erreur);
		return $this->_connected;
	}

	//---------------------------------------
	//fermeture de la connexion LDAP
	// Entree : rien
	// Retour : rien
	//---------------------------------------
	public function close() {
		$retour = @ldap_close($this->_ldapConnexionId);
		if ($retour) {
			$this->_ldapConnexionId = null;
			$this->_connected = false;
		}
		else {
			$this->_readError();
		}
	}

	//---------------------------------------
	// décode un SID microsoft AD
	// Entree : la valeur du SID codéerien
	// Retour : chaine de caractère du SID décodé
	//---------------------------------------
	public function decodeSID($value) {
		$sid = 'S-';
		// Convert Bin to Hex and split into byte chunks
		$sidinhex = str_split(bin2hex($value), 2);
		// Byte 0 = Revision Level
		$sid = $sid.hexdec($sidinhex[0]).'-';
		// Byte 1-7 = 48 Bit Authority
		$sid = $sid.hexdec($sidinhex[6].$sidinhex[5].$sidinhex[4].$sidinhex[3].$sidinhex[2].$sidinhex[1]);
		// Byte 8 count of sub authorities – Get number of sub-authorities
		$subauths = hexdec($sidinhex[7]);
		//Loop through Sub Authorities
		for($i = 0; $i < $subauths; $i++) { 
			$start = 8 + (4 * $i); // X amount of 32Bit (4 Byte) Sub Authorities 
			$sid = $sid.'-'.hexdec($sidinhex[$start+3].$sidinhex[$start+2].$sidinhex[$start+1].$sidinhex[$start]); 
		}
		return $sid;
    }

	//---------------------------------------
	// Lance une recherche
	// et charge les données trouvées
	// Entree : $valeur (cn recherché)
	//			$lesInfos (tableau en retour qui recoit les données chargées)
	// Retour : $nombre trouvé données chargées
	//			false (user non trouvé)
	//---------------------------------------
	public function search($valeur, &$lesInfos) {

		//rajouté par Nico le 23/07/2018
		//voir https://forum.phpfrance.com/php-avance/suivre-referral-ldap-t35236.html
		//ou https://www.developpez.net/forums/d123654/php/bibliotheques-frameworks/ldap-ldap_search/
		//le referral est une définition dans OpenLDAP qui permet de dire au CLIENT d'aller voir ailleurs s'il ne trouve pas la réponse dans l'OpenLDAP directement.
		//Mettre LDAP_OPT_REFERRALS à 0 permets donc de virer l'OU lorsque l'on souhaite faire une recherche sur l'intégralité de l'AD.
		@ldap_set_option($this->_ldapConnexionId, LDAP_OPT_REFERRALS, 0);         // NFE/2S : Patch to look beyond OU
		@ldap_set_option($this->_ldapConnexionId, LDAP_OPT_PROTOCOL_VERSION, 3);  // NFE/2S : Patch to look beyond OU
		//fin rajouté par Nico le 23/07/2018

		$searchId = @ldap_search($this->_ldapConnexionId, $this->_dn, $valeur);
		$this->_readError();
		if ($searchId !== false) {
			$nombreTrouve = ldap_count_entries($this->_ldapConnexionId, $searchId);
			if ($nombreTrouve > 0) {
				$lesInfos = ldap_get_entries($this->_ldapConnexionId, $searchId);
				return $nombreTrouve;
			}
		}
		return false;
	}
	
	//---------------------------------------
	//test de l'authentification d'une utilisateur selon son uid / mod de passe
	// Entree : $uid (id de l'utilisateur à tester)
	//			$mdp (mot de passe de l'utilisateur)
	// Retour : true / false
	//---------------------------------------
	public function authentifieUser($uid, $mdp) {
		$retour = @ldap_bind($this->_ldapConnexionId, $uid, $mdp);
		$this->_readError();
		return $retour;
	}

	//---------------------------------------
	// Lit une entrée d'objet LDAP
	// Entree : $data (l'ensemble des données récupérée par search)
	//			$num (numéro de l'enregistrement choisi) 
	//			$entry (l'entrée à exporter, cad l'indice du tableau $data auquel on s'interresse)
	// Retour : tableau des informations récoltées
	//---------------------------------------
	public function readAttrib($data, $num, $entry) {
		$infos = array();
		$infos = $data[$num][$entry];
		unset($infos['count']);
		return $infos;
	}

	//---------------------------------------
	// Ajoute un attribut à une entrée de l'annuaire
	// Entree : $valeur (valeur à ajouter)
	// Retour : true / false
	//---------------------------------------
	public function addAttrib($valeur) {
		$res = @ldap_mod_add($this->_ldapConnexionId, $this->_dn, $valeur);
		$this->_readError();
		return $res;
	}

	//---------------------------------------
	// Effface un attribut à une entrée de l'annuaire
	// Entree : $valeur (valeur à ajouter)
	// Retour : true / false
	//---------------------------------------
	public function deleteAttrib($valeur) {
		$res = @ldap_mod_del($this->_ldapConnexionId, $this->_dn, $valeur);
		$this->_readError();
		return $res;
	}

}