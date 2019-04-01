<?php
//==============================================================
// CLASSE : form_squelette
//--------------------------------------------------------------
// Squelette de formulaire
//==============================================================

class Form_exemple_videotheque extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	private $_tabAutorise =				//tableau des fichiers autorisés
		array(
			'image/gif' => array('.gif', 250000),
			'image/jpeg' => array('.jpg', 250000)
		);
		
	//======================================
	// Méthodes privées
	//======================================
	// initialisation des données de travail	
	//--------------------------------------

	protected function initDonnees() {
		$this->_tab_donnees['titre'] = '';
		$this->_tab_donnees['annee'] = '';
		$this->_tab_donnees['visuel'] = 'couleur';
		$this->_tab_donnees['vu'] = false;
		$this->_tab_donnees['dvd'] = true;
		$this->_tab_donnees['bluray'] = false;
		$this->_tab_donnees['divx'] = false;
		$this->_tab_donnees['genre'] = 'horreur';
	}

	//----------------------------------------
	// construction des champs du formulaire
	//----------------------------------------

	protected function construitChamps() {
		parent::construitChamps();

		//---------------------
		//BROUILLON
		//---------------------
		$this->createField('text', 'titre', array(
			'newLine' => true,
			'dbfield' => 'titre',
			'label' => 'Titre du film',
			'llong' => 'col-2',
			'lalign' => 'right',
			'labelHelp' => 'aide sur titre',
			'labelHelpPos' => 'right',
			'clong' => 'col-5',
			'testMatches' => array('REQUIRED'),
			'value' => $this->_tab_donnees['titre'],
			'javascript' => 'onkeypress="capLock(event, \'idTitre\', \'fr\')"'
			));
		$this->createField('text', 'annee', array(
			'newLine' => true,
			'dbfield' => 'annee',
			'label' => 'Année de production',
			'llong' => 'col-2',
			'lalign' => 'right',
			'clong' => 'col-2',
			'testMatches' => array('REQUIRED', 'CHECK_INTEGER_4'),
			'value' => $this->_tab_donnees['annee'],
			));
		$this->createField('radio', 'nb', array(
			'newLine' => true,
			'groupName' => 'visuel',
			'dbfield' => 'cnb',
			'dpos' => 'first',
			'titre' => 'Visuel',
			'tlong' => 'col-2',
			'talign' => 'right',
			'titreHelp' => 'Préciser la chromatographie du film',
			'label' => 'Noir & blanc', 
			'lpos' => 'after', 
			'clong' => 'col-3',
			'value' => 'nb',
			'checked' => ($this->_tab_donnees['visuel'] == 'nb')
			));
		$this->createField('radio', 'couleur', array(
			'groupName' => 'visuel',
			'dbfield' => 'cnb',
			'dpos' => 'inter',
			'label' => 'Couleur', 
			'lpos' => 'after',
			'checked' => ($this->_tab_donnees['visuel'] == 'couleur'),
			'value' => 'couleur'
			));
		$this->createField('radio', 'nb_couleur', array(
			'groupName' => 'visuel',
			'dbfield' => 'cnb',
			'dpos' => 'last',
			'label' => 'Noir & Blanc et Couleur', 
			'lpos' => 'after',
			'checked' => ($this->_tab_donnees['visuel'] == 'les deux'),
			'value' => 'les deux'
			));	

		//Construction de la checkbox 'Vu'
		$javascript = 'onclick="';
		$javascript.= 'uf_showOnChecked(\'genre_film\', \'film_vu\');';
		$javascript.= '"';
		$this->createField('checkbox', 'film_vu', array(
			'newLine' => true,
			'dbfield' => 'vu',
			'dpos' => 'alone',
			'titre'	=> 'Vu',
			'tlong'	=> 'col-2',
			'talign' => 'right',
			'titreHelp' => 'Cocher si le film a déjà été vu',
			'label' => '',
			'lpos' => 'after',
			'clong' => 'col-1',
			'checked' => ($this->_tab_donnees['vu'] == true),
			'javascript' => $javascript
			));
	
		//construction du groupe de checkbox 'possede'
		$this->createField('checkbox', 'dvd', array(
			'groupName' => 'possede',
			'dbfield' => 'dvd',
			'dpos' => 'first',
			'titre' => 'Possède',
			'tlong' => 'col-1',
			'label' => 'DVD',
			'lpos' => 'before',
			'clong' => 'col-3',
			'border' => true,
			'checked' => ($this->_tab_donnees['dvd'] == true)
			));	
		$this->createField('checkbox', 'bluray', array(
			'groupName' => 'possede',
			'dbfield' => 'bluray',
			'dpos' => 'inter',
			'label' => 'Blu-ray',
			'lpos' => 'before',
			'checked' => ($this->_tab_donnees['bluray'] == true)
			));	
		$this->createField('checkbox', 'divx', array(
			'groupName' => 'possede',
			'dbfield' => 'divx',
			'dpos' => 'last',
			'label' => 'DivX',
			'lpos' => 'before',
			'checked' => ($this->_tab_donnees['divx'] == true)
		));	

		//Sélecteur genre_film
		//Noter ici que l'on ne remplace pas la classe par "bg-info" mais que l'on ajoute (ou enleve) la classe "bg-info" à la classe existante !
		//En effet, la classe form-control est une classe de base du selecteur (fournie par Boostrap) qu'il ne faut pas supprimer.
		$javascript = 'onchange="';
		$javascript.= 'if (this.options[this.selectedIndex].value == \'comedie\') {';
		$javascript.= 'document.getElementById(\'idTitre\').className = \'form-control bg-info\';';
		$javascript.= 'document.getElementById(\'idAnnee\').className = \'form-control bg-info\';';
		$javascript.= '}';
		$javascript.= 'else {';
		$javascript.= 'document.getElementById(\'idTitre\').className = \'form-control\';';
		$javascript.= 'document.getElementById(\'idAnnee\').className = \'form-control\';';
		$javascript.= '}';
		$javascript.= '"'; 
		$this->createField('select', 'genre_film', array(
			'newLine' => true,
			'dbfield' => 'genre',
			'label' => 'Genre',
			'llong' => 'col-2',
			'lalign' => 'right',
			'complement' => 'fillSelect',
			'clong' => 'col-3',
			'value' => $this->_tab_donnees['genre'],
			'javascript' => $javascript
			));

		//construction bouton Submit
		$this->createField('bouton', 'submit', array(
			'newLine' => true,
			'dbfield' => 'btvalide',
			'inputType' => 'submit',
			'decalage' => 'col-2',
			'label' => 'Ok',
			'llong' => 'col-12',
			'lclass' => 'btn btn-primary',
			'clong' => 'col-2',
			'value' => 'Ok'
			));
		//construction bouton Reset
		$this->createField('bouton', 'annuler', array(
			'inputType' => 'reset',
			'label' => 'Annuler',
			'llong' => 'col-12',
			'lclass' => 'btn btn-secondary',
			'clong' => 'col-2',
			'value' => 'Annuler'
			)); 
		//construction bouton Button
		$javascript = 'onclick="alert(\'Ceci est une action javascript\');"';
		$this->createField('bouton', 'bouton', array(
			'inputType' => 'button',
			'label' => 'Test',
			'lclass' => 'btn btn-danger',
			'llong' => 'col-12',
			'clong' => 'col-2',
			'javascript' => $javascript,
			'value' => 'Test'
		)); 
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
	public function charger($id) {
/*		//charger les données à afficher dans le formulaire : $id est l'id unique du tuple à afficher
		$tuple = ...
		//chargement des données récupérées dans la structure de données du formulaire 
		$this->setIdTravail($id);
		$this->_tab_donnees['data1'] = $tuple['data1'];
		$this->_tab_donnees['data2'] = $tuple['data2'];
		$this->_tab_donnees['data3'] = $tuple['data3'];
		$this->_tab_donnees['data4'] = $tuple['data4'];
		$this->_tab_donnees['data5'] = $tuple['data5'];
		$this->_tab_donnees['data6'] = $tuple['data6'];
*/		$this->construitChamps();
		return true;  //ou false si erreur de chargement données
	}

	//surcharge de la méthode doThings()
	//la méthode est appelée immédiatement après construitChamps() et permet d'affiner la construction du formulaire
	//noter ici que l'on ne remplace pas la classe par "bg-info" mais que l'on ajoute la classe "bg-info" à la classe existente !
	protected function doThings() {
		if ($this->field('genre_film')->value() == 'comedie') {
			$this->field('titre')->setCclass(trim($this->field('titre')->cclass().' bg-info'));
			$this->field('annee')->setCclass(trim($this->field('annee')->cclass().' bg-info'));
		}
	}

	//modification des données renvoyées par le formulaire juste après le POST et avant de getData()
	//Sur la première ligne on ajoute un champ 'preferences' aux données renvoyées par le formulaire
	//Sur la 2ème ligne on modifie le genre du film et on le force à "Science-fiction" si le titre du film est "Star Wars"
	protected function doUltimateThings(&$donnees) {
		//création d'un champ supplémentaire
		$donnees['preferences'] = 24;
		//modification d'un champ existant
		if ($donnees['titre'] = 'Star Wars') $donnees['genre'] = 'Science-fiction';
	}


	//--------------------------------------
	// Tests unitaires supplementaires
	//--------------------------------------

	protected function testsSupplementaires($champ) {
		if ($champ->idField() == 'annee') {
			if (($champ->value() < 1920) || ($champ->value() > 2020)) {
				$champ->setErreur(true);
				$champ->setLiberreur('L\'année doit être comprise entre 1910 et 2020');
				return true;
			}
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
		//test existence saisie horaires à faire ici
		if (($this->field('titre')->value() == 'La Guerre des étoiles') && ($this->field('annee')->value() != '1977')){ 
			$this->field('annee')->setErreur(true);
			$this->field('annee')->setLiberreur('La Guerre des étoiles ne peut pas être sorti à une autre année que 1977');
			return true;
		}
		if (($this->field('film_vu')->checked()) && ($this->field('annee')->value() > 2016)){
			$this->field('film_vu')->setErreur(true);
			$this->field('film_vu')->setLiberreur('Vous ne pouvez pas avoir vu ce film');
			return true;
		}
		return false; //pas d'erreur
	}
	
	//--------------------------------------
	// affichage du formulaire
	//--------------------------------------
	public function afficher() {
		parent::afficher();		//permet d'ajouter des tests de construction du formulaire
		$chaine = '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
			$chaine.= '<fieldset class="border p-3">';
				$chaine.= '<h1>Formulaire</h1>';
				$chaine.= $this->draw(true);
				$chaine.= '<p class="small">(*) Champ requis (1) Lecture seule</p>';
			$chaine.= '</fieldset>';
		$chaine.= '</form>';

		return $chaine;
	}

}