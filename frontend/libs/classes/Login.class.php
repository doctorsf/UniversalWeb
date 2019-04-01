<?php
//**********************************************************************
// Auteur : Fabrice Labrousse											
// Classe Login														
// Date : 05.08.2014													
//**********************************************************************
// Cette classe s'appuie sur la classe User. Destinée a être instanciée
// pour l'utilisateur qui est logué
// éè : UTF-8															
//**********************************************************************

class Login extends User {
	private $_logged;				//booleen dit si loggué ou non
	private $_prefs;				//tableau des préférences utilisateur sauvegardées par cookie

	//=======================================
	// Constructeur
	//=======================================

	public function __construct() {
		$this->_init();
		$this->_getPreferences();
	}

	//=======================================
	// Methodes privées
	//=======================================
	//initialisations
	//---------------------------------------
	private function _init() {
		$this->_logged = false;
	}

	//---------------------------------------
	//obtenir les préférences de l'utilisateur
	//les préférences sont stockées dans le cookie "user_prefs"
	// Entree : rien
	// Retour : rien
	//---------------------------------------
	private function _getPreferences() {
		//test existence cookie de préférences
		if (isset($_COOKIE['user_prefs'])) {
			//le cookie existe, on charge les valeurs
			$this->_prefs = unserialize($_COOKIE['user_prefs']);
		}
		else {
			$this->_prefs = array('langue_pref' => 'fr');
			//sauvegarde des infos dans le cookie user_prefs (positionné pour 1 an)
		    setcookie('user_prefs', serialize($this->_prefs), strtotime('+1 year'), '/', $_SERVER['HTTP_HOST'], false, false);
		}
	}

	//---------------------------------------
	// GETTERS
	//---------------------------------------

	public function getLogged()				{return $this->_logged;}
	public function getLanguePref()			{return $this->_prefs['langue_pref'];}

	//---------------------------------------
	// SETTERS
	//---------------------------------------

	private function _setLogged($info)			{$this->_logged = $info;}




	//=======================================
	// Methodes publiques
	//=======================================

	//---------------------------------------------------------------
	//dit si utilisateur loggé (true/false)
	// Entree : rien
	// Retour : logué (true) / non logué (false)
	//---------------------------------------------------------------
	public function isLogged() {
		return $this->_logged;
	}

	//---------------------------------------------------------------
	//modifie la préférence de langue de l'utilisateur
	//cette methode n'est pas utilisée mais sert de modèle pour la
	//sauvegarde d'une préférences de l'utilisateur
	// Entree : $langue (langue de preference fr/en)
	// Retour : rien
	//---------------------------------------------------------------
	public function changeLangue($langue) {													
		$this->_prefs['langue_pref'] = $langue;
		//sauvegarde des préférence dans le cookie user_prefs (positionné pour 1 an)
		setcookie('user_prefs', serialize($this->_prefs), strtotime('+1 year'), '/', $_SERVER['HTTP_HOST'], false, false);
	}

	//---------------------------------------------------------------
	// Logue un utilisateur. On considere que tous les tests (user existe, valide, etc.) 
	// on été fait au préalable de cette méthode
	// Entree :	$id (identificateur de l'utilisateur)
	// Retour :	si erreur au chargement retourne l'erreur telles : 
	//			User::ERREUR_USER_UNKNOWN				//utilisateur non trouvé dans la base de données de l'application
	//			ou true si ok
	//---------------------------------------------------------------
	public function login($id) {
		//on charge les infos de l'utilisateur à logguer
		$retour = $this->chargeUser($id);
		if ($retour !== true) {
			//inscription dans le log de connexions
			if ($retour == User::ERREUR_USER_UNKNOWN)	sqlLogs_log(_LOG_CONNEXION_, getLib('LOG_USER_UNKNOWN', $id));
			else										sqlLogs_log(_LOG_CONNEXION_, getLib('LOG_ERREUR', $id));
			return $retour;
		}
		//met à jour l'heure de dernier acces de l'utilisateur dans la base de données de l'appication
		$this->majDernierAcces();
		//logué !
		$this->_setLogged(true);				
		//inscription dans le log de connexions
		sqlLogs_log(_LOG_CONNEXION_, getLib('LOG_LOGIN', $this->getId()));
		return true;
	}

	//---------------------------------------------------------------
	// Délogue client
	// Entree :	Rien
	// Retour : Rien
	//---------------------------------------------------------------
	public function logout() {
		$this->_init();
		//inscription dans le log de connexions
		sqlLogs_log(_LOG_CONNEXION_, getLib('LOG_LOGOUT', $this->getId()));
		return $this->_logged;
	}

}