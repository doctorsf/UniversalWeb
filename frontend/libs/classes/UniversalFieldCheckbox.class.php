<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'checkbox'
// Version 3.11.3 du 17.01.2019
//==============================================================

class UniversalFieldCheckbox extends UniversalField {

	private $_valueBase = 1;
	private $_valueInverse = 0;
	private $_checked;
	private $_groupName = '';													//nom d'un groupe de checkbox
	private $_choixPossibles = array('first', 'inter', 'last', 'alone');		//choix possible pour le champ dpos

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	public function __construct(array $donnees, $idformulaire) {
		$this->setFieldType('checkbox');	//type de champ
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
	public function groupName()		{return $this->_groupName;}
	public function checked()		{return $this->_checked;}

	//--------------------------------------
	// Setters
	//--------------------------------------

	protected function setPostName($postName) {
		parent::setPostName('chk'.ucfirst($postName));
	}

	public function setValueInverse($valeur) {$this->_valueInverse = $valeur;}

	public function setGroupName($name) {
		$this->_groupName = $name;
	}

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
			$chaine.= '<input type="checkbox"'.$enable.$cclass.' name="'.$this->postName().'" id="'.$this->id().'" style="margin-bottom:.5rem;'.$cursor.'" value="'.$this->_valueBase.'" '.$this->javascript().$checked.'></input>';
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
		if (!in_array($this->dpos(), $this->_choixPossibles)) {
			$libelle = 'Paramètre \'dpos\' manquant ou inaproprié pour la checkbox \''.$this->idField().'\'';
			$help = 'Paramètre \'dpos\' obligatoire. Choisir parmi \'first\', \'inter\', \'last\' ou \'alone\'';
			return '<p class="form_error" title="'.$help.'">'.$libelle.'</p>';
		}
		//test si le paramatre 'lpos' est valide
		elseif (!in_array($this->lpos(), array('before', 'after'))) {
			$libelle = 'Paramètre \'lpos\' => \''.$this->lpos().'\' inaproprié pour la checkbox \''.$this->idField().'\'';
			$help = 'Paramètre \'lpos\' obligatoire. Choisir parmi \'before\' ou \'after\'';
			$this->setLpos('before');
			return '<p class="form_error" title="'.$help.'">'.$libelle.'</p>';
		}
		//test si manque clong pour le premier radio
		elseif ((($this->dpos() == 'first') || ($this->dpos() == 'alone')) && ($this->clong() == '')) {
			$libelle = 'Paramètre \'clong\' manquant pour la checkbox \''.$this->idField().'\'';
			$help = 'Paramètre \'clong\' est fortement conseillé pour une checkbox ou pour la première checkbox d\'un groupe';
			return '<p class="form_error" title="'.$help.'">'.$libelle.'</p>';
		}
		return false;
	}

	//------------------------------------
	//DESIN
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

		//une checkbox seule est forcement inline
		//libellés et checkbox sont toujours en ligne, c'est l'ensemble checkbox + libellé qui peut être online
		if ($this->dpos() == 'alone') {
			$this->setDesign('inline');					//inline obligatoire
			$this->setGroupName($this->idField());		//on force pour être certain que "groupName" n'ait pas été oublié
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
		if (($this->dpos() == 'first') || ($this->dpos() == 'alone')) {

			//legende
			if ($this->titre() != '') {
				($this->tlong() != '') ? $tlong = ' '.$this->tlong() : $tlong = '';
				($this->tclass() != '') ? $tclass = ' '.$this->tclass() : $tclass = '';
				($this->titreHelpPos() != '') ? $titreHelp = ' data-placement="'.$this->titreHelpPos().'"' : $titreHelp = '';
				($this->titreHelp() != '') ? $titreHelp.= ' title="'.$this->titreHelp().'"' : $titreHelp.= '';
				$classeTitre = 'col-form-label'.$tlong.$tclass;
				$style = '';
				$html.= '<legend class="text-'.$this->talign().' '.$classeTitre.'"'.$style.'>';
				if (($this->dpos() == 'alone') && ($this->label() == '')) {
					if ($titreHelp != '') {
						$html.= '<label for="'.$this->id().'"'.$titreHelp.' data-toggle="tooltip" style="margin-bottom:0">'.$this->titre().'</label>';
					}
					else {
						$html.= '<label for="'.$this->id().'" style="margin-bottom:0">'.$this->titre().'</label>';
					}
				}
				else {
					if ($titreHelp != '') {
						$html.= '<span data-toggle="tooltip"'.$titreHelp.'>'.$this->titre().'</span>';
					}
					else {
						$html.= $this->titre();
					}
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
			if (($this->dpos() == 'last') || ($this->dpos() == 'alone')) {
				($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';
				//affiche seulement les erreurs qui ne sont pas des erreurs de construction
				($this->erreur()) ? $erreur = $this->liberreur() : $erreur = '';
				if ($this->erreur()) $html.= '<p class="form_error"'.$libErreurHelp.'>'.$erreur.'</p>';
			}
		}

		//fin de groupe
		//----------------------------------------
		if (($this->dpos() == 'last') || ($this->dpos() == 'alone')) {
			$html.= '</div>';
		}

		return $html;
	}

}