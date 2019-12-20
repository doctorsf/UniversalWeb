<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'image'
// Version 3.17.0 du 13.12.2019
//==============================================================

class UniversalFieldImage extends UniversalField {

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	//surcharge du constructeur pour l'initialisation de cet objet
	public function __construct(array $donnees, $idformulaire) {
		$this->setValue('');
		$this->setFieldType('image');
		$this->setIdParentForm($idformulaire);
		parent::__construct($donnees);
	}

	public function setPostName($postName) {
		parent::setPostName('img'.ucfirst($postName));
	}

	//dessin label inline
	private function _drawLabelInline() {
		//label inline
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		($this->lclass() != '') ? $lclass = ' '.$this->lclass() : $lclass = '';
		$labelHelp = '';
		if ($this->labelHelp() != '') {
			$labelHelp = ' title="'.htmlspecialchars($this->labelHelp()).'"';
			($this->labelHelpPos() != '') ? $labelHelp.= ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp.= ' data-placement="auto"';
		}
		$style = '';
		$html = '<div id="'.$this->idztitre().'" class="text-'.$this->lalign().' '.$this->llong().$lclass.$invisible.'"'.$style.'>';
			if ($labelHelp == '') {
				//il n'y a pas d'aide sur le label
				$html.= '<label class="mb-0'.$erreur.'" for="'.$this->id().'">'.$this->label().'</label>';
			}
			else {
				//on ajoute une aide sur le label
				$html.= '<label class="mb-0'.$erreur.'" for="'.$this->id().'">';
					$html.= '<span data-toggle="tooltip"'.$labelHelp.'>'.$this->label().'</span>';
				$html.= '</label>';
			}
		$html.= '</div>';
		return $html;
	}

	//dessin champ inline
	private function _drawChampInline($enable) {
		//erreur ou bordure standard
		if (($this->erreur() == true) || ($this->border() === true)) {
			$border = '';			//on efface car form-control possede déjà une bordure
		}
		//bordure personnalisée (on affiche la bordure personnalisée avant d'avoir effacée la bordure de form-control)
		elseif ($this->border() != '') {
			$border = 'border:none;'.$this->border();
		}
		//aucune bordure (on efface celle de form-control)
		else {
			$border = 'border:none;padding:0;';
		}
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		$html = '<div id="'.$this->idzchamp().'" class="mb-3 '.$this->clong().$invisible.'">';
			$html.= '<img class="form-control h-auto img-fluid'.$cclass.$erreur.'" id="'.$this->id().'" src="'.$this->value().'" style="'.$border.'" '.$this->javascript().'/>';
			$html.= '<input type="hidden" name="'.$this->postName().'" value="'.$this->value().'" />';
			if ($this->erreur()) $html.= '<p class="form_error" title="'.$this->libErreurHelp().'">'.$this->libErreur().'</p>';
		$html.= '</div>';
		return $html;
	}

	//dessin label online
	private function _drawLabelOnline() {
		//label online
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		$labelHelp = '';
		if ($this->labelHelp() != '') {
			$labelHelp = ' title="'.htmlspecialchars($this->labelHelp()).'"';
			($this->labelHelpPos() != '') ? $labelHelp.= ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp.= ' data-placement="auto"';
		}
		$style = '';
		$html = '<div id="'.$this->idztitre().'" class="text-'.$this->lalign().' '.$this->lclass().'"'.$style.'>';
			if ($labelHelp == '') {
				//il n'y a pas d'aide sur le label
				$html.= '<label class="mb-0'.$erreur.'" for="'.$this->id().'">'.$this->label().'</label>';
			}
			else {
				//on ajoute une aide sur le label
				$html.= '<label class="mb-0'.$erreur.'" for="'.$this->id().'">';
					$html.= '<span data-toggle="tooltip"'.$labelHelp.'>'.$this->label().'</span>';
				$html.= '</label>';
			}
		$html.= '</div>';
		return $html;
	}

	//dessin champ online
	private function _drawChampOnline($enable) {
		//erreur ou bordure standard
		if (($this->erreur() == true) || ($this->border() === true)) {
			$border = '';			//on efface car form-control possede déjà une bordure
		}
		//bordure personnalisée (on affiche la bordure personnalisée avant d'avoir effacée la bordure de form-control)
		elseif ($this->border() != '') {
			$border = 'border:none;'.$this->border();
		}
		//aucune bordure (on efface celle de form-control)
		else {
			$border = 'border:none;padding:0;';
		}
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		$html = '<div id="'.$this->idzchamp().'">';
			$html.= '<img class="form-control h-auto img-fluid'.$cclass.$erreur.'" id="'.$this->id().'" src="'.$this->value().'" style="'.$border.'" '.$this->javascript().'/>';
			$html.= '<input type="hidden" name="'.$this->postName().'" value="'.$this->value().'" />';
			if ($this->erreur()) $html.= '<p class="form_error" title="'.$this->libErreurHelp().'">'.$this->libErreur().'</p>';
		$html.= '</div>';
		return $html;
	}

	//fonction privée qui test les erreurs éventuelles de construction du radio
	private function _testConstructionErreur() {
		//test si le paramatre 'lpos' est valide
		if (!in_array($this->lpos(), array('before', 'after'))) {
			$this->setErreur(true);
			$this->setLiberreur('Paramètre \'lpos\' => \''.$this->lpos().'\' inaproprié pour le champ area \''.$this->idField().'\'');
			$this->setLiberreurHelp('Paramètre \'lpos\' obligatoire. Choisir parmi \'before\' ou \'after\'');
			$this->setLpos('before');
		}
		return $this->erreur();
	}

	//--------------------------------------------
	// Draw
	//--------------------------------------------
	public function draw($enabled) {
		$enable = true;
		$html = '';

		//recupération des éventuelles erreurs de construction
		$this->_testConstructionErreur();

		//decalage
		if ($this->decalage() != '') {
			($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
			$html.= '<div class="'.$this->decalage().$invisible.'">';		//sert de push à droite
			$html.= '</div>';
		}

		//design 'inline' (label à la suite (ou après) du champ)
		if ($this->design() == 'inline') {
			//dessin
			if ($this->lpos() == 'before') {
				$html.= $this->_drawLabelInline();
				$html.= $this->_drawChampInline($enable);
			}
			elseif ($this->lpos() == 'after') {
				$html.= $this->_drawChampInline($enable);
				$html.= $this->_drawLabelInline();
			}
		}

		//design 'online' (label au-dessus (ou après) du champ)
		if ($this->design() == 'online') {
			//prise en compte de certaines données
			($this->clong() != '') ? $clong = ' '.$this->clong() : $clong = '';
			($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
			$classes = trim($clong.$invisible);
			//dessin
			$html.= '<div id="'.$this->idbchamp().'" class="mb-3 '.$classes.'">';
			if ($this->lpos() == 'before') {
				$html.= $this->_drawLabelOnline();
				$html.= $this->_drawChampOnline($enable);
			}
			elseif ($this->lpos() == 'after') {
				$html.= $this->_drawChampOnline($enable);
				$html.= $this->_drawLabelOnline();
			}
			$html.= '</div>';
		}

		return $html; 
	}

}