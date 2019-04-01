<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Elements 'div' et 'divfin'
// Version 3.11.3 du 17.01.2019
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
		}
		else {
			$html.= '<div id="'.$this->idbchamp().'" class="'.$this->cclass().'">';
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