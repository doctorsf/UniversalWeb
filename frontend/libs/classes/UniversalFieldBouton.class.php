<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'bouton'
// Version 3.11.3 du 17.01.2019
//==============================================================

class UniversalFieldBouton extends UniversalField {

	private $_valueBase;

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	//surcharge du constructeur pour l'initialisation de cet objet
	public function __construct(array $donnees, $idformulaire) {
		$this->setFieldType('bouton');				//sauvegarde du type de champ
		$this->setIdParentForm($idformulaire);		//sauvegarde de l'id du formulaire parent de ce champ
		if (!isset($donnees['value'])) {
			$donnees['value'] = $this->_valueBase;
		}
		else {
			$this->_valueBase = $donnees['value'];	//sauvegarde du libellé dans valueBase (réutilisé pour affichage)
		}
		parent::__construct($donnees);
	}

	protected function setPostName($postName) {
		parent::setPostName('bout'.ucfirst($postName));
	}

	public function valueBase() {
		return $this->_valueBase;
	}

	//fonction privée qui test les erreurs éventuelles de construction du radio
	private function _testConstructionErreur() {
		//test si le paramatre 'inputType' est valide
		if (!in_array($this->inputType(), array('submit', 'button', 'reset'))) {
			$this->setErreur(true);
			$this->setLiberreur('Paramètre \'inputType\' => \''.$this->inputType().'\' inaproprié pour le champ bouton \''.$this->idField().'\'');
			$this->setLiberreurHelp('Choisir parmi \'submit\', \'button\' ou \'reset\'');
		}
		return $this->erreur();
	}

	public function draw($enabled) {
		$html = '';

		//decalage
		if ($this->decalage() != '') {
			($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
			$html.= '<div class="'.$this->decalage().$invisible.'">';		//sert de push à droite
			$html.= '</div>';
		}

		//test erreurs éventuelles de construction
		$this->_testConstructionErreur();

		//bordure standard
		if ($this->border() === true) {
			$style = '';
			$border = ' border';
		}
		//bordure personnalisée
		elseif ($this->border() != '') {
			$border = '';
			$style = ' style="'.$this->border().'"';
		}
		//aucune bordure
		else {
			$border = '';
			$style = '';
		}

		(!$this->enable()) ? $enable = ' disabled' : $enable = '';
		($this->llong() != '') ? $llong = ' '.$this->llong() : $llong = '';
		($this->lclass() != '') ? $lclass = ' '.$this->lclass() : $lclass = '';
		($this->clong() != '') ? $clong = $this->clong() : $clong = '';
		($this->erreur() == true) ? $erreur = ' designError' : $erreur = '';
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		($this->labelHelpPos() != '') ? $labelHelp = ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp = '';
		($this->labelHelp() != '') ? $labelHelp.= ' title="'.$this->labelHelp().'"' : $labelHelp.= '';
		$zchampClasses = trim($clong.$border.$erreur.$invisible);
		$buttonClasses = trim($llong.$lclass);

		$html.= '<div id="'.$this->idzchamp().'" class="mb-3 '.$zchampClasses.'"'.$style.'>';
			//le libellé du bouton retourné est toujours valueBase (valeur de base) car value peut être "notposted" si le bouton 
			//submit n'est pas cliqué (cas de plusieurs submits sur le même formulaire
			$html.= '<button type="'.$this->inputType().'"'.$enable.' id="'.$this->id().'" class="'.$buttonClasses.'"'.$this->javascript().' name="'.$this->postName().'" value="'.$this->valueBase().'"'.$labelHelp.' data-toggle="tooltip"/>'.$this->label().'</button>';
			if ($this->showErreur()) $html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
		$html.= '</div>';

		return $html; 
	}

}