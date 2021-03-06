<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element 'select'
// Version 3.22.0 du 05.05.2020
//==============================================================

class UniversalFieldSelect extends UniversalField {

	private $_size = 1;						//taille par défaut des listes : 1 ligne
	private $_multiple = false;				//selection multiple possible (attention si true, renvoie un tableau dans le POST)

	//----------------------------------------------------------------------
	// OBTENIR UNE CHAINE COMPRISE ENTRE DEUX TAGS
	// exemple : permet d'obtenir la chaine après le tag [ra]
	// [f]un roman[a]a novel[rf]The Sentinel[ra]The Sentinel
	//----------------------------------------------------------------------
	private function getBetween($tagdebut, $tagfin, $temp) {
		if (($tagdebut == '') && ($tagfin == ''))
			return $temp;
		if (($tagdebut == '') && ($tagfin <> ''))
			return substr($temp, 0, strpos($temp, $tagfin));
		if (($tagdebut <> '') && ($tagfin == ''))
			return substr($temp, strpos($temp, $tagdebut) + strlen($tagdebut), strlen($temp) - strpos($temp, $tagdebut) - strlen($tagdebut));
		if (($tagdebut <> '') && ($tagfin <> ''))
			return substr($temp, strpos($temp, $tagdebut) + strlen($tagdebut), strpos($temp, $tagfin) - strpos($temp, $tagdebut) - strlen($tagdebut));
	}

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	//surcharge du constructeur pour l'initialisation de cet objet
	public function __construct(array $donnees, $idformulaire) {
		$this->setFieldType('select');
		$this->setIdParentForm($idformulaire);
		parent::__construct($donnees);
	}

	//--------------------------------------
	// Getters
	//--------------------------------------
	public function size()		{return $this->_size;}
	public function multiple()	{return $this->_multiple;}

	//--------------------------------------
	// Setters
	//--------------------------------------
	public function setSize($valeur)		{$this->_size = $valeur;}
	public function setMultiple($valeur) {
		$this->_multiple = $valeur;
		//on avertit l'objet que la variable POST sera un tableau
		if ($valeur == true) {
			//on avertit l'objet que la variable POST sera un tableau
			$this->setPostIsTab(true);
		}
	}
	protected function setPostName($postName) {
		parent::setPostName('lst'.ucfirst($postName));
	}

	//--------------------------------------
	// Methodes
	//--------------------------------------

	public function relever() {
		parent::relever();
		if ($this->value() === 'notposted') {
			$this->setValue('');
//			$this->setEnable(false);  //enlevé le 12.06.2018 car bug empéchait testMatches 'REQUIRED_SELECTION' de fonctionner
		}
	}

	//dessin du label inline
	private function _drawLabelInline() {
		//label inline
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		($this->lclass() != '') ? $lclass = ' '.$this->lclass() : $lclass = '';
		$labelHelp = '';
		if ($this->labelHelp() != '') {
			$labelHelp = ' data-toggle="tooltip" title="'.htmlspecialchars($this->labelHelp()).'"';
			($this->labelHelpPos() != '') ? $labelHelp.= ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp.= ' data-placement="auto"';
			($this->labelHelpHtml() == true) ? $labelHelp.= ' data-html="true"' : $labelHelp.= '';
		}
		$style = '';
		$html = '<div id="'.$this->idztitre().'" class="text-'.$this->lalign().' '.$this->llong().$lclass.$invisible.$erreur.'"'.$style.'>';
//			if ($labelHelp == '') {
//				//il n'y a pas d'aide sur le label
//				$html.= '<label class="mb-0'.$erreur.'" for="'.$this->id().'">'.$this->label().'</label>';
//			}
//			else {
//				//on ajoute une aide sur le label
				$html.= '<label class="mb-0'.$erreur.'" for="'.$this->id().'">';
					$html.= '<span'.$labelHelp.'>'.$this->label().'</span>';
				$html.= '</label>';
//			}
		$html.= '</div>';
		return $html;
	}

	//dessin du champ inline
	private function _drawChampInline($enable) {
		//champ inline
		($this->invisible() == true) ? $invisible = ' invisible' : $invisible = '';
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		($this->_size > 1) ? $size = ' size="'.$this->_size.'"' : $size = '';
		($this->_multiple) ? $multiple = ' multiple' : $multiple = '';
		($this->postIsTab()) ? $postNameTableau = $this->postName().'[]' : $postNameTableau = $this->postName();
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		if ($enable != '') $cclass = '';		//annule la personnalisation de la classe du select si champ disabled ou readonly
		($this->cheight() != '') ? $cheight = ' form-control-'.$this->cheight() : $cheight = '';
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';

		//ATTENTION : le mode "readonly" sur un INPUT SELECT ne fonctionne pas et reste disponible pour l'utilisateur.
		//Pour simuler le "readonly" on remplace le SELECT par 2 champs : 
		//1 champ TEXT qui ne sert qu'à afficher le texte correspondant à la valeur d'option <option>text</option>
		//1 champ hidden qui a le même nom que le SELECT qui contiendra la valeur de l'option (option value)
		//Ainsi, non seulement on ne peut plus changer l'option mais en plus le getData() renvoie la valeur même sur le "readonly".
		//Dans le cas d'un SELECT MULTIPLE, le subterfuge consiste à écrire les valeurs dans un <textarea> readonly de même taille 
		//et de faire envoyer les données par un SELECT MULTIPLE caché
		if ($this->readonly()) {
			//recharger les options de la liste avec valeur en cours
			$liste = call_user_func($this->complement(), $this->value());
			//recuperer le(s) libellé(s) et la(les) valeur(s) selected par analyse des tags <option>
			$tabOptions = explode('</option>', $liste);
			array_pop($tabOptions);
			foreach($tabOptions as $indice => $option) {
				if (strpos($option, 'selected') !== false) {
					//recuperation des valeurs (<value>)
					$valeur[] = $this->getBetween('value="', '" selected', $option);
					//recuperation des textes (<option>)
					$texte[] = $this->getBetween('>', '', $option);
				}
			}
			//afficher champs "fake"
			$html = '<div id="'.$this->idzchamp().'" class="mb-3 '.$this->clong().$invisible.$erreur.'">';
				if ($this->_multiple == true) {
					//afficher champs "fake" <textarea> et <select multiple hidden> à la place du select multiple
					$html.= '<textarea'.$enable.' class="form-control'.$cheight.$erreur.'" rows="'.$this->size().'" id="'.$this->id().'">';
					foreach($texte as $option) {
						$html.= $option.chr(13);
					}
					$html.= '</textarea>';
					$html.= '<select multiple name="'.$postNameTableau.'" style="display:none">';
						$html.= call_user_func($this->complement(), $this->value());
					$html.= '</select>';
				}
				else {
					//afficher champs "fake" <text> et <hidden> à la place du select
					$html.= '<input type="text"'.$enable.' class="form-control'.$cheight.$cclass.$erreur.'" id="'.$this->id().'" value="'.$texte[0].'" />';
					$html.= '<input type="hidden" name="'.$postNameTableau.'" value="'.$valeur[0].'" />';
				}
				$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
			$html.= '</div>';
		}
		else {
			//affichage du select
			$html = '<div id="'.$this->idzchamp().'" class="mb-3 '.$this->clong().$invisible.$erreur.'">';
				$html.= '<select'.$enable.$size.$multiple.' class="form-control'.$cheight.$cclass.$erreur.'" id="'.$this->id().'" name="'.$postNameTableau.'" '.$this->javascript().'>';
					$html.= call_user_func($this->complement(), $this->value());
				$html.= '</select>';
				$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
			$html.= '</div>';
		}
		return $html;
	}

	//dessin du label online
	private function _drawLabelOnline() {
		//label online
		($this->erreur() == true) ? $erreur = ' danger-color' : $erreur = '';
		$labelHelp = '';
		if ($this->labelHelp() != '') {
			$labelHelp = ' data-toggle="tooltip" title="'.htmlspecialchars($this->labelHelp()).'"';
			($this->labelHelpPos() != '') ? $labelHelp.= ' data-placement="'.$this->labelHelpPos().'"' : $labelHelp.= ' data-placement="auto"';
			($this->labelHelpHtml() == true) ? $labelHelp.= ' data-html="true"' : $labelHelp.= '';
		}
		$style = '';
		$html = '<div id="'.$this->idztitre().'" class="text-'.$this->lalign().' '.$this->lclass().'"'.$style.'>';
//			if ($labelHelp == '') {
//				//il n'y a pas d'aide sur le label
//				$html.= '<label class="mb-0 '.$erreur.'" for="'.$this->id().'">'.$this->label().'</label>';
//			}
//			else {
//				//on ajoute une aide sur le label
				$html.= '<label class="mb-0 '.$erreur.'" for="'.$this->id().'">';
					$html.= '<span'.$labelHelp.'>'.$this->label().'</span>';
				$html.= '</label>';
//			}
		$html.= '</div>';
		return $html;
	}

	//dessin du champ online
	private function _drawChampOnline($enable) {
		//champ online
		($this->_size > 1) ? $size = ' size="'.$this->_size.'"' : $size = '';
		($this->_multiple) ? $multiple = ' multiple' : $multiple = '';
		($this->postIsTab()) ? $postNameTableau = $this->postName().'[]' : $postNameTableau = $this->postName();
		($this->cclass() != '') ? $cclass = ' '.$this->cclass() : $cclass = '';
		if ($enable != '') $cclass = '';		//annule la personnalisation de la classe du select si champ disabled ou readonly
		($this->cheight() != '') ? $cheight = ' form-control-'.$this->cheight() : $cheight = '';
		($this->erreur() == true) ? $erreur = ' is-invalid' : $erreur = '';
		($this->liberreurHelp() != '') ? $libErreurHelp = ' title="'.$this->liberreurHelp().'"' : $libErreurHelp = '';

		//ATTENTION : le mode "readonly" sur un INPUT SELECT ne fonctionne pas et reste disponible pour l'utilisateur.
		//Pour simuler le "readonly" on remplace le SELECT par 2 champs : 
		//1 champ TEXT qui ne sert qu'à afficher le texte correspondant à la valeur d'option <option>text</option>
		//1 champ hidden qui a le même nom que le SELECT qui contiendra la valeur de l'option (option value)
		//Ainsi, non seulement on ne peut plus changer l'option mais en plus le getData() renvoie la valeur même sur le "readonly".
		//Dans le cas d'un SELECT MULTIPLE, le subterfuge consiste à écrire les valeurs dans un <textarea> readonly de même taille 
		//et de faire envoyer les données par un SELECT MULTIPLE caché
		if ($this->readonly()) {
			//recharger les options de la liste avec valeur en cours
			$liste = call_user_func($this->complement(), $this->value());
			//recuperer le(s) libellé(s) et la(les) valeur(s) selected par analyse des tags <option>
			$tabOptions = explode('</option>', $liste);
			array_pop($tabOptions);
			foreach($tabOptions as $indice => $option) {
				if (strpos($option, 'selected') !== false) {
					//recuperation des valeurs (<value>)
					$valeur[] = $this->getBetween('value="', '" selected', $option);
					//recuperation des textes (<option>)
					$texte[] = $this->getBetween('>', '', $option);
				}
			}
			//afficher champs "fake" text et hidden à la place du select
			$html = '<div id="'.$this->idzchamp().'">';
				if ($this->_multiple == true) {
					//afficher champs "fake" <textarea> et <select multiple hidden> à la place du select multiple
					$html.= '<textarea'.$enable.' class="form-control'.$cheight.$erreur.'" rows="'.$this->size().'" id="'.$this->id().'">';
					foreach($texte as $option) {
						$html.= $option.chr(13);
					}
					$html.= '</textarea>';
					$html.= '<select multiple name="'.$postNameTableau.'" style="display:none">';
						$html.= call_user_func($this->complement(), $this->value());
					$html.= '</select>';
				}
				else {
					//afficher champs "fake" <text> et <hidden> à la place du select
					$html.= '<input type="text"'.$enable.' class="form-control'.$cheight.$cclass.$erreur.'" id="'.$this->id().'" value="'.$texte[0].'" />';
					$html.= '<input type="hidden" name="'.$postNameTableau.'" value="'.$valeur[0].'" />';
				}
				$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
			$html.= '</div>';
		}
		else {
			//affichage du <select>
			$html = '<div id="'.$this->idzchamp().'">';
				$html.= '<select'.$enable.$size.$multiple.' class="form-control'.$cheight.$cclass.$erreur.'" id="'.$this->id().'" name="'.$postNameTableau.'" '.$this->javascript().'>';
					$html.= call_user_func($this->complement(), $this->value());
				$html.= '</select>';
				$html.= '<p class="form_error"'.$libErreurHelp.'>'.$this->libErreur().'</p>';
			$html.= '</div>';
		}
		return $html;
	}

	//fonction privée qui test les erreurs éventuelles de construction du radio
	private function _testConstructionErreur() {
		//test si le paramatre 'lpos' est valide
		if (!in_array($this->lpos(), array('before', 'after'))) {
			$this->setErreur(true);
			$this->setLiberreur('Paramètre \'lpos\' => \''.$this->lpos().'\' inaproprié pour le champ select \''.$this->idField().'\'');
			$this->setLiberreurHelp('Paramètre \'lpos\' obligatoire. Choisir parmi \'before\' ou \'after\'');
			$this->setLpos('before');
		}
		//test si le paramètre cheight est cohérent
		if (!in_array($this->cheight(), array('', 'lg', 'sm'))) {
			$this->setErreur(true);
			$this->setLiberreur('Paramètre \'cheight\' => \''.$this->cheight().'\' inaproprié pour le champ select \''.$this->idField().'\'');
			$this->setLiberreurHelp('Paramètre \'cheight\' optionnel. Choisir parmi \'sm\' ou \'lg\'');
		}
		//test si la fonction callback de remplissage du select existe
		if (!function_exists($this->complement())) {
			$this->setErreur(true);
			$this->setLiberreur('Fonction callback \''.$this->complement().'\' inexistante pour le champ select \''.$this->idField().'\'');
			$this->setLiberreurHelp('Il est obligatoire de fournir le nom de la fonction de remplissage du select au paramètre \'complement\'. Cette fonction doit également exister !');
		}
		return $this->erreur();
	}

	//-----------------------------
	//dessin du selecteur
	//-----------------------------
	public function draw($enabled) {
		$html = '';

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