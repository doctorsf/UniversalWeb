<?php
//==============================================================
// CLASSE : form_recherche_addon (formulaire de recherche avec addon)
//--------------------------------------------------------------
// éè
//==============================================================

class Form_recherche_addon extends UniversalForm {

	private	$_tab_donnees = array();								//tampon des données

	//======================================
	// Méthodes privées
	//======================================
	// initialisation des données de travail	
	//--------------------------------------

	protected function initDonnees() {
		$this->_tab_donnees['range']	= 'hostname';				//hostname/ip/mac/responsable/fonction/description
		$this->_tab_donnees['saisie']	= '';
	}

	//----------------------------------------
	// construction des champs du formulaire
	//----------------------------------------

	protected function construitChamps() {
		parent::construitChamps();

		//construction zone search
		//dans un champ de type recherche, le bouton fait office de label
		$javascript = '';
		$this->createField('search', 'saisie', array(
			'newLine' => false,
			'dbfield' => 'saisie',
			'inputType' => 'search',				//search(defaut), text, time, date, etc.
			'addon' => true,
			'apos' => 'before',
			'aclass' => 'btn btn-success',
			'complement' => array('hostname' => 'Hostname', 'ip' => '@IP', 'mac' => '@Mac', 'os' => 'OS', 'responsable' => 'Responsable', 'fonction' => 'Fonction', 'description' => 'Description', 'groupe' => 'Groupe', 'si' => 'SI'),
			'label' => '<span class="fas fa-search"></span>',
			'lpos' => 'before',
			'llong' => '',										//col-12 dessine le bouton sur la totalité de sa largeur. Si vide, le bouton est dessiné à la largeur du libellé
			'lclass' => 'btn btn-success',						//classe du bouton
			'clong' => 'col no-horizontal-padding',				//longueur de la zone de champ (du bouton)
			'cclass' => '',
			'maxlength' => 50,
			'placeholder' => 'recherche',
			'value' => array($this->_tab_donnees['range'], $this->_tab_donnees['saisie']),
			'javascript' => $javascript,
			'enable' => true,
			'readonly' => false,
			'invisible' => false
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
	}

	//charger les données de l'utilisateur à partir d'une base de données
	public function charger($donnees) {
		$this->_tab_donnees['range']	= $donnees['range'];
		$this->_tab_donnees['saisie']	= $donnees['saisie'];
		$this->construitChamps();
		return true;  //ou false si erreur de chargement données
	}

	//--------------------------------------
	// Tests supplémentaires sur certains champs, en plus des test de			
	// validation définit à la construction	par le paramètre : testMatches
	// $champ : nom du champ testé
	//--------------------------------------

	protected function testsSupplementaires($champ) {
		return false; //pas d'erreur
	}

	//--------------------------------------
	// Tests supplementaires postérieurs.	
	// Executés une fois que tous les tests supplémentaires unitaires par champ	on été réalisés.
	// Implémenté pour tester la corrélation d'un champ saisi par rapport à un autre.
	// Comme il faut attendre la validation du formulaire pour faire ces tests, cette méthode est faite pour cela
	//--------------------------------------

	protected function testsSupplementairesPosterieurs() {
		return false;		//pas d'erreur
	}

	//--------------------------------------
	// affichage du formulaire
	//--------------------------------------
	public function afficher() {
		parent::afficher();		//permet d'ajouter des tests de construction du formulaire
		//ATTENTION : les champs disabled ne renvoient aucun POST !!! Donc impossible de récupérer les données depuis une suppression
		$enable = true;
		$chaine = '';
		$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
		$chaine.= $this->draw($enable);
		$chaine.= '</form>';
		return $chaine;
	}
}