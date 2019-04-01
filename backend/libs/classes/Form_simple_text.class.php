<?php
/****************************************************************/
/* CLASSE : Form_simple_text									*/
/*--------------------------------------------------------------*/
/* éè utf-8														*/
/* Formulaire de saisie d'un champ textuel simple				*/
/****************************************************************/

class Form_simple_text extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	//======================================
	// Méthodes protégées					
	//======================================
	// initialisation des données de travail
	//--------------------------------------

	protected function initDonnees() {
		//initialisations supplémentaires
		$this->_tab_donnees['champ'] = '';
		$this->_tab_donnees['callback'] = '';
	}

	//--------------------------------------
	// construction des champs du formulaire
	//--------------------------------------

	protected function construitChamps() {
		//construction des champs du formulaire
		parent::construitChamps();
		$this->createField('text', 'champ', array(
			'newLine' => true,												//nouvelle ligne ? false par défaut
			'dbfield' => 'champ',											//retour de la saisie
			'design' => 'inline',											//inline (defaut) / online
			'inputType' => 'text',											//type d'input
			//'decalage' => 'col-2',										//décallage en colonnes boostrap
			'label' => getLib('SAISIE'),									//label
			'llong' => 'col-2',												//longueur de la zone de titre (seulement pour design inline)
			'lclass' => '',													//classe du label
			'lalign' => 'right',											//left (defaut) / right / center / jutify
			//'labelHelp' => 'Aide sur label',								//aide sur le label
			'lpos' => 'before',												//position du label par rapport au champ : before (defaut) / after
			'clong' => 'col-10',											//longueur de la zone de champ
			//'cclass' => '',												//classe de la zone de champ
			//'maxlength' => 10,											//nb caractres max en saisie
			//'spellcheck' => true,											//correction orthographique
			'placeholder' => getLib('SAISIE'),								//texte pré-affiché
			//'testMatches' => array('REQUIRED', 'CHECK_ALPHA_NOMS'),		//test de la saisie
			'value' => '',													//valeur de la saisie
			//'javascript' => '',											//code javascript associé
			//'enable' => true,												//active, désactive le champ (dbfield renvoie NULL si false)
			//'readonly' => false,											//lecture seule (defield renvoi value si readonly)
			//'invisible' => false											//rend invisible le champ
		));
		$this->createField('hidden', 'callback', array(
			'dbfield' => 'callback',
			'value' => $this->_tab_donnees['callback']
			)); 
		//construction bouton Cancel
		$labelBoutonValidation = getLib('ANNULER');
		$javascript = 'onclick="javascript:history.back();"';
		$this->createField('bouton', 'annule', array(
			'newLine' => true,												//nouvelle ligne ? false par défaut
			//'dbfield' => 'btbouton',										//retour de la saisie
			'decalage' => 'col-2',											//décallage en colonnes boostrap
			'inputType' => 'button',										//submit (defaut), button, reset
			'label' => $labelBoutonValidation,								//label
			'llong' => 'col-12',											//col-xs-12 dessine le bouton sur la totalité de sa largeur clong. Si vide, le bouton est dessiné à la largeur du libellé
			'lclass' => 'btn btn-default',									//classes graphique du bouton
			'clong' => 'col-2 pull-right',									//longueur de la zone de champ (du bouton)
			//'cclass' => '',												//classe personnalisée du bloc de champ
			//'value' => $labelBoutonValidation,							//valeur renvoyée dans dbfield
			'javascript' => $javascript,									//code javascript associé
			//'enable' => true,												//active, désactive le champ (dbfield renvoie NULL si false)
			//'invisible' => false											//rend invisible le champ
		)); 
		//construction bouton Submit
		$labelBoutonValidation = 'OK';
		$javascript = '';
		$this->createField('bouton', 'submit', array(
			//'newLine' => true,											//nouvelle ligne ? false par défaut
			//'dbfield' => 'btbouton',										//retour de la saisie
			//'decalage' => 'col-2',										//décallage en colonnes boostrap
			'inputType' => 'submit',										//submit (defaut), button, reset
			'label' => $labelBoutonValidation,								//label
			'llong' => 'col-12',											//col-xs-12 dessine le bouton sur la totalité de sa largeur clong. Si vide, le bouton est dessiné à la largeur du libellé
			'lclass' => 'btn btn-primary',									//classes graphique du bouton
			'clong' => 'col-2 pull-right',									//longueur de la zone de champ (du bouton)
			//'cclass' => '',												//classe personnalisée du bloc de champ
			//'value' => $labelBoutonValidation,							//valeur renvoyée dans dbfield
			'javascript' => $javascript,									//code javascript associé
			//'enable' => true,												//active, désactive le champ (dbfield renvoie NULL si false)
			//'invisible' => false											//rend invisible le champ
		)); 
	}

	//======================================
	// Methodes	publiques					
	//======================================
	// Chargement des données depuis la		
	// base de données. reponse requete		
	//======================================

	//initialisation des données et construction des champs initialisés
	public function init($callback) {
		$this->initDonnees();			//initialisation des données
		$this->_tab_donnees['callback'] = $callback;
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
		$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
			$chaine.= '<fieldset style="border:1px silver solid;padding:1.5rem">';
				$chaine.= '<h1 style="margin-bottom: 2rem;">'._APP_TITLE_.'</h1>';
				$chaine.= $this->draw($enable);
			$chaine.= '</fieldset>';
			$chaine.= '<p class="small">(*) '.getLib('CHAMP_REQUIS').' (1) '.getLib('LECTURE_SEULE').'</p>';
		$chaine.= '</form>';
		return $chaine;
	}

}