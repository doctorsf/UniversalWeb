<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'switch'
// Version 3.18.0 du 07.01.2020
//==============================================================

class UniversalFieldSwitch extends UniversalField {

	private $_valueBase = 1;
	private $_valueInverse = 0;
	private $_checked;
	private $_custom = 'switch';

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	public function __construct(array $donnees, $idformulaire) {
		$this->setFieldType('switch');	//type de champ
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
	public function custom()		{return $this->_custom;}

	//--------------------------------------
	// Setters
	//--------------------------------------

	protected function setPostName($postName) {
		parent::setPostName('sw'.ucfirst($postName));
	}

	public function setValueInverse($valeur) {$this->_valueInverse = $valeur;}

	public function setChecked($checked) {
		$this->_checked = ($checked === true);
		if ($this->_checked == true) 
			$this->setValue($this->_valueBase);
		else 
			$this->setValue($this->_valueInverse);
    }

	protected function setCustom($valeur) {
		$this->_custom = $valeur;
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
	private function _drawZtitre() {
		($this->lclass() != '') ? $lclass = $this->lclass() : $lclass = '';
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		$labelHelp = '';
		if ($this->labelHelp() != '') {
			$labelHelp = ' title="'.$this->labelHelp().'"';
			($this->labelHelpPos() != '') ? $labelHelp.= ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp.= ' data-placement="auto"';
		}
		if ($labelHelp == '') {
			//il n'y a pas d'aide sur le label
			$chaine = '<label for="'.$this->id().'" class="'.trim('custom-control-label '.$lclass.$erreur).'">'.$this->label().'</label>';
		}
		else {
			//on ajoute une aide sur le label
			$chaine = '<label for="'.$this->id().'" class="'.trim('custom-control-label '.$lclass.$erreur).'">';
				$chaine.= '<span data-toggle="tooltip"'.$labelHelp.'>'.$this->label().'</span>';
			$chaine.= '</label>';
		}
		return $chaine;
	}

	//affichage de la zone de champ
	private function _drawZchamp($enable, $cursor) {
		($this->checked()) ? $checked = ' checked' : $checked = '';
		$chaine = '<input type="checkbox"'.$enable.$checked.' class="custom-control-input" name="'.$this->postName().'" id="'.$this->id().'" style="'.$cursor.'" value="'.$this->_valueBase.'" '.$this->javascript().'></input>';
		//ATTENTION : un champ checkbox qui est "disabled" n'est pas vu par le POST et aucune info n'est renvoyé de son état.
		//Pour palier au problème, si la checkbox est "disabled" (c'est à dire que le paramètre readonly = true) on ajoute un champ fictif hidden 
		//du même nom que la checkbox et on lui donne comme valeur la valeur de demarrage de la checkbox. Ainsi on ne perd pas cette valeur qui 
		//est restituée par le getData().
		if ($this->readonly()) {
			($this->_checked == true) ? $valeur = $this->_valueBase : $valeur = $this->_valueInverse;
			$chaine.= '<input type="hidden" name="'.$this->postName().'" value="'.$valeur.'"/>';
		}
		return $chaine;
	}

	//fonction privée qui test les erreurs éventuelles de construction du switch
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
			($this->invisible() == true) ? $invisible = ' d-none' : $invisible = '';
			$html.= '<div class="'.$this->decalage().$invisible.'"></div>';		//sert de push à droite
		}

		//titre
		//----------------------------------------
		if ($this->titre() != '') {
			($this->tlong() != '') ? $tlong = ' '.$this->tlong() : $tlong = '';
			($this->tclass() != '') ? $tclass = ' '.$this->tclass() : $tclass = '';
			$titreHelp = '';
			if ($this->titreHelp() != '') {
				$titreHelp = ' title="'.htmlspecialchars($this->titreHelp()).'" data-toggle="tooltip"';
				($this->titreHelpPos() != '') ? $titreHelp.= ' data-placement="'.$this->titreHelpPos().'"' : $titreHelp.= ' data-placement="auto"';
			}
			$classeTitre = 'col-form-label pt-0'.$tlong.$tclass;
			$style = '';
			$html.= '<legend class="text-'.$this->talign().' '.$classeTitre.'"'.$style.'>';
			if (($this->dpos() == 'alone') && ($this->label() == '')) {
				//cas du le titre qui remplace le label
				if ($titreHelp != '') {
					$html.= '<label for="'.$this->id().'"'.$titreHelp.' data-toggle="tooltip">'.$this->titre().'</label>';
				}
				else {
					$html.= '<label for="'.$this->id().'">'.$this->titre().'</label>';
				}
			}
			else {
				if ($titreHelp != '') {
					$html.= '<span '.$titreHelp.'>'.$this->titre().'</span>';
				}
				else {
					$html.= '<span>'.$this->titre().'</span>';
				}
			}
			$html.= '</legend>';
		}

		//Dessin du switch
		//----------------------------------------
		($this->clong() == '') ? $clong = 'col-12' : $clong = $this->clong();
		($this->invisible() == true) ? $invisible = 'd-none ' : $invisible = '';
		$html.= '<div id="'.$this->idbchamp().'" class="'.trim($invisible.$clong).'">';
			$html.= '<div class="custom-control custom-'.$this->custom().'">';

				$html.= $this->_drawZchamp($enable, $cursor);
				$html.= $this->_drawZtitre();

			$html.= '</div>';

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

		$html.= '</div>';

		return $html;
	}

}