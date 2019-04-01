<?php
//==============================================================
// CLASSE : form_user
//--------------------------------------------------------------
// Formulaire de saisie user
//--------------------------------------------------------------
// 15.01.2018 : correction petit bug (ajout addslashes à code javascript)
//==============================================================

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
		$this->_tab_donnees['password'] = '';
		$this->_tab_donnees['password2'] = '';
		$this->_tab_donnees['langue'] = 'fr';
		$this->_tab_donnees['profil'] = 0;
		$this->_tab_donnees['testeur'] = 0;
		$this->_tab_donnees['date_creation'] = date('d/m/Y H:i');
		$this->_tab_donnees['dernier_acces'] = '0000-00-00 00:00:00';
		$this->_tab_donnees['action_demandee'] = 0;
		$this->_tab_donnees['code_validation'] = 0;
		$this->_tab_donnees['active'] = 0;
		$this->_tab_donnees['autolog'] = 0;
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
		$affichable = (accesAutorise('FONC_ADM_APP') && 
						(($this->getOperation() != self::MODIFIER) || ($this->getIdTravail() != $_SESSION[_APP_LOGIN_]->getId()))
					  );

		if ($affichable)
		$this->createField('checkbox', 'active', array(
				'dbfield' => 'active',
				'group' => 'alone',
				'label' => ucfirst(getLib('COMPTE_ACTIF')),
				'lclass' => 'w10',
				'border' => false,
				'checked' => ($this->_tab_donnees['active'] == 1)
				));
		if ($affichable)
		$this->createField('checkbox', 'testeur', array(
				'dbfield' => 'testeur',
				'group' => 'alone',
				'label' => ucfirst(getLib('TESTEUR')),
				'lclass' => 'w10',
				'border' => false,
				'checked' => ($this->_tab_donnees['testeur'] == 1)
				));
		$this->createField('separateur', 'separateur', array(
				'label' => 'Informations utilisateur',
				'lclass' => 'gras souligne green upper'
				));
		$this->createField('text', 'idUser', array(
				'dbfield' => 'id_user',
				'maxlength' => 100,
				'lineType' => 'lineOnline',
				'lineClass' => 'w15 padding10',
				'label' => getLib('IDENTIFIANT'),
				'cclass' => 'tominuscules w15',
				'value' => $this->_tab_donnees['id_user'],
				'testMatches' => array('REQUIRED', 'CHECK_ALPHA_SIMPLE')
				));
		$this->createField('text', 'nom', array(
				'dbfield' => 'nom',
				'maxlength' => 100,
				'lineType' => 'lineOnline',
				'lineClass' => 'w25',
				'label' => getLib('NOM'),
				'cclass' => 'tominuscules w25',
				'value' => $this->_tab_donnees['nom'],
				'testMatches' => array('REQUIRED', 'CHECK_ALPHA_NOMS')
				));
		$this->createField('text', 'prenom', array(
				'dbfield' => 'prenom',
				'maxlength' => 100,
				'lineType' => 'lineOnline',
				'lineClass' => 'w25',
				'label' => getLib('PRENOM'),
				'cclass' => 'tominuscules w25',
				'value' => $this->_tab_donnees['prenom'],
				'testMatches' => array('REQUIRED', 'CHECK_ALPHA_NOMS')
				));
		$this->createField('text', 'password', array(
				'dbfield' => 'password',
				'inputType' => 'password',
				'maxlength' => 40,
				'lineType' => 'lineOnline',
				'lineClass' => 'first w25 padding10',
				'label' => getLib('MOT_DE_PASSE'),
				'cclass' => 'w25',
				'javascript' => 'onkeypress="capLock(event, \'idPassword\')"',
				'value' => $this->_tab_donnees['password'],
				'testMatches' => array('REQUIRED')
				));
		$this->createField('text', 'password2', array(
				'dbfield' => 'password2',
				'inputType' => 'password',
				'maxlength' => 40,
				'lineType' => 'lineOnline',
				'lineClass' => 'w25',
				'label' => getLib('MOT_DE_PASSE_RETAPER'),
				'cclass' => 'w25',
				'javascript' => 'onkeypress="capLock(event, \'idPassword2\')"',
				'value' => $this->_tab_donnees['password2'],
				'testMatches' => array('REQUIRED')
				));
		$this->createField('text', 'email', array(
				'dbfield' => 'email',
				'inputType' => 'email',
				'maxlength' => 255,
				'lineType' => 'lineOnline',
				'lineClass' => 'w20',
				'label' => getLib('EMAIL'),
				'cclass' => 'w20',
				'value' => $this->_tab_donnees['email'],
				'testMatches' => array('REQUIRED')
				));
		if ($affichable)
		$this->createField('select', 'profil', array(
				'dbfield' => 'profil',
				'lineType' => 'lineOnline',
				'lineClass' => 'first w10 padding10',
				'group' => 'alone',
				'label' => getLib('PROFIL'),
				'lpos' => 'before',
				'cclass' => 'w10',
				'value' => $this->_tab_donnees['profil'],
				'testMatches' => array('REQUIRED')
				));
		($affichable) ? $lineClass = 'w10' : $lineClass = 'first w10 padding10';
		$this->createField('select', 'langue', array(
				'dbfield' => 'langue',
				'lineType' => 'lineOnline',
				'lineClass' => $lineClass,
				'group' => 'alone',
				'label' => getLib('LANGUE'),
				'lpos' => 'before',
				'cclass' => 'w10',
				'value' => $this->_tab_donnees['langue'],
				'testMatches' => array('REQUIRED')
				));

		if ($this->getOperation() != self::AJOUTER) {
			if ($affichable)
			$this->createField('text', 'dateCreation', array(
					'dbfield' => 'date_creation',
					'lineType' => 'lineOnline',
					'lineClass' => 'first w20 padding10',
					'label' => getLib('DATE_CREATION'),
					'cclass' => 'w15',
					'value' => date(_FORMAT_DATE_TIME_NOS_, strtotime($this->_tab_donnees['date_creation'])),
					'testMatches' => array('REQUIRED')
					));
			if ($affichable)
			$this->createField('text', 'dernierAcces', array(
					'dbfield' => 'dernier_acces',
					'lineType' => 'lineOnline',
					'lineClass' => 'w10',
					'inputType' => 'datetime',
					'label' => getLib('DERNIER_ACCES'),
					'cclass' => 'w10',
					'value' => date(_FORMAT_DATE_TIME_, strtotime($this->_tab_donnees['dernier_acces'])),
					'readonly' => true
					));
			if ($affichable)
			$this->createField('text', 'ip', array(
					'dbfield' => 'ip',
					'lineType' => 'lineOnline',
					'lineClass' => 'w10',
					'label' => getLib('DERNIERE_IP'),
					'cclass' => 'w10',
					'value' => $this->_tab_donnees['ip'],
					'readonly' => true
					));
			if ($affichable)
			$this->createField('text', 'actionDemandee', array(
					'dbfield' => 'action_demandee',
					'lineType' => 'lineOnline',
					'lineClass' => 'first w5 padding10',
					'label' => getLib('ACTION_DEMANDEE'),
					'cclass' => 'w5',
					'value' => $this->_tab_donnees['action_demandee']
					));
			if ($affichable)
			$this->createField('text', 'codeValidation', array(
					'dbfield' => 'code_validation',
					'lineType' => 'lineOnline',
					'lineClass' => 'w20 padding5',
					'label' => getLib('CODE_VALIDATION'),
					'cclass' => 'w20',
					'value' => $this->_tab_donnees['code_validation']
					));
		}

		$this->createField('checkbox', 'autolog', array(
				'dbfield' => 'autolog',
				'group' => 'alone',
				'label' => getLib('AUTOLOG'),
				'lclass' => 'w10',
				'checked' => ($this->_tab_donnees['autolog'] == 1)
				));
		if ($affichable)
		$this->createField('area', 'notesPrivees', array(
				'dbfield' => 'notes_privees',
				'cols' => 58,
				'rows' => 5,
				'label' => getLib('NOTES_PRIVEES'),
				'lclass' => 'w10',
				'value' => $this->_tab_donnees['notes_privees']
				));

		//construction bouton Submit
		if ($this->getOperation() == self::CONSULTER) {
			$labelBoutonValidation = getLib('RETOUR');
		}
		elseif ($this->getOperation() == self::AJOUTER) {
			$labelBoutonValidation = getLib('AJOUTER');
		}
		elseif ($this->getOperation() == self::MODIFIER) {
			$labelBoutonValidation = getLib('MODIFIER');
		}
		elseif ($this->getOperation() == self::DUPLIQUER) {
			$labelBoutonValidation = getLib('DUPLIQUER');
		}
		elseif ($this->getOperation() == self::SUPPRIMER) {
			$labelBoutonValidation = getLib('SUPPRIMER');
		}

		//boutons
		$javascript = '';
		if ($this->getOperation() == self::SUPPRIMER)
			$javascript = 'onclick="return confirm(\''.addslashes(getLib('CERTAIN_SUPPRIMER_USER')).'\');"';
		$this->createField('submit', 'submit', array(
			'group' => 'first',
			'cclass' => 'submit_right btn',
			'value' => $labelBoutonValidation,
			'javascript' => $javascript
			));
		//ajout d'un bouton d'annulation de suppression
		if (($this->getOperation() == self::SUPPRIMER) || ($this->getOperation() == self::AJOUTER)) {
			$javascript = 'onclick="window.history.back()"';
			$this->createField('button', 'retour', array(
				'group' => 'last',
				'cclass' => 'submit_right btn',
				'value' => getLib('RETOUR'),
				'javascript' => $javascript
				)); 		
		}
		//ajout d'un bouton d'édition si on est en consultation
		if ($this->getOperation() == self::CONSULTER) {
			$libelle = getLib('EDITER_CET_UTILISATEUR');
			if ($this->getIdTravail() == $_SESSION[_APP_LOGIN_]->getId()) $libelle = 'Editer mon compte';
			$adresse = str_replace('consulter', 'modifier', $_SERVER['REQUEST_URI']);
			$javascript = 'onclick="location.href=\''.$adresse.'\'; return false;"';
			$this->createField('button', 'edit', array(
				'group' => 'last',
				'cclass' => 'submit_right btn',
				'value' => $libelle,
				'javascript' => $javascript
				)); 		
		}
		//ajout d'un bouton d'annulation d'édition si on est en édition
		if ($this->getOperation() == self::MODIFIER) {
			$adresse = str_replace('modifier', 'consulter', $_SERVER['REQUEST_URI']);
			$javascript = 'onclick="location.href=\''.$adresse.'\'; return false;"';
			$this->createField('button', 'annule', array(
				'group' => 'last',
				'cclass' => 'submit_right btn',
				'value' => getLib('ANNULER_EDITION'),
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
			$this->_tab_donnees['langue'] = $user->getLangue();
			$this->_tab_donnees['profil'] = $user->getProfil();
			$this->_tab_donnees['testeur'] = $user->getTesteur();
			$this->_tab_donnees['date_creation'] = $user->getDateCreation();
			$this->_tab_donnees['dernier_acces'] = $user->getDernierAcces();
			$this->_tab_donnees['action_demandee'] = $user->getActionDemandee();
			$this->_tab_donnees['code_validation'] = $user->getCodeValidation();
			$this->_tab_donnees['active'] = $user->getActive();
			$this->_tab_donnees['autolog'] = $user->getAutolog();
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
		if ($champ->idField() == 'idUser') {
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
		//vérification si le contenu des champ mot de passe est identique
		$fieldPwd1 = $this->field('password');
		$fieldPwd2 = $this->field('password2');
		if ($fieldPwd1->value() != $fieldPwd2->value()) {
			$fieldPwd1->setErreur(true);
			$fieldPwd1->setLiberreur('Saisie non identique sur mot de passe');
			$fieldPwd2->setErreur(true);
			$fieldPwd2->setLiberreur('Saisie non identique sur mot de passe');
			return true;
		}
		return false;		//pas d'erreur
	}

	/*--------------------------------------*/
	/* affichage du formulaire				*/
	/*--------------------------------------*/
	public function afficher() {

		
		//ATTENTION : les champs disabled ne renvoient aucun POST !!! Donc impossible de récupérer les données depuis une suppression
		$enable = (!(($this->getOperation() == self::CONSULTER) || ($this->getOperation() == self::SUPPRIMER)));
		$chaine = '';

		$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" class="full_length_form">';
		$chaine.= '<fieldset>';
			$color = '';
			foreach($this->fields() as $objet) {
				if ($objet->idField() == 'profil') {$chaine.= $objet->draw($enable, sqlDroits_buildProfilesList($objet->value()));}
				elseif ($objet->idField() == 'langue') {$chaine.= $objet->draw($enable, sqlDivers_buildLanguesDispo($objet->value()));}
				else 
					$chaine.= $objet->draw($enable);
			}
		$chaine.= '</fieldset>';
		$chaine.= '</form>';
		$chaine.= '<p class="small">(*) '.getLib('CHAMP_REQUIS').' (1) '.getLib('LECTURE_SEULE').'</p>';
		return $chaine;
	}

}