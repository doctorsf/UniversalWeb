<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'search' (combiné txt + bouton)
// Version 3.22.0 du 05.05.2020
//==============================================================

class UniversalFieldSearch extends UniversalField {

	private $_addon = false;						//addon au champ de recherche sous forme de liste déroulante -> si true, value renvoyée est un tableau 
	private $_aclass = '';							//classe CSS personnalisée du menu déroulant addon
	private $_apos = 'before';						//position du addon para rapport au label (before (defaut) / after)

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	//surcharge du constructeur pour l'initialisation de cet objet
	//avec une valeur vide (et non 0 qui est l'initialisation par défaut de l'objet parent UniversalField
	public function __construct(array $donnees, $idformulaire) {
		$this->setValue('');
		$this->setFieldType('search');
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
		parent::setPostName('src'.ucfirst($postName));
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
	private function _drawAddon($enable, $position) {
		$arrondi = '';
		if ($position == 'first') {
			$arrondi = ' rounded-0';
			$style = 'border-top-left-radius:.25rem!important; border-bottom-left-radius:.25rem!important;';
		}
		elseif ($position == 'middle') {
			$arrondi = ' rounded-0';
			$style = '';
		}
		elseif ($position == 'last') {
			$arrondi = ' rounded-0';
			$style = 'border-top-right-radius:.25rem!important;border-bottom-right-radius:.25rem!important;';
		}
		($this->postIsTab()) ? $postNameTableau = $this->postName().'[0]' : $postNameTableau = $this->postName();
		($this->aclass() != '') ? $aclass = ' '.$this->aclass() : $aclass = '';
		($this->erreur() == true) ? $erreur = ' btn-danger' : $erreur = '';
		$idAddon = 'idAddon'.$this->postName();
		$idLabel = 'idLabel'.$this->postName();
		if ($this->readonly()) {
			$html = '<span class="input-group-btn">';
				$html.= '<input type="hidden" name="'.$postNameTableau.'" value="'.$this->value()[0].'">';
				$html.= '<button'.$enable.' id="'.$idLabel.'" type="button" class="'.$aclass.$arrondi.$erreur.'">';
					$html.= $this->complement()[$this->value()[0]];
				$html.= '</button>';
			$html.= '</span>';
		}
		else {
			$html = '<span class="input-group-btn" style="position:static">';
				$html.= '<input type="hidden" id="'.$idAddon.'" name="'.$postNameTableau.'" value="'.$this->value()[0].'">';		//valeur du addon (bouton addon)
				$html.= '<button'.$enable.' id="'.$idLabel.'" type="button" class="dropdown-toggle'.$aclass.$arrondi.$erreur.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="'.$style.'">';
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
			$html.= '</span>';
		}
		return $html;
	}

	//affichage du champ de saisie. $position donne la position de l'addon dans le flux (addon, saisie, bouton), ce qui permet de gérer les arrondis CSS
	private function _drawInput($enable, $position) {
		//champ online
		$html = '';
		($this->postIsTab()) ? $postNameTableau = $this->postName().'[1]' : $postNameTableau = $this->postName();
		if ($this->inputType() == '') $this->setInputType('search');
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		($this->maxlength() > 0) ? $maxlength = ' maxlength="'.$this->maxlength().'"' : $maxlength = '';
		($this->spellcheck() == false) ? $spellcheck = ' spellcheck="false"' : $spellcheck = '';
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		$form_control = 'form-control';
		if ($this->postIsTab()) {
			$value = 'value="'.htmlspecialchars(stripslashes($this->value()[1]), ENT_COMPAT, 'UTF-8').'"';
		}
		else {
			$value = 'value="'.htmlspecialchars(stripslashes($this->value()), ENT_COMPAT, 'UTF-8').'"';
		}
		$html.= '<input id="'.$this->idzchamp().'"'.$maxlength.$spellcheck.$enable.' type="'.$this->inputType().'" class="'.$form_control.$erreur.$cclass.'" name="'.$postNameTableau.'" placeholder="'.$this->placeholder().'" '.$value.'/>';
		return $html;
	}

	//affichage du bouton. $position donne la position de l'addon dans le flux (addon, saisie, bouton), ce qui permet de gérer les arrondis CSS
	private function _drawBouton($enable, $position) {
		//label online
		if ($this->label() == '<small><sup>1</sup></small>') $this->setLabel('<span class="fas fa-search"></span><small><sup>1</sup></small>');
		if ($this->label() == '') $this->setLabel('<span class="fas fa-search"></span>');
		($this->lclass() != '') ? $lclass = ' '.$this->lclass() : $lclass = '';
		($this->erreur() == true) ? $erreur = ' btn-danger' : $erreur = '';
		$buttonClasses = trim($lclass.$erreur);
		if ($this->lpos() == 'before') {
			$arrondi = ' rounded-0';
			$style = 'border-top-right-radius:.25rem!important;border-bottom-right-radius:.25rem!important;';
		}
		elseif ($this->lpos() == 'after') {
			$arrondi = ' rounded-0';
			$style = 'border-top-left-radius:.25rem!important; border-bottom-left-radius:.25rem!important;';
		}
		$html = '<div id="'.$this->idztitre().'" class="input-group-btn">';
			$html.= '<button type="submit"'.$enable.' class="h-100 '.$buttonClasses.$arrondi.'"'.$this->javascript().' style="'.$style.'" />'.$this->label().'</button>';
		$html.= '</div>';
		return $html;
	}

	//fonction privée qui test les erreurs éventuelles de construction du radio
	private function _testConstructionErreur() {
		//test si le paramatre 'lpos' est valide
		if (!in_array($this->lpos(), array('before', 'after'))) {
			$this->setErreur(true);
			$this->setLiberreur('Paramètre \'lpos\' => \''.$this->lpos().'\' inaproprié pour le champ search \''.$this->idField().'\'');
			$this->setLiberreurHelp('Paramètre \'lpos\' obligatoire. Choisir parmi \'before\' ou \'after\'');
			$this->setLpos('before');
		}
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
				$this->setLiberreur('Paramètre \'value\' inaproprié pour le addon du champ search \''.$this->idField().'\'');
				$this->setLiberreurHelp('\'value\' doit être un tableau recevant 1 couple valeur => libellé');
			}
			if ($this->complement() == '') {
				$this->setErreur(true);
				$this->setLiberreur('Paramètre \'complement\' inaproprié pour le addon du champ search \''.$this->idField().'\'');
				$this->setLiberreurHelp('\'complement\' doit être un tableau de valeurs => libellé');
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
			$titreHelp = '';
			if ($this->titreHelp() != '') {
				$titreHelp = ' data-toggle="tooltip" title="'.htmlspecialchars($this->titreHelp()).'"';
				($this->titreHelpPos() != '') ? $titreHelp.= ' data-placement="'.$this->titreHelpPos().'"' : $titreHelp.= ' data-placement="auto"';
				($this->titreHelpHtml() == true) ? $titreHelp.= ' data-html="true"' : $titreHelp.= '';
			}
			($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
			$classeTitre = 'text-'.$this->talign().' col-form-label'.$tlong.$tclass.$invisible;
			$html.= '<legend class="'.$classeTitre.'">';
				$html.= '<span'.$titreHelp.'>'.$this->titre().'</span>';
			$html.= '</legend>';
		}

		//design 'inline'(toujours pour cet objet)
		//prise en compte de certaines données
		$labelHelp = '';
		if ($this->labelHelp() != '') {
			$labelHelp = ' data-toggle="tooltip" title="'.htmlspecialchars($this->labelHelp()).'"';
			($this->labelHelpPos() != '') ? $labelHelp.= ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp.= ' data-placement="auto"';
			($this->labelHelpHtml() == true) ? $labelHelp.= ' data-html="true"' : $labelHelp.= '';
		}
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';
		($this->clong() != '') ? $clong = ' '.$this->clong() : $clong = '';
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		$classes = trim($clong.$invisible);
		//dessin
		$html.= '<div id="'.$this->idbchamp().'" class="mb-3 '.$classes.'"'.$labelHelp.'>';
			$html.= '<span class="input-group">';			
			if ($this->lpos() == 'before') {
				if ($this->addon()) {
					if ($this->apos() == 'before') {
						$html.= $this->_drawAddon($enable, 'first');
						$html.= $this->_drawInput($enable, 'middle');
						$html.= $this->_drawBouton($enable, 'last');
					}
					else {
						$html.= $this->_drawInput($enable, 'first');
						$html.= $this->_drawAddon($enable, 'middle');
						$html.= $this->_drawBouton($enable, 'last');
					}
				}
				else {
					$html.= $this->_drawInput($enable, 'first');
					$html.= $this->_drawBouton($enable, 'last');
				}
			}
			elseif ($this->lpos() == 'after') {
				if ($this->addon()) {
					if ($this->apos() == 'before') {
						$html.= $this->_drawBouton($enable, 'first');
						$html.= $this->_drawAddon($enable, 'middle');
						$html.= $this->_drawInput($enable, 'last');
					}
					else {
						$html.= $this->_drawBouton($enable, 'first');
						$html.= $this->_drawInput($enable, 'middle');
						$html.= $this->_drawAddon($enable, 'last');
					}
				}
				else {
					$html.= $this->_drawBouton($enable, 'first');
					$html.= $this->_drawInput($enable, 'last');
				}
			}
			$html.= '</span>';
			$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
		$html.= '</div>';

		return $html; 
	}

}