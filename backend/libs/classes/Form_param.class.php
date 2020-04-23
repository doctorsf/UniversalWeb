<?php
//==============================================================
// CLASSE : Form_param
//--------------------------------------------------------------
// formulaire d'affichage d'un paramètre application
//==============================================================

function selTypesParams($defaut) {
	$texte = '';
	($defaut == 'text') ? $selected = ' selected' : $selected = '';
	$texte.= '<option value="text"'.$selected.'>text</option>';
	($defaut == 'boolean') ? $selected = ' selected' : $selected = '';
	$texte.= '<option value="boolean"'.$selected.'>boolean</option>';
	($defaut == 'number') ? $selected = ' selected' : $selected = '';
	$texte.= '<option value="number"'.$selected.'>number</option>';
	($defaut == 'date') ? $selected = ' selected' : $selected = '';
	$texte.= '<option value="date"'.$selected.'>date</option>';
	($defaut == 'datetime') ? $selected = ' selected' : $selected = '';
	$texte.= '<option value="datetime"'.$selected.'>datetime</option>';
	return $texte;
}

class Form_param extends UniversalForm {

	private $_tab_donnees = array();

	//======================================
	// Méthodes privées
	//======================================
	// initialisation des données de travail	
	//--------------------------------------

	protected function initDonnees() {
		$this->_tab_donnees['id'] = 0;
		$this->_tab_donnees['ordre'] = 0;
		$this->_tab_donnees['parametre'] = '';
		$this->_tab_donnees['valeur'] = '0';
		$this->_tab_donnees['reglable'] = 0;
		$this->_tab_donnees['type'] = 'text';
		$this->_tab_donnees['libelle'] = '';
		$this->_tab_donnees['min'] = '';
		$this->_tab_donnees['max'] = '';
		$this->_tab_donnees['step'] = '';
		$this->_tab_donnees['comment'] = '';
	}

	//----------------------------------------
	// construction des champs du formulaire
	//----------------------------------------

	protected function construitChamps() {
		parent::construitChamps();

		$this->createField('hidden', 'ordre', array(
			'dbfield' => 'ordre',
			'value' => $this->_tab_donnees['ordre']
		));

		$this->createField('text', 'parametre', array(
			'newLine' => true,
			'dbfield' => 'parametre',
			'design' => 'online',
			'inputType' => 'text',
			'label' => getLib('PARAMETRE'),
			'clong' => 'col-12 col-md-6',
			'maxlength' => 32,
			'spellcheck' => false,
			'placeholder' => 'paramètre',
			'testMatches' => array('REQUIRED'),
			'value' => $this->_tab_donnees['parametre']
		));

		$this->createField('select', 'type', array(
			'newLine' => false,
			'dbfield' => 'type',
			'design' => 'online',
			'label' => getLib('PARAMETRE_TYPE'),
			'labelHelp' => getLib('PARAMETRE_TYPE_HELP'),
			'clong' => 'col-12 col-md-6',
			'value' => $this->_tab_donnees['type'],
			'complement' => 'selTypesParams'
		));

		$this->createField('area', 'valeur', array(
			'newLine' => true,
			'dbfield' => 'valeur',
			'design' => 'online',
			'label' => getLib('VALEUR'),
			'clong' => 'col-12 col-md-6',
			'rows' => 4,
			'maxlength' => 255,
			'placeholder' => 'valeur',
			'value' => $this->_tab_donnees['valeur']
		));

		$this->createField('area', 'comment', array(
			'newLine' => false,
			'dbfield' => 'comment',
			'design' => 'online',
			'label' => getLib('COMMENTAIRES'),
			'labelHelp' => getLib('PARAM_COMMENTS_HELP'),
			'clong' => 'col-12 col-md-6',
			'rows' => 4,
			'maxlength' => 255,
			'spellcheck' => true,
			'placeholder' => getLib('COMMENTAIRES'),
			'value' => $this->_tab_donnees['comment']
		));

		$this->createField('separateur', 'sep1', array(
			'newLine' => true,
			'label' => getLib('PARAMETRE_REGLAGE'), 
			'lclass' => 'font-weight-bold text-info',
			'flexLine' => 'px-3 mt-4',										//contourne propr. flexLine pour mettre petit padding en x
			'clong' => 'col-6 mb-3 px-0 border-bottom border-info'
		));

		$checked = ($this->_tab_donnees['reglable'] == 1);
		$this->createField('switch', 'reglable', array(
			'newLine' => true,
			'dbfield' => 'reglable',
			'design' => 'inline',
			'titre' => getLib('PARAMETRE_REGLAGE'),
			'titreHelp' => getLib('PARAMETRE_REGLAGE_HELP'),
			'label' => '',
			'tlong' => 'col-10 col-md-4',
			'clong' => 'col-2 col-md-4 text-right text-md-left',
			'checked' => $checked
		));

		($this->_tab_donnees['reglable']) ? $testMatch = array('REQUIRED') : $testMatch = null;
		$this->createField('text', 'libelle', array(
			'newLine' => true,
			'dbfield' => 'libelle',
			'design' => 'online',
			'inputType' => 'text',
			'label' => getLib('LIBELLE'),
			'labelHelp' => getLib('PARAMETRE_LIBELLE_HELP'),
			'clong' => 'col-12',
			'maxlength' => 64,
			'spellcheck' => true,
			'placeholder' => getLib('LIBELLE'),
			'testMatches' => $testMatch,
			'value' => $this->_tab_donnees['libelle']
		));

		$this->createField('text', 'min', array(
			'newLine' => true,
			'dbfield' => 'min',
			'design' => 'online',
			'inputType' => 'text',
			'label' => getLib('PARAMETRE_MIN'),
			'labelHelp' => getLib('PARAMETRE_MIN_HELP'),
			'labelHelpHtml' => true, 
			'flexLine' => 'mb-5',										//contourne propr. flexLine pour mettre petit padding en x
			'clong' => 'col-6 col-md-4',
			'spellcheck' => false,
			'placeholder' => getLib('PARAMETRE_MIN'),
			'value' => $this->_tab_donnees['min']
		));

		$this->createField('text', 'max', array(
			'newLine' => false,
			'dbfield' => 'max',
			'design' => 'online',
			'inputType' => 'text',
			'label' => getLib('PARAMETRE_MAX'),
			'labelHelp' => getLib('PARAMETRE_MAX_HELP'),
			'labelHelpHtml' => true,	
			'clong' => 'col-6 col-md-4',
			'spellcheck' => false,
			'placeholder' => getLib('PARAMETRE_MAX'),
			'value' => $this->_tab_donnees['max']
		));

		$this->createField('text', 'step', array(
			'newLine' => false,
			'dbfield' => 'step',
			'design' => 'online',
			'inputType' => 'text',
			'label' => getLib('PARAMETRE_STEP'),
			'labelHelp' => getLib('PARAMETRE_STEP_HELP'),
			'labelHelpHtml' => true, 
			'clong' => 'col-6 col-md-4',
			'spellcheck' => false,
			'placeholder' => getLib('PARAMETRE_STEP'),
			'value' => $this->_tab_donnees['step']
		));

		//-------------------
		//boutons
		//-------------------

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
		}

		$javascript = '';
		if ($this->getOperation() == self::SUPPRIMER)
			$javascript = 'onclick="return confirm(\''.addslashes(getLib('SUPPRIMER_PARAMETRE_CERTAIN')).'\');"';
		$this->createField('bouton', 'submit', array(
			'newLine' => true,
			'dbfield' => 'bouton',
			'inputType' => 'submit',
			'decalage' => '',
			'label' => $labelBoutonValidation,
			'clong' => 'col-12 col-sm-6 col-md-4 offset-md-3 col-lg-3 offset-lg-6',
			'llong' => 'col-12',
			'lclass' => 'btn btn-primary',			//classes graphique du bouton
			'javascript' => $javascript,
			'value' => $valueBoutonValidation
			));
		//ajout d'un bouton d'annulation de suppression
		if (($this->getOperation() == self::SUPPRIMER) || ($this->getOperation() == self::AJOUTER)) {
			$libelle = '<span class="fas fa-reply mr-3"></span>'.getLib('RETOUR');
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
		if ($this->getOperation() == self::CONSULTER) {
			$libelle = '<span class="far fa-edit mr-3"></span>'.getLib('EDITER');
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
		$this->doThings();
	}

	//charger les données de l'utilisateur à partir d'une base de données
	public function charger($id, $tuple) {
		//initialisation des données
		$this->initDonnees();
		//positionnement de l'id du tuple sur lequel on travaille
		$this->setIdTravail($id);
		//hydratation des donnees
		$this->_tab_donnees = $tuple;
		//hydratation des donnees locales au formulaire
		$this->construitChamps();
		$this->doThings();
		return true;
	}

	protected function doThings() {
		$reglable = $this->field('reglable');
		$libelle = $this->field('libelle');
		if ($reglable->value()) {
			$libelle->setTestMatches(array('REQUIRED'));
		}
	}

	protected function testsSupplementairesPosterieurs() {
		if ($this->field('type')->value() == 'number') {
			$min = $this->field('min');
			$max = $this->field('max');
			if ($max->value() < $min->value()) {
				$min->setErreur(true);
				$min->setLiberreur(getLib('MINI_INF_MAXI'));
				$max->setErreur(true);
				$max->setLiberreur(getLib('MAXI_SUP_MINI'));
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
		$enable = (!(($this->getOperation() == self::CONSULTER) || ($this->getOperation() == self::SUPPRIMER)));
		$chaine = '';

		$chaine.= '<div class="container-lg px-0">';
			$chaine.= '<h1>'.getLib('PARAMETRE').'</h1>';
			$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
				$chaine.= '<fieldset class="border p-4">';
					$chaine.= $this->draw($enable);
				$chaine.= '</fieldset>';
				$chaine.= '<p class="small">(*) '.getLib('CHAMP_REQUIS').'</p>';
			$chaine.= '</form>';
		$chaine.= '</div>';
		return $chaine;
	}
}