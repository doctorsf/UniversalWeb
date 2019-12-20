<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Elements 'div' et 'divfin'
// Version 3.17.0 du 13.12.2019
//==============================================================

class UniversalFieldDiv extends UniversalField {

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	//surcharge du constructeur pour l'initialisation de cet objet
	public function __construct(array $donnees, $idformulaire) {
		$this->setFieldType('div');
		$this->setIdParentForm($idformulaire);
		parent::__construct($donnees);
	}

	public function draw($enabled) {
		$html = '';
		if ($this->invisible()) {
			$html.= '<div id="'.$this->idbchamp().'" class="invisible">';
			$html.= '<div id="'.$this->idztitre().'"></div>'; 
			$html.= '<div id="'.$this->idzchamp().'"></div>';
		}
		else {
			$html.= '<div id="'.$this->idbchamp().'" class="'.$this->cclass().'">';
			$html.= '<div id="'.$this->idztitre().'"></div>'; 
			$html.= '<div id="'.$this->idzchamp().'"></div>';
		}
		return $html;
	}
}

class UniversalFieldDivFin extends UniversalField {

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	//surcharge du constructeur pour l'initialisation de cet objet
	public function __construct(array $donnees, $idformulaire) {
		$this->setFieldType('divfin');
		$this->setIdParentForm($idformulaire);
		parent::__construct($donnees);
	}

	public function draw($enabled) {
		$html = '</div>';
		return $html;
	}
}