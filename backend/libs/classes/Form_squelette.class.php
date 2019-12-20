<?php
//==============================================================
// CLASSE : form_squelette
//--------------------------------------------------------------
// Squelette de formulaire
//--------------------------------------------------------------
// 28.03.2018
//		Amélioration des boutons pour être adaptive
//==============================================================

class Form_squelette extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	//======================================
	// Méthodes privées
	//======================================
	// initialisation des données de travail	
	//--------------------------------------

	protected function initDonnees() {
		$this->_tab_donnees['libelle'] = '';						//champ texte
	}

	//----------------------------------------
	// construction des champs du formulaire
	//----------------------------------------

	protected function construitChamps() {
		parent::construitChamps();
		$this->createField('text', 'libelle', array(
			'newLine' => true,												//nouvelle ligne ? false par défaut
			'dbfield' => 'libelle',											//retour de la saisie
			'design' => 'inline',											//inline (defaut) / online
			'inputType' => 'text',											//type d'input
			//'decalage' => 'col-2',										//décallage en colonnes boostrap
			'label' => getLib('LIBELLE'),									//label
			'llong' => 'col-2',												//longueur de la zone de titre (seulement pour design inline)
			'lclass' => '',													//classe du label
			'lalign' => 'right',											//left (defaut) / right / center / jutify
			'labelHelp' => '',												//aide sur le label
			'lpos' => 'before',												//position du label par rapport au champ : before (defaut) / after
			'clong' => 'col-10',											//longueur de la zone de champ
			'cclass' => '',													//classe de la zone de champ
			'maxlength' => 128,												//nb caractres max en saisie
			'spellcheck' => true,											//correction orthographique
			'placeholder' => 'libellé',										//texte pré-affiché
			'testMatches' => array('REQUIRED'),								//test de la saisie
			'value' => $this->_tab_donnees['libelle'],						//valeur de la saisie
			'javascript' => '',												//code javascript associé
			'enable' => true,												//active, désactive le champ (dbfield renvoie NULL si false)
			'readonly' => false,											//lecture seule (defield renvoi value si readonly)
			'invisible' => false											//rend invisible le champ
		));

		//-------------------
		// Boutons
		//-------------------

		//construction bouton Submit
		$couleurBouton = 'primary';

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

		$javascript = '';
		if ($this->getOperation() == self::SUPPRIMER)
			$javascript = 'onclick="return confirm(\''.getLib('EMBALLAGE_SUPPRIMER_CERTAIN').'\');"';
		if ($this->getOperation() == self::RETIRER)
			$javascript = 'onclick="return confirm(\''.getLib('EMBALLAGE_RETIRER_CERTAIN').'\');"';
		$this->createField('bouton', 'submit', array(
			'newLine' => true,
			'dbfield' => 'bouton',
			'inputType' => 'submit',
			'decalage' => '',
			'label' => $labelBoutonValidation,
			'clong' => 'mt-5 col-12 col-sm-6 col-md-4 col-lg-3 ml-auto',
			'llong' => 'col-12',
			'lclass' => 'btn btn-'.$couleurBouton,			//classes graphique du bouton
			'javascript' => $javascript,
			'value' => $valueBoutonValidation
			));
		//ajout d'un bouton d'annulation
		if (($this->getOperation() == self::SUPPRIMER) || ($this->getOperation() == self::RETIRER) || ($this->getOperation() == self::AJOUTER)) {
			$libelle = '<span class="fas fa-reply mr-3"></span>'.getLib('RETOUR');
			$javascript = 'onclick="location.href=\''.echoPageBack().'\'; return false;"';
			$this->createField('bouton', 'retour', array(
				'newLine' => false,
				'inputType' => 'button',
				'label' => $libelle,
				'clong' => 'mt-5 col-12 col-sm-6 col-md-4 col-lg-3',
				'llong' => 'col-12',
				'lclass' => 'btn btn-secondary',	//classes graphique du bouton
				'javascript' => $javascript
				)); 		
		}
		//ajout d'un bouton d'édition si on est en consultation (sauf pour id_emballage <= 0)
		if (($this->getOperation() == self::CONSULTER) && ($this->_tab_donnees['id_emballage'] > _ID_SYSTEM_)) {
			$libelle = '<span class="far fa-edit mr-3"></span>'.getLib('EDITER');
			$adresse = str_replace('consulter', 'modifier', $_SERVER['REQUEST_URI']);
			$javascript = 'onclick="location.href=\''.$adresse.'\'; return false;"';
			$this->createField('bouton', 'edit', array(
				'newLine' => false,
				'inputType' => 'button',
				'label' => $libelle,
				'clong' => 'mt-5 col-12 col-sm-6 col-md-4 col-lg-3',
				'llong' => 'col-12',
				'lclass' => 'btn btn-secondary',	//classes graphique du bouton
				'javascript' => $javascript
				)); 		
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
				'clong' => 'mt-5 col-12 col-sm-6 col-md-4 col-lg-3',
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
	public function charger($id, $tuple) {
		//initialisation des données
		$this->initDonnees();
		//positionnement de l'id du tuple sur lequel on travaille
		$this->setIdTravail($id);
		//hydratation des donnees
		$this->_tab_donnees['libelle'] = $tuple['libelle'];
		//hydratation des donnees locales au formulaire
		$this->construitChamps();
		return true;
	}

	//--------------------------------------
	// Tests supplémentaires sur certains champs, en plus des test de			
	// validation définit à la construction	par le paramètre : testMatches
	// $champ : nom du champ testé
	//--------------------------------------

	protected function testsSupplementaires($champ) {
/*		if ($champ->idField() == 'data1') {
			if (fonction_test_existence_dans_base($champ->value()) {
				$champ->setErreur(true);
				$champ->setLiberreur('Cette valeur existe déjà dans la base de données');
			}
			return $champ->erreur();
		}
*/		return false; //pas d'erreur
	}

	//--------------------------------------
	// Tests supplementaires postérieurs.	
	// Executés une fois que tous les tests supplémentaires unitaires par champ	on été réalisés.
	// Implémenté pour tester la corrélation d'un champ saisi par rapport à un autre.
	// Comme il faut attendre la validation du formulaire pour faire ces tests, cette méthode est faite pour cela
	//--------------------------------------

	protected function testsSupplementairesPosterieurs() {
/*		$fieldData2 = $this->field('data2');
		$fieldData3 = $this->field('data3');
		if ($fieldData3->value() > $fieldData2->value()) {
			$fieldData3->setErreur(true);
			$fieldData3->setLiberreur('Le champ data3 ne doit pas être supérieur au champ data2');
			return true;
		}
*/		return false;		//pas d'erreur
	}

	//--------------------------------------
	// affichage du formulaire
	//--------------------------------------
	public function afficher() {
		parent::afficher();		//permet d'ajouter des tests de construction du formulaire
		//ATTENTION : les champs disabled ne renvoient aucun POST !!! Donc impossible de récupérer les données depuis une suppression
		$enable = (!(($this->getOperation() == self::CONSULTER) || ($this->getOperation() == self::SUPPRIMER)));
		$chaine = '';

		$chaine.= '<div class="container-lg px-0 mt-5">';
			$chaine.= '<h1>Titre</h1>';
			$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
				$chaine.= '<fieldset class="border p-4">';
					$chaine.= $this->draw($enable);
				$chaine.= '</fieldset>';
				$chaine.= '<p class="small">(*) '.getLib('CHAMP_REQUIS').' (1) '.getLib('LECTURE_SEULE').'</p>';
			$chaine.= '</form>';
		$chaine.= '</div>';
		return $chaine;
	}
}