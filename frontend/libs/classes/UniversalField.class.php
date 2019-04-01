<?php
//==============================================================
// Classe d'élément de formulaire
//--------------------------------------------------------------
// Element parent
// Version 3.11.3 du 17.01.2019
//==============================================================

defined('CHECK_INTEGER')			|| define('CHECK_INTEGER',			'#^[-+]?[0-9]{1,}$#');			//1..n chiffres signé (pas de signe -+ obligatoire)
defined('CHECK_DOLLARS')			|| define('CHECK_DOLLARS',			'#^[$]?[0-9 ]{1,}$#');			//$99 999 999)
defined('CHECK_MONNAIE')			|| define('CHECK_MONNAIE',			'#^(€|\$|£)?[0-9 ]{1,}$#');		//$ ou £ ou € et 99 999 999)
defined('CHECK_INTEGER_SPACED')		|| define('CHECK_INTEGER_SPACED',	'#^[0-9 ]{1,}$#');				//99 999 999)
defined('CHECK_UNSIGNED_INTEGER')	|| define('CHECK_UNSIGNED_INTEGER',	'#^[0-9]{1,}$#');				//1..n chiffres	(signe -+ interdit)
defined('CHECK_SIGNED_INTEGER')		|| define('CHECK_SIGNED_INTEGER',	'#^[-+][0-9]{1,}$#');			//1..n chiffres (signe -+ obligatoire)
defined('CHECK_INTEGER_1OU2')		|| define('CHECK_INTEGER_1OU2',		'#^[0-9]{1,2}$#');				//1 ou 2 chiffre obligatoire
defined('CHECK_FLOAT')				|| define('CHECK_FLOAT',			'#^[0-9]+(\.[0-9]{1,})?$#');	//nombre à virgule avec décimales optionnelles
defined('CHECK_FLOAT_2DEC')			|| define('CHECK_FLOAT_2DEC',		'#^[0-9]+(\.[0-9]{1,2})?$#');	//nombre à virgule avec 2 décimales optionnelles (ex : prix)
defined('CHECK_INTEGER_4')			|| define('CHECK_INTEGER_4',		'#^[0-9]{4}$#');				//4 chiffres obligatoires (ex : annee)
defined('CHECK_INTEGER_8')			|| define('CHECK_INTEGER_8',		'#^[0-9]{8}$#');				//8 chiffres obligatoires (ex : reference)
defined('CHECK_BOOLEAN')			|| define('CHECK_BOOLEAN',			'#^[01]{1}$#');					//0 ou 1
defined('CHECK_DATETIME')			|| define('CHECK_DATETIME',			'#^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})?$#'); //datetime : 0000-00-00 00:00 (les secondes sont optionnelles)
defined('CHECK_EMAIL')				|| define('CHECK_EMAIL',			'#^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$#');	//eMail
defined('CHECK_EMAIL_APOSTROPHE')	|| define('CHECK_EMAIL_APOSTROPHE',	'#^[\'_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$#');	//eMail
defined('CHECK_ALPHA_SIMPLE')		|| define('CHECK_ALPHA_SIMPLE',		'#^[_\.0-9A-Za-z- ]+$#');		//alphanumérique simple (chifre + minuscules + _ . - [espace]
defined('CHECK_ALPHA_NOMS')			|| define('CHECK_ALPHA_NOMS',		'/^[[:alpha:]|àéèêëïôöûüç\\\\\' -]+$/');	//majuscules/minuscules/accents/espace/apostrophe/tiret
defined('CHECK_FILE_NAME')			|| define('CHECK_FILE_NAME',		'#^[_\.0-9A-Za-z-/]+$#');		//compatible nommage des fichiers
defined('CHECK_URL')				|| define('CHECK_URL',				'@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS');	//une url (http://mathiasbynens.be/demo/url-regex)
defined('CHECK_IPV4')				|| define('CHECK_IPV4',				'/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/');	//Adresse ip V4
defined('CHECK_MAC')				|| define('CHECK_MAC',				'/^(([0-9a-f]){2}:){5}([0-9a-f]){2}$/');	//Adresse mac
defined('CHECK_SHA1')				|| define('CHECK_SHA1',				'/^([0-9a-f]){40}$/');		//40 hexa

class UniversalField {

	private $_idParentForm = 0;		//id du formulaire parent
	private $_fieldType = '';		//type de champ (text / radio / area / etc.)
	private $_idField = '';			//identificateur (nom) de l'objet champ du formulaire (ne pas confondre avec l'id du champ)
	private $_dbfield = '';			//colonne associée à la données à récupérer
	private	$_postName = '';
	private $_postIsTab = false;	//le POST attendu est un tableau (cas des seclect multiple). Défaut false.
	private $_id = '';

	private $_newLine = false;		//prévient du dessin du prochain champ sur une nouvelle ligne
	private $_flexLine = '';		//permet de controler le layout, les alignement, la taille de tout ce qui va être dans la ligne (utilise les utilitaires flexbox responsives de Bootstrap)

	private $_design = 'inline';	//design du champ -> inline (label et champ en ligne)(défaut) ou online (label au-dessus du champ)
	private $_dpos = 'alone';		//position du champ sur une ligne du formulaire -> first:premier champ ; last->dernier champ; inter->champ intermédiaire; alone->champ seul
	private $_decalage = '';		//classe Boostrap de décallagez du champ vers la droite

	private $_titre = '';			//titre de groupe (permet par exemple de mettre sur une seule ligne plusieurs radio buttons)
	private $_titreHelp = '';		//chaine de caractere sur titre de groupe (agit comme title="" sur un label titre de groupe)
	private $_titreHelpPos = '';	//position du libellé d'aide sur le titre (left, top (defaut), right, bottom). Valable uniquement lorsque les tooltips Bootstrap sont activés
	private $_tlong = '';			//longueur (1..12) du titre (légende)(pour radio et checkbox seulement)
	private $_tclass = '';			//classe css du titre
	private $_talign = 'left';		//alignement du titre -> left / right / center / justify

	private $_label = 'champ';
	private $_llong = '';			//longueur (1..12) du bloc zone titre
	private $_lclass = '';			//classe css du label correspondant
	private $_labelHelp = '';		//chaine de caractere (agit comme title="" sur le label)
	private $_labelHelpPos = '';	//position du libellé d'aide sur le label (left, top (defaut), right, bottom). Valable uniquement lorsque les tooltips Bootstrap sont activés
	private $_lalign = 'left';		//alignement du label -> left / right / center / justify
	
	private $_labelPlus = '';		//Permet de scinder le label en 2 parties : à gauche contenu de label, à droite le texte (ou code) contenu ici. En pratique (souvent) une icone cadrée à droite.
	private $_labelPlusHelp = '';	//chaine de caractere (agit comme title="" sur le label)
	private $_labelPlusHelpPos = '';//position du libellé d'aide sur le label (left, top (defaut), right, bottom). Valable uniquement lorsque les tooltips Bootstrap sont activés

	private $_spellcheck = true;	//si true la correction automatique est activée sur le champ, false sinon
	private $_placeholder = '';		//placeholder sur les champs text

	private $_inputType = 'text';	//type de champ input (par defaut 'text') choix parmi button/checkbox/color/date/datetime/datetime-local/email/file/hidden/image/month/number/password/radio/range/reset/search/submit/tel/text/time/url/week
	private $_clong = '';			//longueur (1..12) du bloc zone champ
	private $_cclass = '';			//classe css du champ input
	private $_value = 0;
	private $_maxlength = 0;		//nombre maxi des caractères saisis dans un champ input	
	private $_testMatches = null;
	private $_javascript = '';
	private $_readonly = false;		//lecture seule du champ ?
	private $_lpos = 'before';		//position du label before (avant) ou after (apres) le champ

	private $_liberreur = '';
	private $_liberreurHelp = '';	//chaine de caractere (agit comme title="" sur le libelle d'erreur (pour un complement d'erreur par exemple))
	private $_erreur = false;		//flag de montée d'erreur sur le champ
	private $_showErreur = true;	//affiche les erreurs ou pas lorsqu'il y en a
	private $_border = false;		//afficher un contour autour du champ ? (true/false)
	private $_invisible = false;	//l'ensemble du champ (titre + champ) est invisible ? (true/false)
	private $_enable = true;		//le champ est accessible ou pas (true/false) (pas de test sur les champ désactivés)

	private $_complement = '';		//champ fourre-tout (utilisé entre autre les champs de type 'file' pour filtrer les fichiers choisis)

	private $_idbchamp = '';		//id du DIV du bloc de champ
	private $_idztitre = '';		//id du DIV de la zone de titre
	private $_idzchamp = '';		//id du DIV de la zone de champ

	private $_MATCHES = array('REQUIRED', 'REQUIRED_SELECTION', 'NUMERIC', 'NOT_ZERO', 'CHECK_INTEGER_1OU2', 'CHECK_FLOAT', 'CHECK_FLOAT_2DEC', 'CHECK_INTEGER_8', 'CHECK_INTEGER', 'CHECK_DOLLARS', 'CHECK_MONNAIE', 'CHECK_INTEGER_SPACED', 'CHECK_UNSIGNED_INTEGER', 'CHECK_SIGNED_INTEGER', 'CHECK_BOOLEAN', 'CHECK_DATETIME', 'CHECK_INTEGER_4', 'CHECK_EMAIL', 'CHECK_EMAIL_APOSTROPHE', 'CHECK_ALPHA_SIMPLE', 'CHECK_ALPHA_NOMS', 'CHECK_FILE_NAME', 'CHECK_URL', 'CHECK_IPV4', 'CHECK_MAC', 'CHECK_SHA1', 
	'UPPERCASE', 'LOWERCASE', 'WORDSCASE', 'FIRST-LETTER', 'FIRST-LETTER-ONLY', 'TRIM', 'NOSPACE', 'NODOUBLESPACE', 'USD', 'EUR', 'GBP', 'MILLE_SPACED');

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	public function __construct(array $donnees) {
		//primordial de sauver le nom du champ en premier
		$this->setIdField($donnees['idfield']);
		//la variable POST par défaut est le nom donné à l'objet UniversalField.
		$this->setPostName($donnees['idfield']);
		//on hydrate les données saisie/
		$this->hydrate($donnees);
		//si il existe déjà un POST pour ce champ on le prend en compte tout de suite		
		if (isset($_POST['hidSoumissionFormulaire'.'_'.$this->_idParentForm])) {
			if (isset($_POST[$this->_postName])) {
				//remplissage du champ correspondant de la structure de données avec la valeur du $_POST
				if ($this->_dbfield != '') {
					$this->_value = rnTo13(mySqlDataProtect($_POST[$this->_postName]));
				}
			}
		}
		//transformation de casse eventuelle
		$this->_transform();
	}

	//--------------------------------------
	// Méthodes privées
	//--------------------------------------

	//test la cohérence d'une chaine via un regex et positionnement du message 
	//correspondant à l'erreur observée dans le champ _liberreur
	private function _regex($regex, $message) {
		if (!preg_match($regex, $this->_value)) {
			$this->_erreur = true;
			$this->_liberreur = $message;
		}
	}

	//--------------------------------------
	// Getters
	//--------------------------------------

	public function border()			{return $this->_border;}
	public function cclass()			{return $this->_cclass;}
	public function clong()				{return $this->_clong;}
	public function complement()		{return $this->_complement;}
	public function dbfield()			{return $this->_dbfield;}
	public function decalage()			{return $this->_decalage;}
	public function design()			{return $this->_design;}
	public function dpos()				{return $this->_dpos;}
	public function enable()			{return $this->_enable;}
	public function erreur()			{return $this->_erreur;}
	public function flexLine()			{return $this->_flexLine;}
	public function showErreur()		{return $this->_showErreur;}
	public function fieldType()			{return $this->_fieldType;}
	public function id()				{return $this->_id;}
	public function idbchamp()			{return $this->_idbchamp;}
	public function idField()			{return $this->_idField;}
	public function idParentForm()		{return $this->_idParentForm;}
	public function idzchamp()			{return $this->_idzchamp;}
	public function idztitre()			{return $this->_idztitre;}
	public function inputType()			{return $this->_inputType;}
	public function invisible()			{return $this->_invisible;}
	public function javascript()		{return $this->_javascript;}
	public function label()				{return $this->_label;}
	public function labelHelp()			{return $this->_labelHelp;}
	public function labelHelpPos()		{return $this->_labelHelpPos;}
	public function lalign()			{return $this->_lalign;}
	public function labelPlus()			{return $this->_labelPlus;}
	public function labelPlusHelp()		{return $this->_labelPlusHelp;}
	public function labelPlusHelpPos()	{return $this->_labelPlusHelpPos;}
	public function lclass()			{return $this->_lclass;}
	public function liberreur()			{return $this->_liberreur;}
	public function liberreurHelp()		{return $this->_liberreurHelp;}
	public function llong()				{return $this->_llong;}
	public function lpos()				{return $this->_lpos;}
	public function maxlength()			{return $this->_maxlength;}
	public function newLine()			{return $this->_newLine;}
	public function placeholder()		{return $this->_placeholder;}
	public function postIsTab()			{return $this->_postIsTab;}
	public function postName()			{return $this->_postName;}
	public function readonly()			{return $this->_readonly;}
	public function spellcheck()		{return $this->_spellcheck;}
	public function talign()			{return $this->_talign;}
	public function tclass()			{return $this->_tclass;}
	public function testMatches()		{return $this->_testMatches;}
	public function titre()				{return $this->_titre;}
	public function titreHelp()			{return $this->_titreHelp;}
	public function titreHelpPos()		{return $this->_titreHelpPos;}
	public function tlong()				{return $this->_tlong;}
	public function value()				{return $this->_value;}

	//--------------------------------------
	// Setters
	//--------------------------------------

	public function setIdParentForm($valeur)	{$this->_idParentForm = $valeur;}
	public function setFieldType($valeur)		{$this->_fieldType = $valeur;}
	private function setIdField($idfield)		{$this->_idField = $idfield;}
	public function setDbfield($dbfield)		{$this->_dbfield = $dbfield;}
	public function setInputType($type)			{$this->_inputType = $type;}
	protected function setPostName($postName)	{$this->_postName = $postName.'_'.$this->_idParentForm;} //le nom du POST doit être unique (en cas de 2 formulaires sur le même script PHP)	 
	public function setPostIsTab($valeur)		{$this->_postIsTab = $valeur;}
	private function _setId($id)				{$this->_id = 'id'.ucfirst($id);}
	public function setDesign($design)			{$this->_design = $design;}
	public function setDpos($dpos)				{$this->_dpos = $dpos;}
	public function setDecalage($decalage)		{$this->_decalage = $decalage;}
	public function setClong($clong)			{$this->_clong = $clong;}
	public function setCclass($cclass)			{$this->_cclass = $cclass;}
	public function setLlong($llong)			{$this->_llong = $llong;}
	public function setLclass($lclass)			{$this->_lclass = $lclass;}
	public function setLabel($label)			{$this->_label = $label;}
	public function setLabelHelp($texte)		{$this->_labelHelp = $texte;}
	public function setLabelHelpPos($pos)		{$this->_labelHelpPos = $pos;}
	public function setLalign($align)			{$this->_lalign = $align;}	
	public function setLabelPlus($info)			{$this->_labelPlus = $info;}
	public function setLabelPlusHelp($texte)	{$this->_labelPlusHelp = $texte;}
	public function setLabelPlusHelpPos($texte)	{$this->_labelPlusHelpPos = $texte;}
	public function setValue($value)			{$this->_value = $value;}
	public function setMaxlength($length)		{$this->_maxlength = $length;}
	public function setJavascript($javascript)	{$this->_javascript = $javascript;}
	public function setTalign($align)			{$this->_talign = $align;}	
	public function setTitre($titre)			{$this->_titre = $titre;}
	public function setTitreHelp($titre)		{$this->_titreHelp = $titre;}
	public function setTitreHelpPos($pos)		{$this->_titreHelpPos = $pos;}
	public function setTclass($tclass)			{$this->_tclass = $tclass;}
	public function setTlong($tlong)			{$this->_tlong = $tlong;}
	public function setLpos($position)			{$this->_lpos = $position;}
	public function setErreur($erreur)			{$this->_erreur = $erreur;}
	public function setShowErreur($valeur)		{$this->_showErreur = $valeur;}
	public function setComplement($info)		{$this->_complement = $info;}
	public function setSpellcheck($info)		{$this->_spellcheck = $info;}
	public function setPlaceholder($placeholder){$this->_placeholder = $placeholder;}
	public function setNewLine($valeur)			{$this->_newLine = $valeur;}
	public function setFlexLine($valeur)		{$this->_flexLine = $valeur;}
	public function setLiberreur($liberreur)	{$this->_liberreur = $liberreur;}
	public function setLiberreurHelp($liberreur){$this->_liberreurHelp = $liberreur;}
	public function setBorder($border)			{$this->_border = $border;}
	public function setInvisible($info)			{$this->_invisible = $info;}

	//ce setter affecté à la propriété &_testMatches est aussi chargé d'afficher l'étoile (*) au label
	//des champs à caractère obligatoires
	public function setTestMatches($testMatches) {
		if (!empty($testMatches)) {
			//test de l'existence des matches passés
			foreach($testMatches as $match) {
				if (!in_array($match, $this->_MATCHES)) {
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_MATCH_INVALIDE');
					break;
				}
			}
			//ajout d'une * aux champs REQUIRED et REQUIRED_SELECTION
			if ((in_array('REQUIRED', $testMatches)) || 
				(in_array('REQUIRED_SELECTION', $testMatches))) {			
				//si pas déja posée, on ajoute un * à la fin du label
				if (substr($this->_label, -1) != '*') $this->_label.= '*';
			}
		}
		//affectation
		$this->_testMatches = $testMatches;
    }

	//ajoute un petit 1 après le label
	public function setReadonly($readonly) {
		if ($readonly == true) {
			$this->_label.= '<small><sup>1</sup></small>';
		}
		$this->_readonly = $readonly;
    }

	public function setEnable($enable) {$this->_enable = $enable;}
	private function _setIdbchamp($info) {$this->_idbchamp = 'ufbchamp_'.$info;}
	private function _setIdztitre($info) {$this->_idztitre = 'ufztitre_'.$info;}
	private function _setIdzchamp($info) {$this->_idzchamp = 'ufzchamp_'.$info;}

	//--------------------------------------
	// Autres methodes
	//--------------------------------------

	//Affichage d'un libellé correspondant à un therme (constante)
	//Si jamais une fonction getLib() existe dans l'application, c'est cette fonction qui sera appelée
	//Ceci permet par exemple aux application de gérer le multi-langues
	public function getLib($mnemo, $param1='') {
		if (function_exists('getLib')) return getLib($mnemo, $param1);
		//dans le cas contraire
		$libelles = array(
			//------------------------------------------
			// Libellés des classes UniversalField (UFC)
			//------------------------------------------
			'UFC_CHAMP_REQUIS'				=> 'Ce champ ne doit pas être vide',
			'UFC_CHAMP_NUMERIQUE'			=> 'Ce champ doit être numérique',
			'UFC_CHOIX_INVALIDE'			=> 'Ce choix n\'est pas valide',
			'UFC_INTEGER_1OU2'				=> 'Entier attendu (2 chiffres max)',
			'UFC_FLOAT'						=> 'Nombre à virgule attendu (décimales optionnelles)',
			'UFC_FLOAT_2DEC'				=> 'Nombre à virgule attendu (2 décimales optionnelles)',
			'UFC_INTEGER_X'					=> '%d chiffres attendus',
			'UFC_INTEGER'					=> 'Entier attendu',
			'UFC_DOLLARS'					=> 'Dollars attendus',
			'UFC_MONNAIE'					=> 'Monnaie attendue',
			'UFC_INTEGER_SPACED'			=> 'Entier attendu (espaces autorisés)',
			'UFC_UNSIGNED_INTEGER'			=> 'Entier non signé attendu',
			'UFC_SIGNED_INTEGER'			=> 'Entier signé attendu',
			'UFC_BOOLEAN'					=> '1 ou 0 attendu',
			'UFC_DATETIME'					=> 'Format date heure attendu : yyyy-mm-jj hh:mm:ss',
			'UFC_EMAIL'						=> 'eMail attendue',
			'UFC_EMAIL_APOSTROPHE'			=> 'eMail attendue (apostrophe autorisée)',
			'UFC_ALPHA_SIMPLE'				=> 'Caractères alphanumériques attendus',
			'UFC_ALPHA_NOMS'				=> 'Caractères alphanumériques accentués attendus',
			'UFC_FILE_NAME'					=> 'Caractères compatibles avec nomage de fichiers attendus',
			'UFC_URL'						=> 'url attendue',
			'UFC_IPV4'						=> 'adresse IP V4 attendue',
			'UFC_MAC'						=> 'adresse Mac attendue',
			'UFC_SHA1'						=> '40 caractères hexadécimaux attendus',
			'UFC_MAJ_VERROUILLEES'			=> 'Attention, les majuscules sont verrouilllées !',
			'UFC_FICHIER_INEXISTANT'		=> 'Fichier inexistant&hellip;',
			'UFC_TYPE_FICHIER_NON_AUTORISE'	=> 'Ce type de fichier n\'est pas autorisé. Sont autorisés : ',
			'UFC_NOM_FICHIER_NON_VALIDE'	=> 'Nom de fichier non valide.',
			'UFC_POIDS_FICHIER_MOINS_DE'	=> 'Votre fichier doit faire moins de ',
			'UFC_UPLOAD_MAX_FILE_SIZE_INI'	=> 'Le poids du fichier dépasse celui précisé par la directive upload_max_filesize du fichier php.ini',
			'UFC_UPLOAD_MAX_FILE_SIZE_FORM'	=> 'Le poids du fichier dépasse celui précisé par la directive MAX_FILE_SIZE du formulaire',
			'UFC_IMAGE_PARTIEL_TELECHARGEE'	=> 'L\'image n\'a été que partiellement téléchargée',
			'UFC_AUCUN_FICHIER_TELECHARGE'	=> 'Aucun fichier n\'a été téléchargé',
			'UFC_DOSSIER_TEMP_MANQUANT'		=> 'Un dossier temporaire est manquant',
			'UFC_ECHEC_ECRITURE_DISQUE'		=> 'Échec de l\'écriture du fichier sur le disque',
			'UFC_ECHEC'						=> 'Échec&hellip;',
			'UFC_MATCH_INVALIDE'			=> 'Paramètre de propriété testMatches inconnu!'
		);
		return sprintf($libelles[$mnemo], $param1);
	}

	//hydratation des données : c'est à dire le remplissage des propriétés de l'objet
	//si la propriete readonly existe, on execute le setReadonly en dernier
	public function hydrate(array $donnees) {
		$readonly = false;
		foreach ($donnees as $key => $value) {
			if ($key == 'readonly') {
				//on traitera à la fin
				$readonly = true;
				$valReadOnly = $value;
				continue;
			}
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
		//prise en compte de la propriete 'readonly' maintenant
		if ($readonly == true) {
			$this->setReadonly($valReadOnly);
		}
		//nomage de l'id du champ
		$this->_setId($this->_idField);
		//nomage ids du "bloc de champ", de la "zone de titre" et de la "zone de champ"
		$this->_setIdbchamp($this->_idField);
		$this->_setIdztitre($this->_idField);
		$this->_setIdzchamp($this->_idField);
	}

	//test du champ selon les criteres choisis
	public function test() {
		//on commence par faire un relevé du POST
		$this->relever();
		
		//traitement des input de type 'file'
		if ($this->inputType() == 'file') {
			//on fait des vérifications non pas sur le $_POST mais sur $_FILES
			// traitement des erreurs renvoyées par PHP (pas d'erreur = 0)
			switch($_FILES[$this->postName()]['error']) {
				//Valeur : 0; Aucune erreur, le fichier a bien été uploadé
				case UPLOAD_ERR_OK:
					// si pas d'erreur on fait des test supplementaires
					// test fichier vide
					if (!file_exists($_FILES[$this->postName()]['tmp_name'])) {
						$this->_erreur = true;
						$this->_liberreur = $this->getLib('UFC_FICHIER_INEXISTANT');
						return;
					}
					//construction de la liste des extensions autorisées
					$extensions_autorisees = array_unique(array_column($this->complement(), 0));
					//test si l'extension du fichier est autorisée
					$extension = '.'.getExtension($_FILES[$this->postName()]['name']);
					if (!in_array($extension, $extensions_autorisees)) {
						$this->_erreur = true;
						$this->_liberreur = $this->getLib('UFC_TYPE_FICHIER_NON_AUTORISE').implode(', ', $extensions_autorisees);
						return;
					}
					//test si le fichier est autorisé (test sur le type mime)
					if (!isset($this->complement()[$_FILES[$this->postName()]['type']])) {
						//construction de la liste des extensions autorisées
						$this->_erreur = true;
						$this->_liberreur = $this->getLib('UFC_TYPE_FICHIER_NON_AUTORISE').implode(', ', $extensions_autorisees);
						return;
					}
					//plus sécurisé, test maintenant sur le fichier lui-même (car le test précedent fonctionne si on change d'extention du fichier)
					//ATTENTION : pour pouvoir utiliser la classe "finfo" il faut que l'extention "extension=php_fileinfo.dll" doit activée dans php.ini
					$finfo = new finfo(FILEINFO_MIME);	
					$mime = $finfo->file($_FILES[$this->postName()]['tmp_name']);
					$dummy = explode(';', trim($mime));
					if (!isset($this->complement()[$dummy[0]])) {
						$this->_erreur = true;
						$this->_liberreur = $this->getLib('UFC_TYPE_FICHIER_NON_AUTORISE').implode(', ', $extensions_autorisees);
						return;
					}
					//vérifie sur le nom du fichier ne comporte pas le caractère null ni, tant qu'à faire, aucun autre caractère de contrôle ou slashe et backslashe. 
					if (preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $_FILES[$this->postName()]['name'])) {
						$this->_erreur = true;
						$this->_liberreur = $this->getLib('UFC_NOM_FICHIER_NON_VALIDE');
						return;
					}
				    //on vérifie la taille du fichier
					//comparaison par rapport à la taille choisie et positionnée dans complement() (et < à 2Mo)
					$taille_max = $this->complement()[$_FILES[$this->postName()]['type']][1];
					if (filesize($_FILES[$this->postName()]['tmp_name']) > $taille_max) {
						$this->_erreur = true;
						$this->_liberreur = $this->getLib('UFC_POIDS_FICHIER_MOINS_DE').($taille_max / 1000).' Ko !';
						return;
					}
					return;					
				//Valeur : 1; Le fichier excède le poids autorisé par la directive upload_max_filesize de php.ini (2M par défaut)
				//"The uploaded file exceeds the upload_max_filesize directive in php.ini";
				case UPLOAD_ERR_INI_SIZE:  
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_UPLOAD_MAX_FILE_SIZE_INI').' ('.ini_get('upload_max_filesize').')';
					return;
				//Valeur : 2; Le fichier excède le poids autorisé par le champ MAX_FILE_SIZE s'il a été donné (marche pas ici)
				//"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
				case UPLOAD_ERR_FORM_SIZE:
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_UPLOAD_MAX_FILE_SIZE_FORM').' ('.$_FILES[$this->postName()]['error'].' Ko)';
			        return;
				//Valeur : 3; Le fichier n'a été uploadé que partiellement
				//"The uploaded file was only partially uploaded"
				case UPLOAD_ERR_PARTIAL:
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_IMAGE_PARTIEL_CHARGEE');
					return;
				//Valeur : 4; Aucun fichier n'a été uploadé
				//"No file was uploaded";
		        case UPLOAD_ERR_NO_FILE:
					//on ne bloque pas si le champ n'a pas été saisi
					if ((!empty($this->_testMatches)) && (in_array('REQUIRED', $this->_testMatches))) {
						$this->_erreur = true;
						$this->_liberreur = $this->getLib('UFC_AUCUN_FICHIER_TELECHARGE');
					}
				    return; 
				//Pas de 5, ne pas demander pourquoi ^^ (voir doc PHP - http://php.net/manual/fr/features.file-upload.errors.php)
				//Valeur : 6. Un dossier temporaire est manquant. Introduit en PHP 5.0.3.
				//"Missing a temporary folder"
				case UPLOAD_ERR_NO_TMP_DIR:
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_DOSSIER_TEMP_MANQUANT');
		            return;
				//Valeur : 7. Échec de l'écriture du fichier sur le disque. Introduit en PHP 5.1.0.
				//"Failed to write file to disk"
			    case UPLOAD_ERR_CANT_WRITE:
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_ECHEC_ECRITURE_DISQUE');
					return;
				//Valeur : 8. Une extension PHP a arrêté l'envoi de fichier. PHP ne propose aucun moyen de déterminer quelle extension est en cause. 
				//"File upload stopped by extension"
			    case UPLOAD_ERR_EXTENSION:
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_ECHEC');
					return;
				default:
					return;
			}
		}

		//suite du traitement
		if ($this->postIsTab()) return;					//pas de test sur les tableaux (cas des select multiples)
		if ($this->_enable == false) return;			//pas de test sur les champs disabled
		elseif ($this->_invisible == true) return;		//pas de test sur les champs invisibles
		elseif ($this->_readonly == true) return;		//pas de test sur les champs readonly
		elseif ($this->_testMatches == null) return;	//pas de test si on en a pas demandé
		foreach($this->_testMatches as $test) {
			if ($test == 'REQUIRED') {
				if ($this->_value == '') {
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_CHAMP_REQUIS');
					break;
				}
			}
			elseif ($test == 'REQUIRED_SELECTION') {
				if (($this->_value == '') || ($this->_value == 'NULL')) {
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_CHOIX_INVALIDE');
				}
			}
			elseif ($test == 'NUMERIC') {
				if (($this->_value != '') && (!is_numeric($this->_value))) {
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_CHAMP_NUMERIQUE');
				}
			}
			elseif ($test == 'NOT_ZERO') {
				if (($this->_value != '') && ($this->_value == 0)) {
					$this->_erreur = true;
					$this->_liberreur = $this->getLib('UFC_CHOIX_INVALIDE');
				}
			}
			elseif ($test == 'CHECK_INTEGER_1OU2') {	//entier à 1 ou 2 chiffre
				if ($this->_value != '') $this->_regex(CHECK_INTEGER_1OU2, $this->getLib('UFC_INTEGER_1OU2'));
			}
			elseif ($test == 'CHECK_FLOAT') {		//nombre à virgule avec décimales optionnelles
				if ($this->_value != '') $this->_regex(CHECK_FLOAT, $this->getLib('UFC_FLOAT'));
			}
			elseif ($test == 'CHECK_FLOAT_2DEC') {		//nombre à virgule avec 2 décimales optionnelles
				if ($this->_value != '') $this->_regex(CHECK_FLOAT_2DEC, $this->getLib('UFC_FLOAT_2DEC'));
			}
			elseif ($test == 'CHECK_INTEGER_8') {		//8 chiffres obligatoires
				if ($this->_value != '') $this->_regex(CHECK_INTEGER_8, $this->getLib('UFC_INTEGER_X', 8));
			}
			elseif ($test == 'CHECK_INTEGER') {			//0..n chiffres (signes +- autorisés)
				if ($this->_value != '') $this->_regex(CHECK_INTEGER, $this->getLib('UFC_INTEGER'));
			}
			elseif ($test == 'CHECK_DOLLARS') {			//($9 999 999 999)
				if ($this->_value != '') $this->_regex(CHECK_DOLLARS, $this->getLib('UFC_DOLLARS'));
			}
			elseif ($test == 'CHECK_MONNAIE') {			//($ou£ou€9 999 999 999)
				if ($this->_value != '') $this->_regex(CHECK_MONNAIE, $this->getLib('UFC_MONNAIE'));
			}
			elseif ($test == 'CHECK_INTEGER_SPACED') {	//($ou£ou€9 999 999 999)
				if ($this->_value != '') $this->_regex(CHECK_INTEGER_SPACED, $this->getLib('UFC_INTEGER_SPACED'));
			}			
			elseif ($test == 'CHECK_UNSIGNED_INTEGER') {//0..n chiffres avec signe +- interdit (donc positif)
				if ($this->_value != '') $this->_regex(CHECK_UNSIGNED_INTEGER, $this->getLib('UFC_UNSIGNED_INTEGER'));
			}
			elseif ($test == 'CHECK_SIGNED_INTEGER') {	//0..n chiffres avec signe obligatoire (+-)
				if ($this->_value != '') $this->_regex(CHECK_SIGNED_INTEGER, $this->getLib('UFC_SIGNED_INTEGER'));
			}
			elseif ($test == 'CHECK_BOOLEAN') {			//0 ou 1
				if ($this->_value != '') $this->_regex(CHECK_BOOLEAN, $this->getLib('UFC_BOOLEAN'));
			}
			elseif ($test == 'CHECK_DATETIME') {		//datetime : 0000-00-00 00:00 (les secondes sont optionnelles)
				if (!empty($this->_value)) $this->_regex(CHECK_DATETIME, $this->getLib('UFC_DATETIME'));
			}
			elseif ($test == 'CHECK_INTEGER_4') {		//annee (4 chiffres obligatoires)
				if ($this->_value != '') $this->_regex(CHECK_INTEGER_4, $this->getLib('UFC_INTEGER_X', 4));
			}
			elseif ($test == 'CHECK_EMAIL') {			//email (@ et .)
				if ($this->_value != '') $this->_regex(CHECK_EMAIL, $this->getLib('UFC_EMAIL'));
			}
			elseif ($test == 'CHECK_EMAIL_APOSTROPHE') {			//email (@ et . et ')
				if ($this->_value != '') $this->_regex(CHECK_EMAIL_APOSTROPHE, $this->getLib('UFC_EMAIL_APOSTROPHE'));
			}
			elseif ($test == 'CHECK_ALPHA_SIMPLE') {	//chiffres + minuscules + espace + "-"
				if ($this->_value != '') $this->_regex(CHECK_ALPHA_SIMPLE, $this->getLib('UFC_ALPHA_SIMPLE'));
			}
			elseif ($test == 'CHECK_ALPHA_NOMS') {		//majuscules + minuscules + accents + espace
				if ($this->_value != '') $this->_regex(CHECK_ALPHA_NOMS, $this->getLib('UFC_ALPHA_NOMS'));
			}
			elseif ($test == 'CHECK_FILE_NAME') {	//chiffres + minuscules + espace + "-"
				if ($this->_value != '') $this->_regex(CHECK_FILE_NAME, $this->getLib('UFC_FILE_NAME'));
			}
			elseif (($test == 'CHECK_URL') && ($this->_value != '')) {				//une url http://...
				if ($this->_value != '') $this->_regex(CHECK_URL, $this->getLib('UFC_URL'));
			}
			elseif (($test == 'CHECK_IPV4') && ($this->_value != '')) {				//une adresse IP v4
				if ($this->_value != '') $this->_regex(CHECK_IPV4, $this->getLib('UFC_IPV4'));
			}			
			elseif (($test == 'CHECK_MAC') && ($this->_value != '')) {				//une adresse IP v4
				if ($this->_value != '') $this->_regex(CHECK_MAC, $this->getLib('UFC_MAC'));
			}
			elseif (($test == 'CHECK_SHA1') && ($this->_value != '')) {				//40 car hexa
				if ($this->_value != '') $this->_regex(CHECK_SHA1, $this->getLib('UFC_SHA1'));
			}
		}
	}

	//transforme la saisie du champ selon les criteres choisis
	private function _transform() {
		if ($this->postIsTab()) return;				//pas de transform sur les tableaux (cas des select multiples)
		if ($this->_invisible == true) return;		//pas de transform sur les champs invisibles
		if ($this->_readonly == true) return;		//pas de transform sur les champs readonly
		if ($this->_testMatches == null) return;	//pas de transform si on en a pas demandé
		foreach($this->_testMatches as $test) {
			if ($test == 'UPPERCASE') {				//mise en majuscules
				$this->_value = mb_strtoupper($this->_value, 'UTF-8');
			}
			elseif ($test == 'LOWERCASE') {				//mise en minuscule
				$this->_value = mb_strtolower($this->_value, 'UTF-8');
			}
			elseif ($test == 'WORDSCASE') {				//mise en minuscule de toute la phrase + mise en majuscule des premières lettres de chaque mot
				$this->_value = mb_strtolower($this->_value, 'UTF-8');
				$this->_value = mb_convert_case($this->_value, MB_CASE_TITLE, 'UTF-8');  //equivalent ucwords mais pour UTF-8
			}
			elseif ($test == 'FIRST-LETTER') {			//mise en majuscule de la première lettre
				//mettre la première lettre en majuscules (equivalent ucfirst mais pour UTF-8)
				mb_internal_encoding('UTF-8');
				$this->_value = mb_strtoupper(mb_substr($this->_value, 0, 1)).mb_substr($this->_value, 1);
			}
			elseif ($test == 'FIRST-LETTER-ONLY') {			//mise en minuscule de toute la phrase + majuscule de la première lettre
				$this->_value = mb_strtolower($this->_value, 'UTF-8');
				//mettre la première lettre en majuscules (equivalent ucfirst mais pour UTF-8)
				mb_internal_encoding('UTF-8');
				$this->_value = mb_strtoupper(mb_substr($this->_value, 0, 1)).mb_substr($this->_value, 1);
			}
			if ($test == 'TRIM') {						//supprimer les espaces avant et après le texte saisi
				$this->_value = trim($this->_value);
			}
			if ($test == 'NOSPACE') {					//supprimer les espaces dans le texte saisi
				$this->_value = trim(preg_replace('/\s/', '', $this->_value));
			}
			if ($test == 'NODOUBLESPACE') {				//supprimer les espaces en doubles dans le texte saisi
				$this->_value = preg_replace('/\s{2,}/', ' ', $this->_value);
			}
			if ($test == 'USD') {						//Mettre sous la forme ($9 999 999) si possible (valeur numérique)
				$aVirer = array('$', '€', '£', ' ');
				$test = str_replace($aVirer, '', $this->_value);
				if (is_numeric($test))
					$this->_value = '$'.number_format((float)$test, 0, ' ', ' ');
			}
			if ($test == 'EUR') {						//Mettre sous la forme (€9 999 999) si possible (valeur numérique)
				$aVirer = array('$', '€', '£', ' ');
				$test = str_replace($aVirer, '', $this->_value);
				if (is_numeric($test))
					$this->_value = '€'.number_format((float)$test, 0, ' ', ' ');
			}
			if ($test == 'GBP') {						//Mettre sous la forme (£9 999 999) si possible (valeur numérique)
				$aVirer = array('$', '€', '£', ' ');
				$test = str_replace($aVirer, '', $this->_value);
				if (is_numeric($test))
					$this->_value = '£'.number_format((float)$test, 0, ' ', ' ');
			}
			if ($test == 'MILLE_SPACED') {				//Mettre sous la forme (9 999 999) si possible (valeur numérique)
				$test = str_replace(' ', '', $this->_value);
				if (is_numeric($test))
					$this->_value = number_format((float)$test, 0, ' ', ' ');
			}
		}
	}

	//Relever le contenu du champ $_POST. 
	//Pour être certain que cela marche aussi pour les textarea on passe la fonction rnTo13 au $_POST
	//Pour la bonne gestion des checkbox (qui ne renvoie pas de POST si pas cochée), le cas est pris en compte
	//ATTENTION, les champs 'disabled' n'envoient JAMAIS de POST !
	public function relever() {
		if (isset($_POST[$this->_postName])) {
			$this->_value = rnTo13(mySqlDataProtect($_POST[$this->_postName]));
			$this->_transform();
		}
		else {
			//dans ce cas précis, il ne peux s'agir que d'une case à cocher qui n'a pas été cochée, on renvoie 'notposted'
			$this->_value = 'notposted';
		}
	}

}