<?php
//==============================================================
// CLASSE : Form_exemples_universalform
//--------------------------------------------------------------
// 26.10.2017
//==============================================================

class Form_exemples_universalform extends UniversalForm {

	private $_tab_donnees = array();	//tableau des données associées au formulaire

	private $_tabAutorise =				//tableau des fichiers autorisés
		array(
			'image/gif' => array('.gif', 250000),
			'image/jpeg' => array('.jpg', 250000)
		);
		
	//======================================
	// Méthodes privées
	//======================================
	// initialisation des données de travail	
	//--------------------------------------

	protected function initDonnees() {
		$this->_tab_donnees['titre'] = 'La guerre des étoiles';	//champ texte
		$this->_tab_donnees['data0'] = '';						//champ texte
		$this->_tab_donnees['etablissement'] = 'Nasa';			//champ texte
		$this->_tab_donnees['ville'] = '';						//champ texte
		$this->_tab_donnees['nom'] = 'Labrousse';				//champ texte
		$this->_tab_donnees['prenom'] = '';						//champ texte
		$this->_tab_donnees['age'] = '';						//champ texte
		$this->_tab_donnees['nom2'] = '';						//champ texte
		$this->_tab_donnees['prenom2'] = '';					//champ texte
		$this->_tab_donnees['age2'] = '';						//champ texte
		$this->_tab_donnees['data8'] = '';						//champ texte
		$this->_tab_donnees['data8_5'] = '';					//champ texte
		$this->_tab_donnees['data9'] = '';						//champ texte
		$this->_tab_donnees['data10'] = '';						//champ texte
		$this->_tab_donnees['data11'] = '';						//champ texte
		$this->_tab_donnees['data12'] = '';						//champ texte
		$this->_tab_donnees['data13'] = '';						//champ texte
		$this->_tab_donnees['data14'] = 'non';					//champ switch
		$this->_tab_donnees['genre1'] = 'horror';				//champ select
		$this->_tab_donnees['genre2'] = array('comedy', 'horror');		//champ select multiple
		$this->_tab_donnees['commentaires'] = '';				//champ area
		$this->_tab_donnees['infosinline'] = 'Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline Ceci est un commentaire inline';
		$this->_tab_donnees['infosonline'] = 'Ceci est un commentaire online Ceci est un commentaire online Ceci est un commentaire online Ceci est un commentaire online Ceci est un commentaire online Ceci est un commentaire online Ceci est un commentaire online Ceci est un commentaire online Ceci est un commentaire online Ceci est un commentaire online ';
		$this->_tab_donnees['filtre2'] = array('comedy', 'romance');	//champ filtreselect multiple
		$this->_tab_donnees['filtre3'] = array('comedy', 'romance');	//champ filtreselect multiple
		$this->_tab_donnees['filtre4'] = 'comedy'; //'romance';			//champ filtreselect
		$this->_tab_donnees['filtre5'] = 'horror'; //'romance';			//champ filtreselect
	}

	//----------------------------------------
	// construction des champs du formulaire
	//----------------------------------------

	protected function construitChamps() {
		parent::construitChamps();

		//---------------------
		//BROUILLON
		//---------------------
		$this->createField('text', 'titre', array(
				'newLine' => true,
				'dbfield' => 'titre',
				'label' => 'Titre du film',
//				'clong' => 'col-5',
				'testMatches' => array('NUMERIC'),
				'value' => $this->_tab_donnees['titre'],
//				'enable' => false,
//				'readonly' => true,
				'invisible' => true
				));

		//---------------------
		//FILE
		//---------------------
		$this->createField('separateur', 'sep1', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur1',				//retour de la saisie
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'FILE (online)',				//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => true,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 1',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('text', 'data0', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'data0',					//retour de la saisie
				'inputType' => 'file',					//type d'input
				'design' => 'online',					//inline (defaut) / online
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Upload de fichier',			//label
				'llong' => 'col-2',						//longueur de la zone de titre
				'lclass' => 'bleu',						//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'before',						//before (defaut) / after
				'clong' => 'col-8',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie',				//texte pré-affiché -> non prit en compte sur type : file
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['data0'], //valeur de la saisie
				'complement' => $this->_tabAutorise,	//tableau des saisies autorisées
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'invisible' => false					//rend invisible le champ
			));

		//---------------------
		//TEXT INLINE
		//---------------------
		$this->createField('separateur', 'sep2', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur2',				//retour de la saisie
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'TEXT (inline)',				//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',			//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 2',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('text', 'etablissement', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'etablissement',			//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'inline',					//inline (defaut) / online
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Etablissement',				//label
				'llong' => 'col-2',						//longueur de la zone de titre
				'lclass' => 'bleu',						//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Décallage 2 colonnes',	//aide sur le label
				'labelHelpPos' => 'right',				//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-5',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'cheight' => 'sm',						//hauteur zone de texte (sm ou lg)
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie établissement',//texte pré-affiché
				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['etablissement'], //valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('text', 'ville', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'ville',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'inline',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Ville',						//label
				'llong' => 'col-2',						//longueur de la zone de titre
				'lclass' => 'rouge',					//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Label après le champ',	//aide sur le label
				'lpos' => 'after',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-10',					//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'cheight' => 'lg',						//hauteur zone de texte (sm ou lg)
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie ville',		//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['ville'],//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('text', 'nom', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'nom',						//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'inline',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Nom',						//label
				'llong' => 'col-2',						//longueur de la zone de titre
				'lclass' => 'bleu',						//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-4',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'cheight' => '',						//hauteur zone de texte (sm ou lg)
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie nom',			//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['nom'],	//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));
		$this->createField('text', 'prenom', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'prenom',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'inline',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Prénom',					//label
				'llong' => 'col-2',						//longueur de la zone de titre
				'lclass' => 'vert',						//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-2',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie prénom',		//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['prenom'],//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));
		$this->createField('text', 'age', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'age',						//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'inline',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Age',						//label
				'llong' => 'col-1',						//longueur de la zone de titre
				'lclass' => 'rouge',					//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-1',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => false,					//correction orthographique
				'placeholder' => 'Saisie age',			//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['age'],	//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('text', 'nom2', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'nom2',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'inline',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Nom',						//label
				'llong' => 'col-2',						//longueur de la zone de titre
				'lclass' => 'bleu',						//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'after',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-4',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie nom',			//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['nom2'],	//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));
		$this->createField('text', 'prenom2', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'prenom2',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'inline',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Prénom',					//label
				'llong' => 'col-2',						//longueur de la zone de titre
				'lclass' => 'vert',						//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'after',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-2',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie prénom',		//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['prenom2'],//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));
		$this->createField('text', 'age2', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'age2',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'inline',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Age',						//label
				'llong' => 'col-1',						//longueur de la zone de titre
				'lclass' => 'rouge',					//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'after',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-1',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => false,					//correction orthographique
				'placeholder' => 'Saisie age',			//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['age2'],	//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//---------------------
		//TEXT ONLINE
		//---------------------
		//en online seule la largeur du champ est donnée dans clong, pas celle du label qui prend la taille maxi du champ puisque positionné au-dessus (ou au-dessous)
		$this->createField('separateur', 'sep3', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur3',				//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'TEXT (online)',				//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 3',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('text', 'data8', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'data8',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'online',					//inline (defaut) / online
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Libellé centré, décalage 2 colonnes',	//label
//				'llong' => 'col-1',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => 'bleu',						//classe du label
				'lalign' => 'center',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-5',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie',				//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['data8'],//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));
		$this->createField('text', 'data8_5', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'data8_5',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'online',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Libellé à gauche',			//label
//				'llong' => 'col-1',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => 'vert',						//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-3',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie',				//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['data8_5'],//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('text', 'data9', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'data9',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'online',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Nom (cadré à gauche)',		//label
//				'llong' => 'col-1',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => 'bleu',						//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-5',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie nom',			//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['data9'],//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));
		$this->createField('text', 'data10', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'data10',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'online',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Prénom (cadré à droite)',	//label
//				'llong' => 'col-1',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => 'vert',						//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-5',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie prénom',		//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['data10'],//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));
		$this->createField('text', 'data11', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'data11',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'online',					//inline (defaut) / online
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => '2ème prénom (centré)',		//label
//				'llong' => 'col-1',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => 'rouge',					//classe du label
				'lalign' => 'center',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-2',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie 2ème prénom',	//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['data11'],//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));
		$this->createField('text', 'data12', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'data12',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'online',					//inline (defaut) / online
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Libellé gauche',			//label
//				'llong' => 'col-1',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => '',							//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label gauche positionné à gauche',	//aide sur le label
				'labelHelpPos' => 'left',				//position de la bulle d'aide du label de gauche
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'labelPlus' => 'Libellé droite',		//addon au label
				'labelPlusHelp' => 'Aide sur label droite positionné dessous',	//aide sur le label
				'labelPlusHelpPos' => 'bottom',			//position de la bulle d'aide sur le label de droite		
				'clong' => 'col-4',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie',				//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['data12'],//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));
		$label = '<a href="javascript:void(0)" role="button" onclick="alert(\'vous avez cliqué\')">';
		$label.= '<span class="fas fa-upload"></span>';
		$label.= '</a>';
		$this->createField('text', 'data13', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'data13',					//retour de la saisie
				'inputType' => 'text',					//type d'input
				'design' => 'online',					//inline (defaut) / online
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Libellé gauche',			//label
//				'llong' => 'col-1',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => '',							//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label gauche positionné à gauche',	//aide sur le label
				'labelHelpPos' => 'right',				//position de la bulle d'aide du label de gauche
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'labelPlus' => $label,					//addon au label
				'labelPlusHelp' => 'Exemple avec une icone cliquable',	//aide sur le label
				'labelPlusHelpPos' => 'left',			//position de la bulle d'aide sur le label de droite		
				'clong' => 'col-3',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 10,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'Saisie',				//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['data13'],//valeur de la saisie
//				'complement' => $this->_tabAutorise,	//inutilisé ici
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//---------------------
		//SELECTEUR
		//---------------------
		$this->createField('separateur', 'sep4', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur4',				//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'SELECT',					//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 4',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('select', 'genre_film', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'genre_film',				//retour de la saisie
				'design' => 'online',					//inline (defaut) / online
//				'multiple' => true,						//dit si la selection multiple est autorisée dans ce select (false par défaut)
//				'size' => 4,							//hauteur du select en nombre de lignes visibles (1 par défaut)
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Genre',						//label
//				'llong' => 'col-2',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => 'bleu',						//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label genre film',	//aide sur le label
				'lpos' => 'after',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-3',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'cheight' => 'lg',						//hauteur de la zone de champ (lg, sm ou vide)
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['genre1'],//valeur de la saisie
				'complement' => 'fillSelect',			//fonction de callback qui doit remplir le select
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => true,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//---------------------
		//SELECTEUR MULTIPLE
		//---------------------
		$this->createField('select', 'genre_film2', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'genre_film2',				//retour de la saisie
				'design' => 'inline',					//inline (defaut) / online
				'multiple' => true,						//dit si la selection multiple est autorisée dans ce select (false par défaut)
				'size' => 4,							//hauteur du select en nombre de lignes visibles (1 par défaut)
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Genre',						//label
				'llong' => 'col-2',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => 'vert',						//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur label genre film',	//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-3',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'cheight' => '',						//hauteur de la zone de champ (lg, sm ou vide)
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['genre2'],//valeur de la saisie (ici un tableau)
				'complement' => 'fillSelect2',			//fonction de callback qui doit remplir le select
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//---------------------
		//AREA
		//---------------------
		$this->createField('separateur', 'sep5', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur5',				//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'AREA',						//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 5',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('area', 'commentaires-inline', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'comments',				//retour de la saisie
				'design' => 'inline',					//inline (defaut) / online
				//'decalage' => 'col-2',				//décallage en colonnes boostrap
				'label' => 'Commentaires',				//label
				'llong' => 'col-2',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => 'bleu',						//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Area large inline',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-5',						//longueur de la zone de champ
				'rows' => 7,							//hauteur du commentaire en nombre de lignes
				'cclass' => '',							//classe de la zone de champ
				'cheight' => 'lg',						//hauteur de la zone de champ (lg, sm ou vide)
				'maxlength' => 128,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'inline lg',			//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['commentaires'],//valeur de la saisie
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('area', 'commentaires', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'comments',				//retour de la saisie
				'design' => 'online',					//inline (defaut) / online
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Commentaires',				//label
//				'llong' => 'col-2',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => 'bleu',						//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Area normale online',	//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-5',						//longueur de la zone de champ
				'rows' => 7,							//hauteur du commentaire en nombre de lignes
				'cclass' => '',							//classe de la zone de champ
				'maxlength' => 128,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'online',				//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['commentaires'],//valeur de la saisie
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		$label = 'label droite <a href="javascript:void(0)" role="button" onclick="alert(\'vous avez cliqué\')">';
		$label.= '<span class="fas fa-upload"></span>';
		$label.= '</a>';
		$this->createField('area', 'commentaires-online', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'comments',				//retour de la saisie
				'design' => 'online',					//inline (defaut) / online
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'Commentaires',				//label
//				'llong' => 'col-2',						//longueur de la zone de titre (inutile dans le cas d'un design online)
				'lclass' => 'vert',						//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Area small online',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'labelPlus' => $label,					//aide sur le label
				'labelPlusHelp' => 'label de droite',	//aide sur le label
				'clong' => 'col-5',						//longueur de la zone de champ
				'rows' => 7,							//hauteur du commentaire en nombre de lignes
				'cclass' => '',							//classe de la zone de champ
				'cheight' => 'sm',						//hauteur de la zone de champ (lg, sm ou vide)
				'maxlength' => 128,						//nb caractres max en saisie
				'spellcheck' => true,					//correction orthographique
				'placeholder' => 'online sm',			//texte pré-affiché
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['commentaires'],//valeur de la saisie
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//---------------------
		//CHECKBOX
		//---------------------
		$this->createField('separateur', 'sep6', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur6',				//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'CHECKBOX',					//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 6',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('checkbox', 'compris', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'compris',					//le groupName est facultatif si dpos alone
				'dbfield' => 'compris',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'alone',							//first / last / inter / alone
//				'titre'	=> 'Titre',							//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'bleu',							//style du titre
//				'titreHelp'	=> 'Aide sur titre',			//aide du titre
				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Compris',						//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Avec envoi d\'erreur',		//aide sur le label
				'labelHelpPos' => 'left',					//aide sur le label
				'lpos' => 'before',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-4',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de la checkbox
//				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('checkbox', 'recommence', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'recommence',				//le groupName est facultatif si dpos alone
				'dbfield' => 'recommence',					//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'alone',							//first / last / inter / alone
//				'titre'	=> 'Titre',							//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'bleu',							//style du titre
//				'titreHelp'	=> 'Aide sur titre',			//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Recommence',					//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur label Recommence',	//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-3',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de la checkbox
//				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => true,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('checkbox', 'enfant', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'enfant',					//le groupName est facultatif si dpos alone
				'dbfield' => 'enfant',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'alone',							//first / last / inter / alone
				'titre'	=> 'Tranches d\'ages',				//on veut un titre (premier élément seulement, sans effet sur les autres)
				'titreHelp'	=> 'Aide sur titre tranches d\'âges',				//on veut un titre (premier élément seulement, sans effet sur les autres)
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
				'tclass' => 'vert',							//style du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Enfant',						//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur le label Enfant',	//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-1',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de la checkbox
				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => true,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => true,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('checkbox', 'ado', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'ado',						//le groupName est facultatif si dpos alone
				'dbfield' => 'ado',							//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'alone',							//first / last / inter / alone
//				'titre'	=> 'Tranches d\'ages',				//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'vert',							//style du titre
//				'titreHelp'	=> 'Aide sur titre',			//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Adolescent',					//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur le label Adolescent', //aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-2',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de la checkbox
				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('checkbox', 'adulte', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'adulte',					//le groupName est facultatif si dpos alone
				'dbfield' => 'adulte',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'alone',							//first / last / inter / alone
//				'titre'	=> 'Tranches d\'ages',				//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'vert',							//style du titre
//				'titreHelp'	=> 'Aide sur titre',			//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Adulte',						//label
				'lclass' => 'rouge',						//classe du label
				'labelHelp' => 'Aide sur le label Adulte', //aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-1',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de la checkbox
				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('checkbox', 'bleu', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'bleu'	,					//le groupName est facultatif si dpos alone
				'dbfield' => 'couleur_bleu',				//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'alone',							//first / last / inter / alone
//				'titre'	=> 'Titre',							//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'vert',							//style du titre
//				'titreHelp'	=> 'Aide sur titre',			//aide du titre
				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Bleu',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur le label bleu',	 //aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-1',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de la checkbox
//				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'bleu',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'pas bleu',				//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => true,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('checkbox', 'vert', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'vert'	,					//le groupName est facultatif si dpos alone
				'dbfield' => 'couleur_vert',				//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'alone',							//first / last / inter / alone
//				'titre'	=> 'Titre',							//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'vert',							//style du titre
//				'titreHelp'	=> 'Aide sur titre',			//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Vert',							//label
				'lclass' => 'vert',							//classe du label
				'labelHelp' => 'Aide sur le label vert',	 //aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-1',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de la checkbox
				'border' => false,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'vert',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'pas vert',				//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => true,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('checkbox', 'rouge', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'rouge'	,					//le groupName est facultatif si dpos alone
				'dbfield' => 'couleur_rouge',				//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'alone',							//first / last / inter / alone
//				'titre'	=> 'Titre',							//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'vert',							//style du titre
//				'titreHelp'	=> 'Aide sur titre',			//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Rouge',							//label
				'lclass' => 'rouge',						//classe du label
				'labelHelp' => 'Aide sur le label rouge',	//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-1',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de la checkbox
				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'rouge',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'pas rouge',				//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => true,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('checkbox', 'enfant_groupe', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'catage'	,					//le groupName est obligatoire si on veut grouper les checkbox
				'dbfield' => 'categorie_enfant',			//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'first',							//first / last / inter / alone
				'titre'	=> 'Catégorie',						//on veut un titre (premier élément seulement, sans effet sur les autres)
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
				'tclass' => 'vert',							//style du titre
				'talign' => 'right',						//cadrage du titre à droite
				'titreHelp'	=> 'Aide sur titre catégorie',	//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Enfant',						//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur le label Enfant',	//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-3',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de la checkbox
				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => true,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('checkbox', 'ado_groupe', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'catage'	,					//le groupName est obligatoire si on veut grouper les checkbox
				'dbfield' => 'categorie_ado',				//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'inter',							//first / last / inter / alone
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Adolescent',					//label
				'lclass' => 'vert',							//classe du label
				'labelHelp' => 'Aide sur le label Adolescent', //aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'cclass' => '',								//classe de la checkbox
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('checkbox', 'adulte_groupe', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'catage'	,					//le groupName est obligatoire si on veut grouper les checkbox
				'dbfield' => 'categorie_adulte',			//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'last',							//first / last / inter / alone
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Adulte',						//label
				'lclass' => 'rouge',						//classe du label
				'labelHelp' => 'Aide sur le label Adulte',	//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'cclass' => '',								//classe de la checkbox
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('checkbox', 'vise', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'vise',						//le groupName est facultatif si dpos alone
				'dbfield' => 'vise',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (forcé à inline pour dpos alone)
				'dpos' => 'alone',							//first / last / inter / alone
//				'titre'	=> 'Visé',							//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'bleu',							//style du titre
//				'titreHelp'	=> 'document visé ?',			//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Visé',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Avec envoi d\'erreur',		//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-1',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de la checkbox
//				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'visé',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'pas encore visé',		//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('checkbox', 'fruitChoix1', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'fruitchx',					//le groupName est obligatoire pour grouper les checkbox
				'dbfield' => 'fruitBanane',					//retour de la saisie
				'design' => 'online',						//inline (defaut) / online
				'dpos' => 'first',							//first / last / inter / alone
				'titre'	=> 'Fruit',							//on veut un titre (premier élément seulement, sans effet sur les autres)
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
				'tclass' => 'vert',							//classe du titre
				'talign' => 'right',						//cadrage du titre à droite
				'titreHelp'	=> 'Aide sur fruit',			//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'banane',						//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur banane',			//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-2',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe (ou alone). Sans effet sur les autres)
				'cclass' => '',								//classe de la checkbox
//				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'banane',						//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => '',						//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'TEST ERREUR',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle erreur enfant_groupe',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => 'onclick="alert(\'banane cliquée\');"', //code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('checkbox', 'fruitChoix2', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'fruitchx',					//le groupName est obligatoire pour grouper les checkbox
				'dbfield' => 'fruitPomme',					//retour de la saisie
				'design' => 'online',						//inline (defaut) / online
				'dpos' => 'inter',							//first / last / inter / alone
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'pomme',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur pomme',			//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'cclass' => '',								//classe de la checkbox
				'value' => 'pomme',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => '',						//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => true,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('checkbox', 'fruitChoix3', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'fruitchx',					//le groupName est obligatoire pour grouper les checkbox
				'dbfield' => 'fruitPoire',					//retour de la saisie
				'design' => 'online',						//inline (defaut) / online
				'dpos' => 'last',							//first / last / inter / alone
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'poire',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur poire',			//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after
				'cclass' => '',								//classe de la checkbox
				'value' => 'poire',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => '',						//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => true,							//cochée (true) / décochée (false)
				'javascript' => '',							//code javascript associé
				'enable' => false,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		//plusieurs inline before
		$this->createField('checkbox', 'essai1', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'essai',						//le groupName est obligatoire pour grouper les checkbox
				'dbfield' => 'esx1',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'first',							//first / last / inter / alone
				'titre'	=> 'Essais',						//on veut un titre (premier élément seulement, sans effet sur les autres)
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
				'tclass' => 'vert',							//classe du titre
				'talign' => 'right',						//cadrage du titre à droite
				'titreHelp'	=> 'plusieurs, inline before',	//aide sur le titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Essai 1',						//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur Essai 1',			//aide sur le label
				'lpos' => 'before',							//position du label par rapport à la checkbox : before (defaut) / after
				'clong' => 'col-2',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe (ou alone). Sans effet sur les autres)
				'cclass' => '',								//classe de la checkbox
				'border' => false,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => '1',								//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => '0',						//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'TEST ERREUR',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle erreur',		//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => 'onclick="alert(\'1 cliqué\');"', //code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('checkbox', 'essai2', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'essai',						//le groupName est obligatoire pour grouper les checkbox
				'dbfield' => 'esx2',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'inter',							//first / last / inter / alone
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Essai 2',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur Essai 2',			//aide sur le label
				'lpos' => 'before',							//position du label par rapport à la checkbox : before (defaut) / after
				'cclass' => '',								//classe de la checkbox
				'value' => '1',								//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => '0',						//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
				'javascript' => 'onclick="alert(\'2 cliqué\');"', //code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => true,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('checkbox', 'essai3', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'essai',						//le groupName est obligatoire pour grouper les checkbox
				'dbfield' => 'esx3',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'last',							//first / last / inter / alone
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Essai 3',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur Essai 3',			//aide sur le label
				'lpos' => 'before',							//position du label par rapport à la checkbox : before (defaut) / after
				'cclass' => '',								//classe de la checkbox
				'value' => '1',								//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => '0',						//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => true,							//cochée (true) / décochée (false)
				'javascript' => 'onclick="alert(\'3 cliqué\');"', //code javascript associé
				'enable' => false,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		//Une seule case avec légende
		$this->createField('checkbox', 'permis', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'permis',					//le groupName est facultatif si dpos = alone
				'dbfield' => 'dbpermis',					//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (inline par défaut si dpos = alone)
				'dpos' => 'alone',							//first / last / inter / alone
				'titre'	=> 'Permis de conduire',			//on veut un titre (premier élément seulement, sans effet sur les autres)
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
				'tclass' => 'bleu',							//classe du titre
				'talign' => 'right',						//cadrage du titre à droite
				'titreHelp'	=> 'Une seule case avec légende',//aide sur le titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => '',								//label
				'lclass' => '',								//classe du label
				'labelHelp' => '',							//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after (ici utiliser after pour un meilleur alignement de la checkbox)
				'clong' => 'col-2',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe (ou alone). Sans effet sur les autres)
				'cclass' => '',								//classe de la checkbox
				'border' => false,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'permis',						//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'pas de permis',			//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'TEST ERREUR',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle erreur',		//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => 'onclick="alert(\'permis cliqué\');"', //code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		//Une seule sans légende
		$this->createField('checkbox', 'majeur', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'majeur',					//le groupName est facultatif si dpos = alone
				'dbfield' => 'dbmajeur',					//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online (inline par défaut si dpos = alone)
				'dpos' => 'alone',							//first / last / inter / alone
//				'titre'	=> 'Majeur',						//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'bleu',							//classe du titre
//				'talign' => 'right',						//cadrage du titre à droite
//				'titreHelp'	=> 'Une seule case sans légende',//aide sur le titre
				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Majeur',						//label
				'lclass' => '',								//classe du label
				'labelHelp' => 'Cocher si majeur',			//aide sur le label
				'lpos' => 'after',							//position du label par rapport à la checkbox : before (defaut) / after (ici utiliser after pour un meilleur alignement de la checkbox)
				'clong' => 'col-2',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe (ou alone). Sans effet sur les autres)
				'cclass' => '',								//classe de la checkbox
				'border' => false,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'majeur',						//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'mineur',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'TEST ERREUR',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle erreur',		//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		//---------------------
		//SWITCH
		//---------------------
		$this->createField('separateur', 'sep13', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur13',				//retour de la saisie
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'SWITCH',						//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Aide sur séparateur SWITCH',//aide sur le séparateur
				'border' => false,							//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',							//longueur du séparateur en colonnes bootstrap
				'cclass' => '',								//classe sur le bloc de champ
				'value' => 'Séparateur 13',					//valeur de la saisie
				'invisible' => false						//rend invisible le champ
			));

		$checked = ($this->_tab_donnees['data14'] == 'oui');
		$this->createField('switch', 'sw_permis', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'dbfield' => 'sw_permis',					//retour de la saisie
//				'titre'	=> 'Titre',							//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'bleu',							//style du titre
//				'titreHelp'	=> 'Aide sur titre',			//aide du titre
				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Permis de conduire',			//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Allumer pour accepter',		//aide sur le label
				'labelHelpPos' => 'right',					//aide sur le label
				'clong' => 'col-4 mb-3',					//longueur du champ en colonnes boostrap
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => $checked,						//allumé (cochée) / éteint (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$checked = ($this->_tab_donnees['data14'] == 'oui');
		$this->createField('switch', 'sw_permis2', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'dbfield' => 'sw_permis2',					//retour de la saisie
				'titre'	=> 'Permis de conduire',			//on veut un titre (premier élément seulement, sans effet sur les autres)
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
				'tclass' => 'bleu',							//style du titre
				'titreHelp'	=> 'Aide sur titre',			//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => '',								//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Allumer pour accepter',		//aide sur le label
				'labelHelpPos' => 'right',					//aide sur le label
				'clong' => 'col-4 mb-3',					//longueur du champ en colonnes boostrap
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => $checked,						//allumé (cochée) / éteint (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('switch', 'swenfant', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'dbfield' => 'sw_enfant',					//retour de la saisie
				'titre'	=> 'Tranches d\'ages',				//on veut un titre (premier élément seulement, sans effet sur les autres)
				'titreHelp'	=> 'Aide sur titre tranches d\'âges',				//on veut un titre (premier élément seulement, sans effet sur les autres)
				'titreHelpPos' => 'bottom',					//aide sur le label
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
				'tclass' => 'vert',							//style du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Enfant',						//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur le label Enfant',	//aide sur le label
				'clong' => 'col-1 mb-3',					//longueur du champ en colonnes boostrap
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => true,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => true,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('switch', 'swado', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'dbfield' => 'sw_ado',							//retour de la saisie
//				'titre'	=> 'Tranches d\'ages',				//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'vert',							//style du titre
//				'titreHelp'	=> 'Aide sur titre',			//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Adolescent',					//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur le label Adolescent', //aide sur le label
				'clong' => 'col-2',							//longueur du champ en colonnes boostrap
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('switch', 'swadulte', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'dbfield' => 'sw_adulte',						//retour de la saisie
//				'titre'	=> 'Tranches d\'ages',				//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => 'vert',							//style du titre
//				'titreHelp'	=> 'Aide sur titre',			//aide du titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Adulte',						//label
				'lclass' => 'rouge',						//classe du label
				'labelHelp' => 'Aide sur le label Adulte', //aide sur le label
				'clong' => 'col-1',							//longueur du champ en colonnes boostrap
				'value' => 'oui',							//valeur renvoyée dans dbfield si checkbox cliquée
				'valueInverse' => 'non',					//valeur renvoyée dans dbfield si checkbox non cliquée
				'checked' => false,							//cochée (true) / décochée (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'Test erreur',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle aide erreur',	//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		//---------------------
		//RADIO
		//---------------------
		//plusieurs online
		$this->createField('separateur', 'sep7', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur7',				//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'RADIO',						//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 7',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('radio', 'genre_homme_0', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'optGenre0',					//le groupName est obligatoire
				'dbfield' => 'genre0',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'first',							//first / last / inter
				'titre'	=> 'Inline avec titre à droite',	//on veut un titre (premier élément seulement, sans effet sur les autres)
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap et décallé de 2 colonnes supplémentaires à droite
				'tclass' => '',								//classe du titre
				'talign' => 'right',						//cadrage du titre à droite
				'titreHelp'	=> 'Genre de l\'abonné',		//aide sur le titre
				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Homme',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur label Homme',		//aide sur le label
				'lpos' => 'after',							//position du label par rapport au bouton radio : before (defaut) / after
				'clong' => 'col-3',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe. Sans effet sur les autres)
				'cclass' => '',								//classe du bouton
				'border' => false,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'Homme',								//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => true,							//coché (true) / décoché (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'TEST ERREUR',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle erreur',		//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => 'onclick="alert(\'Homme cliqué\');"', //code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => true,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('radio', 'genre_femme_0', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'optGenre0',					//le groupName est obligatoire
				'dbfield' => 'genre0',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'inter',							//first / last / inter
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Femme',							//label
				'lclass' => 'vert',							//classe du label
				'labelHelp' => 'Aide sur label Femme',		//aide sur le label
				'lpos' => 'after',							//position du label par rapport au bouton radio : before (defaut) / after
				'cclass' => '',								//classe du bouton
				'value' => 'Femme',							//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => false,							//coché (true) / décoché (false)
				'javascript' => 'onclick="alert(\'Femme cliquée\');"', //code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('radio', 'genre_inconnu_0', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'optGenre0',					//le groupName est obligatoire pour grouper les checkbox
				'dbfield' => 'genre0',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'last',							//first / last / inter
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Inconnu',						//label
				'lclass' => 'rouge',						//classe du label
				'labelHelp' => 'Aide sur label Inconnu',	//aide sur le label
				'lpos' => 'after',							//position du label par rapport au bouton radio : before (defaut) / after
				'cclass' => '',								//classe du bouton
				'value' => 'Inconnu',						//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => false,							//coché (true) / décoché (false)
				'javascript' => 'onclick="alert(\'Inconnu cliquée\');"', //code javascript associé
				'enable' => false,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('radio', 'niv_debutant', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'optNiveau',					//le groupName est obligatoire
				'dbfield' => 'niveau',						//retour de la saisie
				'design' => 'online',						//inline (defaut) / online
				'dpos' => 'first',							//first / last / inter
//				'titre'	=> 'Online avec titre à droite',	//on veut un titre (premier élément seulement, sans effet sur les autres)
//				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
//				'tclass' => '',								//classe du titre
//				'talign' => 'right',						//cadrage du titre à droite
//				'titreHelp'	=> 'Niveau de l\'abonné',		//aide sur le titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Débutant',						//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur label Débutant',	//aide sur le label
				'lpos' => 'after',							//position du label par rapport au bouton radio : before (defaut) / after
				'clong' => 'col-3',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe. Sans effet sur les autres)
				'cclass' => '',								//classe du bouton
				'border' => true,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'Débutant',								//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => true,							//coché (true) / décoché (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'TEST ERREUR',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle erreur',		//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => true,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('radio', 'niv_confirme', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'optNiveau',					//le groupName est obligatoire
				'dbfield' => 'niveau',						//retour de la saisie
				'design' => 'online',						//inline (defaut) / online
				'dpos' => 'last',							//first / last / inter
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Confirmé',						//label
				'lclass' => '',								//classe du label
				'labelHelp' => 'Aide sur label Confirmé',	//aide sur le label
				'lpos' => 'after',							//position du label par rapport au bouton radio : before (defaut) / after
				'cclass' => '',								//classe du bouton
				'value' => 'Confirmé',						//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => false,							//coché (true) / décoché (false)
				'javascript' => '',							//code javascript associé
				'enable' => false,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		//online
		$this->createField('radio', 'fruitPrefereBanane', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'fruitpref',					//le groupName est obligatoire
				'dbfield' => 'fruitPrefere',				//retour de la saisie
				'design' => 'online',						//inline (defaut) / online
				'dpos' => 'first',							//first / last / inter
				'titre'	=> 'Fruit préféré (pomme invisible)',//on veut un titre (premier élément seulement, sans effet sur les autres)
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
				'tclass' => 'vert',							//classe du titre
				'talign' => 'right',						//cadrage du titre à droite
				'titreHelp'	=> 'Aide sur titre Fruits',		//aide sur le titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'banane',						//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur label banane',		//aide sur le label
				'lpos' => 'before',							//position du label par rapport au bouton radio : before (defaut) / after
				'clong' => 'col-2',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe. Sans effet sur les autres)
				'cclass' => '',								//classe du bouton
				'border' => false,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'banane',								//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => false,							//coché (true) / décoché (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'TEST ERREUR',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle erreur',		//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('radio', 'fruitPreferePomme', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'fruitpref',					//le groupName est obligatoire
				'dbfield' => 'fruitPrefere',				//retour de la saisie
				'design' => 'online',						//inline (defaut) / online
				'dpos' => 'inter',							//first / last / inter
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'pomme',							//label
				'lclass' => '',								//classe du label
				'labelHelp' => 'Aide sur label pomme',		//aide sur le label
				'lpos' => 'before',							//position du label par rapport au bouton radio : before (defaut) / after
				'cclass' => '',								//classe du bouton
				'value' => 'pomme',							//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => false,							//coché (true) / décoché (false)
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => true							//rend invisible (true) le champ
			));
		$this->createField('radio', 'fruitPreferePoire', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'fruitpref',					//le groupName est obligatoire
				'dbfield' => 'fruitPrefere',				//retour de la saisie
				'design' => 'online',						//inline (defaut) / online
				'dpos' => 'last',							//first / last / inter
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'poire',							//label
				'lclass' => '',								//classe du label
				'labelHelp' => 'Aide sur label poire',		//aide sur le label
				'lpos' => 'before',							//position du label par rapport au bouton radio : before (defaut) / after
				'cclass' => '',								//classe du bouton
				'value' => 'poire',							//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => true,							//coché (true) / décoché (false)
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => true,							//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		//inline
		$this->createField('radio', 'calories100', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'calories',					//le groupName est obligatoire
				'dbfield' => 'nbCalories',					//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'first',							//first / last / inter
				'titre'	=> 'Nombre de calories',			//on veut un titre (premier élément seulement, sans effet sur les autres)
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
				'tclass' => 'vert',							//classe du titre
				'talign' => 'right',						//cadrage du titre à droite
				'titreHelp'	=> 'Aide sur titre nb calories',//aide sur le titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => '100',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur 100',				//aide sur le label
				'lpos' => 'after',							//position du label par rapport au bouton radio : before (defaut) / after
				'clong' => 'col-2',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe. Sans effet sur les autres)
				'cclass' => '',								//classe du bouton
//				'border' => false,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => '100',							//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => false,							//coché (true) / décoché (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'TEST ERREUR',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle erreur',		//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => 'onclick="alert(\'100 cliqué\');"',	//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('radio', 'calories200', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'calories',					//le groupName est obligatoire
				'dbfield' => 'nbCalories',					//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'inter',							//first / last / inter
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => '200',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur 200',				//aide sur le label
				'lpos' => 'after',							//position du label par rapport au bouton radio : before (defaut) / after
				'cclass' => '',								//classe du bouton
				'value' => '200',							//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => false,							//coché (true) / décoché (false)
				'javascript' => 'onclick="alert(\'200 cliqué\');"',	//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('radio', 'calories500', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'calories',					//le groupName est obligatoire
				'dbfield' => 'nbCalories',					//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'last',							//first / last / inter
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => '500',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur 500',				//aide sur le label
				'lpos' => 'after',							//position du label par rapport au bouton radio : before (defaut) / after
				'cclass' => '',								//classe du bouton
				'value' => '500',							//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => true,							//coché (true) / décoché (false)
				'javascript' => 'onclick="alert(\'500 cliqué\');"',	//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		//inline label avant
		$this->createField('radio', 'caloriesMax1000', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'groupName' => 'caloriesMax',				//le groupName est obligatoire
				'dbfield' => 'nbCaloriesMax',				//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'first',							//first / last / inter
				'titre'	=> 'Nombre de calories maximum',	//on veut un titre (premier élément seulement, sans effet sur les autres)
				'tlong'	=> 'col-2',							//on veut un titre sur 2 colonnes boostrap
				'tclass' => 'vert',							//classe du titre
				'talign' => 'left',							//cadrage du titre à gauche
				'titreHelp'	=> 'Aide sur titre calories max',//aide sur le titre
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => '1000',							//label
				'lclass' => 'vert',							//classe du label
				'labelHelp' => 'Aide sur 1000',				//aide sur le label
				'lpos' => 'before',							//position du label par rapport au bouton radio : before (defaut) / after
				'clong' => 'col-2',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe. Sans effet sur les autres)
				'cclass' => '',								//classe du bouton
				'border' => false,							//defaut : false. A définir une seule fois sur le premier éléments checkbox (false/true(encadrement par défaut) ou bordure personnnalisée)
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => '1000',							//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => true,							//coché (true) / décoché (false)
//				'erreur' => true,							//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreur' => 'TEST ERREUR',				//A définir une seule fois sur le premier élément. Ignoré sur les autres.
//				'liberreurHelp' => 'libelle erreur',		//A définir une seule fois sur le premier élément. Ignoré sur les autres.
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('radio', 'caloriesMax1500', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'caloriesMax',				//le groupName est obligatoire
				'dbfield' => 'nbCaloriesMax',				//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'inter',							//first / last / inter
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => '1500',							//label
				'lclass' => 'bleu',							//classe du label
				'labelHelp' => 'Aide sur 1500',				//aide sur le label
				'lpos' => 'before',							//position du label par rapport au bouton radio : before (defaut) / after
				'cclass' => '',								//classe du bouton
				'value' => '1500',							//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => false,							//coché (true) / décoché (false)
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));
		$this->createField('radio', 'caloriesMax2000', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'groupName' => 'caloriesMax',				//le groupName est obligatoire
				'dbfield' => 'nbCaloriesMax',				//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
				'dpos' => 'last',							//first / last / inter
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => '2000',							//label
				'lclass' => 'rouge',						//classe du label
				'labelHelp' => 'Aide sur 2000',				//aide sur le label
				'lpos' => 'before',							//position du label par rapport au bouton radio : before (defaut) / after
				'cclass' => '',								//classe du bouton
				'value' => '2000',							//valeur renvoyée dans dbfield si checkbox cliquée
				'checked' => false,							//coché (true) / décoché (false)
				'javascript' => '',							//code javascript associé
				'enable' => true,							//disponible (dbfield renvoie NULL sinon)
				'readonly' => false,						//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible (true) le champ
			));

		//---------------------
		//COMMENT
		//---------------------
		$this->createField('separateur', 'sep8', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur8',				//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'COMMENT',					//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 8',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('comment', 'infos1', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'dbfield' => 'infos1',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Commentaires',					//label
				'lpos' => 'before',							//position du label par rapport au commentaire : before (defaut) / after
				'llong' => 'col-2',							//longueur de la zone de titre (pour design inline seulement)
				'lclass' => 'vert',							//classe du label
				'lalign' => 'right',						//left (defaut) / right / center / jutify  (aligmenent du label)
//				'labelHelp' => 'Aide sur label commentaires 1',	//aide sur le label
				'clong' => 'col-5',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe (ou alone). Sans effet sur les autres)
				'cclass' => '',								//classe du commentaire (div)
				'cheight' => 'sm',							//taille du champ (lg ou sm)
//				'border' => true,							//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => $this->_tab_donnees['infosinline'],	//valeur renvoyée dans dbfield
//				'erreur' => true,							//montée erreur
//				'liberreur' => 'TEST ERREUR',				//libellé de l'érreur
//				'liberreurHelp' => 'libelle erreur',		//Aide sur l'erreur
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('comment', 'infos', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'dbfield' => 'infos',						//retour de la saisie
				'design' => 'online',						//inline (defaut) / online
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Commentaires',					//label
				'lpos' => 'after',							//position du label par rapport au commentaire : before (defaut) / after
//				'llong' => 'col-2',							//longueur de la zone de titre (pour label inline seulement)
				'lclass' => 'bleu',							//classe du label
				'lalign' => 'center',						//left (defaut) / right / center / jutify  (aligmenent du label)
				'labelHelp' => 'Aide sur label commentaires', //aide sur le label
				'clong' => 'col-5',							//longueur du champ en colonnes boostrap (a définir sur le premier du groupe (ou alone). Sans effet sur les autres)
				'cclass' => 'vert',							//classe du commentaire (div)
				'border' => true,							//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => $this->_tab_donnees['infosonline'],	//valeur renvoyée dans dbfield
//				'erreur' => true,							//montée erreur
//				'liberreur' => 'TEST ERREUR',				//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',		//Aide sur l'erreur
				'invisible' => false						//rend invisible (true) le champ
			));

		//---------------------
		//IMAGE
		//---------------------
		$this->createField('separateur', 'sep9', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur9',				//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'IMAGE',						//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 9',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		$this->createField('image', 'image1', array(
				'newLine' => true,							//nouvelle ligne ? false par défaut
				'dbfield' => 'image1',						//retour de la saisie
				'design' => 'inline',						//inline (defaut) / online
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Avion',							//label
				'llong' => 'col-2',							//longueur de la zone de titre (pour design inline seulement)
				'lclass' => 'rouge',						//classe du label
				'lalign' => 'right',						//left (defaut) / right / center / jutify  (alignement du label)
				'labelHelp' => 'Aide sur label Avion',		//aide sur le label
				'lpos' => 'before',							//position du label par rapport à l'image : before (defaut) / after
				'clong' => 'col-5',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de l'image
				'border' => true,							//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'images/alphajet.jpg',			//valeur renvoyée dans dbfield
//				'erreur' => true,							//montée erreur
//				'liberreur' => 'TEST ERREUR',				//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',		//Aide sur l'erreur
				'javascript' => 'onclick="alert(\'Avion cliqué\');"', //code javascript associé
				'invisible' => false						//rend invisible (true) le champ
			));

		$this->createField('image', 'image2', array(
				'newLine' => false,							//nouvelle ligne ? false par défaut
				'dbfield' => 'image2',						//retour de la saisie
				'design' => 'online',						//inline (defaut) / online
//				'decalage' => 'col-2',						//décallage en colonnes boostrap
				'label' => 'Avion',							//label
//				'llong' => 'col-2',							//longueur de la zone de titre (pour design inline seulement)
				'lclass' => 'bleu',							//classe du label
				'lalign' => 'right',						//left (defaut) / right / center / jutify  (alignement du label)
				'labelHelp' => 'Aide sur label Avion',		//aide sur le label
				'lpos' => 'after',							//position du label par rapport à l'image : before (defaut) / after
				'clong' => 'col-4',							//longueur du champ en colonnes boostrap
				'cclass' => '',								//classe de l'image
				'border' => false,							//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'images/alphajet.jpg',			//valeur renvoyée dans dbfield
//				'erreur' => true,							//montée erreur
//				'liberreur' => 'TEST ERREUR',				//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',		//Aide sur l'erreur
				'javascript' => 'onclick="alert(\'Avion cliqué\');"', //code javascript associé
				'invisible' => false						//rend invisible (true) le champ
			));

		//---------------------
		//SEARCH
		//---------------------
		$this->createField('separateur', 'sep10', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur10',			//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'SEARCH',					//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 10',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		//ajout d'un champ de recherche
		//dans un champ de type recherche, le bouton fait office de label
		$javascript = '';
		$this->createField('search', 'simple', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'addon' => false,
				'aclass' => 'btn btn-success',
				'apos' => 'before',
//				'complement' => array('choix1' => 'Choix 1', 'separateur', 'choix2' => 'Choix 2', 'choix3' => 'Choix 3', 'separator', 'choix4' => 'Choix 4'),
//				'value' => array('choix3', ''),			//valeur de la saisie
				'dbfield' => 'btrecherchesimple',		//retour de la saisie
				'titre' => 'Champ de recherche simple',	//titre
				'tlong' => 'col-2',						//longueur du titre en colonnes bootstrap
				'tclass' => 'vert',						//classe CSS du titre
				'talign' => 'right',					//alignement du titre
				'titreHelp' => 'Aide sur le titre de la recherche', //aide sur le titre
				'inputType' => '',						//search(defaut), text, time, date, etc.
				'decalage' => 'col-6',					//décallage en colonnes boostrap
				'label' => '',							//libellé du bouton (par défaut "champ") ou une icone loupe si vide ou glyph icon font-awesome "<span class="fas fa-search"></span>"
				'lpos' => 'before',						//position du champ de saisie par rapport au bouton
				'labelHelp' => 'aide sur le champ de recherche',	//aide sur le champ
				'lclass' => 'btn btn-success',			//classe du bouton
				'clong' => 'col-4',						//longueur du bloc champ
				'cclass' => '',							//classe de la zone de saisie
				'maxlength' => 10,						//taille maximum de la saisie en nombre de caractèresz
				'placeholder' => 'recherche',			//placeholder de la saisie
				'spellcheck' => false,					//correction orthographique ?
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//ajout d'un champ de recherche
		//dans un champ de type recherche, le bouton fait office de label
		$javascript = '';
		$this->createField('search', 'recherche', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'addon' => true,
				'aclass' => 'btn btn-success',
				'apos' => 'after',
				'complement' => array('choix1' => 'Choix 1', 'separateur', 'choix2' => 'Choix 2', 'choix3' => 'Choix 3', 'separator', 'choix4' => 'Choix 4'),
				'value' => array('choix3', ''),			//valeur de la saisie
				'dbfield' => 'btrecherche',				//retour de la saisie
				'titre' => 'Champ de recherche avec addon',	//titre
				'tlong' => 'col-2',						//longueur du titre en colonnes bootstrap
				'tclass' => 'bleu',						//classe CSS du titre
				'talign' => 'right',					//alignement du titre
				'titreHelp' => 'Aide sur le titre de la recherche', //aide sur le titre
				'inputType' => '',						//search(defaut), text, time, date, etc.
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => '',							//libellé du bouton (par défaut "champ") ou une icone loupe si vide ou glyph icon font-awesome "<span class="fas fa-search"></span>"
				'lpos' => 'after',						//position du champ de saisie par rapport au bouton
				'labelHelp' => 'aide sur le champ de recherche',	//aide sur le champ
				'llong' => 'col-12',							//col-12 dessine le bouton sur la totalité de sa largeur. Si vide, le bouton est dessiné à la largeur du libellé
				'lclass' => 'btn btn-success',			//classe du bouton
				'clong' => 'col-4',						//longueur du bloc champ
				'cclass' => '',							//classe de la zone de saisie
				'maxlength' => 10,						//taille maximum de la saisie en nombre de caractèresz
				'placeholder' => 'recherche',			//placeholder de la saisie
				'spellcheck' => false,					//correction orthographique ?
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//ajout d'un champ de recherche
		//dans un champ de type recherche, le bouton fait office de label
		$javascript = '';
		$this->createField('search', 'recherche2', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'btrecherche2',			//retour de la saisie
				'inputType' => 'date',					//search(defaut), text, time, date, etc.
				'titre' => 'Recherche d\'une date',		//titre
				'tlong' => 'col-2',						//longueur du titre en colonnes bootstrap
				'tclass' => 'bleu',						//classe CSS du titre
				'talign' => 'right',					//alignement du titre
				'titreHelp' => 'Aide sur le titre de la recherche', //aide sur le titre
				'decalage' => 'col-4',					//décallage en colonnes boostrap
				'label' => 'Recherche date',			//libellé du bouton (ici c'est un glyphicon)
				'lpos' => 'after',						//position du champ de saisie par rapport au bouton
				'labelHelp' => 'aide sur le champ de recherche',	//aide sur le champ
//				'llong' => 'col-12',					//col-12 dessine le bouton sur la totalité de sa largeur. Si vide, le bouton est dessiné à la largeur du libellé
				'lclass' => 'btn btn-warning text-white',	//classe du bouton
				'clong' => 'col-4',						//longueur du bloc champ
				'cclass' => '',							//classe de la zone de saisie
//				'maxlength' => 10,						//taille maximum de la saisie en nombre de caractèresz
				'placeholder' => 'recherche',			//placeholder de la saisie
				'spellcheck' => false,					//correction orthographique ?
				'value' => '',							//valeur de la saisie
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//---------------------
		//FILTRE
		//---------------------
		$this->createField('separateur', 'sep12', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur10',			//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'FILTRE',					//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
//				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 10',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		//filtretext : design 'online'(toujours pour cet objet)
		$javascript = 'onclick="alert(\'Click intercepté\');"';
		$this->createField('filtretext', 'filtre1', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'addon' => true,
				'aclass' => 'btn btn-success',
				'apos' => 'before',
				'complement' => array('tout' => 'Tout', 'commence' => 'Commence par', 'separateur', 'egal' => 'Egal à', 'contient' => 'Ressemble à', 'separator', 'finit' => 'Finit par'),
				'decalage' => 'col-6',					//décallage en colonnes boostrap
				'titre' => 'Filtre textuel',
				'tlong' => 'col-2',
				'tclass' => 'vert',						//classe du titre
				'talign' => 'right',					//cadrage du titre à droite
				'titreHelp' => 'Aide sur le titre',		//Aide sur le titre
				'titreHelpPos' => 'bottom',				//Aide sur le titre
				'label' => 'Juste le label',			//label
				'lclass' => 'btn btn-success',
				'lalign' => 'right',
				//'value' => 'titi',					//valeur de la saisie dans le cas ou addon = false
				'value' => array('egal', 'test'),		//valeur de la saisie (tableau) dans le cas ou addon = true
				'dbfield' => 'filtre1',					//retour de la saisie
				'inputType' => '',						//search(defaut), text, time, date, etc.
				'labelHelp' => 'Aide sur le filtre',	//aide sur le champ
				'labelHelpPos' => 'right',	//aide sur le champ
				'clong' => 'col-4',						//longueur du bloc champ (ici cadré à droite)
				'cclass' => '',							//classe de la zone de saisie
				'cheight' => '',						//hauteur du texte de saisie (lg ou sm)
				'maxlength' => 10,						//taille maximum de la saisie en nombre de caractèresz
				'placeholder' => 'filtre',				//placeholder de la saisie
				'spellcheck' => false,					//correction orthographique ?
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false						//rend invisible le champ
			));

		//filtreselect multiple online
		$this->createField('filtreselect', 'filtre2', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'filtre2',					//retour de la saisie
				'design' => 'online',					//inline (defaut) / online
				'multiple' => true,						//dit si la selection multiple est autorisée dans ce select (false par défaut)
				'size' => 4,							//hauteur du select en nombre de lignes visibles (1 par défaut)
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'filtreselect multiple online',				//label
				'lclass' => 'btn btn-success',			//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur filtre2',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-3',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'cheight' => 'sm',						//hauteur du select (lg ou sm)
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['filtre2'],//valeur de la saisie
				'complement' => 'fillSelect2',			//fonction de callback qui doit remplir le select
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//filtreselect multiple inline
		$this->createField('filtreselect', 'filtre3', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'filtre3',					//retour de la saisie
				'design' => 'inline',					//inline (defaut) / online
				'multiple' => true,						//dit si la selection multiple est autorisée dans ce select (false par défaut)
				'size' => 4,							//hauteur du select en nombre de lignes visibles (1 par défaut)
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'filtreselect multiple inline',	//label
				'lclass' => 'btn btn-success',			//classe du label
				'lalign' => 'right',					//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur filtre3',		//aide sur le label
				'lpos' => 'after',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-3',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'cheight' => 'lg',						//hauteur du select (lg ou sm)
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['filtre3'],//valeur de la saisie
				'complement' => 'fillSelect2',			//fonction de callback qui doit remplir le select
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//filtreselect simple inline
		$this->createField('filtreselect', 'filtre4', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'filtre4',					//retour de la saisie
				'design' => 'inline',					//inline (defaut) / online
//				'multiple' => true,						//dit si la selection multiple est autorisée dans ce select (false par défaut)
//				'size' => 4,							//hauteur du select en nombre de lignes visibles (1 par défaut)
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'filtreselect simple inline',//label
				'lclass' => 'btn btn-success',			//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur filtre4',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-3',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'cheight' => '',						//hauteur du select (lg ou sm)
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['filtre4'],//valeur de la saisie
				'complement' => 'fillSelect',			//fonction de callback qui doit remplir le select
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//filtreselect simple online
		$this->createField('filtreselect', 'filtre5', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'filtre5',					//retour de la saisie
				'design' => 'online',					//inline (defaut) / online
//				'multiple' => true,						//dit si la selection multiple est autorisée dans ce select (false par défaut)
//				'size' => 4,							//hauteur du select en nombre de lignes visibles (1 par défaut)
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'filtreselect simple online',//label
				'lclass' => 'btn btn-success',			//classe du label
				'lalign' => 'left',						//left (defaut) / right / center / jutify
				'labelHelp' => 'Aide sur filtre5',		//aide sur le label
				'lpos' => 'before',						//position du label par rapport au champ : before (defaut) / after
				'clong' => 'col-3',						//longueur de la zone de champ
				'cclass' => '',							//classe de la zone de champ
				'cheight' => '',						//hauteur du select (lg ou sm)
//				'testMatches' => array('REQUIRED'),		//test de la saisie
				'value' => $this->_tab_donnees['filtre5'],//valeur de la saisie
				'complement' => 'fillSelect',			//fonction de callback qui doit remplir le select
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => '',						//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'readonly' => false,					//lecture seule (defield renvoi value si readonly)
				'invisible' => false					//rend invisible le champ
			));

		//---------------------
		//BOUTON
		//---------------------
		$this->createField('separateur', 'sep11', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'separateur11',			//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'label' => 'BOUTON',					//libellé du séparateur
				'lclass' => 'font-weight-bold bleu souligne_epais',	//classe du sé&parateur
				'labelHelp' => 'Ceci est une aide sur le label',//aide sur le séparateur
				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'clong' => 'col-5',						//longueur du séparateur en colonnes bootstrap
				'cclass' => '',							//classe sur le bloc de champ
				'value' => 'Séparateur 11',				//valeur de la saisie
				'invisible' => false					//rend invisible le champ
			));

		//construction bouton Submit
		$javascript = '';
		$this->createField('bouton', 'submit', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'btvalide',				//retour de la saisie
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'inputType' => 'submit',				//submit (defaut), button, reset
				'label' => 'Valider le formulaire',		//label
				'labelHelp' => 'Aide sur bouton Valider', //label
				'llong' => 'col-12',					//col-12 dessine le bouton sur la totalité de sa largeur clong. Si vide, le bouton est dessiné à la largeur du libellé
				'lclass' => 'btn btn-primary',			//classes graphique du bouton
				'clong' => 'col-5',						//longueur de la zone de champ (du bouton)
				'cclass' => '',							//classe personnalisée du bloc de champ
//				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'value' => 'Valider',					//valeur renvoyée dans dbfield
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'invisible' => false					//rend invisible le champ
			));
		//ajout d'un bouton d'annulation de suppression
		$javascript = 'onclick="window.history.back()"';
		$this->createField('bouton', 'retour', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'btbouton',				//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'inputType' => 'button',				//submit (defaut), button, reset
				'label' => 'Retour',					//label
				'llong' => 'col-12',					//col-12 dessine le bouton sur la totalité de sa largeur clong. Si vide, le bouton est dessiné à la largeur du libellé
				'lclass' => 'btn btn-secondary',		//classes graphique du bouton
				'clong' => 'col-1',						//longueur de la zone de champ (du bouton)
				'cclass' => '',							//classe personnalisée du bloc de champ
//				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'value' => 'Test retour',				//valeur renvoyée dans dbfield
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'invisible' => false					//rend invisible le champ
			)); 		
		//ajout d'un bouton d'annulation d'édition si on est en édition
		$javascript = '';
		$this->createField('bouton', 'raz', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'btretour',				//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'inputType' => 'reset',					//submit (defaut), button, reset
				'label' => 'Reset',						//label
				'llong' => 'col-12',					//col-12 dessine le bouton sur la totalité de sa largeur clong. Si vide, le bouton est dessiné à la largeur du libellé
				'lclass' => 'btn btn-secondary',		//classes graphique du bouton
				'clong' => 'col-1',						//longueur de la zone de champ (du bouton)
				'cclass' => '',							//classe personnalisée du bloc de champ
//				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'value' => 'Reset',						//valeur renvoyée dans dbfield
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'invisible' => false					//rend invisible le champ
			));

		//ajout d'un bouton d'annulation d'édition si on est en édition
		$javascript = '';
		$this->createField('bouton', 'search', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'dbfield' => 'btsearch',				//retour de la saisie
				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'inputType' => 'submit',				//submit (defaut), button, reset
				'label' => '<span class="fas fa-search"></span>',	//label
				'llong' => 'col-12',					//col-12 dessine le bouton sur la totalité de sa largeur. Si vide, le bouton est dessiné à la largeur du libellé
				'lclass' => 'btn btn-success',			//classes graphique du bouton
				'clong' => 'col-1',						//longueur de la zone de champ (du bouton)
				'cclass' => '',							//classe personnalisée du bloc de champ
//				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'value' => 'Ok Search',					//valeur renvoyée dans dbfield
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'invisible' => false					//rend invisible le champ
			));

		//ajout d'un bouton d'annulation d'édition si on est en édition
		$javascript = '';
		$this->createField('bouton', 'filtre', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'btfiltre',				//retour de la saisie
//				'decalage' => 'col-2',					//décallage en colonnes boostrap
				'inputType' => 'submit',				//submit (defaut), button, reset
				'label' => 'OK',						//label
				'labelHelp' => 'Aide sur le bouton filtre',	//label
				'llong' => '',							//col-12 dessine le bouton sur la totalité de sa largeur. Si vide, le bouton est dessiné à la largeur du libellé
				'lclass' => 'filtre',					//classes graphique du bouton
				'clong' => 'col-1',						//longueur de la zone de champ (du bouton)
				'cclass' => '',							//classe personnalisée du bloc de champ
				'border' => 'margin-left:.2rem;padding-bottom:.5rem;border-bottom:2px dotted silver;', //bordure personnalisée
				'value' => 'ok',						//valeur renvoyée dans dbfield
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'invisible' => false					//rend invisible le champ
			));

		//ajout d'un bouton d'annulation d'édition si on est en édition
		$javascript = '';
		$this->createField('bouton', 'btflex1', array(
				'newLine' => true,						//nouvelle ligne ? false par défaut
				'flexLine' => 'flex-row-reverse',		//les élémenjts de la nouvelle ligne seront cadrés à droite
														//essayer avec , flex-md-row-reverse, justify-content-center, justify-content-lg-center, justify-content-between, justify-content-around
				'dbfield' => 'btflex1',					//retour de la saisie
				'inputType' => 'submit',				//submit (defaut), button, reset
				'label' => 'Bouton 1',					//label
				'labelHelp' => 'Boutons 1 cadré à a droite',//labelhelp
				'llong' => 'col-12',					//col-12 dessine le bouton sur la totalité de sa largeur. Si vide, le bouton est dessiné à la largeur du libellé
				'lclass' => 'btn btn-success',			//classes graphique du bouton
				'clong' => 'col-12 col-sm-4 col-xl-2',	//longueur de la zone de champ (du bouton)
				'cclass' => '',							//classe personnalisée du bloc de champ
//				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'value' => 'btflex1',					//valeur renvoyée dans dbfield
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'invisible' => false					//rend invisible le champ
			));
		//ajout d'un bouton d'annulation d'édition si on est en édition
		$javascript = '';
		$this->createField('bouton', 'btflex2', array(
				'newLine' => false,						//nouvelle ligne ? false par défaut
				'dbfield' => 'btflex2',					//retour de la saisie
				'inputType' => 'button',				//submit (defaut), button, reset
				'label' => 'Bouton 2',					//label
				'labelHelp' => 'Boutons 2 cadré à a droite',//labelhelp
				'labelHelpPos' => 'bottom',	
				'llong' => 'col-12',					//col-12 dessine le bouton sur la totalité de sa largeur. Si vide, le bouton est dessiné à la largeur du libellé
				'lclass' => 'btn btn-warning',			//classes graphique du bouton
				'clong' => 'col-12 col-sm-4 col-xl-2',	//longueur de la zone de champ (du bouton)
				'cclass' => '',							//classe personnalisée du bloc de champ
//				'border' => false,						//defaut : false. false / true(encadrement par défaut) ou bordure personnnalisée
//				'border' => 'border-top:2px dotted silver;', //bordure personnalisée
				'value' => 'btflex2',					//valeur renvoyée dans dbfield
//				'erreur' => true,						//montée erreur
//				'liberreur' => 'TEST ERREUR',			//libellé de l'erreur
//				'liberreurHelp' => 'libelle erreur',	//Aide sur l'erreur
				'javascript' => $javascript,			//code javascript associé
				'enable' => true,						//active, désactive le champ (dbfield renvoie NULL si false)
				'invisible' => false					//rend invisible le champ
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

	//charger les données de l'utilisateur à partir d'une base de données
	public function charger($id) {
/*		//charger les données à afficher dans le formulaire : $id est l'id unique du tuple à afficher
		$tuple = ...
		//chargement des données récupérées dans la structure de données du formulaire 
		$this->setIdTravail($id);
		$this->_tab_donnees['data1'] = $tuple['data1'];
		$this->_tab_donnees['data2'] = $tuple['data2'];
		$this->_tab_donnees['data3'] = $tuple['data3'];
		$this->_tab_donnees['data4'] = $tuple['data4'];
		$this->_tab_donnees['data5'] = $tuple['data5'];
		$this->_tab_donnees['data6'] = $tuple['data6'];
*/		$this->construitChamps();
		return true;  //ou false si erreur de chargement données
	}

	//--------------------------------------
	// Tests supplémentaires sur certains champs, en plus des test de			
	// validation définit à la construction	par le paramètre : testMatches
	// $champ : nom du champ testé
	//--------------------------------------

	protected function testsSupplementaires($champ) {
/*		if ($champ->idField() == 'data1') {
			if (fonction_test_existence_dans_base($champ->value())) {
				$champ->setErreur(true);
				$champ->setLiberreur('Cette valeur existe déjà dans la base de données');
			}
			return $champ->erreur();
		}
*/		return false; //pas d'erreur
	}

	//--------------------------------------
	// Tests supplementaires postérieurs.	
	// Executés une fois que tous les tests supplémentaires unitaires par champ	on été réalisés.
	// Implémenté pour tester la corrélation d'un champ saisi par rapport à un autre.
	// Comme il faut attendre la validation du formulaire pour faire ces tests, cette méthode est faite pour cela
	//--------------------------------------

	protected function testsSupplementairesPosterieurs() {
/*		$fieldData2 = $this->field('data2');
		$fieldData3 = $this->field('data3');
		if ($fieldData3->value() > $fieldData2->value()) {
			$fieldData3->setErreur(true);
			$fieldData3->setLiberreur('Le champ data3 ne doit pas être supérieur au champ data2');
			return true;
		}
*/		return false;		//pas d'erreur
	}

	//--------------------------------------
	// affichage du formulaire
	//--------------------------------------
	public function afficher() {
		parent::afficher();		//permet d'ajouter des tests de construction du formulaire
		//ATTENTION : les champs disabled ne renvoient aucun POST !!! Donc impossible de récupérer les données depuis une suppression
		$enable = (!(($this->getOperation() == self::CONSULTER) || ($this->getOperation() == self::SUPPRIMER)));
		$chaine = '';

		$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
			$chaine.= '<fieldset class="border p-3">';
				$chaine.= '<h1>Formulaire</h1>';
				$chaine.= $this->draw($enable);
				$chaine.= '<p class="small">(*) Champ requis (1) Lecture seule</p>';
			$chaine.= '</fieldset>';
		$chaine.= '</form>';

		return $chaine;
	}

}