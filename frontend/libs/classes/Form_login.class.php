<?php
//---------------------------------------------------------------
// CLASSE : Form_login
//---------------------------------------------------------------
// éè utf-8
// Formulaire de login
//---------------------------------------------------------------

class Form_login extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	//======================================
	// Méthodes protégées					
	//======================================
	// initialisation des données de travail
	//--------------------------------------

	protected function initDonnees() {
		//initialisations supplémentaires
		$annuaireUtilise = get_class($_SESSION[_APP_LOGIN_]);
		if ($annuaireUtilise == 'Login_kerberos') {
			//pour une authentification kerberos, le login de l'utilisateur se trouve dans la variable serveur REMOTE_USER
			list($sAMAccountName, $domain) = explode('@', $_SERVER['REMOTE_USER'], 2);
			$this->_tab_donnees['login'] = $sAMAccountName;
		}
		else {
			$this->_tab_donnees['login'] = '';
		}
		$this->_tab_donnees['password'] = '';
	}

	//--------------------------------------
	// construction des champs du formulaire
	//--------------------------------------

	protected function construitChamps() {
		//construction des champs du formulaire
		parent::construitChamps();

		if (get_class($_SESSION[_APP_LOGIN_]) == 'Login_kerberos') $cclass = 'form-control-lg font-weight-bold border-0'; else $cclass = '';
		$this->createField('text', 'login', array(
			'newline' => true,
			'dbfield' => 'login',
			'design' => 'online',
			'label' => getLib('IDENTIFIANT_UTILISATEUR'),
			'clong' => 'col-12',
			'maxlength' => 100,
			'spellcheck' => false,
			'placeholder' => 'login',
			'testMatches' => array('REQUIRED'),
			'cclass' => $cclass,
			'value' => $this->_tab_donnees['login'],
			//si authentification kerberos le champ login est readonly (on ne peut pas entrer un autre utilisateur que soit-même)
			'readonly' => (get_class($_SESSION[_APP_LOGIN_]) == 'Login_kerberos')
		));

		//on affiche le champ de saisie du mot de passe seulement pour une authentification
		//qui n'est pas KERBEROS. Kerberos sert de SSO dont pas de mot de passe à saisir
		$this->createField('text', 'password', array(
			'newline' => true,
			'dbfield' => 'password',
			'inputType' => 'password',
			'design' => 'online',
			'label' => getLib('MOT_DE_PASSE'),
			'clong' => 'col-12',
			'maxlength' => 40,
			'spellcheck' => false,
			'placeholder' => 'mot de passe',
			'testMatches' => array('REQUIRED'),
			'value' => $this->_tab_donnees['password'],
			'javascript' => 'onkeypress="capLock(event, \'idPassword\')"',
			'invisible' => (get_class($_SESSION[_APP_LOGIN_]) == 'Login_kerberos')
		)); 

		//construction bouton Submit
		$this->createField('bouton', 'submit', array(
			'newLine' => true,
			'inputType' => 'submit',
			'label' => 'OK',
			'llong' => 'col-12',
			'lclass' => 'btn btn-primary',
			'clong' => 'col-12',
			'value' => 'OK'
		)); 
	}

	//======================================
	// Methodes	publiques					
	//======================================
	// Chargement des données depuis la		
	// base de données. reponse requete		
	//======================================

	//initialisation des données et construction des champs initialisés
	public function init() {
		$this->initDonnees();			//initialisation des données
		$this->construitChamps();		//constuction à vide... (cad avec données d'initiation)
	}

	//--------------------------------------
	// Tests supplémentaires sur certains	
	// champs, en plus des test de			
	// validation définit à la construction	
	//--------------------------------------

	protected function testsSupplementaires($champ) {
		return false;
	}

	//--------------------------------------
	// tests supplementaires postérieurs	
	// executés une fois que tous les tests 
	// supplémentaires unitaires par champ	
	// on été réalisés				
	//--------------------------------------

	protected function testsSupplementairesPosterieurs() {
		//on, va tester la validité du login ici
		$fieldLogin = $this->field('login');
		$fieldPassword = $this->field('password');
		//test si l'utilisateur est connu dans l'annuaire
		$annuaireUtilise = get_class($_SESSION[_APP_LOGIN_]);

		//Cas du login utilisateur géré dans la base de données de l'appli
		//----------------------------------------------------------------
		if ($annuaireUtilise == 'Login') {
			$retour = User::isGrantedUser($fieldLogin->value(), $fieldPassword->value());
			if ($retour == User::ERREUR_USER_UNKNOWN) {
				$fieldLogin->setErreur(true);
				$fieldLogin->setLiberreur(getLib('UTILISATEUR_INCONNU'));
				//on loggue l'information
				sqlLogs_log(_LOG_CONNEXION_, getLib('LOG_USER_UNKNOWN', $fieldLogin->value()));
				return true;
			}
			//test de la validité du mot de passe
			elseif ($retour == User::ERREUR_USER_BADPWD) {
				$fieldPassword->setErreur(true);
				$fieldPassword->setLiberreur(getLib('MOT_DE_PASSE_ERRONE'));
				//on loggue l'information
				sqlLogs_log(_LOG_CONNEXION_, getLib('LOG_USER_BAD_PASSWORD', $fieldLogin->value()));
				return true;
			}
			//test si le compte est activé
			elseif ($retour == User::ERREUR_USER_INACTIVE) {
				$fieldLogin->setErreur(true);
				$fieldLogin->setLiberreur(getLib('COMPTE_DESACTIVE'));
				return true;
			}
		}

		//Cas du login utilisateur géré par un autre annuaire à faire ici
		//------------------------------------------------

		return false;		//pas d'erreur
	}

	//--------------------------------------
	// affichage du formulaire
	//--------------------------------------
	public function afficher() {
		parent::afficher();
		$chaine = '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
			$chaine.= '<fieldset style="border:1px silver solid;padding:1.5rem">';
				$chaine.= '<h1>'._APP_TITLE_.'</h1>';
				$chaine.= $this->draw(true);
			$chaine.= '</fieldset>';
			$chaine.= '<p class="small">(*) '.getLib('CHAMP_REQUIS').' (1) '.getLib('LECTURE_SEULE').'</p>';
		$chaine.= '</form>';

		return $chaine;
	}
}