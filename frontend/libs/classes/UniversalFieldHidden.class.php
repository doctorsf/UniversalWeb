<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'hidden'
// Version 3.22.0 du 05.05.2020
//==============================================================

class UniversalFieldHidden extends UniversalField {

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	//surcharge du constructeur pour l'initialisation de cet objet
	public function __construct(array $donnees, $idformulaire) {
		$this->setFieldType('hidden');
		$this->setIdParentForm($idformulaire);
		parent::__construct($donnees);
	}

	protected function setPostName($postName) {
		parent::setPostName('hid'.ucfirst($postName));
	}

	public function draw($enabled) {
		return '<input type="hidden" id="'.$this->id().'" name="'.$this->postName().'" value="'.stripslashes($this->value()).'" />';
	}
}