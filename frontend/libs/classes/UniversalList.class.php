<?php
//------------------------------------------------------------------
// Classes UniversalList, UniversalListColonne, UniversalListFormFiltres et UniversalListFiltreExterne
// Gèrent une liste complexe dont les colonnes possèdes des filtres
//------------------------------------------------------------------
// - Fonctionnement des filtres externes :
// La classe "UniversalList" est spécialisées dans la création de tables de type listes (<table> au sens HTML)
// possedant un certain nombre de colonnes dont les informations sont stockées dans le tableau _cols d'objets "Colonne". 
// Chaque colonne peut contenir un filtre qui s'appuie sur un champ de colonne SQL.
// Il est aussi possible de gérer des filtres externes, c'est à dire non
// directement associés à une colonne comme un champ de recherche simple ou multiple (avec addon).
// A la manière des colonnes, tous les filtres externes sont stockées dans le tableau _filtresExternes d'objets "UniversalListFiltreExterne". 
// Chaque objet "UniversalListFiltreExterne" est composé des propriété suivantes : 
//		_filtreType		type de filtre externe 'search' ou 'multisearch' (pour les prédéfinis) ou autre
//		_filtreScope	tableau de couple valeur => libellé pour le addon du champ de classe UniversalFieldSearch
//		_filtreRange	étendue de la recherche sur la colonne (UniversalListColonne::TOUT/EGAL/DIFFERENT/COMMENCE/CONTIENT/CONTIENTPAS/FINIT)
//		_filtreValue	valeur de la recherche pour le filtre
// Il est tout à fait possible de mixer filtres de colonnes ET filtres externe. 
// Veiller cependant à ce que deux filtres ne pointent pas sur le même champ SQL : c'est possible mais
// si tel est le cas, le filtre externe aura la priorité sur le filtre de colonne, puisque traité en dernier.
//------------------------------------------------------------------
// éè : pour enregistrement UTF-8
// Auteur : Fabrice Labrousse
// Date : 14 mars 2017
//------------------------------------------------------------------
// 25.07.2017 : Mise à jour du flag _filtrageEnCours après un chargement depuis la base de données
// 26.07.2017 : Création des méthodes publiques setCols() et getCols()
// 25.10.2017 : Création de la méthode getDisplayedSize()
// 15.11.2017 : Ajout des filtres externes (bouton de recherche par exemple)
// 16.11.2017 : Création de l'objet FiltreExterne en remplacement du tableau pour une meilleure gestion
//				Intégration de la classe "Colonne" dans le même fichier que la classe "Listing"
// 22.11.2017 : Ajout de la propriété nbLignesParPages et de son getter et setter. Permet de se mémorer 
//				cette info poour un usage extérieur à la classe comme usage classe PageNavigator
// 01.12.2017 : Ajout de la méthod getData() pour demander à charger les données depuis SQL
//				Ajout de la propriété sqlLimitStart et de son getter et setter. Elle permet de mémoriser
//				l'indice de démarrage d'affichage dans les tuples recherchés de la base se données, c'est à dire
//				le tuple à afficher sur la première ligne de la page en cours.
//------------------------------------------------------------------
// 19.12.2017 : VERSION 2.0.0
//	-La version 2 encapsule automatiquement le fomulaire de filtres de colonnes. 
//	Donc économie d'1 fichier class 'form_', beaucoup moins de code à écrire et 
//	meilleure gestion de l'ensemble.
// 21.12.2017 : VERSION 2.1.0
//	- Amélioration pour prise en compte de filtres externes standards (search/multiseasch/checkbox)
// 12.01.2018 : VERSION 2.2.0
//	- Renommé parametre de colonne sens en triSens
//	- Renommé méthode getSens() en getTriSens()
//	- Renommé méthode setSens() en setTriSens()
//	- Renommé propriété privée _sensEncours en _triSensEncours
//	- Renommé propriété privée _sensDefault en _triSensDefault
//	- Renommé méthode getSensEncoursLibelle() en getTriSensEncoursLibelle()
//	- Ajout du paramètre de colonne titlePos qui positionne le title (top/right/bottom/left)
// 16.01.2018 : VERSION 2.3.0
//	- Les méthodes getCols() et setCols() redeviennet publiques et non privées
//	- Ajout des méthodes publiques cols() et col($id) pour recupérer les objets UniversalColonnes de la liste
//	- Ajout de la constante NB_LIGNES_PAR_PAGE qui renvoie le nombre de lignes max par page par défaut de la classe
//	- Ajout de la constante SHOW_BUTTONS qui renvoie le type de bouttons de validation par defaut de la classe
//	- Création de la méthode getShowButtonsState() qui renvoie le style de boutons du formulaire (true : boutons standards, false : boutons simplifiés)
//	- Correction disfonctionnement méthode setFiltre(). Elle ne mettait pas à jour le formulaire
//	- Correction disfonctionnement méthode setCols(). Elle ne mettait pas à jour le formulaire
// 16.04.2018 : VERSION 2.4.0
//	- Ajout	de la méthode publique setTriEncours() (modification du tri en cours)
//	- Ajout de la méthode publique setTriSensEncours() (modification du sens du tri en cours)
//	- Ajout de la méthode publique getTriEncours() qui renvoie le champ trié en cours
//	- Ajout de la méthode publique getTriSensEncours() qui renvoie le champ trié en cours
// 12.12.2018 : VERSION 2.5.0
//	- Ajout dela méthode publique UniversalListColonne::setFiltreValueDefault() (elle était jusque là seulement privée)
//	- Ajout de la méthode publique UniversalList::colExist($id) qui renseigne si la colonne identifiée par $id existe dans la liste (renvoie true ou false)
// 07.02.2019 : VERSION 2.5.1
//	- Correction de la méthode createTable() pour corriger la structure de la table Listing. tinyint(3) à la place de tinyint(3)
// 14.03.2019 : VERSION 2.6.0
//	- Ajout des méthodes publiques UniversalList::setHeadClass() et UniversalList::getHeadClass() pour modifier la classe CSS de l'entête du tableau
//	- Ajout des méthodes publiques UniversalList::setFiltresClass() et UniversalList::getFiltresClass() pour modifier la classe CSS du bandeau de filtres du tableau (valeur défaut "thead-light")
//	- Ajout du paramètre "header" dans la construction de la colonne de la table. (objet UniversalListColonne) : true (la colonne est une entetre pour la ligne), false sinon (valeur par défaut)
//	ce paramètre indique si la donnée de la colonne doit servir d'entête pour la ligne.
//	- Ajout des méthodes UniversalListColonne::setHeader() et UniversalListColonne:: getHeader() pour prendre en compte le nouveau paramètre "header"
//	- Affichage du listing : supprimé la taille de la colonne pour permettre à du code javascript de la modifier en drag & drop 
//	(voir code https://www.brainbell.com/javascript/making-resizable-table-js.html)
// 16.04.2019 : VERSION 2.7.0
//	- Modification de la table xx_listings : ajout du champ last_update qui donne le timestamp de la création de la liste et de sa dernière modification (public function createTable())
// 07.01.2020 : VERSION 2.8.0
//	- Modification des filtres de colonnes en small
// 08.01.2020 : VERSION 2.9.0
//	- Ajout des méthodes privées UniversalListFormFiltres::_translate et UniversalListFiltreExterne::_translate qui prennent en charge la traduction de certaines 
//	  chaines de caractères (ex : les placeholders)
//------------------------------------------------------------------
// 09.01.2020 : VERSION 3.0.0
//	Attention : le code écrit avec des version inférieures ne fonctionne plus
//	- Changement de tous les mots clés de test (constantes de la classe UniversalListColonne)en version anglaise pour une meilleurs compréhension du fonctionnement
//		TOUT => CMP_ALL									valeur (ALL)
//		EGAL => CMP_EQUAL								valeur (EQL)
//		DIFFERENT => CMP_DIFFERENT						valeur (DIF)
//		COMMENCE => CMP_BEGINS_BY						valeur (BEG)
//		CONTIENT => CMP_CONTENDS						valeur (CON)
//		CONTIENTPAS => CMP_DO_NOT_CONTENDS				valeur (DNC)
//		FINIT => CMP_ENDS_BY							valeur (END)
//		IGNORE => CMP_IGNORE							valeur (IGN)
//		COMMENCENUM => CMP_BEGINS_BY_NUMBER				valeur (BBN)
//		SUPERIEURA => CMP_GREATER_THAN					valeur (GRT)
//		SUPERIEUROUEGALA => CMP_GREATER_OR_EQUAL_TO		valeur (GET)
//		INFERIEURA => CMP_LOWER_THAN					valeur (LOT)
//		INFERIEUROUEGALA => CMP_LOWER_OR_EQUAL_TO		valeur (LET)
//		EGALA => EQUAL_TO								valeur (ETO)
//	- Remplacement des mots clés 'TOUT' et 'TOUTES' et par la constante UniversalListColonne::CMP_ALL pour désigner la sélection de toutes les valeurs d'un filtre select (méthode _buildFiltreSelect)
// 13.01.2020 : VERSION 3.0.1
//	- Correction bugs avec usage IGN qui ne fonctionnait plus suites à modifs v3.0.0
// 31.01.2020 : VERSION 3.0.2
//	- Correction méthode drawBody : remplacé tag html 'align=' par class='text-'
// 31.01.2020 : VERSION 3.1.0
//	- Les filtres externes de type 'search' (donc les plus simples) peuvent maintenant effectuer leur recherche sur plusieurs champs. Pour ce faire, il suffit de saisir dans le paramètre 'filtreScope' du 
//		filtre externe la liste des champs à interroger séparés par le caractère pipe (|) ex : 'filtreScope' => 'titre|resume'
//------------------------------------------------------------------
// 10.02.2020 : VERSION 4.0.0 (ancien code V3 compatible)
//	Extension d'utilisation des filtres externes de type 'none' (cad non graphique) pour envoyer directement des bribes complexes de SQL : Ceci a impliqué les modifications suivantes
//	- Ajout du mot cle CMP_SQL (valeur 'SQL') pour signifier un filtre externe qui envoi son propre code SQL (uniquement pour les types 'none')
//	- Correction du constructeur UniversalFiltreExterne => pas de création d'objet UniversalForm pour les filtres externes de type 'none' puisque ils sont non graphique (gain mémoire)
//	- Méthode UniversalListFiltreExterne::getActif modifiée pour que les filtres externes de type 'none' renvoient la valeur brute du champ _actif
//	- Modification du filtre externe checkbox (n'affiche plus une checkbox mais un switch customisable via le nouveau paramètre filtreCustom uniquement valable pour ce type de filtre externe)
// 15.03.2020 : Version 4.1.0
//	- Ajout des méthodes setFiltreTag et getFiltreTag sur les filtres externes. Permet de passer n'importe quelle information supplémentaire
// 19.03.2020 : Version 4.2.0
//	- passage de la fonction createTable() en fonction statique
// 05.05.2020 : Version 4.2.1
//	- Correction méthode afficher : remplacé tags html 'align=' et 'size=' par class='text-' et class='uw-w'
//	- Amélioré présentation des filtres de colonnes (affiche maintenant un filtre checkbox avec label au-dessus de la checkbox)
//	- Rajouté cursor:pointer sur label filtres checkbox (lclass) (attention : classe 'uw-pointer' déclarée dans universalweb.css)
// 05.05.2020 : Version 5.0.0 
//	- filtre de colonne checkbox : remplacement de l'objet 'UniversalFieldCheckbox' par le nouvel objet 'UniversalFieldFiltrecheckbox' spécialement conçu. Ceci permet de simplifier 
//		l'usage et la génération de code pour ce type de filtre.
//	- Version minimale de UniversalForm : 3.22
//------------------------------------------------------------------

//*****************************************************************************************
//
// Classe UniversalListColonne
// Gere une colonne de liste UniversalList
//
//*****************************************************************************************

class UniversalListColonne {

	private $_id					 = '';			//id de la colonne
	private $_order					 = 1;			//ordre d'affichage de la colonne
	private $_libelle				 = 'NAME';		//libellé a afficher de la colonne
	private $_title					 = '';			//title sur la colonne
	private $_titlePos				 = '';			//title sur la colonne
	private $_size					 = 10;			//taille de la colonne en pourcentage de la liste
	private $_align					 = 'left';		//alignement de la colonne (left / center/ right)
	private $_tri					 = false;		//booléen dit si on peut trier sur cette colonne
	private $_triSql				 = '';			//champ SQL concerné par le tri eventuel de la colonne
	private $_triSqlSecondaire		 = '';			//champ SQL secondaire concerné par le tri eventuel de la colonne
	private $_triLibelle			 = '...';		//libellé d'affichage du tri en clair
	private $_triSens				 = 'ASC';		//sens du tri eventuel de la colonne (ASC, DESC)
	private $_filtre				 = false;		//booléen champ filtré ou non
	private $_filtreType			 = 'text';		//text / select / checkbox (type de filtre de colonne "texte", "selecteur" ou "checkbox")
	private $_filtreActif			 = true;		//boooléen filtre actif ou non
	private $_filtreScope			 = '';			//étendue des données recevables par le filtre (ex : tout/egal/different/commence/contient/contientpas/finit)
	private $_filtreRange			 = '';			//range : indice du choix parmi l'étendue des données
	private $_filtreRangeDefault	 = '';			//range : valeur par défaut
	private $_filtreValue			 = '';			//valeur de la recherche sur la colonne
	private $_filtreValueDefault	 = '';			//valeur de la recherche par défaut
	private $_filtreSqlField		 = '';			//nom du champ SQL ciblé par le filtre
	private $_filtreCaption			 = '';			//libelle sur le filtre (filtres select et checkbox seulement)
	private $_filtreColor			 = 'primary';	//Couleur du champ de filtrage UniversalForm
	private $_filtreHelp			 = '';			//libelle d'aide sur le filtre
	private $_display				 = true;		//booléen affichage de la colonne
	private $_header				 = false;		//booléen indique si la colonne contient l'information d'entete pour la ligne (scope="row")

	//mots clés utilisés par les classes pour les filtres de type 'text'
	//pour info les valeurs passées à ces constantes ne sont jamais utilisées, on pourrait y mettre n'importe quoi (0..100 par ex), elle permettent juste de donner une unicité à chaque constante
	//ce sont les constantes qui sont utilisées !
	const CMP_ALL					= 'ALL';
	const CMP_SQL					= 'SQL';		//ajout V4.0.0
	const CMP_EQUAL					= 'EQL';
	const CMP_DIFFERENT				= 'DIF';
	const CMP_BEGINS_BY				= 'BEG';
	const CMP_CONTENDS				= 'CON';
	const CMP_DO_NOT_CONTENDS		= 'DNC';
	const CMP_ENDS_BY				= 'END';
	const CMP_IGNORE				= 'IGN';
	const CMP_BEGINS_BY_NUMBER		= 'BBN';

	//pour les comparaisons numériques
	const CMP_GREATER_THAN			= 'GRT';
	const CMP_GREATER_OR_EQUAL_TO	= 'GET';
	const CMP_LOWER_THAN			= 'LOT';
	const CMP_LOWER_OR_EQUAL_TO		= 'LET';
	const EQUAL_TO					= 'ETO';

	//exemples de menus en français directement utilisables pour les filtres de type 'text'
	const MENU = array(
					   self::CMP_ALL => 'Tous', 
					   self::CMP_EQUAL => 'Egal à', 
					   self::CMP_DIFFERENT => 'Différent de', 
					   self::CMP_BEGINS_BY => 'Commence par', 
					   self::CMP_CONTENDS => 'Contient', 
					   self::CMP_DO_NOT_CONTENDS => 'Ne contient pas', 
					   self::CMP_ENDS_BY => 'Se termine par');
	const MENU_IGNORE = array(
					   self::CMP_ALL => 'Tous', 
					   self::CMP_EQUAL => 'Egal à', 
					   self::CMP_DIFFERENT => 'Différent de', 
					   self::CMP_BEGINS_BY => 'Commence par', 
					   self::CMP_CONTENDS => 'Contient', 
					   self::CMP_DO_NOT_CONTENDS => 'Ne contient pas', 
					   self::CMP_ENDS_BY => 'Se termine par',
					   self::CMP_IGNORE => 'Ignorer le champ');

	//=======================================
	// Constructeur
	// Entrée : $donnees : tableau des paramètres de caractérisation de la colonne.
	//	les paramètres possibles sont : 
	//		order				//ordre d'affichage de la colonne
	//		libelle				//libellé à afficher en entete de la colonne
	//		title				//title sur la colonne
	//		titlePos			//positionnement du title de la colonne (top / right / bottom / left)
	//		size				//taille de la colonne en %
	//		align				//alignement de la colonne (left / center/ right)(defaut left)
	//		tri					//booléen dit si on peut trier sur cette colonne
	//		triSql				//champ SQL concerné par le tri eventuel de la colonne
	//		triSqlSecondaire	//champ SQL secondaire concerné par le tri eventuel de la colonne
	//		triLibelle			//libellé d'affichage du tri en clair
	//		triSens				//sens du tri eventuel de la colonne (ASC, DESC)
	//		filtre				//booleen : indique la présence d'un filtre sur ce champ
	//		filtreType			//text / select / checkbox (type de filtre de colonne "texte", "selecteur" ou "checkbox")
	//		filtreActif			//booleen : fitre actif ou non
	//		filtreScope			//étendue des données recevables par le filtre (ex : tout/egal/different/commence/contient/contientpas/finit)
	//		filtreRange			//range : indice du choix parmi l'étendue des données
	//		filtreValue			//valeur de la recherche sur la colonne
	//		filtreSqlField		//nom du champ SQL ciblé par le filtre
	//		filtreCaption		//libelle sur le filtre (filtres "select" seulement)
	//		filtreColor			//Couleur du champ de filtrage UniversalForm
	//		filtreHelp			//libelle d'aide sur le filtre
	//		display				//booleen affichage ou pas de la colonne
	//=======================================

	public function __construct(array $donnees) {
		$this->_hydrate($donnees);
	}

	//=======================================
	// Méthodes privées
	//=======================================

	//hydratation des données : c'est à dire le remplissage des propriétés de l'objet
	private function _hydrate(array $donnees) {
		foreach ($donnees as $key => $value) {
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
		//correction a faire à la fin
		if ($this->_filtre == true) {
			if ($this->_filtreType == 'text') {
				$this->_filtreRangeDefault = $this->_filtreRange;
				$this->_filtreValueDefault = $this->_filtreValue;
			}
			if ($this->_filtreType == 'select') {
				$this->_filtreRangeDefault = self::CMP_EQUAL;			//on force à 'egal' car un select cherche l'égalité
				$this->_filtreValueDefault = $this->_filtreValue;
				//dans le cas d'un CMP_IGNORE au démarrage, on désactive le filtre
				$this->_filtreActif = ($this->_filtreValue !== UniversalListColonne::CMP_IGNORE); 
			}
			if ($this->_filtreType == 'checkbox') {
				$this->_filtreRangeDefault = self::CMP_EQUAL;			//on force à 'egal' car on cherche l'égalité de la valeur du check coché
				$this->_filtreValueDefault = $this->_filtreScope[0];	//la valeur par défaut est le premier paramètre du scope (le deuxième est la valeur renvoyées si la case est cochée)
			}
		}
		else {
			$this->setFiltreActif(false);
			$this->setFiltreRange('');
			$this->setFiltreValue('');
		}
	}

	//=======================================
	// Setters
	//=======================================

	public function setId($valeur)					{$this->_id = $valeur;}
	public function setOrder($valeur)				{$this->_order = $valeur;}
	public function setLibelle($valeur)				{$this->_libelle = $valeur;}
	public function setTitle($valeur)				{$this->_title = $valeur;}
	public function setTitlePos($valeur)			{$this->_titlePos = $valeur;}
	public function setSize($valeur)				{$this->_size = $valeur;}
	public function setAlign($valeur)				{$this->_align = $valeur;}
	public function setTri($valeur)					{$this->_tri = $valeur;}
	public function setTriSql($valeur)				{$this->_triSql = $valeur;}
	public function setTriSqlSecondaire($valeur)	{$this->_triSqlSecondaire = $valeur;}
	public function setTriLibelle($valeur)			{$this->_triLibelle = $valeur;}
	public function setTriSens($valeur)				{$this->_triSens = $valeur;}
	public function setFiltre($valeur)				{$this->_filtre = $valeur;}
	public function setFiltreType($valeur)			{$this->_filtreType = $valeur;}
	public function setFiltreActif($valeur)			{$this->_filtreActif = $valeur;}
	public function setFiltreScope($valeur)			{$this->_filtreScope = $valeur;}
	public function setFiltreRange($valeur)			{$this->_filtreRange = $valeur;}
	public function setFiltreValue($valeur)			{$this->_filtreValue = $valeur;}
	public function setFiltreValueDefault($valeur)	{$this->_filtreValueDefault = $valeur;}
	public function setFiltreSqlField($valeur)		{$this->_filtreSqlField = $valeur;}
	public function setFiltreCaption($valeur)		{$this->_filtreCaption = $valeur;}
	public function setFiltreColor($valeur)			{$this->_filtreColor = $valeur;}
	public function setFiltreHelp($valeur)			{$this->_filtreHelp = $valeur;}
	public function setDisplay($valeur)				{$this->_display = $valeur;}
	public function setHeader($valeur)				{$this->_header = $valeur;}

	//=======================================
	// Getters
	//=======================================

	public function getId()							{return $this->_id;}
	public function getOrder()						{return $this->_order;}
	public function getLibelle()					{return $this->_libelle;}
	public function getTitle()						{return $this->_title;}
	public function getTitlePos()					{return $this->_titlePos;}
	public function getSize()						{return $this->_size;}
	public function getAlign()						{return $this->_align;}
	public function getTri()						{return $this->_tri;}
	public function getTriSql()						{return $this->_triSql;}
	public function getTriSqlSecondaire()			{return $this->_triSqlSecondaire;}
	public function getTriLibelle()					{return $this->_triLibelle;}
	public function getTriSens()					{return $this->_triSens;}
	public function getFiltre()						{return $this->_filtre;}
	public function getFiltreType()					{return $this->_filtreType;}
	public function getFiltreActif()				{return $this->_filtreActif;}
	public function getFiltreScope()				{return $this->_filtreScope;}
	public function getFiltreRange()				{return $this->_filtreRange;}
	public function getFiltreRangeDefault()			{return $this->_filtreRangeDefault;}
	public function getFiltreValue()				{return $this->_filtreValue;}
	public function getFiltreValueDefault()			{return $this->_filtreValueDefault;}
	public function getFiltreSqlField()				{return $this->_filtreSqlField;}
	public function getFiltreCaption()				{return $this->_filtreCaption;}
	public function getFiltreColor()				{return $this->_filtreColor;}
	public function getFiltreHelp()					{return $this->_filtreHelp;}
	public function getDisplay()					{return $this->_display;}
	public function getHeader()						{return $this->_header;}

	//=======================================
	// Méthodes publiques
	//=======================================

	public function colonneActive() {
		return (
			($this->getDisplay()) && 
			($this->getFiltre()) &&
			($this->getFiltreActif())
			);
	}
	
}



//*****************************************************************************************
//
// Classe UniversalListFiltreExterne
// Gères les filtres externes, CAD hors des filtres de colonnes
//
//*****************************************************************************************

class UniversalListFiltreExterne {

	private $_id = '';														//id (nom) du filtre externe
	private $_type = 'externe';												//'externe'
	private $_actif = false;												//fitre actif ou non (true / false) - actif signifie qu'il est utilisé par l'utilisateur (saisie non vide)
	private $_libelle = 'NAME';												//libellé du filtre à afficher (checkbox seulement)
	private $_sqlFields = array();											//tableau de champs SQL concernés par le filtre ('indice au choix : un nombre, un libellé' => 'champ SQL')
	private $_universalForm = null;											//valeur de la recherche sur la colonne	concernée
	private $_filtreType			= 'search';								//type de filtre externe 'search' ou 'multisearch' (pour les prédéfinis) ou autre
	private $_filtreCustom			= 'checkbox';							//Customisation du filtre externe (réservé aux type checkbox)
	private $_filtreScope			= '';									//tableau de couple valeur => libellé pour le addon du champ de classe UniversalFieldSearch
	private $_filtreRange			= UniversalListColonne::CMP_CONTENDS;	//étendue de la recherche sur la colonne (par défaut comparaison sur 'contient')
	private $_filtreRangeDefault	= UniversalListColonne::CMP_CONTENDS;	//range : valeur par défaut
	private $_filtreValue			= '';									//valeur de la recherche pour le filtre
	private $_filtreValueDefault	= '';									//valeur du filtre par défaut
	private $_filtreColor			= 'primary';							//Couleur du champ de filtrage UniversalForm
	private $_filtreHelp			= '';									//libelle d'aide sur le filtre
	private $_filtreTag				= '';									//On y met ce que l'on veux, cela permet de communiquer avec le filtre

	public function __construct(array $donnees) {
		$this->_hydrate($donnees);
		//préparation / modification des champs SQL, scope et value
		$this->_saveSqlFields($this->_filtreScope, $this->_filtreValue);
		//sauve les valeurs par défaut
		$this->_filtreRangeDefault = $this->_filtreRange;
		$this->_filtreValueDefault = $this->_filtreValue;

		//construction du formulaire UniversalForm
		if ($this->_filtreType != 'none') {									//correction V4.0.0 => pas de création d'objet UniversalForm pour les filtres externes de type 'none' puisque non graphique
			//on commence par un seul type de filtre externe : search avec addon
			$this->_universalForm = new UniversalForm('fe'.$this->_id, rand (1000, 9999));
			$this->_universalForm->createField('hidden', 'soumissionFormulaire', array(
				'value' => 'ok'
				));
			if ($this->_filtreType == 'search') {
				//type search avec addon
				$this->_universalForm->createField('search', $this->_id, array(
					'dbfield' => $this->_id,
					'inputType' => 'search',
					'label' => '<span class="fas fa-search"></span>',
					'labelHelp' => $this->_filtreHelp,
					'lpos' => 'before',
					'lclass' => 'btn btn-'.$this->_filtreColor,
					'maxlength' => 255,
					'placeholder' => $this->_translate('RECHERCHE', 'recherche'),
					'value' => $this->_filtreValue
				));
			}
			elseif ($this->_filtreType == 'multisearch') {
				//type search avec addon
				$this->_universalForm->createField('search', $this->_id, array(
					'dbfield' => $this->_id,
					'inputType' => 'search',
					'addon' => true,
					'apos' => 'before',
					'aclass' => 'btn btn-'.$this->_filtreColor,
					'complement' => $this->_filtreScope,
					'label' => '<span class="fas fa-search"></span>',
					'labelHelp' => $this->_filtreHelp,
					'lpos' => 'before',
					'lclass' => 'btn btn-'.$this->_filtreColor,
					'maxlength' => 255,
					'placeholder' => $this->_translate('RECHERCHE', 'recherche'),
					'value' => $this->_filtreValue
				));
			}
			elseif ($this->_filtreType == 'checkbox') {
				//type search avec addon
				$this->_universalForm->createField('switch', $this->_id, array(
					'dbfield' => $this->_id,
					'label' => $this->_libelle,
					'custom' => $this->_filtreCustom,
					'labelHelp' => $this->_filtreHelp,
					'clong' => 'col-12',
					'javascript' => 'onchange="submit()"',
					'value' => 1,
					'valueInverse' => 0,
					'checked' => ($this->_actif == true)
				));
				/* Ancien filtre checkbox : a été remplacé par un switch customisable bootstrap dans la V4.0.0
				$this->_universalForm->createField('checkbox', $this->_id, array(
					'dbfield' => $this->_id,
					'dpos' => 'alone',
					'label' => $this->_libelle,
					'lpos' => 'after',
					'labelHelp' => $this->_filtreHelp,
					'clong' => 'col-12',
					'javascript' => 'onchange="submit()"',
					'value' => 1,
					'valueInverse' => 0,
					'checked' => ($this->_actif == true)
				)); */
			}
		}
	}

	//-------------------------------------
	// traduit un terme dans la langue en cours
	// Entrée : $mnemo : ménemonique à traduire
	//		    $ou : texte à renvoyer si échec de la traduction (ex : aucun fichier de traduction en cours)
	// Sortie : la traduction
	//-------------------------------------
	private function _translate($mnemo, $ou) {
		if (function_exists('getLib')) {
			if (existeLib($mnemo)) {
				return getLib($mnemo);
			}
		}
		return $ou;
	}

	//hydratation des données privées : c'est à dire le remplissage des propriétés privées de l'objet
	private function _hydrate(array $donnees) {
		foreach ($donnees as $key => $value) {
			$method = '_set'.ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}
	
	//pour les filtres externes de type 'multisearch' on prépare les données (champs SQL, retouche Scope et value pour correspondre à l'indice du addon)
	//$_sqlFields = (tableau)	Array ([0] => titre, [1] => genre))
	//$_filtreScope = (tableau)	Array ([0] => Titre, [1] => Genre))
	//$filtreValue = (tableau)	Array ([0] => 0,     [1] => )
	//pour les filtres externes de type 'search' c'est plus siple : pas de addon => pas de scope => pas de tableaux
	private function _saveSqlFields($scope, $value) {
		$this->_sqlFields = array();
		if (is_array($scope)) {
			$this->_sqlFields = array_keys($scope);
			$this->_filtreScope = array_values($scope);
			$this->_filtreValue = $value;
			$this->_filtreValue[0] = array_flip($this->_sqlFields)[$value[0]];
		}
		else {
			$this->_filtreScope = '';
			$this->_sqlFields = $scope;
			$this->_filtreValue = $value;
		}
	}

	//=======================================
	// Setters
	//=======================================
	private function _setId($valeur)			{$this->_id = $valeur;}
	private function _setLibelle($valeur)		{$this->_libelle = $valeur;}
	private function _setActif($valeur)			{$this->_actif = $valeur;}
	private function _setFiltreType($valeur)	{$this->_filtreType = $valeur;}
	private function _setFiltreCustom($valeur)	{$this->_filtreCustom = $valeur;}
	private function _setFiltreScope($valeur)	{$this->_filtreScope = $valeur;}
	private function _setFiltreRange($valeur)	{$this->_filtreRange = $valeur;}
	private function _setFiltreValue($valeur)	{$this->_filtreValue = $valeur;}
	private function _setFiltreColor($valeur)	{$this->_filtreColor = $valeur;}
	private function _setFiltreHelp($valeur)	{$this->_filtreHelp = $valeur;}

	public function setActif($valeur)			{
		$this->_actif = $valeur;
		//on coche la case
		if ($this->_filtreType == 'checkbox') $this->_universalForm->field($this->_id)->setChecked($this->_actif);
	}
	public function setFiltreType($valeur)		{$this->_filtreType = $valeur;}
	public function setFiltreRange($valeur)		{$this->_filtreRange = $valeur;}
	public function setFiltreValue($valeur)	{
		if ($this->_filtreType == 'checkbox') {
			//interdiction de changer la valeur pour un filtre externe de type checkbox
			if (($valeur != 0) && ($valeur != 1)) die('UniversalListFiltreExterne.class => il est interdit de changer la valeur d\'un filtre externe de type checkbox&hellip;');
			//on active la checkbox (cad que l'on prend en compte le filtre) si $valeur = 1 (valeur renvoyée par la checkbox si cochée)
			$this->_actif = ($valeur == 1);
			//coche la case à cocher si nécessaire
			$this->_universalForm->field($this->_id)->setChecked($this->_actif);
		}
		else {
			//pour les autres types de filtres on prend en compte la valeur saisie
			//et le filtre est actif si la valeur n'est pas vide ''
			if (is_array($valeur)) $saisie = $valeur[1]; else $saisie = $valeur;
			$this->_actif = ($valeur != '');
			$this->_filtreValue = $valeur;
			//$this->_filtreTypeentre la saisie dans la zone de saisie du filtre externe ('search' et 'multisearch' seulement)
			if (($this->_filtreType == 'search') || ($this->_filtreType == 'multisearch')) $this->_universalForm->field($this->_id)->setValue($valeur);
		}
	}

	public function setFiltreTag($valeur)	{$this->_filtreTag = $valeur;}

	//=======================================
	// Getters
	//=======================================
	public function getId()					{return $this->_id;}
	public function getFiltreCustom()		{return $this->_filtreCustom;}
	public function getValue()				{if (is_array($this->_filtreValue)) return $this->_filtreValue[1]; else return $this->_filtreValue;}
	public function getFiltreRange()		{return $this->_filtreRange;}
	public function getFiltreValue()		{return $this->_filtreValue;}
	public function getChampSql()			{if (is_array($this->_sqlFields)) return $this->_sqlFields[$this->_filtreValue[0]]; else return $this->_sqlFields;}
	public function getUniversalForm()		{return $this->_universalForm;}
	public function getActif() {
		if (($this->_filtreType == 'checkbox') || ($this->_filtreType == 'none')) {					//modification V4.0.0
			return $this->_actif;
		}
		if (is_array($this->_filtreValue)) $saisie = $this->_filtreValue[1]; else $saisie = $this->_filtreValue;
		$this->_actif = ($saisie != '');
		return $this->_actif;
	}
	public function getFiltreTag()			{return $this->_filtreTag;}

	//=======================================
	// Méthodes publiques
	//=======================================
	public function initValue() {
		$this->_filtreRange = $this->_filtreRangeDefault;
		$this->_filtreValue = $this->_filtreValueDefault;
		if (($this->_filtreType == 'search') || ($this->_filtreType == 'multisearch')) $this->_universalForm->field($this->_id)->setValue($this->_filtreValue);
	} 

	public function afficher() {
		if ($this->_filtreType == 'none') return;			//correction V4.0.0 => un filtre externe de type 'none' n'est pas affichable car non graphique
		$chaine = '';
		$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
		$chaine.= $this->_universalForm->draw(true);
		$chaine.= '</form>';
		return $chaine;
	}
}




//*****************************************************************************************
//
// Classe UniversalListFormFiltres
// Formulaire contenant les champs UniversalField pour chaque colonne filtrée
//
//*****************************************************************************************

class UniversalListFormFiltres extends UniversalForm {

	private $_buffer = array();		//buffer contenant les informations de construction des filtres
	private $_showButtons = true;	//affiche (true) ou n'affiche pas (false) les boutons OK et RAZ du formulaire
									//S'il ne sont pas affichés ils sont remplacés par 2 icones font-awesome

	//----------------------------------------	
	// Modifie la couleur de l'entête du filtre si 
	// celui-ci est actif et utilisé
	// Entrée : 
	//		$colonne : colonne de la liste (objet de classe Colonne)
	//		$idChamp : id du champ UniversalField à colorier
	//----------------------------------------	
	private function _highlightFiltre($colonne, $idChamp)
	{
		if (($colonne->getFiltreActif()) && 
			(($colonne->getFiltreRange() != $colonne->getFiltreRangeDefault()) || 
			($colonne->getFiltreValue() != $colonne->getFiltreValueDefault()))	
			) {
			//on change la couleur pour prévenir d'un filtre actif
			if ($colonne->getFiltreType() == 'text') {
				//consiste en le changement de la classe CSS du addon
				$this->field($idChamp)->setAclass('btn btn-'.$colonne->getfiltreColor());
			}
			elseif ($colonne->getFiltreType() == 'select') {
				//consiste en le changement de la classe CSS du label
				$this->field($idChamp)->setLclass('btn btn-'.$colonne->getfiltreColor());
			}
			elseif ($colonne->getFiltreType() == 'checkbox') {
				//consiste en l'ajout de CSS sur la classe CSS du label
				$this->field($idChamp)->setLclass($this->field($idChamp)->lClass().' bg-'.$colonne->getfiltreColor().' text-white');
			}
		}
	}

	//----------------------------------------
	// Mise à jour des champs du formulaire depuis le buffer
	// - Pour les champs de type 'text' et 'select'
	// recopie des 'value' du buffer
	// - Pour les champs de type 'checkbox'
	// recopie l'état 'checked' du buffer
	//----------------------------------------
	private function _updateChamps() {
		//construction des champs
		foreach($this->_buffer as $key => $filtre) {
			if ($filtre['filtreType'] == 'checkbox') {
				$this->field($key)->setChecked($filtre['checked']);
			}
			else {
				$this->field($key)->setValue($filtre['value']);
			}
		}
	}

	//-------------------------------------
	// traduit un terme dans la langue en cours
	// Entrée : $mnemo : ménemonique à traduire
	//		    $ou : texte à renvoyer si échec de la traduction (ex : aucun fichier de traduction en cours)
	// Sortie : la traduction
	//-------------------------------------
	private function _translate($mnemo, $ou) {
		if (function_exists('getLib')) {
			if (existeLib($mnemo)) {
				return getLib($mnemo);
			}
		}
		return $ou;
	}

	//-------------------------------------
	// positionne le drapeau _showButton
	// Entrée : $valeur : booleen
	// Sortie : Rien
	//-------------------------------------
	public function showButtons($valeur) {
		$this->_showButtons = (bool)$valeur;
	}

	//-------------------------------------
	// récupere l'état (boolean) showButtons
	// qui dit si les boutons standards sont utilisés (true) ou pas (false)
	//-------------------------------------
	public function getShowButtonsState() {
		return (bool)$this->_showButtons;
	}

	//-------------------------------------
	// Vide le buffer
	//-------------------------------------
	public function resetBuffer() {
		$this->_buffer = null;
	}

	//-------------------------------------
	// Debuggage du buffer
	//-------------------------------------
	public function debugBuffer() {
		DEBUG_('BUFFER', $this->_buffer);
	}

	//-------------------------------------
	// Prise en compte d'un filtre par le buffer
	// Entree : 
	//		$id_filtre : id du filtre (idem id de la colonne)
	//		$objCol : objet Colonne (contient les informations de caractérisation de la colonne)
	// Retour : Rien
	//-------------------------------------
	public function toBuffer($id_filtre, $objCol) {
		if (isset($this->_buffer[$id_filtre])) die('UniversalListFormFiltres.class => id de filtre de colonne "'.$id_filtre.'" dupliqué&hellip;');
		if ($objCol->getFiltreType() == 'text') {
			$filtre = array(
				'newLine' => false,														//nouvelle ligne ? false par défaut
				'dbfield' => $id_filtre,												//retour de la saisie
				'addon' => true,														//bouton de choix type de recherche
				'aclass' => 'btn btn-outline-'.$objCol->getfiltreColor(),				//classe du bouton addon
				'apos' => 'before',														//position du bouton addon
				'complement' => $objCol->getFiltreScope(),								//items du menu
				'value' => array($objCol->getFiltreRange(), $objCol->getFiltreValue()),	//valeur de la saisie (tableau : item => valeur)
				'clong' => 'px-0',														//longueur du bloc champ
				'cheight' => 'sm',														//petite hauteur de saisie
				'labelHelp' => $objCol->getFiltreHelp(),								//aide sur le label
				'maxlength' => 255,														//taille maximum de la saisie en nombre de caractères
				'placeholder' => $this->_translate('FILTRE', 'filtre'),					//placeholder de la saisie
				'javascript' => 'onchange="submit()"',									//code javascript (permet de valider par entrée)
				'spellcheck' => false,													//pas de correction orthographique
				'filtreType' => $objCol->getFiltreType()								//ajout du type de filtre
			);
		}
		elseif ($objCol->getFiltreType() == 'select') {
			$filtre = array(
				'newLine' => false,														//nouvelle ligne ? false par défaut
				'dbfield' => $id_filtre,												//retour de la saisie
				'design' => 'online',													//inline (defaut) / online
				'label' => $objCol->getFiltreCaption(),									//libellé du filtre
				'lclass' => 'btn btn-outline-'.$objCol->getfiltreColor(),				//classe du label
				'lalign' => 'center',													//left (defaut) / right / center / jutify
				'labelHelp' => $objCol->getFiltreHelp(),								//aide sur le label
				'lpos' => 'before',														//position du label par rapport au champ : before (defaut) / after
				'clong' => 'px-0',														//longueur du bloc champ
				'cheight' => 'sm',														//petite hauteur de saisie
				'complement' => $objCol->getFiltreScope(),								//fonction de callback qui doit remplir le <select>
				'javascript' => 'onchange="submit()"',									//code javascript (permet de valider dès clic sur une autre option)
				'value' => $objCol->getFiltreValue(),									//valeur de la saisie
				'filtreType' => $objCol->getFiltreType()								//ajout du type de filtre
			);
		}
		elseif ($objCol->getFiltreType() == 'checkbox') {
			$filtre = array(
				'newLine' => false,														//nouvelle ligne ? false par défaut
				'dbfield' => $id_filtre,												//retour de la saisie
				'label' => $objCol->getFiltreCaption(),									//libellé du filtre
				'lclass' => 'uw-pointer',												//classe du label
				'lalign' => 'center',													//left (defaut) / right / center / jutify
				'labelHelp' => $objCol->getFiltreHelp(),								//aide sur le label
				'javascript' => 'onchange="submit()"',									//code javascript (permet de valider dès clic sur une autre option)
				'value' => $objCol->getFiltreScope()[1],								//valeur si la case à cocher est cochée (2ème valeur du scope)
				'valueInverse' => $objCol->getFiltreScope()[0],							//valeur renvoyée si la case n'est pas cochée (1ère valeur du scope)
				'checked' => ($objCol->getFiltreValue() == $objCol->getFiltreScope()[1]),//coché ou pas ?
				'filtreType' => $objCol->getFiltreType()								//ajout du type de filtre,
			);
		}
		//ajout de la stucture à la liste des filtres
		$this->_buffer[$id_filtre] = $filtre;
	}

	//----------------------------------------
	// Construction des champs du formulaire
	//----------------------------------------
	public function construitChamps() {
		//appel au constructeur parent
		parent::construitChamps();

		//construction des champs
		foreach($this->_buffer as $key => $filtre) {
			$this->createField('filtre'.$filtre['filtreType'], $key, $filtre);
		}

		//construction bouton Submit
		$this->createField('bouton', 'ok', array(
			'dbfield' => 'bouton_ok',
			'inputType' => 'submit',
			'lclass' => 'btn btn-outline-primary btn-sm',
			'label' => 'Ok',
			'llong' => 'col-12',
			'labelHelp' => 'Appliquer les filtres',
			'labelHelpPos' => 'right',
			'clong' => 'col-12 px-0 mb-0',
			'value' => true,
			'showErreur' => false
		)); 

		//construction bouton reset des champs filtres
		$this->createField('bouton', 'reset', array(
			'dbfield' => 'bouton_raz',
			'inputType' => 'submit',
			'lclass' => 'btn btn-outline-warning btn-sm',
			'llong' => 'col-12',
			'label' => 'RAZ',
			'labelHelp' => 'Réinitialiser les filtres',
			'labelHelpPos' => 'left',
			'clong' => 'col-12 px-0 mb-0',
			'value' => true,
			'showErreur' => false
		)); 
	}

	//--------------------------------------
	// Mise à jour du formulaire
	// Entrée : $donnees : tableau de données issu de la validation du formulaire. Ex : 
	//	Array (
	//	[titre] => Array (
	//		[actif] => 1			//si le filtre est actif
	//		[range] => tout			//range du filtre
	//		[value] =>				//valeur du filtre
	//	)
	//	[genre] => Array (
	//		[actif] => 1
	//		[range] => egal
	//		[value] => Comédie musicale
	//	))
	// Retour : rien
	//--------------------------------------
	public function update($donnees) {
		//on charge le buffer avec les données reçues de l'état des filtres (range et value)
		foreach($donnees as $key => $filtre) {
			if (isset($this->_buffer[$key])) {
				if ($this->_buffer[$key]['filtreType'] == 'text') {
					//Pour les filtres de type 'text', c'est un tableau (range, valeur) qu'il faut passer.
					//Effacer la zone de saisie si RANGE est UniversalListColonne::CMP_ALL ou UniversalListColonne::CMP_IGNORE.
					if (($filtre['range'] == UniversalListColonne::CMP_ALL) || ($filtre['range'] == UniversalListColonne::CMP_IGNORE)) $filtre['value'] = '';
					$this->_buffer[$key]['value'] = array($filtre['range'], $filtre['value']);
				}
				elseif ($this->_buffer[$key]['filtreType'] == 'select') {
					//pour les filtres de type 'select', c'est juste la valeur qu'il faut passer
					$this->_buffer[$key]['value'] = $filtre['value'];
				}
				elseif ($this->_buffer[$key]['filtreType'] == 'checkbox') {
					//pour les filtres de type 'checkbox', on ne touche pas à value. Il faut modifier la valeur 'checked'.
					//elle doit être à 'true' si la valeur de la donnée est égale à la valeur attendue en cas de case cochée
					//elle doit être à 'false' si la valeur de la donnée est égale à la valeur inverse de la case à cocher
					$this->_buffer[$key]['checked'] = ($filtre['value'] == $this->_buffer[$key]['value']);
				}
			}
		}
		//on modifie la construction des champs du formulaire
		//en fonction des données du buffer (qui viennent d'être mises à jour)
		$this->_updateChamps();
		return true;
	}

	//--------------------------------------
	// Affichage du formulaire
	// Entrée : 
	//		$cols : tableau des colonnes (objets UniversalListColonne) de la liste
	//		$filtrageEnCours : booléen positionné si un filtrage est en cours
	// Retour : chaine HTML d'affichage du formulaire
	//--------------------------------------
	public function afficher($cols=null, $filtrageEnCours=null) {
		parent::afficher();
		$chaine = '';
		$javascript_ok = 'onclick="document.getElementById(\'idOk\').click()"';
		$javascript_cancel = 'onclick="document.getElementById(\'idReset\').click()"';
		$chaine.= '<form class="uf" action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
		//opération de gestion du formulaire en cours
		$chaine.= $this->field('operation')->draw(true);
		$chaine.= $this->field('idTravail')->draw(true);
		$chaine.= $this->field('soumissionFormulaire')->draw(true);
		//affichage des boutons OK et RAZ
		//concernant le bouton RAZ on le rend invisible si il n'y a aucun filtrage en cours
		$this->field('reset')->setInvisible(!$filtrageEnCours);
		$style = (!$this->_showButtons) ? ' style="display:none!important"' : '';
		$chaine.= '<div class="d-flex flex-row justify-content-between mx-0 pt-3"'.$style.'">';
			$chaine.= '<div class="col-2 py-0">'.$this->field('ok')->draw(true).'</div>';
			$chaine.= '<div class="col-2 py-0">'.$this->field('reset')->draw(true).'</div>';
		$chaine.= '</div>';
		if (!$this->_showButtons) {
			if ($filtrageEnCours) {
				$chaine.= '<span class="float-right btn btn-warning btn-sm mb-1" '.$javascript_cancel.' title="'.$this->field('reset')->labelHelp().'" data-placement="left" data-toggle="tooltip">RAZ</span>';
				$chaine.= '<span class="float-right btn btn-primary btn-sm mb-1 mr-1" '.$javascript_ok.' title="'.$this->field('ok')->labelHelp().'" data-placement="left" data-toggle="tooltip">FILTRER</span>';
			}
		}
		//affichage de chacune des colonnes de la liste
		foreach($cols as $indice => $colonne) {
			if ($colonne->getDisplay()) {
				$chaine.= '<th class="text-center uw-w'.$colonne->getSize().' py-0">';
				if ($colonne->getFiltre()) {
					$this->_highlightFiltre($colonne, $colonne->getId());
					$chaine.= $this->field($colonne->getId())->draw(true);
				}
				$chaine.= '</th>';
			}
		}
		$chaine.= '</form>';
		return $chaine;
	}

}



//*****************************************************************************************
//
// Classe UniversalList
// Classe principale de construction de notre liste
//
//*****************************************************************************************

class UniversalList {

	private $_cols = array();				//Colonnes de la liste (tableau d'objets de classe Colonne)
	private $_filtresExternes = array();	//tableau des filtres externe (hors colonnes)
	private $_pageEncours = 1;				//page en cours d'affichage
	private $_triEncours = '';				//colonne triée
	private $_triDefault = '';				//colonne triée par défaut
	private $_triSensEncours = 'ASC';		//sens du tri de la colonne triée
	private $_triSensDefault = 'ASC';		//sens du par défaut (ASC / DESC) de la colonne triée
	private $_filtrageEnCours = false;		//filtrage en cours ?
	private $_nbLinesParPage = 25;			//nombre de lignes max à ramener par page dans la liste
	private $_sqlLimitStart = 0;			//indice de la première ligne de données à ramener par la requete SQL
	private $_nbFiltres = 0;				//nombre de colonnes filtrées
	private $_formulaire = null;			//formulaire contenant tous les filtres UniversalForm de la liste (objet UniversalListFormFiltres)
	private $_headClass = '';				//classe CSS de l'entête de la table
	private $_filtresClass = 'thead-light';	//classe CSS du bandeau de filtres (première ligne du tableau)

	const VERSION = 'V5.0.0 (2020-05-05)';
	const NB_LIGNES_PAR_PAGE = 25;
	const SHOW_BUTTONS = true;

	//=======================================
	// Constructeur
	// - instanciation du formulaire (champs de filtres)
	// - construction des colonnes de la liste
	// - prise en compte paramètres par défauts
	// - tri des colonnes par ordre d'affichage
	// - construction des champs du formulaire
	//=======================================

	public function __construct() {
		//instanciation du formulaire (action par défaut = 'filtres' et numéro unique aléatoire 
		//entre 1000 et 9999 afin de ne pas interférer avec d'éventuels autres formulaires
		$this->_formulaire = new UniversalListFormFiltres('filtres', rand (1000, 9999));
		//construction des colonnes de la liste
		$this->construitColonnes();
		//premiere colonne triable sera celle par défaut jusqu'à nouvel ordre
		foreach($this->_cols as $col) {
			if ($col->getTri()) {
				$this->_triDefaut = $col->getId();
				$this->_triEncours = $col->getId();
				$this->_triSensEncours = $col->getTriSens();
				$this->_triSensDefault = $col->getTriSens();
				break;
			}
		}
		//classement des colonnes selon le sens d'affichage demandé
		$this->_sortOrder();
		//construction des champs du formulaire
		$this->_formulaire->construitChamps();
		//construction des filtres externes (appel de la méthode protégée)
		$this->construitFiltresExternes();
	}

	//=======================================
	// Méthodes protégées à surcharger
	//=======================================
	protected function construitColonnes() {
	}

	protected function construitFiltresExternes() {
	}

	//=======================================
	// Méthodes privées
	//=======================================

	//-------------------------------------
	// traduit un terme dans la langue en cours
	// Entrée : $mnemo : ménemonique à traduire
	//		    $ou : texte à renvoyer si échec de la traduction (ex : aucun fichier de traduction en cours)
	// Sortie : la traduction
	//-------------------------------------
	private function _translate($mnemo, $ou) {
		if (function_exists('getLib')) {
			if (existeLib($mnemo)) {
				return getLib($mnemo);
			}
		}
		return $ou;
	}

	//-------------------------------------
	// Ordonne les colonnes du tableau selon l'ordre choisi dans la propriété 'order'
	//-------------------------------------
	private function _sortOrder() {
	    $new_array = array();
		$sortable_array = array();
	    if (count($this->_cols) > 0) {
			//création d'un tableau cle => ordre
		    foreach ($this->_cols as $k => $v) {
			    $sortable_array[$k] = $v->getOrder();
		    }
			//tri ascendant du tableau sur la valeur ordre
		    asort($sortable_array);
			//création du tableau final	
		    foreach ($sortable_array as $k => $v) {
			    $new_array[$k] = $this->_cols[$k];
	        }
			//réinjection du tableau trié dans l'objet
			$this->_cols = $new_array;
		}
	}

	//-------------------------------------
	//renvoie un tableau contenant les id de colonnes sur lesquelles le tri est possible
	//ex : Array {
	//	[0] => nom_prenom
	//	[1] => statut
	//	[2] => matricule
	//	[3] => poste
	//)
	//-------------------------------------
	private function _getTrisPossibles() {
		$liste = array();
		foreach($this->_cols as $key => $col) {
			if ($col->getTri()) $liste[] = $key;
		}
		return $liste;
	}

	//----------------------------------------------------------------------
	// Construction d'un filtre de type 'text' (plusieurs choix : tout, commence par, etc..)
	// Entree
	//		$id			: id de la colonne
	//		$champSQL	: le champ SQL concerné par le filtre
	// Retour
	//		le code SQL qui correspond aux filtre
	//----------------------------------------------------------------------
	private function _buildFiltreText($id, $champSQL) 
	{
		$leFiltre = '';
		if ($this->_cols[$id]->getFiltreActif()){
			if (($this->_cols[$id]->getFiltreRange() == UniversalListColonne::CMP_ALL) || ($this->_cols[$id]->getFiltreRange() == UniversalListColonne::CMP_IGNORE)) {
			}
			elseif ($this->_cols[$id]->getFiltreRange() == UniversalListColonne::CMP_EQUAL) {
				$leFiltre.= "AND ".$champSQL." = '".$this->_cols[$id]->getFiltreValue()."' ";
			}
			elseif ($this->_cols[$id]->getFiltreRange() == UniversalListColonne::CMP_DIFFERENT) {
				$leFiltre.= "AND ".$champSQL." != '".$this->_cols[$id]->getFiltreValue()."' ";
			}
			elseif ($this->_cols[$id]->getFiltreRange() == UniversalListColonne::CMP_BEGINS_BY) {
				$leFiltre.= "AND ".$champSQL." LIKE '".$this->_cols[$id]->getFiltreValue()."%' ";
			}
			elseif ($this->_cols[$id]->getFiltreRange() == UniversalListColonne::CMP_CONTENDS) {
				$leFiltre.= "AND ".$champSQL." LIKE '%".$this->_cols[$id]->getFiltreValue()."%' ";
			}
			elseif ($this->_cols[$id]->getFiltreRange() == UniversalListColonne::CMP_DO_NOT_CONTENDS) {
				$leFiltre.= "AND ".$champSQL." NOT LIKE '%".$this->_cols[$id]->getFiltreValue()."%' ";
			}
			elseif ($this->_cols[$id]->getFiltreRange() == UniversalListColonne::CMP_ENDS_BY) {
				$leFiltre.= "AND ".$champSQL." LIKE '%".$this->_cols[$id]->getFiltreValue()."' ";
			}
		}
		return $leFiltre;
	}

	//----------------------------------------------------------------------
	// Construction d'un filtre de type 'select'  (1 seul choix : valeur égale à)
	// Entree
	//		$id			: id de la colonne
	//		$champSQL	: le champ SQL concerné par le filtre
	// Retour
	//		le code SQL qui correspond aux filtre
	//----------------------------------------------------------------------
	private function _buildFiltreSelect($id, $champSQL) 
	{
		$leFiltre = '';
		if ($this->_cols[$id]->getFiltreActif()){
			if (($this->_cols[$id]->getFiltreValue() != UniversalListColonne::CMP_ALL) && ($this->_cols[$id]->getFiltreValue() != UniversalListColonne::CMP_IGNORE)) {
				$leFiltre.= "AND ".$champSQL." = '".$this->_cols[$id]->getFiltreValue()."' ";
			}
		}
		return $leFiltre;
	}

	//----------------------------------------------------------------------
	// Construction d'un filtre de type 'checkbox'  (1 seul choix : valeur égale à)
	// Entree
	//		$id			: id de la colonne
	//		$champSQL	: le champ SQL concerné par le filtre
	// Retour
	//		le code SQL qui correspond aux filtre
	//----------------------------------------------------------------------
	private function _buildFiltreCheckbox($id, $champSQL) 
	{
		$leFiltre = '';
		if ($this->_cols[$id]->getFiltreActif()){
			//si la valeur du filtre est différente de la première valeur du scope (qui est la valeur quand la case à cocher est non cochée)
			//alors cela signifie que l'on a un filtre mis en place sur cette checkbox
			if ($this->_cols[$id]->getFiltreValue() != $this->_cols[$id]->getFiltreScope()[0]) {
				$leFiltre.= "AND ".$champSQL." = '".$this->_cols[$id]->getFiltreValue()."' ";
			}
		}
		return $leFiltre;
	}

	//----------------------------------------------------------------------
	// Construction d'un filtre de type 'externe'
	// Entree
	//		$id			: id du filtre
	//		$filtreExt	: objet UniversalListFiltreExterne
	// Retour
	//		le code SQL qui correspond aux filtre
	//----------------------------------------------------------------------
	private function _buildFiltreExterne($id, $filtreExt) 
	{
		$leFiltre = '';

		//si le fitre externe est actif
		if ($filtreExt->getActif()) {

			//on explose le champ pour vérifier si il y en a plusieurs sur lesquels on veut appliquer le filtre (V3.1.0)
			$lesChampsSql = explode('|', $filtreExt->getChampSql());

			//creation SQL du filtre par traitement de l'opérateur de comparaison
			//CMP_ALL
			//-----------
			if ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_ALL) {
			}

			//CMP_SQL (Ajout V4.0.0)
			//envoi du code SQL stocké dans la valeur du filtre (_filtreValue)
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_SQL) {
				$leFiltre.= "AND ".$filtreExt->getValue()." ";
			}

			//CMP_EQUAL
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_EQUAL) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." = '".$filtreExt->getValue()."'";
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." = '".$filtreExt->getValue()."' ";
			}

			//CMP_DIFFERENT
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_DIFFERENT) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." != '".$filtreExt->getValue()."'";
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." != '".$filtreExt->getValue()."' ";
			}

			//CMP_BEGINS_BY
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_BEGINS_BY) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." LIKE '".$filtreExt->getValue()."%'";
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." LIKE '".$filtreExt->getValue()."%' ";
			}

			//CMP_CONTENDS
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_CONTENDS) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." LIKE '%".$filtreExt->getValue()."%'";
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." LIKE '%".$filtreExt->getValue()."%' ";
			}

			//CMP_DO_NOT_CONTENDS
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_DO_NOT_CONTENDS) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." NOT LIKE '%".$filtreExt->getValue()."%'";
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." NOT LIKE '%".$filtreExt->getValue()."%' ";
			}

			//CMP_ENDS_BY
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_ENDS_BY) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." LIKE '%".$filtreExt->getValue()."'";
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." LIKE '%".$filtreExt->getValue()."' ";
			}

			//CMP_BEGINS_BY_NUMBER
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_BEGINS_BY_NUMBER) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." REGEXP '^[0-9]'";
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." REGEXP '^[0-9]' ";
			}

			//CMP_GREATER_THAN
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_GREATER_THAN) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." > ".$filtreExt->getValue();
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." > ".$filtreExt->getValue()." ";
			}

			//CMP_GREATER_OR_EQUAL_TO
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_GREATER_OR_EQUAL_TO) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." >= ".$filtreExt->getValue();
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." >= ".$filtreExt->getValue()." ";
			}

			//CMP_LOWER_THAN
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_LOWER_THAN) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." < ".$filtreExt->getValue();
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." < ".$filtreExt->getValue()." ";
			}

			//CMP_LOWER_OR_EQUAL_TO
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_LOWER_OR_EQUAL_TO) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." <= ".$filtreExt->getValue();
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." <= ".$filtreExt->getValue()." ";
			}

			//EQUAL_TO
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::EQUAL_TO) {
				if (count($lesChampsSql) > 1) {
					$leFiltre.= "AND (";
					foreach($lesChampsSql as $key => $champSql) {
						$leFiltre.= $champSql." = ".$filtreExt->getValue();
						if ($key < count($lesChampsSql) - 1) $leFiltre.= " OR ";
					}
					$leFiltre.= ") ";
				}
				else 
					$leFiltre.= "AND ".$filtreExt->getChampSql()." = ".$filtreExt->getValue()." ";
			}

			//CMP_IGNORE
			//-----------
			elseif ($filtreExt->getFiltreRange() == UniversalListColonne::CMP_IGNORE) {
			}

		}
		return $leFiltre;
	}

	//-------------------------------------
	// Positionne la structure des colonnes de la liste avec des données extérieures
	// $data doit être une forme condensée (encodée 64 et sérialisé) récupérée depuis la méthode getCols()
	// Entrée : $data : données encodées 64 et sérialisées qui encapsulent des colonnes
	//-------------------------------------
	private function _setCols($data) {
		$this->_cols = unserialize(base64_decode($data));
		//positionnement du drapeau positionné si au moins 1 filtre de colonne est en cours
		$this->_filtrageEnCours = $this->isAnyFiltreEnCours();
		//mise à jour des filtres du formulaire
		$this->_formulaire->update($this->getFiltres());
	}

	//-------------------------------------
	// Renvoie la structure des colonnes de la liste sous une 
	// forme condensée (encodée 64 et sérialisé) qui permet 
	// d'être éventuellement stockée en base de donnée
	//-------------------------------------
	private function _getCols() {
		return base64_encode(serialize($this->_cols));
	}

	//=======================================
	// Setters
	//=======================================

	public function setCols($data)				{$this->_setCols($data);}
	public function setNbLinesParPage($valeur)	{$this->_nbLinesParPage = $valeur;}
	public function setPageEncours($valeur)		{$this->_pageEncours = $valeur;}

	//modification du tri en cours ($valeur = id du champ sur lequel trier)
	public function setTriEncours($valeur)		{
		if ((isset($this->_cols[$valeur])) && ($this->_cols[$valeur]->getTri())) {
			$this->_triEncours = $valeur;
		}
	}

	//modification du sens du tri en cours ($valeur = ASC / DESC)
	public function setTriSensEncours($valeur)	{$this->_triSensEncours = $valeur;}
	public function setHeadClass($valeur)		{$this->_headClass = $valeur;}								//possitionne la classe CSS de l'entête du tableau
	public function setFiltresClass($valeur)	{$this->_filtresClass = $valeur;}							//possitionne la classe CSS du bandeau de filtres

	//=======================================
	// Getters
	//=======================================

	public function cols()					{return $this->_cols;}											//renvoie le tableau des objets "UniversalListColonne" de la liste
	public function col($id)				{return $this->_cols[$id];}										//renvoie l'objet "UniversalListColonne" identifié par $id
	public function getCols()				{return $this->_getCols();}										//renvoie une version encodée (serialisée et encodée 64) des colonnes de la liste
	public function colExist($id) {																			//renseigne si la colonne identifiée $id existe dans la liste
		foreach($this->_cols as $colonne) {
			if ($colonne->getId() == $id) return true;
		}
		return false;
	}
	public function getFiltresExternes()	{return $this->_filtresExternes;}
	public function getFiltreExterne($id)	{return $this->_filtresExternes[$id];}
	public function getPageEncours()		{return $this->_pageEncours;}									//renvoie la page en cours
	public function getTriEncours()			{return $this->_triEncours;}									//renvoie le champ trié en cours
	public function getTriEncoursLibelle()	{return $this->_cols[$this->_triEncours]->getTriLibelle();}		//renvoie le libelle en clair correspondant au tri en cours
	public function getTriSensEncours()		{return $this->_triSensEncours;}								//renvoie le champ trié en cours
	public function getTriSensEncoursLibelle() {															//renvoie le libellé en clair du sens d'affichage en cours
		return ($this->_triSensEncours == 'ASC') ? getLib('CLASSEMENT_ASCENDANT') : getLib('CLASSEMENT_DESCENDANT');
	}
	public function getNbLinesParPage()		{return $this->_nbLinesParPage;}								//renvoie le nombre de ligne maximum à afficher par page
	public function getSqlLimitStart()		{return $this->_sqlLimitStart;}									//renvoie à l'attention du code SQL l'indice de la première ligne de données à ramener
	public function getShowButtonsState()	{return $this->_formulaire->getShowButtonsState();}				//renvoie l'état des boutons du formulaire (true : boutons standards, false : boutons simplifiés)
	public function getHeadClass()			{return $this->_headClass;}										//renvoie la classe CSS de l'entête du tableau
	public function getFiltresClass()		{return $this->_filtresClass;}									//renvoie la classe CSS du bandeau de filtres

	//=======================================
	// Méthode publiques
	//=======================================

	//-------------------------------------
	// Création de la table SQL listings
	// Elle va permettre de sauvegarder les colonnes des listes afin de 
	// pouvoir les recharger
	// ATTENTION : Ne sauvegarde pas les filtres externes
	// Retour : true / false
	//-------------------------------------
	static function createTable() {
		$requete = "CREATE TABLE IF NOT EXISTS "._PREFIXE_TABLES_."listings (";
		$requete.= "id tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, ";
		$requete.= "titre varchar(255) NOT NULL, ";
		$requete.= "id_user varchar(100) NOT NULL, ";
		$requete.= "id_listing varchar(30) NOT NULL, ";
		$requete.= "last_update timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
		$requete.= "data text NOT NULL, ";
		$requete.= "PRIMARY KEY (id), ";
		$requete.= "KEY titre (titre), ";
		$requete.= "KEY id_user_2 (id_user), ";
		$requete.= "KEY id_listing (id_listing)"; 
		$requete.= ") ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		return $res;
	}

	//-------------------------------------
	// Ajout d'un filtre externe au tableau des filtres externes
	// Un filtre externe est composé des champs suivants : 
	//  filtreType			//'externe'
	//	filtreActif			//fitre actif ou non (true / false)
	//	filtreSqlFields		//tableau de champs SQL concernés par le filtre ('indice au choix : un nombre, un libellé' => 'champ SQL')
	//	filtreFieldChoice	//indice du champ SQL actuellement sélectionné (dans le tableau filtreSqlFields)
	//	filtreRange			//étendue de la recherche sur la colonne (UniversalListColonne::CMP_ALL/CMP_EQUAL/CMP_DIFFERENT/CMP_BEGINS_BY/CMP_CONTENDS/CMP_DO_NOT_CONTENDS/CMP_ENDS_BY)
	//	filtreValue			//valeur de la recherche sur la colonne	concernée 
	//-------------------------------------	
	public function createFiltreExterne($id_filtre, $filtre) {
		//test si id n'existe pas déja
		if ((isset($this->_cols[$id_filtre])) || (isset($this->_filtresExternes[$id_filtre]))) die('UniversalList.class => id de filtre externe "'.$id_filtre.'" dupliqué&hellip;');
		$filtre['id'] = $id_filtre;
		$this->_filtresExternes[$id_filtre] = new UniversalListFiltreExterne($filtre);
	}

	//-------------------------------------
	// création d'une colonne de la liste
	//-------------------------------------
	//	Entree : 
	//		$id_colonne : id de la colonne
	//		$tabInfos : tableau de paramètres de la colonne
	// Retour : 
	//		Rien
	//-------------------------------------
	public function createCol($id_colonne, $tabInfos) {
		//test si id n'existe pas déja
		if ((isset($this->_cols[$id_colonne])) || (isset($this->_filtresExternes[$id_colonne]))) die('UniversalList.class => id de colonne "'.$id_colonne.'" dupliqué&hellip;');
		$tabInfos['id'] = $id_colonne;
		$this->_cols[$id_colonne] = new UniversalListColonne($tabInfos);
		//si la colonne possède un filtre, on passe l'info au formulaire pour stockage dans son buffer...
		if (!empty($tabInfos['filtre'])) {
			$this->_formulaire->toBuffer($id_colonne, $this->_cols[$id_colonne]);
			//...et on incrémente le nombre de filtres présents sur la liste
			$this->_nbFiltres++;
		}
	}

	//-------------------------------------
	// Affiche ou n'affiche pas les boutons
	// OK et RAZ du formulaire
	// Entrée : $valeur : true / false
	// Retour : rien
	//-------------------------------------	
	public function showFormButtons($valeur) {
		$this->_formulaire->showButtons($valeur);
	}

	//-------------------------------------
	// Afficher les colonne en DEBUG
	//-------------------------------------
	public function debugCols() {
		echo '<pre>';
		var_dump($this->_cols);
		echo '</pre>';
	}

	//-------------------------------------
	// Renvoie la taille totale du tableau en %
	// Les colonne non affichées sont aussi prises en compte
	// Entrée : rien
	// Sortie : taille totale de la liste en pourcentale
	//-------------------------------------
	public function getSize() {
		$total = 0;
		foreach($this->_cols as $indice => $colonne) {
			$total+= $colonne->getSize();
		}
		return $total;
	}

	//-------------------------------------
	// Renvoie la taille totale du tableau des 
	// colonnes actives en %
	// Entrée : rien
	// Sortie : taille totale de la liste affichée en pourcentale
	//-------------------------------------
	public function getDisplayedSize() {
		$total = 0;
		foreach($this->_cols as $indice => $colonne) {
			if ($colonne->getDisplay())
				$total+= $colonne->getSize();
		}
		return $total;
	}

	//-------------------------------------
	// Obtenir un tableau des filtres de colonne. Exemple : 
	// Array (
	//	[nom_prenom] => Array (
	//		[actif] => 1
	//		[range] => tout
	//		[value] => 
	//	)
	//-------------------------------------
	public function getFiltres() {
		$filtre = array();
		//chargement des filtres de colonnes
		foreach($this->_cols as $indice => $colonne) {
			if ($colonne->getFiltre()) {
				//la colonne possède bien un filtre
				$filtre[$indice]['actif'] = $colonne->getFiltreActif();
				$filtre[$indice]['range'] = $colonne->getFiltreRange();
				$filtre[$indice]['value'] = $colonne->getFiltreValue();
			}
		}
		return $filtre;
	}

	//-------------------------------------
	// Récupération des paramètres d'affichages demandés par l'utilisateur :
	// - tri souhaité, sens d'affichage, page encours, tri encours
	// Gestion du formulaire :
	// - test si un POST a été généré
	// - prise en compte des informations du formulaire
	//-------------------------------------
	public function getParams() {
		//recuperation de la page encours
		//avec tests d'intégrité
		$page = 1;
		if (isset($_GET['page'])) {
			$page = MySQLDataProtect($_GET['page']);
			if (!preg_match(PAGEREGEX, $page)) $page = 1;
			if ($page <= 0) $page = 1;
		}
		$this->_pageEncours = $page;

		//recuperation du tri souhaité
		$choixTri = $this->_getTrisPossibles();
		if (isset($_GET['tri'])) $this->_triEncours = MySQLDataProtect($_GET['tri']);
		if (!in_array($this->_triEncours, $choixTri)) $this->_triEncours = $this->_triDefault;

		//recuperation du sens d'affichage souhaité ascendant (ASC) ou descendant (DESC)
		$choixSens = array('ASC', 'DESC');
		if (isset($_GET['sens'])) $this->_triSensEncours = MySQLDataProtect($_GET['sens']);
		if (!in_array($this->_triSensEncours, $choixSens)) $this->_triSensEncours = 'ASC';

		//swap du sens d'affichage proposé sur la colonne de tri en cours
		foreach($this->_cols as $key => $colonne) {
			if ($key == $this->_triEncours) {
				if ($this->_triSensEncours == 'ASC') {
					$colonne->setTriSens('DESC');
				}
				else {
					$colonne->setTriSens('ASC');
				}
				break;
			}
		}

		//récupération des modifications de filtres de colonne (formulaire)
		if ($this->_nbFiltres > 0) {
			//DEBUG_('POST', $_POST);
			//une action a été effectuée sur le formulaire ?
			$action = $this->_formulaire->getAction();
			//DEBUG_('action', $action);
			if ($action == 'valid_filtres') {
				//une action a été remarquée (POST détecté)
				if ($this->_formulaire->tester()) {
					//la validation des champs du formulaire est OK
					//chargement des données issues du formulaire
					$donnees = $this->_formulaire->getData();
					//DEBUG_('donnees filtres colonnes', $donnees);
					if ($donnees['bouton_raz'] == '1') {
						//on a appuyé sur le bouton de remise à zero
						//reinitialisation des filtres de colonne
						$this->filtresInit();
						//et mise à jour du formulaire
						$this->_formulaire->update($this->getFiltres());
					}
					else {
						//on a modifié un filtre. Mise à jour des filtres de colonnes
						$this->filtresUpdate($donnees);
						//et du formulaire
						$this->_formulaire->update($this->getFiltres());
					}
				} 
			}
		}

		//recuperation des évènements sur les filtres externes
		foreach ($this->_filtresExternes as $key => $filtreExterne) {
			if (null !== $filtreExterne->getUniversalForm()) {					//ajout V4.0.0 => pas de récupération d'action sur filtre externe type 'none' puisqu'il n'encapsule plus d'objet UniversalForm
				$action = $filtreExterne->getUniversalForm()->getAction();
				if ($action == 'valid_fe'.$key) {
					$donnees = $filtreExterne->getUniversalForm()->getData();
					//DEBUG_('donnees de '.$key, $donnees);
					$this->filtresUpdate($donnees);
				}
			}
		}

	}

	//-------------------------------------
	// Dessin du formulaire si au moins 1 champ présent.
	// Il se place alors au-dessus de l'entête de la liste.
	// Entree : Rien
	// Retour : code HTML d'affichage
	//-------------------------------------
	public function drawFiltresColonnes() {
		if ($this->_nbFiltres > 0) {
			$chaine = '<thead class="'.$this->_filtresClass.'">';
			$chaine.= $this->_formulaire->afficher($this->_cols, $this->_filtrageEnCours);
			$chaine.= '</thead>';
			echo $chaine;
		}
	}

	//-------------------------------------
	// Dessin de l'entête de la liste
	// Entree : Rien
	// Retour : echo du code HTML d'affichage
	//-------------------------------------
	public function drawHead() {
		//on supprime les parametres tri et sens de l'url
		$leLienColonne = delUrlParameter($_SERVER['REQUEST_URI'], 'tri=');
		$leLienColonne = delUrlParameter($leLienColonne, 'sens=');
		$leLienColonne = delUrlParameter($leLienColonne, 'page=');

		//test si le lien possède encore au moins un paramètre
		$tabUrl = parse_url($leLienColonne);
		$possedeParams = (isset($tabUrl['query']));

		$chaine = '';
		$chaine.= '<thead class="'.$this->_headClass.'">';
		foreach($this->_cols as $key => $colonne) {
			if ($colonne->getDisplay()) {
				($colonne->getTitlePos() != '') ? $placement = ' data-placement="'.$colonne->getTitlePos().'"' : $placement = '';
				//supprimé la taille de la colonne pour permettre à du code javascript de la modifier en drag & drop
				//$chaine.= '<th scope="col" class="text-'.$colonne->getAlign().'" width="'.$colonne->getSize().'%">';
				$chaine.= '<th scope="col" class="text-'.$colonne->getAlign().'">';
				if ($colonne->getTri()) {
					if ($colonne->getTitle() != '') {
						$chaine.= '<span data-toggle="tooltip" title="'.$colonne->getTitle().'"'.$placement.'>';
					}
					if ($key == $this->_triEncours) {
						if ($this->_triSensEncours	== 'ASC')	$chaine.= '<span class="fas fa-caret-down">&nbsp</span>';
						if ($this->_triSensEncours == 'DESC')	$chaine.= '<span class="fas fa-caret-up">&nbsp</span>';
					}
					if ($possedeParams) {
						$chaine.= '<a href="'.$leLienColonne.'&amp;tri='.$key.'&amp;sens='.$colonne->getTriSens().'">'.$colonne->getLibelle().'</a>';
					}
					else {
						$chaine.= '<a href="'.$leLienColonne.'?tri='.$key.'&amp;sens='.$colonne->getTriSens().'">'.$colonne->getLibelle().'</a>';
					}
					if ($colonne->getTitle() != '') {
						$chaine.= '</span>';
					}
				}
				else {
					if ($colonne->getTitle() != '') {
						$chaine.= '<span data-toggle="tooltip" title="'.$colonne->getTitle().'"'.$placement.'>';
					}
					$chaine.= $colonne->getLibelle();
					if ($colonne->getTitle() != '') {
						$chaine.= '</span>';
					}
				}
				$chaine.= '</th>';
			}
		}
		$chaine.= '</thead>';
		echo $chaine;
	}

	//--------------------------------------------------------------------------
	// Dessin du corps de la liste (ne pas mettre autre chose que echo sinon marche pas)
	// la couleur du background de la ligne est donné par le champ 'line-color' des données à afficher
	// Entree : 
	//		$listing : le tableau contenant les données à afficher
	// Retour
	//		echo du code HTML d'affichage
	//--------------------------------------------------------------------------
	public function drawBody($listing)
	{
		//affichage du corps du tableau : donnees
		echo '<tbody>';
		foreach($listing as $ligne)	{
			(!empty($ligne['line-color'])) ? $couleur = ' class="'.$ligne['line-color'].'"' : $couleur = '';
			echo '<tr'.$couleur.'>';
			foreach($this->_cols as $key => $colonne) {
				if ($colonne->getDisplay()) {
					($colonne->getHeader()) ? $tag = 'th scope="row"' : $tag = 'td';
					//supprimé la taille de la colonne pour permettre à du code javascript de la modifier en drag & drop
					//echo '<'.$tag.' width="'.$colonne->getSize().'%" align="'.$colonne->getAlign().'">';
					$classe = '';
					if (($colonne->getAlign() == 'center') || ($colonne->getAlign() == 'right')) {
						$classe = ' class="text-'.$colonne->getAlign().'"';
					}
					echo '<'.$tag.$classe.'>';
					if (($colonne->getFiltre()) && (!$colonne->getFiltreActif())) {
						//la colonne possède un filtre inactif
						echo '<span class="text-warning">'.$this->_translate('INACTIF', 'inactif').'</span>';
					}
					else {
						//pas de filtre sur la colonne ou filtre actif
						$methode = 'Col_'.$key;
						if (method_exists($this, $methode)) {call_user_func(array($this, $methode), $ligne);}
					}
					echo '</td>';
				}
			}
			echo '</tr>';
		}
		echo '</tbody>';
	}

	//-------------------------------------
	// Sauvegarde une configuration de la liste dans la base de données
	// Entrée : 
	//		$titre : titre donnée à la liste sauvegardée
	//		$id_user : id de l'utilisateur qui a généré cette sauvegarde
	//		id_listing : identifiant de liste (peut être n'imposte quelle entrée)
	// Retour : false si erreur SQL / id du tuple inséré si ok
	//-------------------------------------
	public function saveList($titre, $id_user, $id_listing) {
		global $dbConnexion_lastInsertId;
		$requete = "INSERT IGNORE INTO "._PREFIXE_TABLES_."listings ";
		$requete.= "(id, titre, id_user, id_listing, data) VALUES (";
		$requete.= "NULL, ";
		$requete.= "'".$titre."', ";
		$requete.= "'".$id_user."', ";
		$requete.= "'".$id_listing."', ";
		$requete.= "'".base64_encode(serialize($this->_cols))."'";
		$requete.= ")";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) return $dbConnexion_lastInsertId;
		return false;
	}

	//-------------------------------------
	// Charge une configuration de la liste depuis la base de données
	// Entrée : $id : identifiant unique dans la base de données pour la liste choisie
	// Retour : true / false (erreur SQL)
	//-------------------------------------
	public function loadList($id) {
		$requete = "SELECT data FROM "._PREFIXE_TABLES_."listings ";
		$requete.= "WHERE id = '".$id."'";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre == 1) {
				//positionne les colonnes de la liste et prend en compte les filtres en cours
				$this->_setCols($res[0]['data']);
				//réinitialisation du buffer du formulaire
				$this->_formulaire->resetBuffer();
				//et du nombre de filtres
				$this->_nbFiltres = 0;
				//charge le buffer du formulaire pour les filtres (stockage dans le buffer du formulaire)
				foreach($this->_cols as $key => $colonne) {
					//s'il s'agit d'une colonne filtree
					if ($colonne->getDisplay() && $colonne->getFiltre()) {
						$this->_formulaire->toBuffer($key, $colonne);
						//et on incrémente le nombre de filtres présents sur la liste
						$this->_nbFiltres++;
					}
				}
				//reconstruit le formulaire
				$this->_formulaire->construitChamps();
				return true;
			}
		}
		return false;
	}

	//----------------------------------------------------------------------
	// Détermine si une colonne filtrée est active
	// Entree : $id (id de la colonne à tester)
	// Retour: true / false
	//----------------------------------------------------------------------
	public function isFiltreActif($id)
	{
		return (
			($this->_cols[$id]->getDisplay()) && 
			($this->_cols[$id]->getFiltre()) &&
			($this->_cols[$id]->getFiltreActif())
			);
	}

	//----------------------------------------------------------------------
	// Dit si au moins un filtre de colonne est actif (valeurs différentes 
	// que celles par défaut)
	//----------------------------------------------------------------------
	public function isAnyFiltreEnCours() {
		foreach($this->_cols as $key => $colonne) {
			//s'il s'agit dune colonne filtree
			if ($colonne->getDisplay() && $colonne->getFiltre()) {
				if ((
					($colonne->getFiltreActif()) && 
					($colonne->getFiltreRange() == $colonne->getFiltreRangeDefault()) && 
					($colonne->getFiltreValue() == $colonne->getFiltreValueDefault())
					) == false) return true;
			}
		}
		return false;
	}

	//----------------------------------------------------------------------
	// Détermine si une colonne est active
	// Elle doit être "affichable" ET ("sans filtre" OU "possède un filtre actif")
	// Entree : $id (id de la colonne à tester)
	// Retour: true / false
	//----------------------------------------------------------------------
	public function isColonneActive($id)
	{
		return (
			($this->_cols[$id]->getDisplay()) && 
			(
				(!$this->_cols[$id]->getFiltre()) || 	
				(
					($this->_cols[$id]->getFiltre()) && ($this->_cols[$id]->getFiltreActif())
				)
			)
		);
	}

	//----------------------------------------------------------------------
	// Construction SQL des filtre de la liste
	// Entree : rien
	// Retour : le code SQL qui correspond à l'ensemble des filtres de la liste
	//----------------------------------------------------------------------
	public function buildFiltres()
	{
		//filtre par défaut : aucun
		$leFiltre = '';
		//prise en compte des filtres de colonnes
		foreach($this->_cols as $key => $colonne) {
			if ($colonne->colonneActive()) {
				//filtre du type text (un champ texte)
				if ($colonne->getFiltreType() == 'text') {
					$leFiltre.= $this->_buildFiltreText($key, $colonne->getFiltreSqlField());
				}
				//filtre du type select (un selecteur)
				if ($colonne->getFiltreType() == 'select') {
					$leFiltre.= $this->_buildFiltreSelect($key, $colonne->getFiltreSqlField());
				}
				//filtre du type checkbox (une checkbox)
				if ($colonne->getFiltreType() == 'checkbox') {
					$leFiltre.= $this->_buildFiltreCheckbox($key, $colonne->getFiltreSqlField());
				}
			}
		}
		//prise en compte des filtres externes
		//consolidation avec les éventuels filtres externes
		foreach($this->_filtresExternes as $key => $filtreExterne) {
			if ($filtreExterne->getActif()) {
				$leFiltre.= $this->_buildFiltreExterne($key, $filtreExterne);
			}
		}
		return $leFiltre;
	}

	//----------------------------------------------------------------------
	// Construit du code SQL chargée du tri de la liste
	// Entree : rien
	// Retour : code SQL qui correspond aux tris choisis
	//----------------------------------------------------------------------
	public function buildTris()
	{
		if ($this->isColonneActive($this->_triEncours)) {
			//la colonne de tri est active
			$tri = $this->_cols[$this->_triEncours]->getTriSql().' '.$this->_triSensEncours;
			if ($this->_cols[$this->_triEncours]->getTriSqlSecondaire() != '') {
				$tri.= ', '.$this->_cols[$this->_triEncours]->getTriSqlSecondaire();
			}
			return $tri;
		}
		//la colonne de tri n'est pas active
		else return '1';
	}

	//----------------------------------------------------------------------
	// Positionne un filtre DE COLONNE (possible seulement si il est affiché)
	// Entree :
	//		$colonne : id de la colonne concernée par le filtre
	//		$range : range du filtre
	//		$value : valeur du filtre
	// Retour : Aucun
	//----------------------------------------------------------------------
	public function setFiltre($colonne, $range, $value) 
	{
		if ($this->_cols[$colonne]->getDisplay() && $this->_cols[$colonne]->getFiltre()) {
			//Prise en compte des données présentées pour le filtre
			if ($this->_cols[$colonne]->getFiltreType() == 'text') {
				//cas d'un filtre de type 'text'
				//si le range choisi est CMP_IGNORE alors on désactive le filtre
				if ($range == UniversalListColonne::CMP_IGNORE) {
					$this->_cols[$colonne]->setFiltreActif(false); 
					$this->_cols[$colonne]->setFiltreRange(UniversalListColonne::CMP_IGNORE);
					$this->_cols[$colonne]->setFiltreValue('');
				}
				//sinon on prend en compte le range et la valeur du filtre
				else { 
					$this->_cols[$colonne]->setFiltreActif(true); 
					$this->_cols[$colonne]->setFiltreRange($range);
					if ($range == UniversalListColonne::CMP_ALL) {
					//si range est CMP_ALL on force value à vide
						$this->_cols[$colonne]->setFiltreValue('');
					}
					else {
						$this->_cols[$colonne]->setFiltreValue($value);
					}
				}
			}
			if ($this->_cols[$colonne]->getFiltreType() == 'select') {
				//cas d'un filtre de type 'select' (seule la valeur choisie peut changer)
				//si c'est CMP_IGNORE qui a été choisi alors on désactive le filtre
				$this->_cols[$colonne]->setFiltreActif($value !== UniversalListColonne::CMP_IGNORE); 
				$this->_cols[$colonne]->setFiltreValue($value);
			}
			if ($this->_cols[$colonne]->getFiltreType() == 'checkbox') {
				//cas d'un filtre de type 'checkbox' (seul le check compte)
				//désactiver le filtre revient à ne pas cocher la case, donc on ne désactive jamais
				$this->_cols[$colonne]->setFiltreValue($value);
			}
			//mise à jour du drapeau positionné si au moins 1 filtre de colonne est en cours
			$this->_filtrageEnCours = $this->isAnyFiltreEnCours();
			//mise à jour du formulaire pour prendre en compte la modification du filtre (relecture de tous les filtres actifs)
			$this->_formulaire->update($this->getFiltres());
		}
	}


	//----------------------------------------------------------------------
	// Positionne un filtre EXTERNE
	// Entree :
	//	filtreId		: id du filtre externe
	//	$range			: étendue de la recherche  (egal, contient, commence par ..) 
	//	$value			: valeur du filtre
	// Retour : Aucun
	//----------------------------------------------------------------------
	public function setFiltreExterne($filtreId, $range, $value) 
	{
		$this->_filtresExternes[$filtreId]->setFiltreRange($range);
		$this->_filtresExternes[$filtreId]->setFiltreValue($value);
		//réinitialisation de la page à 1
		$this->_pageEncours = 1;
	}

	//----------------------------------------------------------------------
	// Initialisation de tous les filtres de la liste
	// Sauf les filtres externes  //ICI
	// Entree : Aucune
	// Retour : Aucun
	//----------------------------------------------------------------------
	public function filtresInit()
	{
		//initialisation des filtres de colonne
		foreach($this->_cols as $key => $colonne) {
			//s'il s'agit dune colonne filtree
			if ($colonne->getFiltre()) {
				$colonne->setFiltreActif(true); 
				$colonne->setFiltreRange($colonne->getFiltreRangeDefault());
				$colonne->setFiltreValue($colonne->getFiltreValueDefault());
			}
		}
/*		//initialisation des filtres externes
		foreach($this->_filtresExternes as $key => $dummy) {
			$this->_filtresExternes[$key]->setActif(false);
			$this->_filtresExternes[$key]->initValue();
		} */
		//positionnement du drapeau signifiant si au moins un filtre est en cours d'utilisation (valeurs différentes des valeurs par défaut)
		$this->_filtrageEnCours = false;
		//on réinitialise la page d'affichage
		$this->_pageEncours = 1;
	}

	//----------------------------------------------------------------------
	// Mise à jour des filtres de la liste selon saisie issue du formulaire
	// - Si valeur du filtre == CMP_IGNORE, on désactive le filtre et on l'active dans le cas contraire
	// ---- Pour les filtres de type 'text' : 
	// - L'indice 0 renvoyé par le paramètre données (issu du formulaire de filtres) correspond au 'RANGE' (range du filtre)
	// - L'indice 1 renvoyé par le paramètre données (issu du formulaire de filtres) correspond a 'VALUE' (valeur du filtre)
	// - Le formulaire renvoie la chaine 'NULL' si le filtre n'est pas actif. Donc à prendre en compte
	// Les données recues du formulaire des filtres sont renvoyées sous forme de tableau
	// array = ([nomfiltre]['0'] (pour le range du filtre) et [nomfiltre]['1'] pour la valeur du filtre
	// ---- Pour les autres types de filtre ('select' et 'checkbox')
	// La donnée reçue du formulaire n'est pas un tableau mais une valeur simple
	// Entree : 
	//		pour un filtre de type 'text' => tableau [filtre][range] et [filtre][valeur]
	//		pour un filtre de type 'select' ou 'checkbox' => [valeur]
	// Retour : Aucun
	//----------------------------------------------------------------------
	public function filtresUpdate($donnees)
	{
		//pour chaque filtre de colonne
		foreach($this->_cols as $key => $colonne) {
			if ($colonne->getFiltre()) {
				if (isset($donnees[$key])) {
					if ($donnees[$key] != 'NULL') {
						//des données sont présentées pour le filtre
						if ($colonne->getFiltreType() == 'text') {
							//cas d'un filtre de type 'text'
							//si le range choisi est CMP_IGNORE alors on désactive le filtre
							if ($donnees[$key]['0'] == UniversalListColonne::CMP_IGNORE) {
								$colonne->setFiltreActif(false); 
								$colonne->setFiltreRange(UniversalListColonne::CMP_IGNORE);
								$colonne->setFiltreValue('');
							}
							//sinon on prend en compte le range et la valeur du filtre
							else { 
								$colonne->setFiltreActif(true); 
								$colonne->setFiltreRange($donnees[$key]['0']);
								if ($donnees[$key]['0'] == UniversalListColonne::CMP_ALL) {
									//si range est CMP_ALL on force value à vide
									$colonne->setFiltreValue('');
								}
								else {
									$colonne->setFiltreValue($donnees[$key]['1']);
								}
							}
						}
						if ($colonne->getFiltreType() == 'select') {
							//cas d'un filtre de type 'select' (seule la valeur choisie peut changer)
							//si c'est CMP_IGNORE qui a été choisi alors on désactive le filtre
							$colonne->setFiltreActif($donnees[$key] !== UniversalListColonne::CMP_IGNORE); 
							$colonne->setFiltreValue($donnees[$key]);
						}
						if ($colonne->getFiltreType() == 'checkbox') {
							//cas d'un filtre de type 'checkbox' (seul le check compte)
							//désactiver le filtre revient à ne pas cocher la case, donc on ne désactive jamais
							$colonne->setFiltreValue($donnees[$key]);
						}
					}
				}
			}
		}
		//pour chaque filtre externe
		foreach($this->_filtresExternes as $key => $filtre) {
			if (isset($donnees[$key])) {
				//des données sont présentées pour le filtre => prise en compte
				$this->_filtresExternes[$key]->setFiltreValue($donnees[$key]);						//valeur du champ
			}
		}
		//positionnement du drapeau signifiant si au moins un filtre de colonnes est en cours d'utilisation (valeurs différentes des valeurs par défaut)
		$this->_filtrageEnCours = $this->isAnyFiltreEnCours();
		//on réinitialise la page d'affichage
		$this->_pageEncours = 1;
	}

	//----------------------------------------------------------------------
	// Méthode d'appel aux méthodes de recupération des données qui doivent être 
	// définies dans la classe fille. Il n'est pas nécessaire de faire appel à cette
	// méthode si l'on définit des fonctions externes. Par contre si l'on souhaite
	// utiliser cette méthode (plus simple d'appel), il est nécessaire de définir 2 classes
	// filles : getListeNombre() et getListe()
	// Entree : 
	//		$laListe : tableau des tuples à charger
	// Retour : 
	//		Le nombre total de lignes pour le tableau si Ok, false sinon
	//----------------------------------------------------------------------
	public function getData(&$laListe) {
		if (method_exists($this, 'getListeNombre')) {
			//calcul du nombre total de lignes
			$nombreTotalDeLignes = call_user_func(array($this, 'getListeNombre'));
			if ($nombreTotalDeLignes !== false) {
			
				if (method_exists($this, 'getListe')) {
					//recuperation des données
					//calcul de la première ligne à ramener par la requete SQL
					$start = ($this->_nbLinesParPage * ($this->_pageEncours - 1));
					if ($start < 0) $start = 0; 
					if ($start > $nombreTotalDeLignes) $start = div($nombreTotalDeLignes, $this->_nbLinesParPage) * $this->_nbLinesParPage;
					$this->_sqlLimitStart = $start;
					//appel de la méthode protégée de récupération des lignes
					$laListe = call_user_func(array($this, 'getListe'));
					if ($laListe !== false) {
						return $nombreTotalDeLignes;
					}
					else return false;
				}
				else {
					die('La méthode '.get_class($this).'::getListe() n\'a pas été définie');
				}

			}
			else return false;
		}
		else {
			die('La méthode '.get_class($this).'::getListeNombre() n\'a pas été définie');
		}
	}

}