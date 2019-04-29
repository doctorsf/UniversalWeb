<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'filtretexte (combiné txt + menu)
// Version 3.12.0 du 17.04.2019
//==============================================================

class UniversalFieldFiltretext extends UniversalField {

	private $_addon = false;						//addon au filtre sous forme de liste déroulante -> si true, value renvoyée est un tableau 
	private $_aclass = '';							//classe CSS personnalisée du menu déroulant addon
	private $_apos = 'before';						//position du addon para rapport au label (before (au-dessus = defaut) / after (au-dessous))

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	//surcharge du constructeur pour l'initialisation de cet objet
	//avec une valeur vide (et non 0 qui est l'initialisation par défaut de l'objet parent UniversalField
	public function __construct(array $donnees, $idformulaire) {
		$this->setValue('');
		$this->setFieldType('filtretext');
		$this->setIdParentForm($idformulaire);
		parent::__construct($donnees);
	}

	//--------------------------------------
	// Getters
	//--------------------------------------
	public function addon()		{return $this->_addon;}	
	public function aclass()	{return $this->_aclass;}	
	public function apos()		{return $this->_apos;}	

	//--------------------------------------
	// Setters
	//--------------------------------------
	public function setAddon($valeur) {
		$this->_addon = $valeur;
		//on avertit l'objet que la variable POST sera un tableau
		if ($valeur == true) {
			//on avertit l'objet que la variable POST sera un tableau
			$this->setPostIsTab(true);
		}
	}
	public function setAclass($valeur) {$this->_aclass = $valeur;}
	public function setApos($valeur) {$this->_apos = $valeur;}
	public function setPostName($postName) {
		parent::setPostName('flt'.ucfirst($postName));
	}

	//--------------------------------------
	// Methodes
	//--------------------------------------

	//surcharge de la méthode relever pour ne pas afficher le texte 'notposted' si le champ était disabled
	//on commence par "relever" les données postées
	public function relever() {
		parent::relever();
		if ($this->value() === 'notposted') {
			$this->setValue('');
			$this->setEnable(false);
		}
	}

	//affichage de l'addon. $position donne la position de l'addon dans le flux (addon, saisie, bouton), ce qui permet de gérer les arrondis CSS
	private function _drawFakeAddon($enable, $position) {
		$arrondi = '';
		if ($position == 'first') {
			$arrondi = ' rounded-0';
			$style = 'border-top-right-radius:.25rem!important; border-top-left-radius:.25rem!important;';
		}
		elseif ($position == 'last') {
			$arrondi = ' rounded-0';
			$style = 'left:1px;border-bottom-right-radius:.25rem!important; border-bottom-left-radius:.25rem!important;margin-top:-3px';
		}
		($this->lclass() != '') ? $lclass = ' '.$this->lclass() : $lclass = '';
		($this->erreur() == true) ? $erreur = ' btn-danger' : $erreur = '';
		$classes = 'w-100 py-0 text-'.$this->lalign().$lclass.$erreur.$arrondi;
		$idLabel = 'idLabel'.$this->postName();
		$html = '<span class="input-group-btn" style="position:static">';
			$html.= '<button'.$enable.' id="'.$idLabel.'" type="button" class="'.$classes.'" style="'.$style.'">';
				$html.= $this->label();
			$html.= '</button>';
		$html.= '</span>';
		return $html;
	}

	//affichage de l'addon. $position donne la position de l'addon dans le flux (addon, saisie, bouton), ce qui permet de gérer les arrondis CSS
	private function _drawAddon($enable, $position) {
		$arrondi = '';
		if ($position == 'first') {
			$arrondi = ' rounded-0';
			$style = 'border-top-left-radius:.25rem!important; border-top-right-radius:.25rem!important;';
		}
		elseif ($position == 'last') {
			$arrondi = ' rounded-0';
			$style = 'border-bottom-left-radius:.25rem!important; border-bottom-right-radius:.25rem!important;margin-top:-3px';
		}
		($this->postIsTab()) ? $postNameTableau = $this->postName().'[0]' : $postNameTableau = $this->postName();
		($this->aclass() != '') ? $aclass = ' '.$this->aclass() : $aclass = '';
		($this->erreur() == true) ? $erreur = ' btn-danger' : $erreur = '';
		$classes = 'w-100 py-0 text-'.$this->lalign().$aclass.$erreur.$arrondi;
		$idAddon = 'idAddon'.$this->postName();
		$idLabel = 'idLabel'.$this->postName();
		if ($this->readonly()) {
			$html = '<div id="'.$this->idztitre().'" class="input-group-btn" style="position:static">';
				$html.= '<input type="hidden" name="'.$postNameTableau.'" value="'.$this->value()[0].'">';
				$html.= '<button'.$enable.' id="'.$idLabel.'" type="button" class="'.$classes.'" style="'.$style.'">';
					$html.= $this->complement()[$this->value()[0]].'<small><sup>1</sup></small>';
				$html.= '</button>';
			$html.= '</div>';
		}
		else {
			$html = '<div id="'.$this->idztitre().'" class="input-group-btn" style="position:static">';
				$html.= '<input type="hidden" id="'.$idAddon.'" name="'.$postNameTableau.'" value="'.$this->value()[0].'">';		//valeur du addon (bouton addon)
				$html.= '<button'.$enable.' id="'.$idLabel.'" type="button" class="dropdown-toggle '.$classes.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="'.$style.'">';
					$html.= $this->complement()[$this->value()[0]];
				$html.= '</button>';
				$html.= '<div class="dropdown-menu" aria-labelledby="'.$idLabel.'">';
					foreach($this->complement() as $valeur => $label) {
						if (($label == 'separateur') || ($label == 'separator')) {
							$html.= '<div role="separator" class="dropdown-divider"></div>';
						}
						else {
							$html.= '<button onclick="document.getElementById(\''.$idLabel.'\').innerHTML = \''.$label.'\';document.getElementById(\''.$idAddon.'\').value = \''.$valeur.'\';" class="dropdown-item" type="button">'.$label.'</button>';
						}
					}
				$html.= '</div>';
			$html.= '</div>';
		}
		return $html;
	}

	//affichage du champ de saisie. $position donne la position de l'addon dans le flux (addon, saisie, bouton), ce qui permet de gérer les arrondis CSS
	private function _drawInput($enable, $position) {
		//champ online
		$html = '';
		$arrondi = '';
		if ($position == 'first') {
			$arrondi = ' rounded-0';
			$style = 'border-top-right-radius: .25rem!important; border-top-left-radius: .25rem!important;';
		}
		elseif ($position == 'last') {
			$arrondi = ' rounded-0';
			$style = 'border-bottom-right-radius: .25rem!important; border-bottom-left-radius: .25rem!important;';
		}
		($this->postIsTab()) ? $postNameTableau = $this->postName().'[1]' : $postNameTableau = $this->postName();
		if ($this->inputType() == '') $this->setInputType('search');
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		($this->maxlength() > 0) ? $maxlength = ' maxlength="'.$this->maxlength().'"' : $maxlength = '';
		($this->spellcheck() == false) ? $spellcheck = ' spellcheck="false"' : $spellcheck = '';
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		$classes = 'form-control'.$cclass.$erreur.$arrondi;
		if (is_array($this->value())) {
			$value = 'value="'.htmlspecialchars(stripslashes($this->value()[1]), ENT_COMPAT, 'UTF-8').'"';
		}
		else {
			$value = 'value="'.htmlspecialchars(stripslashes($this->value()), ENT_COMPAT, 'UTF-8').'"';
		}
		$html = '<div id="'.$this->idzchamp().'">';
			$html.= '<input'.$maxlength.$spellcheck.$enable.' type="'.$this->inputType().'" class="'.$classes.'" name="'.$postNameTableau.'" id="'.$this->id().'"'.$this->javascript().' placeholder="'.$this->placeholder().'" '.$value.' style="'.$style.'"/>';
		$html.= '</div>';
		return $html;
	}

	//fonction privée qui test les erreurs éventuelles de construction du radio
	private function _testConstructionErreur() {
		//si addon, vérification que apos soit 'before' ou 'after' seulement
		if (!in_array($this->apos(), array('before', 'after'))) {
			$this->setErreur(true);
			$this->setLiberreur('Paramètre \'apos\' => \''.$this->apos().'\' inaproprié pour le addon du champ search \''.$this->idField().'\'');
			$this->setLiberreurHelp('Paramètre \'apos\' obligatoire. Choisir parmi \'before\' ou \'after\'');
			$this->setApos('before');
		}
		//si addon, vérification que apos soit 'before' ou 'after' seulement
		if ($this->addon() == true) {
			if (!is_array($this->value())) {
				$this->setErreur(true);
				$this->setLiberreur('Paramètre \'value\' inaproprié pour le addon du champ \''.$this->idField().'\'');
				$this->setLiberreurHelp('\'value\' doit être un tableau recevant 1 couple valeur => libellé');
			}
			if (!is_array($this->complement())) {
				$this->setErreur(true);
				$this->setLiberreur('Paramètre \'complement\' inaproprié pour le addon du champ \''.$this->idField().'\'');
				$this->setLiberreurHelp('\'complement\' doit être un tableau de valeurs => libellé');
			}
		}
		else {
			//pas d'addon. value doit obligatoirement etre une valeur et pas un tableau
			if (is_array($this->value())) {
				$this->setErreur(true);
				$this->setLiberreur('Paramètre \'value\' inaproprié pour le champ \''.$this->idField().'\'');
				$this->setLiberreurHelp('\'value\' ne doit pas être un tableau car addon est à false');
			}
			if (is_array($this->complement())) {
				$this->setErreur(true);
				$this->setLiberreur('Paramètre \'complement\' inaproprié pour le champ \''.$this->idField().'\'');
				$this->setLiberreurHelp('\'complement\' ne doit pas être un tableau car addon est à false');
			}
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

		//decalage
		if ($this->decalage() != '') {
			($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
			$html.= '<div class="'.$this->decalage().$invisible.'"></div>';		//sert de push à droite
		}

		//legende (titre)
		if ($this->titre() != '') {
			($this->tlong() != '') ? $tlong = ' '.$this->tlong() : $tlong = '';
			($this->tclass() != '') ? $tclass = ' '.$this->tclass() : $tclass = '';
			($this->titreHelpPos() != '') ? $titreHelp = ' data-placement="'.$this->titreHelpPos().'"' : $titreHelp = ' data-placement="auto"';
			($this->titreHelp() != '') ? $titreHelp.= ' title="'.$this->titreHelp().'"' : $titreHelp.= '';
			($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
			$classeTitre = 'text-'.$this->talign().' col-form-label'.$tlong.$tclass.$invisible;
			$html.= '<legend class="'.$classeTitre.'">';
			if ($titreHelp != '') {
				$html.= '<span data-toggle="tooltip"'.$titreHelp.'>'.$this->titre().'</span>';
			}
			$html.= '</legend>';
		}

		//design 'online'(toujours pour cet objet)
		//prise en compte de certaines données
		($this->labelHelpPos() != '') ? $labelHelp = ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp = ' data-placement="auto"';
		($this->labelHelp() != '') ? $labelHelp.= ' title="'.htmlspecialchars($this->labelHelp()).'"' : $labelHelp.= '';
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';
		($this->clong() != '') ? $clong = ' '.$this->clong() : $clong = '';
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		$classes = trim($clong.$invisible);
		//dessin
		$html.= '<div id="'.$this->idbchamp().'" class="mb-3 '.$classes.'"'.$labelHelp.' data-toggle="tooltip">';
			if ($this->addon()) {
				if ($this->apos() == 'before') {
					$html.= $this->_drawAddon($enable, 'first');
					$html.= $this->_drawInput($enable, 'last');
				}
				else {
					$html.= $this->_drawInput($enable, 'first');
					$html.= $this->_drawAddon($enable, 'last');
				}
			}
			else {
				if ($this->apos() == 'before') {
					$html.= $this->_drawFakeAddon($enable, 'first');
					$html.= $this->_drawInput($enable, 'last');
				}
				else {
					$html.= $this->_drawInput($enable, 'first');
					$html.= $this->_drawFakeAddon($enable, 'last');
				}
			}
			$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
		$html.= '</div>';

		return $html; 
	}

}