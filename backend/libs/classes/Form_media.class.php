<?php
//==============================================================
// CLASSE : Form_media
//--------------------------------------------------------------
// Formulaire d'upload et gestion des media du site
//==============================================================

//fonction de remplissage de la liste déroulante path
function formMediaFillPath($defaut) {
	$texte = '';
	for ($i = 0; $i < count(_PATHS_MEDIA_AUTORISES_); $i++) {
		$cle = array_keys(_PATHS_MEDIA_AUTORISES_)[$i];
		($defaut == $cle) ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="'.$cle.'"'.$selected.'>'._PATHS_MEDIA_AUTORISES_[$cle].'</option>';
	}
	return $texte;
}

class Form_media extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	//type mime des fichiers autorisés (pour liste complète voir http://www.sitepoint.com/web-foundations/mime-types-complete-list/)
	//tableau dont la clé est le type mime et qui definit un autre tableau comportant l'extention du fichier correspondant et le poids maxi d'upload
	private $_tabAutorises = array(
		'image/gif' => array('.gif', 250000), 
		'image/jpeg' => array('.jpeg', 250000), 
		'image/jpeg' => array('.jpg', 250000), 
		'image/pjpeg' => array('.jpg', 250000), 
		'image/png' => array('.png', 250000)
	);

	//regex des extensions autorisées à afficher
	private $_extensions = '#^(?!small).*(\.(jpg|jpeg|gif|png)$)#';

	//chemins possibles proposés par l'applicaton (définis dans defines.inc.php)
	private $_paths = _PATHS_MEDIA_AUTORISES_;

	//méthode d'ouverture des images 
	//permet de spécifier comment les images sont chargées. Si il s'agit d'ouvrir des images accessibles depuis une url externe alors passer cette url. 
	//Pour afficher des images qui ne sont pas accessible via une url, passer une fonction chargée de l'ouverture.
	//Si les media sont ceux du backend, renvoyer simplement $image. 
	private function _openingMethod($image) {
		//return $image;												//cas des images du backend
		//return 'createanyimage.php?img='.$image;						//cas d'images hors domaine et hors internet
		return str_replace(_PATH_FRONT_END_, _URL_FRONT_END_, $image);	//cas d'images hors domaine mais sur internet
	}

	//======================================
	// Méthodes privées
	//======================================
	// initialisation des données de travail	
	//--------------------------------------

	protected function initDonnees() {
		$this->_tab_donnees['upload'] = '';
		//le chemin de media par défaut est le premier autorisé
		if (!isset($_SESSION['mediaPath'])) $_SESSION['mediaPath'] = array_keys($this->_paths)[0];
		$this->_tab_donnees['path'] = $_SESSION['mediaPath'];
	}

	//----------------------------------------
	// construction des champs du formulaire
	//----------------------------------------

	protected function construitChamps() {
		parent::construitChamps();

		//selecteur de chemins pre-chargés (et donc autorisés) d'accès aux media
		$javascript = 'onChange="document.getElementById(\'idSubmitPath\').click();"';
		$this->createField('select', 'path', array(
			'newLine' => true,											//nouvelle ligne ? false par défaut
			'dbfield' => 'path',										//retour de la saisie
			'design' => 'online',										//inline (defaut) / online
			'label' => getLib('MEDIA_PATH'),							//label
			'labelHelp' => getLib('MEDIA_PATH_HELP'),					//aide sur le label
			'clong' => 'col-12',										//longueur de la zone de champ
			'value' => $this->_tab_donnees['path'],						//valeur de la saisie
			'complement' => 'formMediaFillPath',						//fonction de callback qui doit remplir le select
			'javascript' => $javascript
		));

		//champ caché contenant le chemin réel d'accès
		$this->createField('hidden', 'realpath', array(
			'dbfield' => 'realpath',									//retour de la saisie
			'value' => $this->_paths[$this->_tab_donnees['path']]		//valeur de la saisie
		));

		//creation d'un bouton invisible qui va servir de déclencheur sur changement
		//de valeur de la liste deroulante des chemins possibles proposés
		$this->createField('bouton', 'submitPath', array(
			'newLine' => true,
			'dbfield' => 'btPath',
			'inputType' => 'submit',
			'label' => 'path',
			'clong' => 'col-12',
			'llong' => 'col-12',
			'lclass' => 'btn btn-primary',
			'value' => 'OK',
			'invisible' => true
		));

		//champ FAKE contenant le nom du fichier à uploader (visible)
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
			'label' => getLib('FICHIER').'s',							//label
			'labelPlus' => $label,										//addon au label
			'labelPlusHelp' => getLib('SELECTION_MEDIA_UPLOAD'),		//aide sur le label
			'lclass' => '',												//classe du label
			'lpos' => 'before',											//position du label par rapport au champ : before (defaut) / after
			'clong' => 'col-12',										//longueur de la zone de champ
			'cclass' => '',												//classe de la zone de champ
			'maxlength' => 255,											//nb caractres max en saisie
			'spellcheck' => false,										//correction orthographique
			'placeholder' => getLib('UPLOAD_MEDIA'),					//texte pré-affiché
			'value' => $this->_tab_donnees['upload'],					//valeur de la saisie
			'readonly' => true
		));

		$this->createField('separateur', 'sep', array(
			'newLine' => true,											//nouvelle ligne ? false par défaut
			'dbfield' => 'sep',											//retour de la saisie
			'label' => getLib('ZONE_DE_TELEVERSEMENT'),					//libellé du séparateur
			'lclass' => 'font-italic small',							//classe du sé&parateur
			'clong' => 'col-6',											//longueur du séparateur en colonnes bootstrap
			'value' => ''												//valeur de la saisie
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
		$valueBoutonValidation = getLib('UPLOADER');
		$labelBoutonValidation = '<span class="fas fa-upload mr-3"></span>'.$valueBoutonValidation;
		$javascript = '';
		$this->createField('bouton', 'submit', array(
			'newLine' => true,
			'dbfield' => 'bouton',
			'inputType' => 'submit',
			'decalage' => '',
			'label' => $labelBoutonValidation,
			'flexLine' => 'flex-row-reverse', 
			'clong' => 'col-12 col-md-4',
			'llong' => 'col-12',
			'lclass' => 'btn btn-primary',			//classes graphique du bouton
			'javascript' => $javascript,
			'value' => $valueBoutonValidation,
			'enable' => false						//par defaut le bouton est grisé car aucun media encore choisi
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

	//changement du chemin d'acces aux media
	public function changePath($newPath) {
		//on modifie le chemin cible courant
		$this->field('path')->setValue($newPath);
		$this->field('realpath')->setValue($this->_paths[$newPath]);
		//on efface aussi les éventuelles selections précédentes
		$this->field('upload')->setValue('');
		$this->field('file')->setValue('');
	}

	//avant de partir
	protected function doUltimateThings(&$donnees) {
		//on corrige le nombre de fichier uploadé si aucun n'a été choisi (sinon il met 1)
		if ($this->field('upload')->value() == '') $donnees['file']['nbFiles'] = 0;
	}

	//--------------------------------------
	// affichage du formulaire
	//--------------------------------------
	public function afficher() {
		parent::afficher();
		//ATTENTION : les champs disabled ne renvoient aucun POST !!! Donc impossible de récupérer les données depuis une suppression
		$enable = true;
		$chaine = '';

		$chaine.= '<div class="container-lg px-0">';
			$chaine.= '<h1><span class="fas fa-photo-video mr-2" data-toggle="tooltip" title="Media"></span>'.geTLib('MEDIA').'</h1>';
			$chaine.= '<p class="lead">'.getLib('MEDIA_DU_SITE').'<p>';
		$chaine.= '</div>';

		$chaine.= '<div class="container-lg px-0">';
			$chaine.= '<div class="card">';
				//$chaine.= '<div class="card-header">';
				//$chaine.= '</div>';
				$chaine.= '<div class="card-body">';

					//----------- Affichage des media ---------------------------
					$dummy = litRepertoire($this->_paths[$this->field('path')->value()], $lesMedias, $this->_extensions);
					//DEBUG_('lesMedias', $lesMedias);
					if (count($lesMedias) > 0) {
						//row de zone d'affichage des photos
						$chaine.= '<div class="row text-center">';
							foreach($lesMedias as $indice => $media) {
								$imageFinale = $this->_paths[$this->field('path')->value()].$media;
								$urlMedia = $this->_openingMethod($imageFinale);
								$chaine.= '<div class="col-6 col-sm-4 col-md-3">';
									$chaine.= '<a onclick="activate('.($indice).', '.count($lesMedias).')" href="#myGallery" data-toggle="modal" data-target="#myModal">';
									$chaine.= '<figure class="figure img-thumbnail" style="border:3px solid #dee2e6;">';
										$chaine.= '<img class="figure-img img-fluid rounded" src="'.$urlMedia.'" alt="'.getLib('MEDIA_DISPONIBLE').'">';
										$chaine.= '<figcaption class="figure-caption">'.$media.'</figcaption>';
									$chaine.= '</figure>';
									$chaine.= '</a>';
								$chaine.= '</div>';
							}
						$chaine.= '</div>';
					}
					else {
						$chaine.= '<div class="alert alert-warning" role="alert">Aucun média disponible&hellip;</div>';
					}

					$chaine.= '<hr />';

					//------------ Affichage du formulaire -----------------------
					$chaine.= '<form class="uf" id="idForm" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
						$chaine.= '<fieldset class="border p-3">';
							$chaine.= $this->draw($enable);
						$chaine.= '</fieldset>';
						$chaine.= '<p class="small">(*) '.getLib('CHAMP_REQUIS').' (1) '.getLib('LECTURE_SEULE').'</p>';
						//préparation d'une zone d'affichage des items choisis (pas encore uploadés)
						$chaine.= '<div id="drop-show"></div>';
					$chaine.= '</form>';

				$chaine.= '</div>';
			$chaine.= '</div>';
		$chaine.= '</div>';

		//----------------------------------------------------
		// Affichage d'une boite modale contenant un caroussel 
		// avec les différentes images disponibles
		//----------------------------------------------------

		$chaine.= '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="Visuels" aria-hidden="true">';
			$chaine.= '<div class="modal-dialog" role="document" style="max-width:900px; padding-right:0;">';
				$chaine.= '<div class="modal-content">';

					//HEADER4
					$chaine.= '<div class="modal-header">';
						$chaine.= '<h5 class="modal-title" id="Visuels">'.getLib('GALERIE').'</h5>';
						$chaine.= '<button type="button" class="close" data-dismiss="modal" aria-label="'.getLib('FERMER').'" title="'.getLib('FERMER').'">';
							$chaine.= '<span aria-hidden="true">&times;</span>';
						$chaine.= '</button>';
					$chaine.= '</div>';

					//BODY
					$chaine.= '<div class="modal-body">';
						$chaine.= '<div id="myGallery" class="carousel slide" data-ride="carousel" data-interval="false">';
							$chaine.= '<div class="carousel-inner">';
								foreach($lesMedias as $indice => $media) {
									$imageFinale = $this->_paths[$this->field('path')->value()].$media;
									$urlMedia = $this->_openingMethod($imageFinale);
									//on ne met pas la classe "active" ici (qui positionne le slide actif), elle sera positionnée par javascript
									//on passe a l'attribut data-path le code du chemin d'accès à l'image autorisé et présent dans $this->_paths
									//on passe a l'attribut data-image le nom du fichier media sans chemin
									//ces deux informations sont prise en compte par javascript pour renseigner (entre autre) le bouton de suppression du media
									$chaine.= '<div id="carimg'.($indice).'" data-path="'.$this->field('path')->value().'" data-image="'.$media.'" class="carousel-item text-center">';
									$chaine.= '<figure class="figure img-thumbnail">';
										$chaine.= '<img class="figure-img img-fluid" src="'.$urlMedia.'">';
										$chaine.= '<figcaption class="figure-caption">'.$media.'</figcaption>';
									$chaine.= '</figure>';
									$chaine.= '</div>';
								}
							$chaine.= '</div>';
							if (count($lesMedias) > 1) {
								//fleche de gauche
								$chaine.= '<a class="carousel-control-prev" href="#myGallery" role="button" data-slide="prev">';
									$chaine.= '<span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span>';
									$chaine.= '<span class="sr-only">Previous</span>';
								$chaine.= '</a>';
								//fleche de droite
								$chaine.= '<a class="carousel-control-next" href="#myGallery" role="button" data-slide="next">';
									$chaine.= '<span class="carousel-control-next-icon text-black" aria-hidden="true" style="filter: invert(1);"></span>';
									$chaine.= '<span class="sr-only">Next</span>';
								$chaine.= '</a>';
							}
						$chaine.= '</div>';
					$chaine.= '</div>';

					//FOOTER
					$chaine.= '<div class="modal-footer justify-content-between">';
						$chaine.= '<small>'._APP_TITLE_.'</small>';
						$chaine.= '<div>';
							$chaine.= '<a id="btDelete">';
								$chaine.= '<button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="tooltip" title="'.getLib('MEDIA_SUPPRIMER').'"><span class="fas fa-trash mr-1"></span>'.getLib('SUPPRIMER').'</button>';
							$chaine.= '</a>';
							$chaine.= '<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">'.getLib('FERMER').'</button>';
						$chaine.= '</div>';
					$chaine.= '</div>';

				$chaine.= '</div>';
			$chaine.= '</div>';
		$chaine.= '</div>';

		return $chaine;
	}
}