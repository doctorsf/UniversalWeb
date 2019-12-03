<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'text'
// Version 3.16.0 du 26.11.2019
//==============================================================

class UniversalFieldText extends UniversalField {

	private $_multiple = false;				//selection multiple possible (dans le cas d'une selection $_FILES)(attention si true, renvoie un tableau dans le POST)
	private $_accept = '';					//liste des extentions acceptées à l'oouverture de la boite de selection de fichiers pour les "file"
	private $_min = '';						//valeur minimale de la saisie possible
	private $_max = '';						//valeur maximale de la saisie possible
	private $_step = '';					//step d'incrément (='any' : permet les décimales pour type=number)
	private $_pattern = '';					//attribut pattern contenant une expression régulière à laquelle le champ input doit valider avant soumission du formulaire.
	private $_autocomplete = 'on';			//autocompletion de la zone de saisie
	private $_autofocus = false;			//si true, le champ a le focus pour la saisie à l'ouverture du dialogue

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
	// Getters
	//--------------------------------------
	public function multiple()		{return $this->_multiple;}
	public function accept()		{return $this->_accept;}
	public function min()			{return $this->_min;}
	public function max()			{return $this->_max;}
	public function step()			{return $this->_step;}
	public function pattern()		{return $this->_pattern;}
	public function autocomplete()	{return $this->_autocomplete;}
	public function autofocus()		{return $this->_autofocus;}

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

	public function setMultiple($valeur) {
		$this->_multiple = $valeur;
		//on avertit l'objet que la variable POST sera un tableau
		if ($valeur == true) {
			//on avertit l'objet que la variable POST sera un tableau
			$this->setPostIsTab(true);
		}
	}

	public function setAccept($valeur)			{$this->_accept = $valeur;}
	public function setMin($valeur)				{$this->_min = $valeur;}
	public function setMax($valeur)				{$this->_max = $valeur;}
	public function setStep($valeur)			{$this->_step = $valeur;}
	public function setPattern($valeur)			{$this->_pattern = $valeur;}
	public function setAutocomplete($valeur)	{$this->_autocomplete = strtolower($valeur);}
	public function setAutofocus($valeur)		{$this->_autofocus = $valeur;}

	public function setPostName($postName) {
		parent::setPostName('txt'.ucfirst($postName));
	}

	//Surcharge de la méthode relever pour ne pas afficher le texte 'notposted' si le champ était disabled
	//On commence par "relever" les données postées
	//Cas particulier : dans le cas d'un input de type 'file', il n'est pas renvoyé de POST (donc la valeur est 'notposted')
	//A la place on charge les données qui se trouvent dans le tableau $_FILES
	public function relever() {
		parent::relever();
		if ($this->inputType() == 'file') {
			//Les input type 'file' ne renvoient pas de $_POST mais des informations dans $_FILES
			//On charge donc la valeur du champ avec le contenu de $_FILES
			//Mise en forme homogène des informations de FILES quel que soit le nombre de fichiers selectionnés :
			//On va créer une structure spécifique avec comme information supplémentaire le nombre de fichiers. La structure sera donc celle-ci (exemple) : 
			//	[nom-du-champ] => Array (
			//		[nbFiles] => 2
			//		[files] => Array (
			//			[0] => Array (
			//				[name] => exemple_import.csv
			//				[type] => application/vnd.ms-excel
			//				[tmp_name] => D:\xampp-7.2.0\tmp\php17C6.tmp
			//				[error] => 0
			//				[size] => 521
			//			)
			//			[1] => Array (
			//				[name] => exemple_import2.csv
			//				[type] => application/vnd.ms-excel
			//				[tmp_name] => D:\xampp-7.2.0\tmp\php17C7.tmp
			//				[error] => 0
			//				[size] => 521
			//			)
			//		)
			//	)
			// ATTENTION : Si aucun fichier n'est chargé, la structure renvoie tout de même 1 entrée, mais il faut considérer le code [error] (ici 4 pas de fichier iploadé) 
			//	[nom-du-champ] => Array (
			//		[nbFiles] => 1
			//		[files] => Array (
			//			[0] => Array (
			//				[name] => 
			//				[type] => 
			//				[tmp_name] => 
			//				[error] => 4
			//				[size] => 0
			//			)
			//		)
			//	)
			if (!$this->_multiple) {
				//Dans le cas de sélection unique (un seul fichier selectable)
				$structure = array('nbFiles' => 1, 'files' => array());
				$structure['files'] = $_FILES[$this->postName()];
			}
			else {
				//Dans le cas de sélection multiple (plusieurs fichiers selectables)
				$structure = array('nbFiles' => count($_FILES[$this->postName()]['name']), 'files' => array());
				foreach($_FILES[$this->postName()]['name'] as $key => $dummy) {
					$structure['files'][$key]['name'] = $_FILES[$this->postName()]['name'][$key];
					$structure['files'][$key]['type'] = $_FILES[$this->postName()]['type'][$key];
					$structure['files'][$key]['tmp_name'] = $_FILES[$this->postName()]['tmp_name'][$key];
					$structure['files'][$key]['error'] = $_FILES[$this->postName()]['error'][$key];
					$structure['files'][$key]['size'] = $_FILES[$this->postName()]['size'][$key];
				}
			}
			//transfert de la stucture dans la valeur du champ
			$this->setValue($structure);
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
		$labelHelp = '';
		if ($this->labelHelp() != '') {
			$labelHelp = ' title="'.htmlspecialchars($this->labelHelp()).'"';
			($this->labelHelpPos() != '') ? $labelHelp.= ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp.= ' data-placement="auto"';
		}
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
					$html.= '<label class="mb-0'.$erreur.'" for="'.$this->id().'">';
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
		($this->autofocus() == true) ? $autofocus = ' autofocus' : $autofocus = '';
		($this->_multiple) ? $multiple = ' multiple' : $multiple = '';
		($this->postIsTab()) ? $postNameTableau = $this->postName().'[]' : $postNameTableau = $this->postName();
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		($this->maxlength() > 0) ? $maxlength = ' maxlength="'.$this->maxlength().'"' : $maxlength = '';
		($this->spellcheck() == false) ? $spellcheck = ' spellcheck="false"' : $spellcheck = '';
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';
		($this->javascript() != '') ? $javascript = ' '.$this->javascript() : $javascript = '';
		//traitement des attributs HTML5 min, max et step
		$minMaxStep = '';
		if (in_array($this->inputType(), array('number', 'range', 'date', 'datetime', 'datetime-local', 'month', 'time', 'week'))) {
			if ($this->min() != '') $minMaxStep.= ' min="'.$this->min().'" ';
			if ($this->max() != '') $minMaxStep.= ' max="'.$this->max().'" ';
			if ($this->step() != '') $minMaxStep.= ' step="'.$this->step().'" ';
			trim($minMaxStep);
		}
		//traitement de l'attribut pattern
		$pattern = '';
		if ($this->pattern() != '') {
			if (in_array($this->inputType(), array('text', 'date', 'search', 'url', 'tel', 'email', 'password'))) {
				$pattern = ' pattern="'.$this->pattern().'"';
			}
		}
		//traitement de l'attribut autocomplete
		$autocomplete = '';
		if (($this->autocomplete() == 'on') || ($this->autocomplete() == 'off')) {
			if (in_array($this->inputType(), array('text', 'search', 'url', 'tel', 'email', 'password', 'datepickers', 'range', 'color'))) {
				$autocomplete = ' autocomplete="'.$this->autocomplete().'"';
			}
		}

		//affichage
		$html = '<div id="'.$this->idzchamp().'" class="mb-3 '.$this->clong().$invisible.'">';
			if ($this->inputType() == 'file') {
				($this->accept() != '') ? $accept = ' accept="'.$this->accept().'"' : $accept = '';
				$html.= '<input'.$enable.' type="file" class="form-control-file'.$erreur.$cclass.'" name="'.$postNameTableau.'" id="'.$this->id().'"'.$multiple.$accept.$autofocus.$javascript.'/>';
			}
			else {
				$value = 'value="'.htmlspecialchars(stripslashes($this->value()), ENT_COMPAT, 'UTF-8').'"';
				$html.= '<input'.$maxlength.$spellcheck.$enable.$autocomplete.' type="'.$this->inputType().'" class="form-control'.$erreur.$cclass.'" name="'.$this->postName().'" id="'.$this->id().'" placeholder="'.$this->placeholder().'" '.$value.$autofocus.$javascript.$minMaxStep.$pattern.'/>';
			}
			$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
		$html.= '</div>';
		return $html;
	}

	private function _drawLabelOnline() {
		//label online
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		$labelHelp = '';
		if ($this->labelHelp() != '') {
			$labelHelp = ' title="'.htmlspecialchars($this->labelHelp()).'"';
			($this->labelHelpPos() != '') ? $labelHelp.= ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp.= ' data-placement="auto"';
		}
		$labelPlusHelp = '';
		if ($this->labelPlusHelp() != '') {
			$labelPlusHelp.= ' title="'.htmlspecialchars($this->labelPlusHelp()).'"';
			($this->labelPlusHelpPos() != '') ? $labelPlusHelp.= ' data-placement="'.$this->labelPlusHelpPos().'"' : $labelPlusHelp.= ' data-placement="auto"';
		}
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
		($this->autofocus() == true) ? $autofocus = ' autofocus' : $autofocus = '';
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		($this->maxlength() > 0) ? $maxlength = ' maxlength="'.$this->maxlength().'"' : $maxlength = '';
		($this->_multiple) ? $multiple = ' multiple' : $multiple = '';
		($this->postIsTab()) ? $postNameTableau = $this->postName().'[]' : $postNameTableau = $this->postName();
		($this->spellcheck() == false) ? $spellcheck = ' spellcheck="false"' : $spellcheck = '';
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';
		($this->javascript() != '') ? $javascript = ' '.$this->javascript() : $javascript = '';
		//traitement des attributs HTML5 min, max et step
		$minMaxStep = '';
		if (in_array($this->inputType(), array('number', 'range', 'date', 'datetime', 'datetime-local', 'month', 'time', 'week'))) {
			if ($this->min() != '') $minMaxStep.= ' min="'.$this->min().'" ';
			if ($this->max() != '') $minMaxStep.= ' max="'.$this->max().'" ';
			if ($this->step() != '') $minMaxStep.= ' step="'.$this->step().'" ';
			trim($minMaxStep);
		}
		//traitement de l'attribut pattern
		$pattern = '';
		if ($this->pattern() != '') {
			if (in_array($this->inputType(), array('text', 'date', 'search', 'url', 'tel', 'email', 'password'))) {
				$pattern = ' pattern="'.$this->pattern().'"';
			}
		}
		//traitement de l'attribut autocomplete
		$autocomplete = '';
		if (($this->autocomplete() == 'on') || ($this->autocomplete() == 'off')) {
			if (in_array($this->inputType(), array('text', 'search', 'url', 'tel', 'email', 'password', 'datepickers', 'range', 'color'))) {
				$autocomplete = ' autocomplete="'.$this->autocomplete().'"';
			}
		}

		$html = '<div id="'.$this->idzchamp().'">';
			if ($this->inputType() == 'file') {
				($this->accept() != '') ? $accept = ' accept="'.$this->accept().'"' : $accept = '';
				$html.= '<input'.$enable.' type="file" class="form-control-file'.$erreur.$cclass.'" name="'.$postNameTableau.'" id="'.$this->id().'"'.$multiple.$accept.$autofocus.$javascript.'/>';
			} 
			else {
				$value = 'value="'.htmlspecialchars(stripslashes($this->value()), ENT_COMPAT, 'UTF-8').'"';
				$html.= '<input'.$maxlength.$spellcheck.$enable.$autocomplete.' type="'.$this->inputType().'" class="form-control'.$erreur.$cclass.'" name="'.$this->postName().'" id="'.$this->id().'" placeholder="'.$this->placeholder().'" '.$value.$autofocus.$javascript.$minMaxStep.$pattern.'/>';
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