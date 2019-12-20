<?php
//==============================================================
// CLASSE : Form_import
//--------------------------------------------------------------
// Formulaire d'import d'un fichier CSV
//==============================================================

class Form_import_csv extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire
	private $_nb_upload_lines = 1;		//nombre de lignes d'upload

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
		for ($i = 1; $i <= $this->_nb_upload_lines; $i++) {
			$this->_tab_donnees['upload'.$i] = '';
		}
	}

	//----------------------------------------
	// construction des champs du formulaire
	//----------------------------------------

	protected function construitChamps() {
		parent::construitChamps();

		echo '<script>';
		echo 'function getfile(original) {';
		echo '	document.getElementById(original).click();';
		echo '}';
		echo 'function getvalue(original, recopie) {';
		//ne pas utiliser document.getElementById(original).value qui selon le navigateur employé renvoie le fakepath ou non
		echo '	document.getElementById(recopie).value = document.getElementById(original).files[0].name;';
		echo '}';
		echo '</script>';

		$this->createField('comment', 'commentaire', array(
			'newline' => true,
			'dbfield' => 'commentaire',
			'design' => 'online',
			'label' => 'ATTENTION',
			'clong' => 'col',
			'value' => 'Chaque fichier d\'importation doit être limité à 1000 lignes. Son format doit être conforme au modèle spécifié dans menu "Import" puis "Modèle de l\'Import". Reportez-vous à ce modèle pour forger le fichier d\'importation CSV attendu (séparateur par point-virgule)',
		));

		//Liens à Uploader
		for ($i = 1;  $i <= $this->_nb_upload_lines; $i++) {
			//champ contenant le nom du fichier à uploader (visible)
			//Comme il est impossible de changer le look d'un champ de type input 'file' on procède à un subterfuge : la création d'un champ texte
			//et d'un bouton, tous les deux fictifs qui recopient (par code javascript) les infos du champ 'file' que du coup on va cacher.		
			$label = '<a href="javascript:void(0)" role="button" onclick="getfile(\'idFile'.$i.'\');">';
			$label.= '<span class="far fa-file"></span>';
			$label.= '</a>';
			$this->createField('text', 'upload'.$i, array(
				'newLine' => true,											//nouvelle ligne ? false par défaut
				'dbfield' => 'upload'.$i,									//retour de la saisie
				'inputType' => 'text',										//type d'input
				'design' => 'online',										//inline (defaut) / online
				'label' => 'Fichier '.$i,									//label
				'labelPlus' => $label,										//addon au label
				'labelPlusHelp' => 'Sélection du fichier à Uploader',		//aide sur le label
				'lclass' => '',												//classe du label
				'lpos' => 'before',											//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-12',										//longueur de la zone de champ
				'cclass' => '',												//classe de la zone de champ
				'maxlength' => 255,											//nb caractres max en saisie
				'spellcheck' => false,										//correction orthographique
				'placeholder' => 'import fichier CSV '.$i,					//texte pré-affiché
				'value' => $this->_tab_donnees['upload'.$i],				//valeur de la saisie
				'readonly' => true
			));
			//champ File de selection du fichier à uploader (caché)
			$this->createField('text', 'file'.$i, array(
				'dbfield' => 'file'.$i,
				'inputType' => 'file',
				'complement' => $this->_tabAutorises,
				'javascript' => 'onChange="getvalue(\'idFile'.$i.'\', \'idUpload'.$i.'\');"',
				'multiple' => false,
				'invisible' => true		
				));
		}

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
		$uneErreur = false;
		for ($i = 1; $i <= $this->_nb_upload_lines; $i++) {
			if ($this->field('file'.$i)->erreur()) {
				$this->field('upload'.$i)->setErreur(true);
				$this->field('upload'.$i)->setLiberreur($this->field('file'.$i)->liberreur());
				$uneErreur = true;
			}
		}
		if ($uneErreur) {
			//si on a trouvé une erreur on vide les précédentes saisies car les champs de type 'file' ne conservent pas la saisie précédente
			//par cette action, l'utilisateur va être obligé de reselectionner les fichiers qui n'étaient pas en erreur
			for ($i = 1; $i <= $this->_nb_upload_lines; $i++) {
				if (!$this->field('file'.$i)->erreur()) {
					$this->field('upload'.$i)->setValue('');
				}
			}
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
			$chaine.= '<h1 class="mt-2">Import CSV</h1>';
			$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
				$chaine.= '<fieldset class="border p-3">';
					$chaine.= $this->draw($enable);
				$chaine.= '</fieldset>';
				$chaine.= '<p class="small">(*) Champ requis (1) Lecture seule</p>';
			$chaine.= '</form>';
		$chaine.= '</div>';
		return $chaine;
	}
}