<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'area'
// Version 3.11.3 du 17.01.2019
//==============================================================

class UniversalFieldArea extends UniversalField {

	private $_rows = 0;

	//--------------------------------------
	// Constructeur
	//--------------------------------------
	
	//surcharge du constructeur pour l'initialisation de cet objet
	//$donnees contient le paramétrage du champ / $idformulaire contien l'id unique du formulaire parent
	public function __construct(array $donnees, $idformulaire) {
		$this->setValue('');
		$this->setFieldType('area');
		$this->setIdParentForm($idformulaire);
		parent::__construct($donnees);
	}

	//--------------------------------------
	// Getters
	//--------------------------------------

	public function rows() {return $this->_rows;}

	//--------------------------------------
	// Setters
	//--------------------------------------

	public function setRows($rows) {$this->_rows = $rows;}
	protected function setPostName($postName) {
		parent::setPostName('mem'.ucfirst($postName));
	}
	
	//--------------------------------------
	// Autres methodes
	//--------------------------------------

	//surcharge de la méthode relever pour ne pas afficher le texte 'notposted' si le champ était disabled
	public function relever() {
		parent::relever();
		if ($this->value() === 'notposted') {
			$this->setValue('');
			$this->setEnable(false);
		}
	}

	private function _drawLabelInline() {
		//label inline
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		($this->lclass() != '') ? $lclass = ' '.$this->lclass() : $lclass = '';
		($this->labelHelpPos() != '') ? $labelHelp = ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp = '';
		($this->labelHelp() != '') ? $labelHelp.= ' title="'.$this->labelHelp().'"' : $labelHelp.= '';
		$style = '';
		$html = '<div id="'.$this->idztitre().'" class="text-'.$this->lalign().' '.$this->llong().$lclass.$erreur.$invisible.'"'.$style.'>';
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

	private function _drawChampInline($enable) {
		//champ inline
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		($this->maxlength() > 0) ? $maxlength = ' maxlength="'.$this->maxlength().'"' : $maxlength = '';
		($this->spellcheck() == false) ? $spellcheck = ' spellcheck="false"' : $spellcheck = '';
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';

		$html = '<div id="'.$this->idzchamp().'" class="mb-3 '.$this->clong().$invisible.'">';
			$html.= '<textarea'.$maxlength.$spellcheck.$enable.' class="form-control'.$erreur.$cclass.'" name="'.$this->postName().'" rows="'.$this->rows().'" id="'.$this->id().'" placeholder="'.$this->placeholder().'" '.$this->javascript().'>'.stripslashes($this->value()).'</textarea>';
			$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
		$html.= '</div>';
		return $html;
	}

	private function _drawLabelOnline() {
		//label online
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		($this->labelHelpPos() != '') ? $labelHelp = ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp = '';
		($this->labelHelp() != '') ? $labelHelp.= ' title="'.$this->labelHelp().'"' : $labelHelp.= '';
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

	private function _drawChampOnline($enable) {
		//champ online
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		($this->maxlength() > 0) ? $maxlength = ' maxlength="'.$this->maxlength().'"' : $maxlength = '';
		($this->spellcheck() == false) ? $spellcheck = ' spellcheck="false"' : $spellcheck = '';
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';

		$html = '<div id="'.$this->idzchamp().'">';
			$html.= '<textarea'.$maxlength.$spellcheck.$enable.' class="form-control'.$erreur.$cclass.'" name="'.$this->postName().'" rows="'.$this->rows().'" id="'.$this->id().'" placeholder="'.$this->placeholder().'" '.$this->javascript().'>'.stripslashes($this->value()).'</textarea>';
			$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
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
		$html = '';

		//mise en place des selecteurs disabled / readonly
		//disabled a toujours je dessus sur readonly
		if ($enabled) {
			$enable = '';
			if (!$this->enable()) $enable.= ' disabled';
			if ($this->readonly()) $enable.= ' readonly';
		}
		else {
			$enable = ' disabled';	//champ désactive
			if ($this->readonly()) $enable.= ' readonly';
		}

		//test erreurs éventuelles de construction
		$this->_testConstructionErreur();

		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';

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
			($this->clong() != '') ? $clong = ' '.$this->clong(): $clong = '';
			($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
			($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
			$classes = trim($clong.$cclass.$invisible);
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