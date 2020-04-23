<?php
//==============================================================
// CLASSE : Form_import
//--------------------------------------------------------------
// Formulaire d'import d'un fichier CSV
//==============================================================

class Form_import_csv extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	//type mime des fichiers autorisés (pour liste complète voir http://www.sitepoint.com/web-foundations/mime-types-complete-list/)
	//tableau dont la clé est le type mime et qui definit un autre tableau comportant l'extention du fichier correspondant et le poids maxi d'upload
	private $_tabAutorises = array('text/csv' => array('.csv', 60000000), 
								  'text/plain' => array('.csv', 60000000), 
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

		$this->createField('comment', 'commentaire', array(
			'newline' => true,
			'dbfield' => 'commentaire',
			'design' => 'online',
			'label' => 'ATTENTION',
			'clong' => 'col',
			'value' => getLib('IMPORT_REGLES')
		));

		//Liens à Uploader
			//champ contenant le nom du fichier à uploader (visible)
			//Comme il est impossible de changer le look d'un champ de type input 'file' on procède à un subterfuge : la création d'un champ texte
			//et d'un bouton, tous les deux fictifs qui recopient (par code javascript) les infos du champ 'file' que du coup on va cacher.		
			$label = '<a id="idBtUpload" class="btn text-primary p-0"><span class="fas fa-file-import"></span></a>';
			$this->createField('text', 'upload', array(
				'newLine' => true,											//nouvelle ligne ? false par défaut
				'dbfield' => 'upload',										//retour de la saisie
				'inputType' => 'text',										//type d'input
				'design' => 'online',										//inline (defaut) / online
				'label' => getLib('FICHIER'),								//label
				'labelPlus' => $label,										//addon au label
				'labelPlusHelp' => getLib('SELECTION_FICHIER_UPLOADER'),	//aide sur le label
				'lclass' => '',												//classe du label
				'lpos' => 'before',											//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-12',										//longueur de la zone de champ
				'cclass' => '',												//classe de la zone de champ
				'maxlength' => 255,											//nb caractres max en saisie
				'spellcheck' => false,										//correction orthographique
				'placeholder' => getLib('IMPORT_FICHIER_CSV_X'),			//texte pré-affiché
				'value' => $this->_tab_donnees['upload'],					//valeur de la saisie
				'readonly' => true
			));
			//champ File de selection du fichier à uploader (caché)
			$this->createField('text', 'file', array(
				'dbfield' => 'file',
				'inputType' => 'file',
				'complement' => $this->_tabAutorises,
				'accept' => implode(', ', array_unique(array_column($this->_tabAutorises, '0'))),
				'multiple' => false,
				'invisible' => true		
				));

		//-------------------
		//boutons
		//-------------------

		//construction bouton Submit
		$valueBoutonValidation = getLib('UPLOADER');
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
			'value' => $valueBoutonValidation,
			'enable' => false						//par defaut le bouton est grisé car aucun fichier encore choisi
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
		if ($this->field('file'.$i)->erreur()) {
			$this->field('upload'.$i)->setErreur(true);
			$this->field('upload'.$i)->setLiberreur($this->field('file'.$i)->liberreur());
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

		$chaine.= '<div class="container-lg px-0">';
			$chaine.= '<h1 class="mt-2">'.getLib('IMPORT_CSV').'</h1>';
			$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
				$chaine.= '<fieldset class="border p-3">';
					$chaine.= $this->draw($enable);
				$chaine.= '</fieldset>';
				$chaine.= '<p class="small">(*) '.getLib('CHAMP_REQUIS').' (1) '.getLib('LECTURE_SEULE').'</p>';
			$chaine.= '</form>';
		$chaine.= '</div>';
		return $chaine;
	}
}