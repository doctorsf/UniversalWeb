<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'text'
// Version 3.12.0 du 17.04.2019
//==============================================================

class UniversalFieldText extends UniversalField {

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	//surcharge du constructeur pour l'initialisation de cet objet
	//avec une valeur vide (et non 0 qui est l'initialisation par défaut de l'objet parent UniversalField
	public function __construct(array $donnees, $idformulaire) {
		$this->setValue('');
		$this->setFieldType('text');
		$this->setIdParentForm($idformulaire);
		$this->setLpos('before');				//par défaut, pour un UniversalFieldText, les labels se trouve devant le champ (car objet initialisé 'after')
		parent::__construct($donnees);
	}

	//--------------------------------------
	// Autres methodes
	//--------------------------------------

	/**
	// exemple de surcharge de la méthode que l'on pourrait faire sur les objets 'text'
	public function test() {
		parent::test();		//appel de la methode parente
		if ($this->value() == '52') {
			$this->setLiberreur('Cette valeur est interdite');
			$this->setErreur(true);
		}
	}
	**/

	public function setPostName($postName) {
		parent::setPostName('txt'.ucfirst($postName));
	}

	//surcharge de la méthode relever pour ne pas afficher le texte 'notposted' si le champ était disabled
	//on commence par "relever" les données postées
	//Cas particulier : dans le cas d'un input de type 'file', il n'est pas renvoyé de POST (donc la valeur est 'notposted')
	//mais à la place la donnée se trouve dans le tableau $_FILES
	public function relever() {
		parent::relever();
		if ($this->inputType() == 'file') {
			//les input type 'file' ne renvoient pas de $_POST mais des informations dans $_FILES
			//on force donc la valeur du champ avec le contenu du $_FILES
			$this->setValue($_FILES[$this->postName()]);
		}
		else {	
			if ($this->value() === 'notposted') {
				$this->setValue('');
				$this->setEnable(false);
			}
		}
	}

	private function _drawLabelInline() {
		//label inline
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		($this->lclass() != '') ? $lclass = ' '.$this->lclass() : $lclass = '';
		($this->labelHelpPos() != '') ? $labelHelp = ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp = ' data-placement="auto"';
		($this->labelHelp() != '') ? $labelHelp.= ' title="'.htmlspecialchars($this->labelHelp()).'"' : $labelHelp.= '';
		$style = '';
		if ($this->label() == '') {
			$html = '<div id="'.$this->idztitre().'"></div>';
		}
		else {
			$html = '<div id="'.$this->idztitre().'" class="text-'.$this->lalign().' '.$this->llong().$lclass.$invisible.'"'.$style.'>';
				if ($labelHelp == '') {
					//il n'y a pas d'aide sur le label
					$html.= '<label class="mb-0'.$erreur.'" for="'.$this->id().'">'.$this->label().'</label>';
				}
				else {
					//on ajoute une aide sur le label
					$html.= '<label class="mb-0 '.$erreur.'" for="'.$this->id().'">';
						$html.= '<span data-toggle="tooltip"'.$labelHelp.'>'.$this->label().'</span>';
					$html.= '</label>';
				}
			$html.= '</div>';
		}
		return $html;
	}

	private function _drawChampInline($enable) {
		//champ inline
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		($this->maxlength() > 0) ? $maxlength = ' maxlength="'.$this->maxlength().'"' : $maxlength = '';
		($this->spellcheck() == false) ? $spellcheck = ' spellcheck="false"' : $spellcheck = '';
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';
		($this->javascript() != '') ? $javascript = ' '.$this->javascript() : $javascript = '';
		
		$html = '<div id="'.$this->idzchamp().'" class="mb-3 '.$this->clong().$invisible.'">';
			if ($this->inputType() == 'file') {
				$html.= '<input'.$enable.' type="file" class="form-control-file'.$erreur.$cclass.'" name="'.$this->postName().'" id="'.$this->id().'"'.$javascript.'/>';
			}
			else {
				$value = 'value="'.htmlspecialchars(stripslashes($this->value()), ENT_COMPAT, 'UTF-8').'"';
				$html.= '<input'.$maxlength.$spellcheck.$enable.' type="'.$this->inputType().'" class="form-control'.$erreur.$cclass.'" name="'.$this->postName().'" id="'.$this->id().'" placeholder="'.$this->placeholder().'" '.$value.$javascript.'/>';
			}
			$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
		$html.= '</div>';
		return $html;
	}

	private function _drawLabelOnline() {
		//label online
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		($this->labelHelpPos() != '') ? $labelHelp = ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp = ' data-placement="auto"';
		($this->labelHelp() != '') ? $labelHelp.= ' title="'.htmlspecialchars($this->labelHelp()).'"' : $labelHelp.= '';
		($this->labelPlusHelpPos() != '') ? $labelPlusHelp = ' data-placement="'.$this->labelPlusHelpPos().'"' : $labelPlusHelp = ' data-placement="auto"';
		($this->labelPlusHelp() != '') ? $labelPlusHelp.= ' title="'.htmlspecialchars($this->labelPlusHelp()).'"' : $labelPlusHelp.= '';
		$style = '';
		if ($this->label() == '') {
			$html = '<div id="'.$this->idztitre().'"></div>';
		}
		else {
			if (!empty($this->labelPlus())) {
				$html = '<div id="'.$this->idztitre().'" class="d-flex justify-content-between '.$this->lclass().'"'.$style.'>';
					if ($labelHelp == '') {
						//il n'y a pas d'aide sur le label
						$html.= '<div>';
						$html.= '<label class="mb-0'.$erreur.'" for="'.$this->id().'">'.$this->label().'</label>';
						$html.= '</div>';
					}
					else {
						$html.= '<div>';
						$html.= '<label class="mb-0'.$erreur.'" for="'.$this->id().'">';
							$html.= '<span data-toggle="tooltip"'.$labelHelp.'>'.$this->label().'</span>';
						$html.= '</label>';
						$html.= '</div>';
					}
					if ($labelPlusHelp == '') {
						//il n'y a pas d'aide sur le labelPlus
						$html.= '<div>';
						$html.= $this->labelPlus();
						$html.= '</div>';
					}
					else {
						//on ajoute une aide sur le label Plus
						$html.= '<div>';
						$html.= '<span data-toggle="tooltip"'.$labelPlusHelp.'>'.$this->labelPlus().'</span>';
						$html.= '</div>';
					}
				$html.= '</div>';
			}
			else {
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
			}
		}
		return $html;
	}

	private function _drawChampOnline($enable) {
		//champ online
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		($this->maxlength() > 0) ? $maxlength = ' maxlength="'.$this->maxlength().'"' : $maxlength = '';
		($this->spellcheck() == false) ? $spellcheck = ' spellcheck="false"' : $spellcheck = '';
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';
		($this->javascript() != '') ? $javascript = ' '.$this->javascript() : $javascript = '';

		$html = '<div id="'.$this->idzchamp().'">';
			if ($this->inputType() == 'file') {
				$html.= '<input'.$enable.' type="file" class="form-control-file'.$erreur.$cclass.'" name="'.$this->postName().'" id="'.$this->id().'"'.$javascript.'/>';
			} 
			else {
				$value = 'value="'.htmlspecialchars(stripslashes($this->value()), ENT_COMPAT, 'UTF-8').'"';
				$html.= '<input'.$maxlength.$spellcheck.$enable.' type="'.$this->inputType().'" class="form-control'.$erreur.$cclass.'" name="'.$this->postName().'" id="'.$this->id().'" placeholder="'.$this->placeholder().'" '.$value.$javascript.'/>';
			}
			$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
		$html.= '</div>';
		return $html;
	}

	//fonction privée qui test les erreurs éventuelles de construction du radio
	private function _testConstructionErreur() {
		//test si le paramatre 'lpos' est valide
		if (!in_array($this->lpos(), array('before', 'after'))) {
			$this->setErreur(true);
			$this->setLiberreur('Paramètre \'lpos\' => \''.$this->lpos().'\' inaproprié pour le champ texte \''.$this->idField().'\'');
			$this->setLiberreurHelp('Paramètre \'lpos\' obligatoire. Choisir parmi \'before\' ou \'after\'');
			$this->setLpos('before');
		}
		//test si le paramètre 'complement' a bien été défini (car obligatoire) dans le cas d'un champ de type 'file'
		elseif (($this->inputType() == 'file') && (empty($this->complement()))) {
			$this->setErreur(true);
			$this->setLibErreur('Le paramètre \'complement\' n\'a pas été défini pour le le champ \'file\' \''.$this->idField().'\'');
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
			($this->clong() != '') ? $clong = ' '.$this->clong() : $clong = '';
			($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
			$classes = $clong.$invisible;
			//dessin
			$html.= '<div id="'.$this->idbchamp().'" class="mb-3'.$classes.'">';
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