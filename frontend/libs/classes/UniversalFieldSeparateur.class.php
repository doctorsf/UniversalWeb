<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'separateur'
// Version 3.17.0 du 13.12.2019
//==============================================================

class UniversalFieldSeparateur extends UniversalField {

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	//surcharge du constructeur pour l'initialisation de cet objet
	public function __construct(array $donnees, $idformulaire) {
		$this->setFieldType('separateur');
		$this->setIdParentForm($idformulaire);
		parent::__construct($donnees);
	}

	protected function setPostName($postName) {
		parent::setPostName('sep'.ucfirst($postName));
	}

	//Un séparateur est forcément 'inline'
	public function draw($enabled) {
		$html = '';

		//decalage
		if ($this->decalage() != '') {
			($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
			$html.= '<div class="'.$this->decalage().$invisible.'"></div>';		//sert de push à droite
		}

		//erreur ou bordure standard
		if ($this->border() === true) {
			$style = '';
			$border = ' border';
		}
		elseif ($this->border() != '') {
			$border = '';
			$style = ' style="'.$this->border().'"';
		}
		//aucune bordure
		else {
			$border = '';
			$style = '';
		}

		($this->invisible()) ? $invisible = ' invisible' : $invisible = '';
		$labelHelp = '';
		if ($this->labelHelp() != '') {
			$labelHelp = ' title="'.htmlspecialchars($this->labelHelp()).'" data-toggle="tooltip"';
			($this->labelHelpPos() != '') ? $labelHelp.= ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp.= ' data-placement="auto"';
		}
		($this->clong() != '') ? $clong = ' '.$this->clong() : $clong = '';
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		$classes = trim($clong.$border.$cclass.$invisible);

		$html.= '<div id="'.$this->idbchamp().'" class="mb-3 '.$classes.'"'.$style.'>';

			$html.= '<div id="'.$this->idztitre().'" class="'.$this->lclass().'">';
				//libellé du séparateur
				if ($labelHelp != '') {
					$html.= '<p class="py-0 form-control-plaintext">';
					$html.= '<span '.$labelHelp.'>'.$this->label().'</span>';
					$html.= '</p>';
				}
				else {
					$html.= '<p class="py-0 form-control-plaintext"><span>'.$this->label().'</span></p>';
				}
			$html.= '</div>';
			$html.= '<div id="'.$this->idzchamp().'">';
				//valeur d'envoi dans le formulaire
				$html.= '<input type="hidden" name="'.$this->postName().'" value="'.$this->value().'">';
			$html.= '</div>';
		$html.= '</div>';

		return $html; 
	}
}