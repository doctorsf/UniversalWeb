<?php
//---------------------------------------------------------------
// CLASSE : Form_input
// Formulaire de saisie standard
//---------------------------------------------------------------
// éè utf-8
// Version : 23.10.2017  (Bootstrap 4 Beta 2)
//---------------------------------------------------------------

class Form_input extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	//======================================
	// Méthodes protégées					
	//======================================
	// initialisation des données de travail
	//--------------------------------------

	protected function initDonnees() {
		//initialisations supplémentaires
		$this->_tab_donnees['form_title'] = '';					//titre du formulaire INPUT
		$this->_tab_donnees['callback'] = '';					//adresse de retour du formulaire INPUT
		$this->_tab_donnees['champs'] = $_SESSION[_APP_INPUT_]['champs'];
	}

	//--------------------------------------
	// méthode privées de construvtion des champs selon leur type
	//--------------------------------------
	
	private function _construitText($nom, $champ) {
		(isset($champ['type'])) ? $type = $champ['type'] : $type = '';
		(isset($champ['newLine'])) ? $newLine = $champ['newLine'] : $newLine = true;
		(isset($champ['design'])) ? $design = $champ['design'] : $design = 'inline';
		(isset($champ['decalage'])) ? $decalage = $champ['decalage'] : $decalage = '';
		(isset($champ['label'])) ? $label = $champ['label'] : $label = '';
		(isset($champ['llong'])) ? $llong = $champ['llong'] : $llong = 'col-2';
		(isset($champ['lclass'])) ? $lclass = $champ['lclass'] : $lclass = '';
		(isset($champ['lalign'])) ? $lalign = $champ['lalign'] : $lalign = 'right';
		(isset($champ['labelHelp'])) ? $labelHelp = $champ['labelHelp'] : $labelHelp = '';
		(isset($champ['lpos'])) ? $lpos = $champ['lpos'] : $lpos = 'before';
		(isset($champ['clong'])) ? $clong = $champ['clong'] : $clong = 'col-10';
		(isset($champ['cclass'])) ? $cclass = $champ['cclass'] : $cclass = '';
		(isset($champ['maxlength'])) ? $maxlength = $champ['maxlength'] : $maxlength = '';
		(isset($champ['spellcheck'])) ? $spellcheck = $champ['spellcheck'] : $spellcheck = true;
		(isset($champ['placeholder'])) ? $placeholder = $champ['placeholder'] : $placeholder = '';
		(isset($champ['testMatches'])) ? $testMatches = $champ['testMatches'] : $testMatches = array();
		(isset($champ['value'])) ? $value = $champ['value'] : $value = '';
		$this->createField('text', $nom, array(
			'newLine' => $newLine,							//nouvelle ligne ? false par défaut
			'dbfield' => $nom,								//retour de la saisie
			'inputType' => 'text',							//type d'input
			'design' => $design,							//inline (defaut) / online
			'decalage' => $decalage,						//décallage en colonnes boostrap
			'label' => $label,								//label
			'llong' => $llong,								//longueur de la zone de titre (seulement pour design inline)
			'lclass' => $lclass,							//classe du label
			'lalign' => $lalign,							//left (defaut) / right / center / jutify
			'labelHelp' => $labelHelp,						//Aide sur label label
			'lpos' => $lpos,								//position du label par rapport au champ : before (defaut) / after
			'clong' => $clong,								//longueur de la zone de champ
			'cclass' => $cclass,							//classe de la zone de champ
			'maxlength' => $maxlength,						//nb caractres max en saisie
			'spellcheck' => $spellcheck,					//correction orthographique
			'placeholder' => $placeholder,					//texte pré-affiché
			'testMatches' => $testMatches,					//test de la saisie
			'value' => $value								//valeur de la saisie
		));
	}

	private function _construitSelect($nom, $champ) {
		(isset($champ['type'])) ? $type = $champ['type'] : $type = 'select';
		(isset($champ['newLine'])) ? $newLine = $champ['newLine'] : $newLine = true;
		(isset($champ['design'])) ? $design = $champ['design'] : $design = 'inline';
		(isset($champ['multiple'])) ? $multiple = $champ['multiple'] : $multiple = false;
		(isset($champ['size'])) ? $size = $champ['size'] : $size = 1;
		(isset($champ['decalage'])) ? $decalage = $champ['decalage'] : $decalage = '';
		(isset($champ['label'])) ? $label = $champ['label'] : $label = '';
		(isset($champ['llong'])) ? $llong = $champ['llong'] : $llong = 'col-2';
		(isset($champ['lclass'])) ? $lclass = $champ['lclass'] : $lclass = '';
		(isset($champ['lalign'])) ? $lalign = $champ['lalign'] : $lalign = 'right';
		(isset($champ['labelHelp'])) ? $labelHelp = $champ['labelHelp'] : $labelHelp = '';
		(isset($champ['lpos'])) ? $lpos = $champ['lpos'] : $lpos = 'before';
		(isset($champ['clong'])) ? $clong = $champ['clong'] : $clong = 'col-10';
		(isset($champ['cclass'])) ? $cclass = $champ['cclass'] : $cclass = '';
		(isset($champ['maxlength'])) ? $maxlength = $champ['maxlength'] : $maxlength = '';
		(isset($champ['spellcheck'])) ? $spellcheck = $champ['spellcheck'] : $spellcheck = true;
		(isset($champ['placeholder'])) ? $placeholder = $champ['placeholder'] : $placeholder = '';
		(isset($champ['testMatches'])) ? $testMatches = $champ['testMatches'] : $testMatches = array();
		(isset($champ['value'])) ? $value = $champ['value'] : $value = '';
		(isset($champ['complement'])) ? $complement = $champ['complement'] : $complement = '';
		$this->createField('select', $nom, array(
			'newLine' => $newLine,							//nouvelle ligne ? false par défaut
			'dbfield' => $nom,								//retour de la saisie
			'design' => $design,							//inline (defaut) / online
			'multiple' => $multiple,						//dit si la selection multiple est autorisée dans ce select (false par défaut)
			'size' => $size,								//hauteur du select en nombre de lignes visibles (1 par défaut)
			'decalage' => $decalage,						//décallage en colonnes boostrap
			'label' => $label,								//label
			'llong' => $llong,								//longueur de la zone de titre (seulement pour design inline)
			'lclass' => $lclass,							//classe du label
			'lalign' => $lalign,							//left (defaut) / right / center / jutify
			'labelHelp' => $labelHelp,						//Aide sur label label
			'lpos' => $lpos,								//position du label par rapport au champ : before (defaut) / after
			'clong' => $clong,								//longueur de la zone de champ
			'cclass' => $cclass,							//classe de la zone de champ
			'maxlength' => $maxlength,						//nb caractres max en saisie
			'spellcheck' => $spellcheck,					//correction orthographique
			'placeholder' => $placeholder,					//texte pré-affiché
			'testMatches' => $testMatches,					//test de la saisie
			'value' => $value,								//valeur de la saisie
			'complement' => $complement						//fonction de callback qui doit remplir le select
		));
	}

	private function _construitComment($nom, $champ) {
		(isset($champ['type'])) ? $type = $champ['type'] : $type = 'comment';
		(isset($champ['newLine'])) ? $newLine = $champ['newLine'] : $newLine = true;
		(isset($champ['design'])) ? $design = $champ['design'] : $design = 'inline';
		(isset($champ['decalage'])) ? $decalage = $champ['decalage'] : $decalage = '';
		(isset($champ['label'])) ? $label = $champ['label'] : $label = '';
		(isset($champ['llong'])) ? $llong = $champ['llong'] : $llong = 'col-2';
		(isset($champ['lclass'])) ? $lclass = $champ['lclass'] : $lclass = '';
		(isset($champ['lalign'])) ? $lalign = $champ['lalign'] : $lalign = 'right';
		(isset($champ['labelHelp'])) ? $labelHelp = $champ['labelHelp'] : $labelHelp = '';
		(isset($champ['lpos'])) ? $lpos = $champ['lpos'] : $lpos = 'before';
		(isset($champ['clong'])) ? $clong = $champ['clong'] : $clong = 'col-10';
		(isset($champ['cclass'])) ? $cclass = $champ['cclass'] : $cclass = '';
		(isset($champ['border'])) ? $border = $champ['border'] : $border = '';
		(isset($champ['value'])) ? $value = $champ['value'] : $value = '';
		$this->createField('comment', $nom, array(
			'newLine' => $newLine,							//nouvelle ligne ? false par défaut
			'dbfield' => $nom,								//retour de la saisie
			'design' => $design,							//inline (defaut) / online
			'decalage' => $decalage,						//décallage en colonnes boostrap
			'label' => $label,								//label
			'llong' => $llong,								//longueur de la zone de titre (seulement pour design inline)
			'lclass' => $lclass,							//classe du label
			'lalign' => $lalign,							//left (defaut) / right / center / jutify
			'labelHelp' => $labelHelp,						//Aide sur label label
			'lpos' => $lpos,								//position du label par rapport au champ : before (defaut) / after
			'clong' => $clong,								//longueur de la zone de champ
			'cclass' => $cclass,							//classe de la zone de champ
			'border' => $border,							//defaut : false. false / true (encadrement par défaut) ou bordure personnnalisée
			'value' => $value								//valeur de la saisie
		));
	}

	private function _construitcheckbox($nom, $champ) {
		(isset($champ['type'])) ? $type = $champ['type'] : $type = 'checkbox';
		(isset($champ['newLine'])) ? $newLine = $champ['newLine'] : $newLine = true;
		(isset($champ['groupName'])) ? $groupName = $champ['groupName'] : $groupName = '';
		(isset($champ['design'])) ? $design = $champ['design'] : $design = 'inline';
		(isset($champ['dpos'])) ? $dpos = $champ['dpos'] : $dpos = 'alone';
		(isset($champ['titre'])) ? $titre = $champ['titre'] : $titre = '';
		(isset($champ['tlong'])) ? $tlong = $champ['tlong'] : $tlong = '';
		(isset($champ['tclass'])) ? $tclass = $champ['tclass'] : $tclass = '';
		(isset($champ['talign'])) ? $talign = $champ['talign'] : $talign = 'right';
		(isset($champ['titreHelp'])) ? $titreHelp = $champ['titreHelp'] : $titreHelp = '';
		(isset($champ['decalage'])) ? $decalage = $champ['decalage'] : $decalage = '';
		(isset($champ['label'])) ? $label = $champ['label'] : $label = '';
		(isset($champ['llong'])) ? $llong = $champ['llong'] : $llong = 'col-2';
		(isset($champ['lclass'])) ? $lclass = $champ['lclass'] : $lclass = '';
		(isset($champ['lalign'])) ? $lalign = $champ['lalign'] : $lalign = 'right';
		(isset($champ['labelHelp'])) ? $labelHelp = $champ['labelHelp'] : $labelHelp = '';
		(isset($champ['lpos'])) ? $lpos = $champ['lpos'] : $lpos = 'before';
		(isset($champ['clong'])) ? $clong = $champ['clong'] : $clong = 'col-10';
		(isset($champ['cclass'])) ? $cclass = $champ['cclass'] : $cclass = '';
		(isset($champ['border'])) ? $border = $champ['border'] : $border = '';
		(isset($champ['value'])) ? $value = $champ['value'] : $value = '1';
		(isset($champ['valueInverse'])) ? $valueInverse = $champ['valueInverse'] : $valueInverse = '0';
		(isset($champ['checked'])) ? $checked = $champ['checked'] : $checked = false;
		$this->createField('checkbox', 'compris', array(
			'newLine' => $newLine,							//nouvelle ligne ? false par défaut
			'groupName' => $groupName,						//le groupName est facultatif si dpos alone
			'dbfield' => $nom,								//retour de la saisie
			'design' => $design,							//inline (defaut) / online
			'dpos' => $dpos,								//first / last / inter / alone
			'titre'	=> $titre,								//on veut un titre (premier élément seulement, sans effet sur les autres)
			'tlong'	=> $tlong,								//longueur du titre en colonnes boostrap
			'talign' => $talign,							//alignement du titre
			'tclass' => $tclass,							//style du titre
			'titreHelp'	=> $titreHelp,						//aide du titre
			'decalage' => $decalage,						//décallage en colonnes boostrap
			'label' => $label,								//label
			'lclass' => $lclass,							//classe du label
			'labelHelp' => $labelHelp,						//Aide sur label label
			'lpos' => $lpos,								//position du label par rapport au champ : before (defaut) / after
			'clong' => $clong,								//longueur de la zone de champ
			'cclass' => $cclass,							//classe de la zone de champ
			'border' => $border,							//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
			'value' => $value,								//valeur de la saisie
			'valueInverse' => $valueInverse,				//valeur renvoyée dans dbfield si checkbox non cliquée
			'checked' => $checked							//cochée (true) / décochée (false)
		));
	}

	private function _construitHidden($nom, $champ) {
		(isset($champ['value'])) ? $value = $champ['value'] : $value = '';
		$this->createField('hidden', $nom, array(
			'dbfield' => $nom,								//retour de la saisie
			'value' => $value								//valeur de la saisie
		)); 
	}

	//--------------------------------------
	// construction des champs du formulaire
	//--------------------------------------

	protected function construitChamps() {
		//construction des champs du formulaire
		parent::construitChamps();

		//construction des champs
		foreach($this->_tab_donnees['champs'] as $key => $champ) {
			//au cas ou on ne founit pas le 'type' de champ -> 'text' par défaut
			(isset($champ['type'])) ? $type = $champ['type'] : $type = 'text';
			$method = '_construit'.ucFirst($type);
			if (method_exists($this, $method)) {
				$this->$method($key, $champ);
			}
		}

		//ajout du champ 'callback'
		$this->createField('hidden', 'callback', array(
			'dbfield' => 'callback',
			'value' => $this->_tab_donnees['callback']
		));
		//ajout du champ 'form_title' (titre du formulaire) 
		$this->createField('hidden', 'form_title', array(
			'dbfield' => 'form_title',
			'value' => $this->_tab_donnees['form_title']
		));

		//construction bouton Cancel
		$javascript = 'onclick="javascript:history.back();"';
		$this->createField('bouton', 'annule', array(
			'newLine' => true,												//nouvelle ligne ? false par défaut
			'decalage' => 'col-2',											//décallage en colonnes boostrap
			'inputType' => 'button',										//submit (defaut), button, reset
			'label' => getLib('ANNULER'),									//label
			'llong' => 'col-12',											//col-12 dessine le bouton sur la totalité de sa largeur clong. Si vide, le bouton est dessiné à la largeur du libellé
			'lclass' => 'btn btn-secondary',								//classes graphique du bouton
			'clong' => 'col-2 ml-auto',										//longueur de la zone de champ (du bouton)
			'javascript' => $javascript										//code javascript associé
		)); 
		//construction bouton Submit
		$this->createField('bouton', 'submit', array(
			'inputType' => 'submit',										//submit (defaut), button, reset
			'label' => 'OK',												//label
			'llong' => 'col-12',											//col-12 dessine le bouton sur la totalité de sa largeur clong. Si vide, le bouton est dessiné à la largeur du libellé
			'lclass' => 'btn btn-primary',									//classes graphique du bouton
			'clong' => 'col-2'												//longueur de la zone de champ (du bouton)
		)); 
	}

	//======================================
	// Methodes	publiques					
	//======================================
	// Chargement des données depuis la		
	// base de données. reponse requete		
	//======================================

	//initialisation des données et construction des champs initialisés
	// $input tableau de 3 valeur : 
	//	[titre] => titre donné au formulaire
	//	[callback] => url de retour à la validation du formulaire
	//	[champs] => tableau des champs paramétrés participants à la construction du formulaire
	//		Un champ paramétré est lui aussi un tableau qui doit avoir cette forme : 
    //		[nom] => Array 
	//		(
    //			[type] => text
    //          [label] => Nom de la sélection
    //          [labelHelp] => Saisir le nom de la sélection correspondante
    //          [value] => Pol-W2k12-OS-CIS-1.2_
	//			etc.
    //		)
	public function init() {
		$this->initDonnees();			//initialisation des données
		$this->_tab_donnees['form_title'] = $_SESSION[_APP_INPUT_]['form_title'];
		$this->_tab_donnees['callback'] = $_SESSION[_APP_INPUT_]['callback'];
		$this->_tab_donnees['champs'] = $_SESSION[_APP_INPUT_]['champs'];
		$this->construitChamps();		//constuction à vide... (cad avec données d'initiation)
	}

	//--------------------------------------
	// affichage du formulaire				
	//--------------------------------------
	public function afficher() {
		parent::afficher();		//permet d'ajouter des tests de construction du formulaire
		//ATTENTION : les champs disabled ne renvoient aucun POST !!! Donc impossible de récupérer les données depuis une suppression
		$enable = true;

		$chaine = '';
		$chaine.= '<h1 class="display-4">'.$this->field('form_title')->value().'</h1>';
		$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
			$chaine.= '<fieldset style="border:1px silver solid;padding:1.5rem">';
				$chaine.= $this->draw($enable);
			$chaine.= '</fieldset>';
			$chaine.= '<p class="small">(*) '.getLib('CHAMP_REQUIS').' (1) '.getLib('LECTURE_SEULE').'</p>';
		$chaine.= '</form>';
		return $chaine;
	}

}