<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'radio' button
// Version 3.11.3 du 17.01.2019
//==============================================================

class UniversalFieldRadio extends UniversalField {

	private $_valueBase;
	private $_checked;
	private $_groupName = '';										//nom d'un groupe de boutons radio
	private $_choixPossibles = array('first', 'inter', 'last');		//choix possible pour le champ dpos

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	public function __construct(array $donnees, $idformulaire) {
		$this->setFieldType('radio');
		$this->setIdParentForm($idformulaire);
		if (!isset($donnees['value'])) {
			$donnees['value'] = '';
		}
		$this->_valueBase = $donnees['value'];
		$this->hydrate($donnees);
		//si il existe déjà un POST pour ce champ on le prend en compte tout de suite		
		if (isset($_POST['hidSoumissionFormulaire'.'_'.$this->idParentForm()])) {
			$this->relever();
		}
	}

	//--------------------------------------
	// Getters
	//--------------------------------------

	public function valueBase()		{return $this->_valueBase;}
	public function checked()		{return $this->_checked;}
	public function groupName()		{return $this->_groupName;}

	//--------------------------------------
	// Setters
	//--------------------------------------

	public function setChecked($checked) {
		$this->_checked = ($checked === true);
		if ($checked === true) 
			$this->setValue($this->_valueBase);
    }

	public function setGroupName($name) {
		$this->_groupName = $name;
		$this->setPostName($name);
	}

	//--------------------------------------
	// Autres methodes
	//--------------------------------------

	public function relever() {
		if (!$this->enable()) { 
			//si le champ est disabled alors le relevé ne sert à rien (pas de valeur renvoyée). On décoche de facto la case à cocher.
			$this->setChecked(false);
			return;
		}
		//un appel natif à la méthode héritée relever() de la classe UniversalField est faite au préalable
		parent::relever();
		//ATTENTION : un champ radio qui est "disabled" n'est pas vu par le POST et aucune info n'est renvoyée de son état.
		//DONC : si le bouton est readonly et checké par défaut, on force un nouveau check ce qui permet à _value de prendre la valeur par défaut _valueBase
		//en fait comme cela, en forcant le check on va forcer le POST sur un bouton qui est readonly et qui donc n'aurait pas eu droit à un POST
		if ($this->value() == 'notposted' && $this->readonly() && $this->checked()) {
			$this->setChecked(true);
			return;
		}
		//si la valeur renvoyée par le relever() est égale à la valeur de base, alors on check le bouton, sinon on le uncheck
		($this->value() == $this->_valueBase) ? $this->setChecked(true) : $this->setChecked(false);
	}

	//affichage de la zone de titre
	private function _drawZtitre($style) {
		($this->lclass() != '') ? $lclass = $this->lclass() : $lclass = '';
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		($this->labelHelpPos() != '') ? $labelHelp = ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp = '';
		($this->labelHelp() != '') ? $labelHelp.= ' title="'.$this->labelHelp().'"' : $labelHelp.= '';
		$chaine = '<div id="'.$this->idztitre().'" style="'.$style.'">';
			if ($labelHelp == '') {
				//il n'y a pas d'aide sur le label
				$chaine.= '<label for="'.$this->id().'" class="'.$lclass.$erreur.'">'.$this->label().'</label>';
			}
			else {
				//on ajoute une aide sur le label
				$chaine.= '<label for="'.$this->id().'" class="'.$lclass.$erreur.'">';
					$chaine.= '<span data-toggle="tooltip"'.$labelHelp.'>'.$this->label().'</span>';
				$chaine.= '</label>';
			}
		$chaine.= '</div>';
		return $chaine;
	}

	//affichage de la zone de champ
	private function _drawZchamp($enable, $cursor, $style) {
		($this->cclass() != '') ? $cclass = ' class="'.$this->cclass().'"' : $cclass = '';
		($this->checked()) ? $checked = ' checked' : $checked = '';
		$chaine = '<div id="'.$this->idzchamp().'" style="'.$style.'">';
			$chaine.= '<input type="radio"'.$enable.$cclass.' name="'.$this->postName().'" id="'.$this->id().'" style="margin-bottom:.5rem;'.$cursor.'" value="'.$this->_valueBase.'" '.$this->javascript().$checked.'></input>';
		$chaine.= '</div>';
		return $chaine;
	}

	//fonction privée qui test les erreurs éventuelles de construction du radio
	private function _testConstructionErreur() {
		if (empty($this->groupName())) {
			$libelle = 'Paramètre \'groupName\' manquant pour le radio \''.$this->idField().'\'';
			$help = 'Paramètre \'groupName\' manquant pour le radio \''.$this->idField().'\'';
			return '<p class="form_error" title="'.$help.'">'.$libelle.'</p>';
		}
		elseif (!in_array($this->dpos(), $this->_choixPossibles)) {
			$libelle = 'Paramètre \'dpos\' manquant ou inaproprié pour le radio \''.$this->idField().'\'';
			$help = 'Paramètre \'dpos\' obligatoire. Choisir parmi \'first\', \'inter\' ou \'last\'';
			return '<p class="form_error" title="'.$help.'">'.$libelle.'</p>';
		}
		//test si le paramatre 'lpos' est valide
		elseif (!in_array($this->lpos(), array('before', 'after'))) {
			$libelle = 'Paramètre \'lpos\' => \''.$this->lpos().'\' inaproprié pour le radio \''.$this->idField().'\'';
			$help = 'Paramètre \'lpos\' obligatoire. Choisir parmi \'before\' ou \'after\'';
			$this->setLpos('before');
			return '<p class="form_error" title="'.$help.'">'.$libelle.'</p>';
		}
		//test si manque clong pour le premier radio
		elseif (($this->dpos() == 'first') && ($this->clong() == '')) {
			$libelle = 'Paramètre \'clong\' manquant pour le radio \''.$this->idField().'\'';
			$help = 'Paramètre \'clong\' est fortement conseillé pour le premier radio. Non utilisé pour les autres';
			return '<p class="form_error" title="'.$help.'">'.$libelle.'</p>';
		}
		//newLine hors du premier element radio
		elseif (($this->newLine()) && ($this->dpos() !== 'first')) {
			$libelle = 'Paramètre \'newLine\' à true semble inadapté pour le radio \''.$this->idField().'\'';
			$help = '';
			return '<p class="form_error" title="'.$help.'">'.$libelle.'</p>';
		}
		return false;
	}


	//------------------------------------
	//DESSIN
	//REMARQUE : le paramètre $enabled sert juste pour l'affichage du radio en mode actif ou non actif mais ne change pas la propriété enable du bouton.
	//------------------------------------
	public function draw($enabled) {
		$html = '';

		//mise en place des selecteurs disabled / readonly
		//disabled a toujours le dessus sur readonly
		$cursor = '';
		if ($enabled) {
			$enable = '';
			if (!$this->enable()) $enable.= ' disabled';
			if ($this->readonly()) {
				$enable.= ' disabled';				//on force à disable pour griser le bouton et empécher toute interaction
				$cursor = 'cursor: default;';		//on enleve le curseur (not-allowed) fourni par Bootstrap et on le remplace par un curseur normal
			}
		}
		else {
			$enable = ' disabled';	//champ désactive
			if ($this->readonly()) $enable.= ' readonly';
		}

		//test erreurs construction radio
		$buildError = $this->_testConstructionErreur();

		//decalage
		//----------------------------------------
		if ($this->decalage() != '') {
			($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
			$html.= '<div class="'.$this->decalage().$invisible.'"></div>';		//sert de push à droite
		}

		//debut de groupe
		//----------------------------------------
		if ($this->dpos() == 'first') {

			//legende
			if ($this->titre() != '') {
				($this->tlong() != '') ? $tlong = ' '.$this->tlong() : $tlong = '';
				($this->tclass() != '') ? $tclass = ' '.$this->tclass() : $tclass = '';
				($this->titreHelpPos() != '') ? $titreHelp = ' data-placement="'.$this->titreHelpPos().'"' : $titreHelp = '';
				($this->titreHelp() != '') ? $titreHelp.= ' title="'.$this->titreHelp().'"' : $titreHelp.= '';
				$classeTitre = 'text-'.$this->talign().' col-form-label'.$tlong.$tclass;
				$style = '';
				$html.= '<legend class="'.$classeTitre.'"'.$style.'>';
				if ($titreHelp != '') {
					$html.= '<span data-toggle="tooltip"'.$titreHelp.'>'.$this->titre().'</span>';
				}
				else {
					$html.= $this->titre();
				}
				$html.= '</legend>';
			}

			//erreur
			if ($buildError) {
				$style = 'margin-left:15px;margin-top:.5rem;padding-top:.0rem;line-height:1.25rem;';
				$html.= '<div class="mb-3 '.$this->clong().' designError" style="'.$style.'">';
			}
			elseif ($this->erreur() == true) {
				$style = 'margin-left:15px;margin-top:.5rem;padding-top:.0rem;line-height:1.25rem;';
				$html.= '<div class="mb-3 '.$this->clong().' designError" style="'.$style.'">';
			}
			//bordure standard
			elseif ($this->border() === true) {
				$style = 'margin-left:15px;margin-top:.5rem;padding-top:.0rem;line-height:1.25rem;';
				$html.= '<div class="mb-3 '.$this->clong().' border" style="'.$style.'">';
			}
			//bordure personnalisée
			elseif ($this->border() != '') {
				$style = 'margin-left:15px;margin-top:.5rem;padding-top:.0rem;line-height:1.25rem;';
				$html.= '<div class="mb-3 '.$this->clong().'" style="'.$style.$this->border().'">';
			}
			//aucune bordure
			else {
				$style = 'margin-top:.25rem;line-height:1.25rem';
				$html.= '<div class="mb-3 '.$this->clong().'" style="'.$style.'">';
			}

		}


		//Dessin de checkbox (toutes les chechbox font partie d'un groupe même si le groupe est composé d'une seule checkbox
		//----------------------------------------

		//stles standards
		$styleBefore	= 'display:inline-block;margin:.5rem .3rem 0 0';
		$styleAfter		= 'display:inline-block;margin:0 .75rem 0 0;';
		
		//dessin bloc champ
		($this->design() == 'inline') ? $design = ' style="display:inline-block;"' : $design = '';
		($this->invisible() == true) ? $invisible = ' class="invisible"' : $invisible = '';
		$html.= '<div id="'.$this->idbchamp().'"'.$invisible.$design.'>';

			//affichage ztitre et zchamp
			if ($this->lpos() == 'before') {
				$html.= $this->_drawZtitre($styleBefore);
				$html.= $this->_drawZchamp($enable, $cursor, $styleAfter);
			}
			else {
				$html.= $this->_drawZchamp($enable, $cursor, $styleBefore);
				$html.= $this->_drawZtitre($styleAfter);
			}

		//fin bchamp
		$html.= '</div>';

		//affichage des eventuelles erreurs de construction
		if ($buildError) {
			$html.= $buildError;
		}
		else {
			//pose message d'erreur
			if ($this->dpos() == 'last') {
				($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';
				//affiche seulement les erreurs qui ne sont pas des erreurs de construction
				($this->erreur()) ? $erreur = $this->liberreur() : $erreur = '';
				if ($this->erreur()) $html.= '<p class="form_error"'.$libErreurHelp.'>'.$erreur.'</p>';
			}
		}

		//fin de groupe
		//----------------------------------------
		if ($this->dpos() == 'last') {
			$html.= '</div>';
		}

		return $html;
	}
}