<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'filtrecheckbox' (un filtre du type checkbox)
// Version 3.22.0 du 05.05.2020
//==============================================================

class UniversalFieldFiltrecheckbox extends UniversalField {

	private $_valueBase = 1;
	private $_valueInverse = 0;
	private $_checked;
	private $_choixPossibles = array('alone');

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	public function __construct(array $donnees, $idformulaire) {
		$this->setFieldType('filtrecheckbox');	//type de champ
		$this->setIdParentForm($idformulaire);
		//la variable POST par défaut est le nom donné à l'objet UniversalField.
		$this->setPostName($donnees['idfield']);
		if (!isset($donnees['value'])) {
			$donnees['value'] = $this->_valueBase;
		}
		else {
			$this->_valueBase = $donnees['value'];
		}
		if (!array_key_exists('valueInverse', $donnees)) $this->setValueInverse(0);
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
	public function valueInverse()	{return $this->_valueInverse;}
	public function checked()		{return $this->_checked;}

	//--------------------------------------
	// Setters
	//--------------------------------------

	protected function setPostName($postName) {
		parent::setPostName('flc'.ucfirst($postName));
	}

	public function setValueInverse($valeur) {$this->_valueInverse = $valeur;}

	public function setChecked($checked) {
		$this->_checked = ($checked === true);
		if ($this->_checked == true) 
			$this->setValue($this->_valueBase);
		else 
			$this->setValue($this->_valueInverse);
    }

	//--------------------------------------
	// Autres methodes
	//--------------------------------------

	//Surcharge (intégrale, cad sans appel à la methode parente) de la méthode test()
	//car on ne test que le cochage
	//Pour une case à cocher, si le $_POST ne renvoie aucune info (unset), 
	//c'est qu'elle est décochée, sinon elle prend la valeur "value"
	public function test() {
		$this->relever();
	}

	public function relever() {
		//surcharge de la methode relever car une checkbox envoie un POST si la case est cochée uniquement
		//un appel natif à la méthode héritée relever() de la classe UniversalField est faite au préalable
		parent::relever();
		if ($this->readonly()) {
			//si le champ est readonly il y a forcement un POST, c'est en réalité celui du champ hidden ajouté
			//la valeur du champ hidden est donc la valeur de base de lma case à cocher.
			//on en profite pour mettre à jour l'état de checked.
			($this->value() == $this->_valueBase) ? $this->setChecked(true) : $this->setChecked(false);
		}
		else {
			//si la valeur a reçu l'intitulé 'notposted' cela signifie que la case n'est pas cochée
			($this->value() === 'notposted') ? $this->setChecked(false) : $this->setChecked(true);
		}
	}

	//affichage de la zone de titre
	private function _drawZtitre($style) {
		($this->lclass() != '') ? $lclass = $this->lclass() : $lclass = '';
		($this->erreur() == true) ? $erreur = ' text-danger' : $erreur = '';
		$labelHelp = '';
		if ($this->labelHelp() != '') {
			$labelHelp = ' data-toggle="tooltip" title="'.$this->labelHelp().'"';
			($this->labelHelpPos() != '') ? $labelHelp.= ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp.= ' data-placement="auto"';
			($this->labelHelpHtml() == true) ? $labelHelp.= ' data-html="true"' : $labelHelp.= '';
		}
		$chaine = '<div id="'.$this->idztitre().'" style="'.$style.'">';
//			if ($labelHelp == '') {
//				//il n'y a pas d'aide sur le label
//				$chaine.= '<label for="'.$this->id().'" class="'.$lclass.$erreur.'">'.$this->label().'</label>';
//			}
//			else {
				//on ajoute une aide sur le label
				$chaine.= '<label for="'.$this->id().'" class="'.$lclass.$erreur.'">';
					$chaine.= '<span'.$labelHelp.'>'.$this->label().'</span>';
				$chaine.= '</label>';
//			}
		$chaine.= '</div>';
		return $chaine;
	}

	//affichage de la zone de champ
	private function _drawZchamp($enable, $style) {
		($this->cclass() != '') ? $cclass = ' class="'.$this->cclass().'"' : $cclass = '';
		($this->checked()) ? $checked = ' checked' : $checked = '';
		$chaine = '<div id="'.$this->idzchamp().'" style="'.$style.'">';
			$chaine.= '<input type="checkbox"'.$enable.$cclass.' name="'.$this->postName().'" id="'.$this->id().'" value="'.$this->_valueBase.'" '.$this->javascript().$checked.'></input>';
			//ATTENTION : un champ checkbox qui est "disabled" n'est pas vu par le POST et aucune info n'est renvoyé de son état.
			//Pour palier au problème, si la checkbox est "disabled" (c'est à dire que le paramètre readonly = true) on ajoute un champ fictif hidden 
			//du même nom que la checkbox et on lui donne comme valeur la valeur de demarrage de la checkbox. Ainsi on ne perd pas cette valeur qui 
			//est restituée par le getData().
			if ($this->readonly()) {
				($this->_checked == true) ? $valeur = $this->_valueBase : $valeur = $this->_valueInverse;
				$chaine.= '<input type="hidden" name="'.$this->postName().'" value="'.$valeur.'" />';
			}
		$chaine.= '</div>';
		return $chaine;
	}

	//fonction privée qui test les erreurs éventuelles de construction de la checkbox
	private function _testConstructionErreur() {
		return false;
	}

	//------------------------------------
	//DESIN
	//------------------------------------
	public function draw($enabled) {
		$html = '';

		//mise en place des selecteurs disabled / readonly
		//disabled a toujours le dessus sur readonly
		if ($enabled) {
			$enable = '';
			if (!$this->enable()) $enable.= ' disabled';
			if ($this->readonly()) {
				$enable.= ' disabled';				//on force à disable pour griser le bouton et empécher toute interaction
			}
		}
		else {
			$enable = ' disabled';	//champ désactive
			if ($this->readonly()) $enable.= ' readonly';
		}

		//test erreurs construction radio
		$buildError = $this->_testConstructionErreur();

		//erreurs et bordures
		//----------------------------------------

		//erreur
		if ($buildError) {
			$style = '';
			$classes = ' border border-danger rounded';
		}
		elseif ($this->erreur() == true) {
			$style = '';
			$classes = ' border border-danger rounded';
		}
		//bordure standard
		elseif ($this->border() === true) {
			$style = 'line-height:.8rem;';
			$classes = ' border';
		}
		//bordure personnalisée
		elseif ($this->border() != '') {
			$style = 'line-height:.8rem;'.$this->border();
			$classes = '';
		}
		//aucune bordure
		else {
			$style='line-height:.8rem';
			$classes = '';
		}

		//Dessin de la checkbox
		//----------------------------------------

		//stles standards
		$styleBefore	= '';
		$styleAfter		= '';
		
		//dessin bloc champ
		($this->clong() != '') ? $clong = $this->clong().' ' : $clong = '';
		($this->invisible() == true) ? $invisible = ' class="invisible"' : $invisible = '';
		$html.= '<div class="'.$clong.'text-center mb-3'.$classes.'" id="'.$this->idbchamp().'"'.$invisible.' style="'.$style.'">';

		//affichage ztitre et zchamp
		$html.= $this->_drawZtitre($styleBefore);
		$html.= $this->_drawZchamp($enable, $styleAfter);

		//affichage des eventuelles erreurs de construction
		if ($buildError) {
			$html.= $buildError;
		}
		else {
			//pose message d'erreur
			($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';
			//affiche seulement les erreurs qui ne sont pas des erreurs de construction
			($this->erreur()) ? $erreur = $this->liberreur() : $erreur = '';
			if ($this->erreur()) $html.= '<p class="form_error"'.$libErreurHelp.'>'.$erreur.'</p>';
		}

		//fin bchamp
		$html.= '</div>';

		return $html;
	}

}