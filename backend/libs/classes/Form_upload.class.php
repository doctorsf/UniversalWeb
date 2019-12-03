<?php
//==============================================================
// CLASSE : Form_upload
//--------------------------------------------------------------
// Formulaire d'upload de fichiers
//==============================================================

class Form_upload extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	//type mime des fichiers autorisés (pour liste complète voir http://www.sitepoint.com/web-foundations/mime-types-complete-list/)
	//tableau dont la clé est le type mime et qui definit un autre tableau comportant l'extention du fichier correspondant et le poids maxi d'upload
	private $_tabAutorises = array('text/csv' => array('.csv', 60000000), 
								  'text/plain' => array('.csv', 60000000), 
								  'text/plain' => array('.txt', 10000000), 
								  'application/vnd.ms-excel' => array('.csv', 60000000));

	//======================================
	// Méthodes privées
	//======================================
	// initialisation des données de travail	
	//--------------------------------------

	protected function initDonnees() {
		$this->_tab_donnees['upload'] = '';
	}

	//----------------------------------------
	// construction des champs du formulaire
	//----------------------------------------

	protected function construitChamps() {
		parent::construitChamps();

		//champ contenant le nom du fichier à uploader (visible)
		//Comme il est impossible de changer le look d'un champ de type input 'file' on procède à un subterfuge : la création d'un champ texte
		//et d'un bouton, tous les deux fictifs qui recopient (par code javascript) les infos du champ 'file' que du coup on va cacher.
		$label = '<a href="javascript:void(0)" role="button" onclick="document.getElementById(\'idFile\').click();">';
		$label.= '<span class="far fa-file"></span>';
		$label.= '</a>';
		$this->createField('area', 'upload', array(
			'newLine' => true,											//nouvelle ligne ? false par défaut
			'dbfield' => 'upload',										//retour de la saisie
			'inputType' => 'text',										//type d'input
			'design' => 'online',										//inline (defaut) / online
			'label' => 'Fichiers ',										//label
			'labelPlus' => $label,										//addon au label
			'labelPlusHelp' => 'Sélection des fichiers à Uploader',		//aide sur le label
			'lclass' => '',												//classe du label
			'lpos' => 'before',											//position du label par rapport au champ : before (defaut) / after
			'clong' => 'col-12',										//longueur de la zone de champ
			'cclass' => '',												//classe de la zone de champ
			'maxlength' => 255,											//nb caractres max en saisie
			'spellcheck' => false,										//correction orthographique
			'placeholder' => 'upload fichiers',							//texte pré-affiché
			'value' => $this->_tab_donnees['upload'],					//valeur de la saisie
			'readonly' => true
		));

		$this->createField('separateur', 'sep', array(
				'newLine' => true,										//nouvelle ligne ? false par défaut
				'dbfield' => 'sep',										//retour de la saisie
				'label' => 'Zone de versement',							//libellé du séparateur
				'lclass' => 'font-italic small',						//classe du sé&parateur
				'clong' => 'col-5',										//longueur du séparateur en colonnes bootstrap
				'value' => ''											//valeur de la saisie
			));

		//champ File de selection du fichier à uploader (caché)
		$this->createField('div', 'deb', array(
			'newLine' => true,
			'cclass' => 'col-12 mb-3 px-0 small text-center font-italic font-weight-light'
		));
		
		//champ File de selection du fichier à uploader (caché)
		$this->createField('text', 'file', array(
			'dbfield' => 'file',
			'inputType' => 'file',
			'design' => 'online',
			'label' => '',
			'clong' => 'col-12 px-0',
			'lclass' => '',
			'complement' => $this->_tabAutorises,
			'multiple' => true,
			'accept' => implode(', ', array_unique(array_column($this->_tabAutorises, '0'))), 
			'invisible' => false		
			));

		//champ File de selection du fichier à uploader (caché)
		$this->createField('divfin', 'fin', array(
		));

		//-------------------
		//boutons
		//-------------------

		//construction bouton Submit
		$valueBoutonValidation = 'Uploader';
		$labelBoutonValidation = '<span class="fas fa-upload mr-3"></span>'.$valueBoutonValidation;
		$javascript = '';
		$this->createField('bouton', 'submit', array(
			'newLine' => true,
			'dbfield' => 'bouton',
			'inputType' => 'submit',
			'decalage' => '',
			'label' => $labelBoutonValidation,
			'clong' => 'col-12 col-sm-6 offset-sm-6 col-md-4 offset-md-8',
			'llong' => 'col-12',
			'lclass' => 'btn btn-primary',			//classes graphique du bouton
			'javascript' => $javascript,
			'value' => $valueBoutonValidation
		));

	}

	//======================================
	// Methodes	publiques					
	//======================================
	// Chargement des données depuis la		
	// base de données. reponse requete		
	//--------------------------------------

	//initialisation des données et construction des champs initialisés
	public function init() {
		$this->initDonnees();			//initialisation des données
		$this->construitChamps();		//constuction à vide... (cad avec données d'initiation)
	}

	//recopie toutes les erreurs des champs 'lienX' (cachés) vers les champs 'fileLienX' artificiels et visibles
	public function recopieErreurs() {
		if ($this->field('file')->erreur()) {
			$this->field('upload')->setErreur(true);
			$this->field('upload')->setLiberreur($this->field('file')->liberreur());
			//on ne veux pas afficher l'erreur sur le 'file' (pour faire plus propre) donc on annule l'affichage de l'erreur (pas l'erreur !)
			$this->field('file')->setLiberreur('');
		}
	}

	//--------------------------------------
	// affichage du formulaire
	//--------------------------------------
	public function afficher() {
		parent::afficher();		//permet d'ajouter des tests de construction du formulaire
		//ATTENTION : les champs disabled ne renvoient aucun POST !!! Donc impossible de récupérer les données depuis une suppression
		$enable = (!(($this->getOperation() == self::CONSULTER) || ($this->getOperation() == self::SUPPRIMER)));
		$chaine = '';

		$chaine.= '<h1 class="mt-2">Upload de fichiers</h1>';
		$chaine.= '<form class="uf" id="idForm" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
			$chaine.= '<fieldset style="border:1px silver solid;padding:1.5rem">';
				$chaine.= $this->draw($enable);
			$chaine.= '</fieldset>';
			$chaine.= '<p class="small">(*) Champ requis (1) Lecture seule</p>';
			//préparation d'une zone d'affichage des items choisis (pas encore uploadés)
			$chaine.= '<div id="drop-show"></div>';
		$chaine.= '</form>';
		return $chaine;
	}
}