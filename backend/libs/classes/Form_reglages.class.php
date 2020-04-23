<?php
//==============================================================
// CLASSE : Form_reglages
// Formulaire des réglages de l'application
//==============================================================

class Form_reglages extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	//======================================
	// Méthodes privées
	//======================================
	// initialisation des données de travail	
	//--------------------------------------

	protected function initDonnees() {
		//l'initialisation est realisée à partir de la variable de session
		//car c'est le seul moyen de passer au formulaire sa structure, puisque variable.
		foreach($_SESSION['listeReglages'] as $reglage) {
			$this->_tab_donnees[$reglage['id']] = $reglage;
		}
	}

	//----------------------------------------
	// construction des champs du formulaire
	//----------------------------------------

	protected function construitChamps() {
		parent::construitChamps();

		foreach($this->_tab_donnees as $reglage) {
			(existeLib($reglage['libelle'])) ? $libelle = getLib($reglage['libelle']) : $libelle = $reglage['libelle'];
			(existeLib($reglage['comment'])) ? $comment = getLib($reglage['comment']) : $comment = $reglage['comment'];

			if ($reglage['type'] == 'text') {
				$this->createField('text', $reglage['id'], array(
					'newLine' => true,												//nouvelle ligne ? false par défaut
					'dbfield' => $reglage['id'],									//retour de la saisie
					'inputType' => 'text',											//type d'input
					'design' => 'inline',											//inline (defaut) / online
					'label' => $libelle,											//label
					'labelHelp' => $comment,										//label
					'lalign' => 'md-right',											//alignement label a droite si res > md
					'llong' => 'col-12 col-md-4',									//longueur zone de titre
					'clong' => 'col-12 col-md-8',									//longueur zone de champ
					'maxlength' => $reglage['max'],									//nb caractres max en saisie
					'spellcheck' => false,											//correction orthographique
					'placeholder' => getLib('VALEUR'),								//texte pré-affiché
					'value' => $reglage['valeur'],									//valeur de la saisie
				));
			}
			elseif ($reglage['type'] == 'number') {
				$this->createField('text', $reglage['id'], array(
					'newLine' => true,												//nouvelle ligne
					'dbfield' => $reglage['id'],									//retour de la saisie
					'inputType' => 'number',										//saisie nombre attendue
					'min' => $reglage['min'],										//saisie min
					'max' => $reglage['max'],										//saisie max
					'step' => $reglage['step'],										//pas de la saisie
					'design' => 'inline',											//design inline
					'label' => $libelle,											//label
					'labelHelp' => $comment,										//label
					'lalign' => 'md-right',											//alignement label a droite si res > md
					'llong' => 'col-12 col-md-4',									//longueur zone de titre
					'clong' => 'col-12 col-md-8',									//longueur zone de champ
					'placeholder' => getLib('VALEUR'),								//texte pré-affiché
					'testMatches' => array('REQUIRED', 'NUMERIC'),					//test de la saisie
					'value' => $reglage['valeur']									//valeur
				));
			}
			elseif ($reglage['type'] == 'boolean') {
				//on se sert des min et max pour donner des veleur et valeurs inverse au switch
				($reglage['min'] != '') ? $valueBase = $reglage['min'] : $valueBase = 1;
				($reglage['max'] != '') ? $valueInverse = $reglage['max'] : $valueInverse = 0;
				$checked = ($reglage['valeur'] == $valueBase);
				$this->createField('switch', $reglage['id'], array(
					'newLine' => true,												//nouvelle ligne
					'dbfield' => $reglage['id'],									//retour de la saisie
					'titre' => $libelle,											//titre
					'titreHelp' => $comment,										//label
					'tlong' => 'col-10 col-md-4',									//longueur du titre
					'talign' => 'md-right',											//alignement label a droite si res > md
					'label' => '',													//label
					'clong' => 'col-2 col-md-4 text-right text-md-left',			//longueur et positionnement de la zone de champ
					'value' => $valueBase,
					'valueInverse' => $valueInverse,
					'checked' => $checked
				));
			}
			elseif ($reglage['type'] == 'date') {
				$this->createField('text', $reglage['id'], array(
					'newLine' => true,												//nouvelle ligne ? false par défaut
					'dbfield' => $reglage['id'],									//retour de la saisie
					'inputType' => 'text',											//type d'input
					'design' => 'inline',											//inline (defaut) / online
					'label' => $libelle,											//label
					'labelHelp' => $comment,										//label
					'lalign' => 'md-right',											//alignement label a droite si res > md
					'llong' => 'col-12 col-md-4',									//longueur zone de titre
					'clong' => 'col-12 col-md-8',									//longueur zone de champ
					'placeholder' => getLib('VALEUR'),								//texte pré-affiché
					'testMatches' => array('REQUIRED'),								//test de la saisie
					'value' => date(_FORMAT_DATE_, strtotime($reglage['valeur']))
				));
			}
			elseif ($reglage['type'] == 'datetime') {
				$this->createField('text', $reglage['id'], array(
					'newLine' => true,												//nouvelle ligne ? false par défaut
					'dbfield' => $reglage['id'],									//retour de la saisie
					'inputType' => 'text',											//type d'input
					'design' => 'inline',											//inline (defaut) / online
					'label' => $libelle,											//label
					'labelHelp' => $comment,										//label
					'lalign' => 'md-right',											//alignement label a droite si res > md
					'llong' => 'col-12 col-md-4',									//longueur zone de titre
					'clong' => 'col-12 col-md-8',									//longueur zone de champ
					'placeholder' => getLib('VALEUR'),								//texte pré-affiché
					'testMatches' => array('REQUIRED'),								//test de la saisie
					'value' => date(_FORMAT_DATE_TIME_, strtotime($reglage['valeur']))
				));
			}

		}

		//-------------------
		// Boutons
		//-------------------

		//construction bouton Submit
		$couleurBouton = 'primary';
		$valueBoutonValidation = getLib('MODIFIER');
		$labelBoutonValidation = '<span class="far fa-edit mr-3"></span>'.$valueBoutonValidation;

		$this->createField('bouton', 'submit', array(
			'newLine' => true,
			'inputType' => 'submit',
			'decalage' => '',
			'flexLine' => 'mt-5',							//(hack) contourne propriété flexLine pour mettre petite marge haute
			'label' => $labelBoutonValidation,
			'clong' => 'col-12 col-sm-6 col-md-4 col-lg-3 ml-auto',
			'llong' => 'col-12',
			'lclass' => 'btn btn-'.$couleurBouton			//classes graphique du bouton
		));
		//ajout d'un bouton d'annulation
		$libelle = '<span class="fas fa-reply mr-3"></span>'.getLib('RETOUR');
		$javascript = 'onclick="location.href=\''.echoPageBack().'\'; return false;"';
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

	//======================================
	// Methodes	publiques					
	//======================================
	// Chargement des données depuis la		
	// base de données. reponse requete		
	//--------------------------------------

	//les données sont été pre-chargées par le formulaire dans une variable de session
	//ceci permet de conserver le pattern de construction du formulaire de réglages qui est par définition variable
	public function charger() {
		//DEBUG_('chargement', $_SESSION['listeReglages']);
		//initialisation des données
		$this->initDonnees();
		//hydratation des donnees
		$this->_tab_donnees = $_SESSION['listeReglages'];
		//hydratation des donnees locales au formulaire
		$this->construitChamps();
		return true;
	}

	//--------------------------------------
	// Choses à faire juste avant d'envoyer les données saisies
	// On transforme les éventuels réglages de type "date" et "datetime" en format SQL pour une injection directe
	//--------------------------------------
	protected function doUltimateThings(&$donnees) {
		foreach($_SESSION['listeReglages'] as $reglage) {
			if ($reglage['type'] == 'datetime') 
				$donnees[$reglage['id']] = changeDateTimeFormat($donnees[$reglage['id']], _FORMAT_DATE_TIME_, _FORMAT_DATE_TIME_SQL_);
			elseif ($reglage['type'] == 'date') 
				$donnees[$reglage['id']] = changeDateTimeFormat($donnees[$reglage['id']], _FORMAT_DATE_, _FORMAT_DATE_SQL_);
		}
	}

	//--------------------------------------
	// affichage du formulaire
	//--------------------------------------
	public function afficher() {
		parent::afficher();		//permet d'ajouter des tests de construction du formulaire
		$chaine = '';
		$chaine.= '<div class="container-lg px-0 mt-5">';
			$chaine.= '<div class="card border-info">';
				$chaine.= '<div class="uw-card-header text-info">';
					$chaine.= '<h3 class="mb-5"><span class="fas fa-sliders-h"></span>&nbsp;'.getLib('REGLAGES_APPLICATION').'</h3>';
				$chaine.= '</div>';
				$chaine.= '<div class="card-body">';
					$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
						$chaine.= $this->draw(true);
						$chaine.= '<p class="small">(*) '.getLib('CHAMP_REQUIS').'</p>';
					$chaine.= '</form>';
				$chaine.= '</div>';
			$chaine.= '</div>';
		$chaine.= '</div>';
		return $chaine;
	}
}