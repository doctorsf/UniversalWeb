<?php
//--------------------------------------------------------------
// CLASSE : form_user
//--------------------------------------------------------------
// Formulaire de saisie user
//--------------------------------------------------------------
// 15.01.2018 : correction petit bug (ajout addslashes à code javascript)
// 27.11.2018 : amélioration de la présentation du formulaire tout device
//--------------------------------------------------------------

class Form_user extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	//======================================
	// Méthodes privées
	//======================================
	// initialisation des données de travail	
	//--------------------------------------

	protected function initDonnees() {
		$this->_tab_donnees['id_user'] = '';
		$this->_tab_donnees['nom'] = '';
		$this->_tab_donnees['prenom'] = '';
		$this->_tab_donnees['email'] = '';
		$this->_tab_donnees['password'] = '';					//ne sert à rien pour une authentification LDAP puisque c'est le LDAP qui gère le mot de passe
		$this->_tab_donnees['password2'] = '';					//ne sert à rien pour une authentification LDAP puisque c'est le LDAP qui gère le mot de passe
//		$this->_tab_donnees['langue'] = 'fr';
		$this->_tab_donnees['profil'] = $_SESSION[_APP_DROITS_]->getIdProfil('PROFIL_VISITEUR');
//		$this->_tab_donnees['testeur'] = 0;
		$this->_tab_donnees['date_creation'] = date('d/m/Y H:i:s');
		$this->_tab_donnees['dernier_acces'] = '';
//		$this->_tab_donnees['action_demandee'] = 0;
//		$this->_tab_donnees['code_validation'] = 0;
		$this->_tab_donnees['active'] = 0;
//		$this->_tab_donnees['autolog'] = 0;
		$this->_tab_donnees['ip'] = '000.000.000.000';
		$this->_tab_donnees['notes_privees'] = '';
	}

	//----------------------------------------
	// construction des champs du formulaire
	//----------------------------------------

	protected function construitChamps() {
		parent::construitChamps();

		//en mode modification certain champs ne sont pas affichables (car sensibles ou parce qu'il ne faut pas les montrer aux utilisateurs)
		//par exemple, certains champs 'sensibles' comme le niveau d'accès ne sont affichable que dans les conditions suivantes : on est 
		//administrateur et on ne cherche pas à modifier son propre compte (pour éviter de s'auto enlever des droits admin)
		$affichable = (accesAutorise('FONC_ADM_GERER_USERS') && 
						(($this->getOperation() != self::MODIFIER) || ($this->getIdTravail() != $_SESSION[_APP_LOGIN_]->getId()))
					  );

		if ($affichable)
		$this->createField('switch', 'active', array(
			'newLine' => true,							//nouvelle ligne ? false par défaut
			'groupName' => 'active',					//le groupName est facultatif si dpos = alone
			'dbfield' => 'active',						//retour de la saisie
			'design' => 'inline',						//inline (defaut) / online (inline par défaut si dpos = alone)
			'dpos' => 'alone',							//first / last / inter / alone
			'flexLine' => 'mb-4',						//contourne propr. flexLine pour mettre petit margin bottom
			'label' => ucfirst(getLib('COMPTE_ACTIF')),	//label
			'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
			'clong' => 'col-12',						//longueur du champ en colonnes boostrap (a définir sur le premier du groupe (ou alone). Sans effet sur les autres)
			'border' => false,							//defaut : false. A définir une seule fois sur le premier éléments checkbox
			'checked' => ($this->_tab_donnees['active'] == 1)	//cochée (true) / décochée (false)
		));
/*
		if ($affichable)
		$this->createField('checkbox', 'testeur', array(
			'groupName' => 'testeur',
			'dbfield' => 'testeur',
			'label' => ucfirst(getLib('TESTEUR')),
			'lpos' => 'after',
			'clong' => 'col-1',
			'checked' => ($this->_tab_donnees['testeur'] == 1)
		));
*/		
		$this->createField('separateur', 'separateur', array(
			'newLine' => true,
			'label' => 'Informations utilisateur',
			'lclass' => 'font-weight-bold text-underline text-uppercase',
			'clong' => 'col-12'
		));

		if (_ANNUAIRE_ != _ANNUAIRE_INTERNE_) {
			//ajout d'un champ de recherche d'utilisateur sur le serveur LDAP
			if (($this->getOperation() == self::AJOUTER) || ($this->getOperation() == self::MODIFIER)) {
				$javascript = '';
				$this->createField('search', 'recherche', array(
					'newLine' => true,						//nouvelle ligne ? false par défaut
					'dbfield' => 'recherche',				//retour de la saisie
					'design' => 'online',
					'inputType' => '',						//search(defaut), text, time, date, etc.
					'decalage' => '',						//décallage en colonnes boostrap
					'label' => '',							//libellé du bouton (par défaut "champ") ou une icone loupe si vide ou glyph icon font-awesome
					'lpos' => 'before',						//position du champ de saisie par rapport au bouton
					'labelHelp' => 'Entrez nom.prenom du personnel recherché',	//aide sur le champ
					'lclass' => 'btn btn-success',			//classe du bouton
					'clong' => 'col-12',					//longueur du bloc champ
					'placeholder' => 'nom.prenom',			//placeholder de la saisie
					'spellcheck' => false,					//correction orthographique ?
					'javascript' => $javascript
				));
			}
		}

		$readonly = ($this->getOperation() != self::AJOUTER);
		$this->createField('text', 'uid', array(
			'newLine' => true,
			'dbfield' => 'id_user',
			'design' => 'online',
			'decalage' => '',
			'label' => getLib('IDENTIFIANT'),
			'clong' => 'col-12 col-md-6',
			'cclass' => 'tominuscules',
			'maxlength' => 100,
			'spellcheck' => false,
			'testMatches' => array('REQUIRED', 'CHECK_ALPHA_SIMPLE'),
			'value' => $this->_tab_donnees['id_user'],
			'readonly' => $readonly
		));

		$this->createField('text', 'email', array(
			'newLine' => false,
			'dbfield' => 'email',
			'inputType' => 'email',
			'design' => 'online',
			'label' => getLib('EMAIL'),
			'clong' => 'col-12 col-md-6',
			'maxlength' => 255,
			'testMatches' => array('REQUIRED'),
			'value' => $this->_tab_donnees['email']
		));

		$this->createField('text', 'nom', array(
			'newLine' => true,
			'dbfield' => 'nom',
			'design' => 'online',
			'decalage' => '',
			'label' => getLib('NOM'),
			'clong' => 'col-12 col-md-6',
			'maxlength' => 100,
			'spellcheck' => false,
			'testMatches' => array('REQUIRED', 'CHECK_ALPHA_NOMS'),
			'value' => $this->_tab_donnees['nom']
		));

		$this->createField('text', 'prenom', array(
			'newLine' => false,
			'dbfield' => 'prenom',
			'design' => 'online',
			'label' => getLib('PRENOM'),
			'clong' => 'col-12 col-md-6',
			'maxlength' => 100,
			'spellcheck' => false,
			'testMatches' => array('REQUIRED', 'CHECK_ALPHA_NOMS'),
			'value' => $this->_tab_donnees['prenom']
		));

		//le formulaire propose la gestion du mot de passe seulement pour une application qui
		//prend en compte les login via la base de données (pas par annuaire LDAP)
		if (get_class($_SESSION[_APP_LOGIN_]) == 'Login') {
			$this->createField('text', 'password', array(
				'newLine' => true,
				'dbfield' => 'password',
				'inputType' => 'password',
				'design' => 'online',
				'decalage' => '',
				'label' => getLib('MOT_DE_PASSE'),
				'clong' => 'col-12 col-md-6',
				'maxlength' => 40,
				'testMatches' => array('REQUIRED'),
				'value' => $this->_tab_donnees['password'],
				'javascript' => 'onkeypress="capLock(event, \'idPassword\')"'
			));
			$this->createField('text', 'password2', array(
				'newLine' => false,
				'dbfield' => 'password2',
				'inputType' => 'password',
				'design' => 'online',
				'label' => getLib('MOT_DE_PASSE_RETAPER'),
				'clong' => 'col-12 col-md-6',
				'maxlength' => 40,
				'testMatches' => array('REQUIRED'),
				'value' => $this->_tab_donnees['password2'],
				'javascript' => 'onkeypress="capLock(event, \'idPassword2\')"'
			));
		}

		if ($affichable)
		$this->createField('select', 'profil', array(
			'newLine' => true,
			'dbfield' => 'profil',
			'design' => 'online',
			'decalage' => '',
			'complement' => 'sqlDroits_buildProfilesList',
			'label' => getLib('PROFIL'),
			'clong' => 'col-12 col-md-6',
			'testMatches' => array('REQUIRED'),
			'value' => $this->_tab_donnees['profil']
		));
/*		
		($affichable) ? $newLine = false : $newLine = true;
		($affichable) ? $decalage = '' : $decalage = 'col-2';
		$this->createField('select', 'langue', array(
			'newLine' => false,
			'dbfield' => 'langue',
			'design' => 'online',
			'complement' => 'sqlDivers_buildLanguesDispo',
			'label' => getLib('LANGUE'),
			'clong' => 'col-12 col-md-6 col-xl-4',
			'testMatches' => array('REQUIRED'),
			'value' => $this->_tab_donnees['langue'],
		));
*/
		if ($this->getOperation() != self::AJOUTER) {
			if ($affichable)
			$this->createField('text', 'dateCreation', array(
				'newLine' => true,
				'dbfield' => 'date_creation',
				'design' => 'online',
				'decalage' => '',
				'label' => getLib('DATE_CREATION'),
				'clong' => 'col-12 col-md-4',
				'testMatches' => array('REQUIRED'),
				'value' => $this->_tab_donnees['date_creation']
			));
			if ($affichable)
			$this->createField('text', 'dernierAcces', array(
				'newLine' => false,
				'dbfield' => 'dernier_acces',
				'inputType' => 'datetime',
				'design' => 'online',
				'label' => getLib('DERNIER_ACCES'),
				'clong' => 'col-12 col-md-4',
				'value' => $this->_tab_donnees['dernier_acces'],
				'readonly' => true
			));
			if ($affichable)
			$this->createField('text', 'ip', array(
				'newLine' => false,
				'dbfield' => 'ip',
				'design' => 'online',
				'label' => getLib('DERNIERE_IP'),
				'clong' => 'col-12 col-md-4',
				'value' => $this->_tab_donnees['ip'],
				'readonly' => true
			));
/*			
			if ($affichable)
			$this->createField('text', 'actionDemandee', array(
				'newLine' => true,
				'dbfield' => 'action_demandee',
				'design' => 'online',
				'decalage' => '',
				'label' => getLib('ACTION_DEMANDEE'),
				'clong' => 'col-12 col-md-4',
				'value' => $this->_tab_donnees['action_demandee']
			));
			if ($affichable)
			$this->createField('text', 'codeValidation', array(
				'newLine' => false,
				'dbfield' => 'code_validation',
				'design' => 'online',
				'label' => getLib('CODE_VALIDATION'),
				'clong' => 'col-12 col-md-4',
				'value' => $this->_tab_donnees['code_validation']
			));
*/		
		}
/*		
		$this->createField('checkbox', 'autolog', array(
			'newLine' => true,
			'groupName' => 'autolog',
			'dbfield' => 'autolog',
			'decalage' => 'col-xl-2',
			'label' => getLib('AUTOLOG'),
			'lalign' => 'left',
			'lpos' => 'after',
			'border' => false,
			'clong' => 'col-12 col-xl-3',
			'checked' => ($this->_tab_donnees['autolog'] == 1)
		));
*/		
		if ($affichable)
		$this->createField('area', 'notesPrivees', array(
			'newLine' => true,
			'dbfield' => 'notes_privees',
			'design' => 'online',
			'decalage' => '',
			'label' => getLib('NOTES_PRIVEES'),
			'clong' => 'col-12',
			'spellcheck' => true,
			'rows' => 5,
			'value' => $this->_tab_donnees['notes_privees']
		));

		//-------------------
		// Boutons
		//-------------------

		//construction bouton Submit
		$couleurBouton = 'primary';

		//construction bouton Submit
		if ($this->getOperation() == self::CONSULTER) {
			$valueBoutonValidation = getLib('RETOUR');
			$labelBoutonValidation = '<span class="fas fa-reply mr-2"></span>'.$valueBoutonValidation;
		}
		elseif ($this->getOperation() == self::AJOUTER) {
			$valueBoutonValidation = getLib('AJOUTER');
			$labelBoutonValidation = '<span class="fas fa-plus-circle mr-3"></span>'.$valueBoutonValidation;
		}
		elseif ($this->getOperation() == self::MODIFIER) {
			$valueBoutonValidation = getLib('MODIFIER');
			$labelBoutonValidation = '<span class="far fa-edit mr-3"></span>'.$valueBoutonValidation;
		}
		elseif ($this->getOperation() == self::DUPLIQUER) {
			$valueBoutonValidation = getLib('DUPLIQUER');
			$labelBoutonValidation = '<span class="fas fa-clone mr-3"></span>'.$valueBoutonValidation;
		}
		elseif ($this->getOperation() == self::SUPPRIMER) {
			$valueBoutonValidation = getLib('SUPPRIMER');
			$labelBoutonValidation = '<span class="fas fa-trash mr-3"></span>'.$valueBoutonValidation;
			$couleurBouton = 'danger';
		}
		elseif ($this->getOperation() == self::RETIRER) {
			$valueBoutonValidation = getLib('RETIRER');
			$labelBoutonValidation = '<span class="fas fa-recycle mr-3"></span>'.$valueBoutonValidation;
		}

		//boutons
		$javascript = '';
		if ($this->getOperation() == self::SUPPRIMER)
			$javascript = 'onclick="return confirm(\''.addslashes(getLib('CERTAIN_SUPPRIMER_USER')).'\');"';
		$this->createField('bouton', 'submit', array(
			'newLine' => true,
			'dbfield' => 'bouton',
			'inputType' => 'submit',
			'decalage' => '',
			'label' => $labelBoutonValidation,
			'clong' => 'col-12 col-sm-6 col-md-4 col-lg-3 ml-auto',
			'llong' => 'col-12',
			'lclass' => 'btn btn-'.$couleurBouton,
			'javascript' => $javascript,
			'value' => $valueBoutonValidation
		));

		//ajout d'un bouton de retour
		if (($this->getOperation() == self::SUPPRIMER) || ($this->getOperation() == self::AJOUTER)) {
			$libelle = '<span class="fas fa-reply mr-2"></span>'.getLib('RETOUR');
			$javascript = 'onclick="window.history.back()"';
			$this->createField('bouton', 'retour', array(
				'newLine' => false,
				'inputType' => 'button',
				'label' => $libelle, 
				'clong' => 'col-12 col-sm-6 col-md-4 col-lg-3',
				'llong' => 'col-12',
				'lclass' => 'btn btn-secondary',	//classes graphique du bouton
				'javascript' => $javascript
			)); 		
		}

		//ajout d'un bouton d'édition si on est en consultation
		$profilEdite = $this->_tab_donnees['profil'];
		$profilAdmin = $_SESSION[_APP_DROITS_]->getIdProfil('PROFIL_ADMIN');
		$monProfil = $_SESSION[_APP_LOGIN_]->getProfil();
		if ($this->getOperation() == self::CONSULTER) {
			if (($profilEdite != $profilAdmin) || ($monProfil == $profilAdmin)) {
				$libelle = '<span class="far fa-edit mr-3"></span>'.getLib('EDITER_CET_UTILISATEUR');
				if ($this->getIdTravail() == $_SESSION[_APP_LOGIN_]->getId()) $libelle = '<span class="far fa-edit mr-3"></span>'.getLib('EDITER_MON_COMPTE');
				$adresse = str_replace('consulter', 'modifier', $_SERVER['REQUEST_URI']);
				$javascript = 'onclick="location.href=\''.$adresse.'\'; return false;"';
				$this->createField('bouton', 'edit', array(
					'newLine' => false,
					'inputType' => 'button',
					'label' => $libelle,
					'clong' => 'col-12 col-sm-6 col-md-4 col-lg-3',
					'llong' => 'col-12',
					'lclass' => 'btn btn-secondary',	//classes graphique du bouton
					'javascript' => $javascript
				));
			}
		}

		//ajout d'un bouton d'annulation d'édition si on est en édition
		if ($this->getOperation() == self::MODIFIER) {
			$libelle = '<span class="far fa-times-circle mr-3"></span>'.getLib('ANNULER_EDITION');
			$adresse = str_replace('modifier', 'consulter', $_SERVER['REQUEST_URI']);
			$javascript = 'onclick="location.href=\''.$adresse.'\'; return false;"';
			$this->createField('bouton', 'annule', array(
				'newLine' => false,
				'inputType' => 'button',
				'label' => $libelle,
				'clong' => 'col-12 col-sm-6 col-md-4 col-lg-3',
				'llong' => 'col-12',
				'lclass' => 'btn btn-secondary',	//classes graphique du bouton
				'javascript' => $javascript
			)); 		
		}
	}

	//======================================
	// Methodes	publiques					
	//======================================
	// Chargement des données depuis la		
	// base de données. reponse requete		
	//--------------------------------------

	//initialisation des données et construction des champs initialisés
	public function init() {
		$this->initDonnees();			//initialisation des données
		$this->construitChamps();		//constuction à vide... (cad avec données d'initiation)
	}

	//charger les données de l'utilisateur à partir d'une base de données
	public function charger($id) {
		$user = new User();
		if ($res = $user->chargeUser($id)) {
			$this->setIdTravail($id);
			//recopie des données dans la structure du formulaire
			$this->_tab_donnees['id_user'] = $user->getId();
			$this->_tab_donnees['nom'] = $user->getNom();
			$this->_tab_donnees['prenom'] = $user->getPrenom();
			$this->_tab_donnees['email'] = $user->getEmail();
			$this->_tab_donnees['password'] = $user->getPassword();
			$this->_tab_donnees['password2'] = $user->getPassword();
//			$this->_tab_donnees['langue'] = $user->getLangue();
			$this->_tab_donnees['profil'] = $user->getProfil();
//			$this->_tab_donnees['testeur'] = $user->getTesteur();
			$this->_tab_donnees['date_creation'] = changeDateTimeFormat($user->getDateCreation(), _FORMAT_DATE_TIME_SQL_, _FORMAT_DATE_TIME_);
			if (empty($user->getDernierAcces())) {
				$this->_tab_donnees['dernier_acces'] = ''; 
			}
			else {
				$this->_tab_donnees['dernier_acces'] = changeDateTimeFormat($user->getDernierAcces(), _FORMAT_DATE_TIME_SQL_, _FORMAT_DATE_TIME_);
			}
//			$this->_tab_donnees['action_demandee'] = $user->getActionDemandee();
//			$this->_tab_donnees['code_validation'] = $user->getCodeValidation();
			$this->_tab_donnees['active'] = $user->getActive();
//			$this->_tab_donnees['autolog'] = $user->getAutolog();
			$this->_tab_donnees['ip'] = $user->getIp();
			$this->_tab_donnees['notes_privees'] = $user->getNotesPrivees();
			$this->construitChamps();
			return true;
		}
		return $res;
	}

	//--------------------------------------
	// Tests supplémentaires sur certains champs, en plus des test de			
	// validation définit à la construction	par le paramètre : testMatches
	// $champ : nom du champ testé
	//--------------------------------------
	protected function testsSupplementaires($champ) {
		if ($champ->idField() == 'uid') {
			if (($this->getOperation() == self::AJOUTER) || ($this->getOperation() == self::DUPLIQUER)) {
				//test si cet id n'est pas déjà présent dans la base de données
				if (sqlUsers_userExists($champ->value())) {
					$champ->setErreur(true);
					$champ->setLiberreur(getLib('UTILISATEUR_EXISTE_DEJA'));
				}
			}
			elseif (($this->getOperation() == self::CONSULTER) || ($this->getOperation() == self::SUPPRIMER)) {
				//test si id existe bien
				if (!sqlUsers_userExists($champ->value())) {
					$champ->setErreur(true);
					$champ->setLiberreur(getLib('UTILISATEUR_INCONNU'));
				}
			}
			elseif ($this->getOperation() == self::MODIFIER) {
				if ($champ->value() != $this->getIdTravail()) {
					//on a modifié l'id unique -> le nouvel id ne doit pas exister dans la base
					if (sqlUsers_userExists($champ->value())) {
						$champ->setErreur(true);
						$champ->setLiberreur(getLib('UTILISATEUR_EXISTE_DEJA'));
					}
				}
			}
			return $champ->erreur();
		}
		return false; //pas d'erreur
	}

	//--------------------------------------
	// Tests supplementaires postérieurs.	
	// Executés une fois que tous les tests supplémentaires unitaires par champ	on été réalisés.
	// Implémenté pour tester la corrélation d'un champ saisi par rapport à un autre.
	// Comme il faut attendre la validation du formulaire pour faire ces tests, cette méthode est faite pour cela
	//--------------------------------------
	protected function testsSupplementairesPosterieurs() {
		if (get_class($_SESSION[_APP_LOGIN_]) == 'Login') {
			//vérification si le contenu des champ mot de passe est identique
			//dans le cas d'une gestion des mots de passe en interne
			$fieldPwd1 = $this->field('password');
			$fieldPwd2 = $this->field('password2');
			if ($fieldPwd1->value() != $fieldPwd2->value()) {
				$fieldPwd1->setErreur(true);
				$fieldPwd1->setLiberreur(getLib('PASSWORD_SAISIE_DIFFERENTE'));
				$fieldPwd2->setErreur(true);
				$fieldPwd2->setLiberreur(getLib('PASSWORD_SAISIE_DIFFERENTE'));
				return true;
			}
		}
		return false;		//pas d'erreur
	}

	//--------------------------------------
	// affichage du formulaire
	//--------------------------------------
	public function afficher() {
		parent::afficher();		//permet d'ajouter des tests de construction du formulaire
		//ATTENTION : les champs disabled ne renvoient aucun POST !!! Donc impossible de récupérer les données depuis une suppression
		$profilEdite = $this->_tab_donnees['profil'];
		$profilAdmin = $_SESSION[_APP_DROITS_]->getIdProfil('PROFIL_ADMIN');
		$monProfil = $_SESSION[_APP_LOGIN_]->getProfil();
		$enable = (! (
						($this->getOperation() == self::CONSULTER) || 
						($this->getOperation() == self::SUPPRIMER)	|| 
						(($profilEdite == $profilAdmin) && ($monProfil != $profilAdmin)) 
					 )
				   );

		$chaine = '';
		$chaine.= '<div class="container-lg px-0">';
			$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
				$chaine.= '<fieldset class="border p-3">';
					$chaine.= $this->draw($enable);
				$chaine.= '</fieldset>';
				$chaine.= '<p class="small">(*) '.getLib('CHAMP_REQUIS').' (1) '.getLib('LECTURE_SEULE').'</p>';
			$chaine.= '</form>';
		$chaine.= '</div>';
		return $chaine;
	}
}