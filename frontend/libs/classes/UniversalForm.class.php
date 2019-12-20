<?php
//==============================================================
// Classe de formulaire
//--------------------------------------------------------------
// Classe de gestion d'un formulaire
// Version 1.01 du 28.10.2014
// Version 1.02 du 18.12.2014
//		- Ajout de la methode getFieldObjectByName()
// Version 1.03 du 21.01.2015
//		- Ajout de la methode doThings()
//		- Ajout de la méthode getFieldObjectById()
// Version 2.0 du 06.02.2015
//		- Modification profondes (creatField / field / fields / etc.)
// Version 2.10 du 20.03.2015
//		- Ajout des fonctions 'mySqlDataProtect' et 'rnTo13' hors classe
//		- Correction disfonctionnement -> Ajout d'un test d'existence des champs dans getData()
// Version 2.11 du 08.07.2015
//		- Ajout d'une propriété fieldType pour chaque objet qui renseigne sur le type d'objet (text / radio / area / div / etc.)
//		- Correction getData suite à bug readonly pour les boutons radio
// Version 2.12 du 04.10.2015
//		- Ajout de la variable idFormulaire pour rendre chaque formulaire unique et ainsi permettre de développer plusieurs formulaire sur la même page PHP
// Version 2.13 du 07.10.2015
//		- Ajout de la fonction "public function testMatches()"
// Version 2.14 du 09.10.2015
//		- Amélioration du CSS pour que le curseur '?' apparaisse lorsqu'un title est positionné, c'est à dire un paramètre "labelHelp"
// Version 2.15 du 11.10.2015
//		- Ajout de 2 propriétés pour les SELECT (SIZE pour donner l'épaisseur de la liste en nombre d'items ; MULTIPLE pour une selection multiple d'items)
// Version 2.16 du 20.10.2015
//		- Ajout de la propriété 'liberreurHelp' pour la classer UniversalFieldArea
// Version 2.17 du 13.11.2015
//		- Ajout du champ 'commentaire' (juste du texte html)
//		- Ajout de la fonction javascript 'uf_setValue'
// Version 2.18 du 20.11.2015
//		- Modification de la classe UniversalFieldText pour prendre en compte les champs de type 'file'. La méthode modifiée est relever()
//		- Ajout du paramètre 'complement' qui sert de fourre-tout. Il a été nécessaire pour donner des informations de filtre aux champs de type text 'file'
//		- Amélioration de la fonction test() pour prendre en compte des input de type 'file' (test de la presence du fichier, taille, et type qui 
//			sont définis dans le paramètre complement())
// Version 2.19 du 30.11.2015
//		- Amélioration des champs 'submit'. Maintenant on peut avoir plusieurs champs submit sur un même formulaire (avant on était limité à 1 seul)
//		- Ajout de la méthode groupName pour la classe UniversalFieldSubmit. Ceci permet (en donnant un nom identique de groupe à chaque bouton submit) de //
//			récupérer la valeur du bouton cliqué (son libellé donc) dans un seul et même champ dbfield
// Version 2.20 du 06.01.2016
//		- Amélioration des tests NUMERIC et NOT_ZERO : maintenant les champs vides ne déclenchent plus l'erreur
// Version 2.21 du 03.02.2016
//		- Corrigé bug methode _transform() de transformation de la saisie
//		- Ajouté TRIM à la methode _transform() qui supprime les espaces avant et après la saisie
// Version 2.22 du 10.02.2016
//		- Correction bug trouvé pour les submit multiples. Se concrétise par : 
//			1. la suppression de la methode groupName (et du paramètre 'groupName' développé dans  la version 2.19) pour la classe UniversalFieldSubmit 
//				qui ne sert plus à rien
//			2. la modification de la classe UniversalFieldSubmit (ajout de la propriété valueBase)
//			3. la modification de la méthode publique getData() afin de ne pas prendre en compte les "submit" qui ne renvoient pas de POST (ceux non cliqués)
// Version 2.23 du 16.02.2016
//		- Ajout du test de saisie CHECK_DOLLARS, un nombre qui peux (uniquement) commencer par $ avec des espaces entre les chiffres
//		- Ajout du test de saisie CHECK_MONNAIE, un nombre qui peux (uniquement) commencer par $ ou £ ou € avec des espaces entre les chiffres
//		- Ajout du test de saisie CHECK_INTEGER_SPACED, un nombre qui peux contenir des espaces entre les chiffres
//		- Ajout du Transformateur 'USD' qui transforme un entier sous la forme $9 9999 999 (entier aux milliers espacés et précédé du signe $)
//		- Ajout du Transformateur 'EUR' qui transforme un entier sous la forme €9 9999 999 (entier aux milliers espacés et précédé du signe €)
//		- Ajout du Transformateur 'GBP' qui transforme un entier sous la forme £9 9999 999 (entier aux milliers espacés et précédé du signe £)
//		- Ajout du Transformateur 'MILLE_SPACED' qui transforme un entier sous la forme 9 9999 999 (entier aux milliers espacés)
//		- Modification de la méthode getData pour que les chiffres transformés vu ci-dessus (EUR, USD, GBP, MILLE_SPACED) renvoie des entiers, ce qui évite
//			au programmeur de transformer manuellement.
// Version 2.24 du 21.02.2016
//		- Correction bug dans de draw de champs hidden. Valeur maintenant correctement echappée.
// Version 2.30 du 02.03.2016
//		- Création de la classe UniversalFieldImage qui permet de positionner une image sur le formulaire.
// Version 2.31 du 03.03.2016
//		- Correction d'un bug dans UniversalFieldRadio, methode relever(). Elle ne checkait pas le bouton radio selon value() renvoyée par le POST (relever)
// Version 2.32 du 08.03.2016
//		- Revu methode d'affichage des checkbox, radio et select. Maintenant titre et label sont gérés independament. Titre doit être explicite
//		- Simplicifation du code
//		- Aide à la conception des checkbox, radio, submit, select, cancel, button, reset. Ajouté controle de manque de paramètres obligatoires. 
//		- ajouté le paramètre 'lpos' sur les champs 'text'
// Version 2.33 du 15.03.2016
//		- ajout du setter setOperation($valeur) a UniversalForm pour forcer l'opération en cours, ce qui peut être parfois utile
//		- ajout d'une constante VERSION définissant la version du code
//		- amélioration de l'objet UniversalFieldComment (ajout paramètres : dbfield, value, cclass, border)
// Version 2.34 du 17.03.2016
//		- création du paramètre "spellcheck" qui s'applique aut classe "area" et "text". Si false, l'orthographe du texte contenu dans ces champs n'est pas vérifié. 
//			Si true (valeur par défaut), c'est le paramétrage du navigateur qui fait foi pour cette fonction
// Version 2.35 du 18.03.2016
//		- ajout de la propriété _message à la classe UniversalForm avec les Setter et Setter getMessage et setMessage. Si besoin, ceci permet de passer 
//			n'importe quel type de message entre l'application et l'objet pour les faires communiquer
// Version 2.36 du 06.04.2016
//		- ajout des traductions des messages d'erreurs de téléchargement de fichier (FILE)
// Version 2.37 du 23.08.2016
//		- amélioration du test des fichiers autorisée en upload d'un champ 'FILE'
//		- renvoie des extentions uniques sur libellé d'erreur mime d'un champ 'FILE'
//==============================================================
// Version 3.0.0 du 12 octobre 2016 - BOOTSTRAP v4.0.0-alpha.4
//		- L'objectif était d'utiliser le plus possible les CSS de Bootstrap et supprimer universalform.css
//		- Utilisation des longueurs de grilles de bootstrap (col-xs-3) pour les longueurs de champ
//		- Introduction des décalage (en colonnes de grille bootstrap)
//		- Intégration des styles CSS dans l'objet (plus besoin d'ajouter universalform.css)
//		- Supprimé les paramètres : lineType, lineClass, carriageReturn, group, la classe UniversalFieldCancel
//		- Ajouté les paramètres : newLine, design, decalage, llong, clong, lalign, placeholder, dpos
//		- Avant : modifier la valeur de 'libErreur' positionnait 'erreur'. Ce n'est maintenant plus le cas.
//		- type "Comment" largement amélioré
//		- plus de notion de groupe sur les select
//		- la gestion du remplissage des select est pris en compte dans l'objet (appel à procédure extérieure)
//		- la classe "UniversalFieldCancel" a été supprimée car c'était exactement la même chose que la classe "UniversalFieldButton"
//		- Ajout de la méthode getLib() sur UniversalField qui permet de se passer de fichiers de langues
// Version 3.1.0 du 28 octobre 2016
//		- Création de la classe unique UniversalFieldBouton qui remplace UniversalFieldSubmit, UniversalFieldButton et UniversalFieldReset
//		- Amélioration UniversalFormText pour affichage si label vide
// Version 3.2.0 du 02.11.2016
//		- Création de la classe UniversalFormSearch
// Version 3.3.0 du 22.11.2016
//		- Refonte de UniversalFormRadio et UniversalFormCheckbox
//		- Améliorations diverses
//		- Ajout d'un Addon sur l'objet Search
// Version 3.3.1 du 23.11.2016
//		- Amélioration des arrondis CSS sur l'objet Search
// Version 3.3.2 du 09.01.2017
//		- Ajout du test CHECK_FILE_NAME (vérifie si saisie compatible avec le nommage d'un fichier)
// Version 3.4.0 du 23.01.2017
//		- Utilise Bootstrap v4.0.0-alpha.6
//			- implique la disparition de col-xs-xx au profit de col ou col-xx
//			- implique la disparition des blocs de champs sur tous les objets au positionnement 'inline'
//			- implique la disparition des zones de titres sur certains objets (bouton et search)
//		- Ajout du validateur CHECK_IPV4
//		- Ajout du validateur CHECK_MAC
// Version 3.5.0 du 23.02.2017
//		- Céation d'un nouveau champ "fitretext"
//		- Céation d'un nouveau champ "fitreselect"
//		- Ajout d'une nouvelle propriété "showErreur" qui affiche ou n'affiche pas les erreurs (actuellement seulement appliqué aux boutons)
//		- Ajout de la propriété "labelHelp" pour le type de champ "bouton"
// Version 3.5.1 du 13.04.2017
//		- Ajout de "tooltip" Bootstrap sur les tag title (propriétés "labelHelp" et "titreHelp")
//		- Ajout d'une sécurité à la création de l'objet pour interdire une operation qui débuterait pas 'valid_', ceci pour 
//			interdire l'utilisation du script en ligne de commande sans passer par le formulaire
// Version 3.5.2 du 25.04.2017
//		- Ajout de la propriété talign : alignement du titre (left, right, center ou justify) pour les "checkbox"
//		- Ajout de la propriété talign : alignement du titre (left, right, center ou justify) pour les "radio"
//		- Ajout de la propriété talign : alignement du titre (left, right, center ou justify) pour les "fitretext"
//		- Correction affichage "tooltip" sur les "fitretext"
//		- Ajout de la propriété talign : alignement du titre (left, right, center ou justify) pour les "search"
//		- Correction affichage "tooltip" sur le titre "search"
//		- Correction bug : idTravail était initialisé avec 0, il est maintenant initialisé avec 'null' (car il pouvait y avoir un id de travail à 0 existant)
// Version 3.5.3 du 05.05.2017
//		- Ajout de la propriété labelHelpPos : alignement du libelle d'aide (left, top (defaut), right, bottom) pour les tous les champs sauf 'div', 'divfin' et 'hidden'
// Version 3.5.4 du 16.05.2017
//		- Amélioration de la méthode privée "_transform" de UniversalField pour transformation correcte des champs en UTF-8
// Version 3.5.5 du 14.06.2017
//		- Rajouté prise en compte javascript sur les champs 'text' 
//		- Ajout de la fonction javascript 'capLock' qui affiche dans la zone error un texte pour dire si la touche 
//			majuscule est activée
//		- Suite à oublie, ajout de la classe protected setPostName($postName) pour les séparateurs (les séparateurs n'avait pas de noms 
//			spécialisés dans le POST. Corrigé afin d'être bien conforme aux règles de nomage)
// Version 3.5.6 du 27.07.2017
//		- Amélioration de la méthode UniversalField::setTestMatches() qui prévient le développeur si le MATCH est inconnu (montée d'erreur setError(true))
// Version 3.5.7 du 08.08.2017
//		- Ajout des Transformers : NOSPACE et NODOUBLESPECE
// Version 3.5.8 du 04.09.2017
//		- Suppression de la méthode usesJavascriptFunctions() au profil d'une intégration du fichier universalform.min.js
//			contenant le code des fonctions javascript.
//		- Sur universalFieldRadio, enlevé le test si champ "value" est vide, ce qui empéchait une valeur 0. Par défaut la valeur du bouton radio est 0.
// Version 3.6.0 du 20.10.2017
//		- Utilise Bootstrap v4.0.0-beta.2 (https://getbootstrap.com)
// Version 3.6.1 du 25.10.2017
//		- Améliorations générales
// Version 3.6.2 du 21.12.2017
// 		- la méthode protégée field() devient publique
// Version 3.7.0 du 13.03.2018
//		- Utilise Bootstrap v4.0.0 (https://getbootstrap.com)
// Version 3.7.1 du 17.03.2018
//		- Correction arrondis des boutons UniversalFieldSearch, UniversalFieldFiltretext et UniversalFieldFiltreselect
// Version 3.8.0 du 14.05.2018
//		- Obligation ajouter la classe "uf" au tag "<form>" qui doit utiliser les classes UniversalForm
//		- Ajout des propriétes labelPlus, labelPlusHelp et labelPlusHelpPos sur les champ de type UniversalFieldText (permet de scinder le label en 2 parties, par exemple un texte et une icone)
// Version 3.9.0 du 17.05.2018
//		- Ajout de la propriété flexLine : application des utilitaires flexbox sur les lignes d'objets (permet des cadrages, positionnement et plein d'autre choses sur les objets de la ligne)
// Version 3.9.1 du 12.06.2018
//		- Correction bug UniversalFieldSelect : le textMatches 'REQUIRED_SELECTION' ne fonctionnait pas car la méthode relever() rendait disable la liste et le match ne se faisait plus
// Version 3.9.2 du 12.11.2018
//		- Ajout du validateur CHECK_SHA1
// Version 3.10.0 du 28.11.2018
//		- Suite à version 4.1.3 de Bootstrap, correctif pour les classes UniversalFieldImage() et UniversalFielfCOmment() -> ajouté un "h-auto" après chaque "form-control"
//			(voir https://github.com/twbs/bootstrap/pull/26820)
// Version 3.10.1 du 01.12.2018
//		- Correction bug getData() "if (null !== $champ->testMatches())" remplacé par "if (!empty($champ->testMatches()))"
// Version 3.11.1 du 13.12.2018
//		- Renommage du Match 'FIRST-LETTER' en 'FIRST-LETTER-ONLY' (il passait toute le texte en minuscule avant de mettre la première lettre en majuscule, donc plus approprié)
//		- Création du Match 'FIRST-LETTER' (mise en majuscule de la première lettre sans passage en minuscule préalable à tout le texte)
// Version 3.11.2 du 16.01.2019
//		- Correction bug méthode testDesignPos()
// Version 3.11.3 du 17.01.2019
//		- En plus d'une valeur vide, le testMatch REQUIRED_SELECTION (pour les select) teste maintenant aussi sur la "chaine de caractère" 'NULL' (et non pas NULL)
//		- Ajout du test matches CHECK_EMAIL_APOSTROPHE (idem CHECK_EMAIL mais autorise l'apostrpohe en plus)
//		- Ajout de la constante COPYRIGHT
// Version 3.12.0 du 17.04.2019
//		- Les positionnements de tooltips sont maintenant positionnés par défaut à "auto" (rappel : les tooltips ne s'affichent pas si leur 'title' est vide). 
//			Le mode "auto" est nouveau, c'est popper qui décide de la meilleure position en fonction du contexte.
// Version 3.13.0 du 24.05.2019
//		- Création du Match 'CHECK_ALPHA_CODE' (majuscule + minuscules + '-' + '_' + '.')
// Version 3.14.0 du 29.07.2019
//		- Ajout de la propriété "multiple" pour l'objet UniversalFieldText. Seulement pris en compte pour les champs de type "file". Grâce à cette propriété on peut maintenant 
//			selectionner plusieurs fichiers en une seule fois. De ce fait la structure envoyée par getData() (pour les champs text de type file seulement) a changé.
// Version 3.15.0 du 02.08.2019
//		- Ajout de zoneTitre et zoneChamp pour les objets DIV 
//		- Ajout du paramètre 'accept' pour les champs texte de type file (selecteur de fichier ne propose que les extentions dans accept)
// Version 3.15.1 du 03.10.2019
//		- Correction bug : le passage de la souris affichait le ? même sur les champs qui n'avaient pas de texte d'aide
// Version 3.16.0 du 26.11.2019
//		- Ajout du composant "switch"
//		- Ajout des propiétés : min, max, step, pattern, autocomplete et autofocus pour les <input> de type text (UniversalFieldText)
// Version 3.17.0 du 13.12.2019
//		- Ajout de la propriété "custom" pour le composant "switch" (switch customisé en check et radio)
//		- Positionnement systématique du focus sur le premier champ en erreur (le focus par défaut est mémorisé puis repositioné lorsqu'il n'y a plus d'erreurs)
//==============================================================

//-------------------------------------------------------------------------
// Protège des données en vue de les injecter dans une base MySQL
// Attention la fonction supprime le caractère tout de qui se trouve après un 
// caractère '<' immédiatement suivi de text (ex : <24)
// Voir http://stackoverflow.com/questions/17650623/php-strip-tags-not-allowing-less-than-in-string
// pour plus d'infos
//-------------------------------------------------------------------------
if (!function_exists('mySqlDataProtect')) {
	function mySqlDataProtect($data) {
		if (is_array($data)) {
			foreach($data as $index => $dummy) {
				$data[$index] = addslashes(strip_tags($data[$index]));
				$data[$index] = str_replace(chr(13).chr(10), '\r\n', $data[$index]);
			}
		}
		else {
			$data = addslashes(strip_tags($data));
			$data = str_replace(chr(13).chr(10), '\r\n', $data);
		}
		return $data;
	}
}
if (!function_exists('rnTo13')) {
	function rnTo13($valeur)
	{
		if (is_array($valeur)) {
			foreach($valeur as $index => $dummy) {
				$valeur[$index] = str_replace('\r\n', "\r\n", $valeur[$index]);
			}
			return $valeur;
		}
		return str_replace('\r\n', "\r\n", $valeur);
	}
}

class UniversalForm {
	private $_operation;				//operation demandée (consulter/ajouter/modifier/supprimer)
	private $_action;					//action choisie par l'objet en retour (consulter/ajouter/modifier/supprimer/valid_consulter/valid_ajouter/valid_modifier/valid_supprimer)
	private $_id_travail = null;		//id article mémorisé sur lequel on travail (important pour le test de la modification de l'id_article)
	private $_lesChamps = array();		//collection de champs du formulaire
	private $_idForm;					//id unique du formulaire
	private $_message = '';				//éventuel message qu'il est possible de faire passer à l'objet
	private $_ligneEncours = false;		//dessin d'une ligne de champs en cours ?
	private $_memAutofocus = null;		//mémorise le premier objet du formulaire qui a le focus

	const VERSION = 'v3.17.0 (2019-12-13)';
	const COPYRIGHT = '&copy;2014-2020 Fabrice Labrousse';
	const CONSULTER = 'consulter';
	const AJOUTER = 'ajouter';
	const MODIFIER = 'modifier';
	const SUPPRIMER = 'supprimer';
	const DUPLIQUER = 'dupliquer';
	const RETIRER = 'retirer';
	const ENVOYER = 'envoyer';

	//--------------------------------------
	// Constructeur
	//--------------------------------------

	public function __construct($operation, $numFormulaire) {
		$this->_operation = $operation;
		$this->_idForm = $numFormulaire;		//id unique de formulaire (entier)
		$this->_actionDecide();
	}

	//--------------------------------------
	// Méthodes privées
	//--------------------------------------

	//--------------------------------------
	// decide de l'action suivante que le
	// formulaire doit mener (routage)
	//--------------------------------------

	private function _actionDecide() {
		//test s'il y a eu un post
		$submit = (isset($_POST['hidSoumissionFormulaire'.'_'.$this->_idForm]));
		if ($submit) {
			if (isset($_POST['hidOperation'.'_'.$this->_idForm])) $this->_operation = mySqlDataProtect($_POST['hidOperation'.'_'.$this->_idForm]);
			$this->_action = 'valid_'.$this->_operation;
		}
		else {
			//afin de garantir que seul le script puisse réaliser des opérations (ajouter, modifier, supprimer, etc), on interdit une
			//operation commençant par 'valid_' s'il n'y a pas eu de submit de formulaire
			if (strpos($this->_operation, 'valid_') === false) {
				$this->_action = $this->_operation;
			}
			else {
				$this->_action = 'erreur';
			}
		}
	}

	//--------------------------------------
	// Methodes protégées
	//--------------------------------------

	//Intégration du code CSS nécessaire en plus de Bootstrap et propre à universalForm
	final function getCSS() {
		$chaine = '';
		$chaine.= '<style>';

		//on enlève la marge inférieure par défaut
		$chaine.= 'form.uf .form-group {';
			$chaine.= 'margin-bottom:0;';
		$chaine.= '}';

		$chaine.= 'form.uf .danger-color {';
			$chaine.= 'background-color:inherit!important;';
			$chaine.= 'color:#d9534f!important;';
		$chaine.= '}';

		$chaine.= 'form.uf .invisible {';
			$chaine.= 'display: none!important;';
		$chaine.= '}';

		$chaine.= 'form.uf .form_error {';
			$chaine.= 'height: 1.3rem;';
			$chaine.= 'max-width: 100%;';
			$chaine.= 'overflow: hidden;';
			$chaine.= '-o-text-overflow: ellipsis;		/* pour Opera 9 */';
			$chaine.= 'text-overflow: ellipsis;			/* pour le reste du monde */';
			$chaine.= 'word-wrap: break-word;			/* on coupe les mots trop longs */';
			$chaine.= 'white-space: nowrap;				/* on coupe pas la phrase aux espaces */';
			$chaine.= 'position: absolute;';
			$chaine.= 'font-size: 0.75rem;';
			$chaine.= 'text-align: left;';
			$chaine.= 'font-style: normal;';
			$chaine.= 'font-variant: normal;';
			$chaine.= 'font-weight: normal;';
			$chaine.= 'text-transform: none;';
			$chaine.= 'text-decoration: none;';
			$chaine.= 'color: #d9534f;';
		$chaine.= '}';

		$chaine.= 'form.uf .form_error:hover {';
			$chaine.= 'overflow: visible;';
		$chaine.= '}';

		//bloc d'erreur - champ entouré de rouge
		$chaine.= 'form.uf .designError {';
			$chaine.= 'border: 1px solid #d9534f;';
			$chaine.= 'border-radius: .25rem;';
		$chaine.= '}';

		//pour mettre les placeholder en italique
		$chaine.= '*::-webkit-input-placeholder {';
			$chaine.= 'font-style: italic;';
		$chaine.= '}';
		$chaine.= '*:-moz-placeholder {';		//FF 4-18
			$chaine.= 'font-style: italic;';
		$chaine.= '}';
		$chaine.= '*::-moz-placeholder {';		//FF 19+
			$chaine.= 'font-style: italic;';
		$chaine.= '}';
		$chaine.= '*:-ms-input-placeholder {';	//IE 10+
			$chaine.= 'font-style: italic;';
		$chaine.= '}';

		//Bordure de champ
		$chaine.= 'form.uf .border {';
			$chaine.= 'border: 1px solid rgba(0,0,0,.15)!important;';
			$chaine.= 'border-radius: .25rem;';
		$chaine.= '}';

		//curseur en forme de point d'interrogation si title sur un label
		$chaine.= 'span[data-toggle="tooltip"], form label[title], form legend[title], form legend[title] label, p[class="form_error"] {';
			$chaine.= 'cursor: help;';
		$chaine.= '}';

		$chaine.= '</style>';
		return $chaine;
	}

	final function createField($fieldType, $idField, $tabInfos) {
		//on ajoute l'info "idfield" au tableau des paramètres
		if ($idField == '') die('Nom du champ de type "'.$fieldType.'" manquant');
		$tabInfos['idfield'] = $idField;
		//création de l'objet champ
		if		($fieldType == 'area')			$this->_lesChamps[$idField] = new UniversalFieldArea($tabInfos, $this->_idForm);
		elseif	($fieldType == 'bouton')		$this->_lesChamps[$idField] = new UniversalFieldBouton($tabInfos, $this->_idForm);
		elseif	($fieldType == 'switch')		$this->_lesChamps[$idField] = new UniversalFieldSwitch($tabInfos, $this->_idForm);
		elseif	($fieldType == 'checkbox') {
			//recherche si il existe déjà un checkbox nommé groupName
			//ceci afin de recopier systématiquement dans tous les checkbox du même groupe les valeurs de : 
			//'border', 'readonly', 'invisible', 'erreur', 'liberreur' et 'liberreurHelp'
			//du PREMIER checkbox 'groupName'
			if (isset($tabInfos['groupName'])) {
				$champ = $this->getFieldObjectByGroupName($tabInfos['groupName']);
				if (isset($champ)) {
					$tabInfos['border'] = $champ->border();
					$tabInfos['erreur'] = $champ->erreur();
					$tabInfos['liberreur'] = $champ->liberreur();
					$tabInfos['liberreurHelp'] = $champ->liberreurHelp();
//					$tabInfos['readonly'] = $champ->readonly();
//					$tabInfos['invisible'] = $champ->invisible();
				}
			}
			$this->_lesChamps[$idField] = new UniversalFieldCheckbox($tabInfos, $this->_idForm);
		}
		elseif	($fieldType == 'div')			$this->_lesChamps[$idField] = new UniversalFieldDiv($tabInfos, $this->_idForm);
		elseif	($fieldType == 'divfin')		$this->_lesChamps[$idField] = new UniversalFieldDivFin($tabInfos, $this->_idForm);
		elseif	($fieldType == 'hidden')		$this->_lesChamps[$idField] = new UniversalFieldHidden($tabInfos, $this->_idForm);
		elseif	($fieldType == 'radio') {
			//recherche si il existe déjà un radio nommé groupName
			//ceci afin de recopier systématiquement dans tous les radio du même groupe les valeurs de : 
			//'border', 'readonly', 'invisible', 'erreur', 'liberreur' et 'liberreurHelp'
			//du PREMIER bouton radio 'groupName'
			if (isset($tabInfos['groupName'])) {
				$champ = $this->getFieldObjectByGroupName($tabInfos['groupName']);
				if (isset($champ)) {
					$tabInfos['border'] = $champ->border();
					$tabInfos['erreur'] = $champ->erreur();
					$tabInfos['liberreur'] = $champ->liberreur();
					$tabInfos['liberreurHelp'] = $champ->liberreurHelp();
//					$tabInfos['readonly'] = $champ->readonly();
//					$tabInfos['invisible'] = $champ->invisible();
				}
			}
			$this->_lesChamps[$idField] = new UniversalFieldRadio($tabInfos, $this->_idForm);
		}
		elseif	($fieldType == 'select')		$this->_lesChamps[$idField] = new UniversalFieldSelect($tabInfos, $this->_idForm);
		elseif	($fieldType == 'separateur')	$this->_lesChamps[$idField] = new UniversalFieldSeparateur($tabInfos, $this->_idForm);
		elseif	($fieldType == 'submit')		$this->_lesChamps[$idField] = new UniversalFieldSubmit($tabInfos, $this->_idForm);
		elseif	($fieldType == 'image')			$this->_lesChamps[$idField] = new UniversalFieldImage($tabInfos, $this->_idForm);
		elseif	($fieldType == 'comment')		$this->_lesChamps[$idField] = new UniversalFieldComment($tabInfos, $this->_idForm);
		elseif	($fieldType == 'search')		$this->_lesChamps[$idField] = new UniversalFieldSearch($tabInfos, $this->_idForm);
		elseif	($fieldType == 'filtretext')	$this->_lesChamps[$idField] = new UniversalFieldFiltretext($tabInfos, $this->_idForm);
		elseif	($fieldType == 'filtreselect')	$this->_lesChamps[$idField] = new UniversalFieldFiltreselect($tabInfos, $this->_idForm);
		else									$this->_lesChamps[$idField] = new UniversalFieldText($tabInfos, $this->_idForm);
	}

	protected function afficher() {
		//intègre le code CSS spécifique aux classes universalField
		echo $this->getCSS();
		//test des parametres dpos (designPos) qui détermine la position des champs (design) sur une ligne
		$this->testDesignPos();
	}

	//Méthode appelée par la méthode tester()
	//après le POST mais juste avant le test des champs du formulaire
	protected function doThings() {
		return true;
	}

	//Méthode appelée juste avant de renvoyer les données à l'utilisateur par l'appel de getData()
	protected function doUltimateThings(&$donnees) {
	}

	//Méthode qui gère les tests supplémentaires de la saisie
	//elle doit être surchargée par l'objet fils si besoin
	protected function testsSupplementaires($champ) {
		return false;
	}

	//Méthode qui gère les tests supplémentaires de la saisie après verification unitaire des champs
	//elle doit être surchargée par l'objet fils si besoin
	protected function testsSupplementairesPosterieurs() {
		return false;
	}

	//Méthode qui initialise les données du formulaire
	//elle doit être surchargée par l'objet fils si besoin
	protected function initDonnees() {
	}

	//Méthode qui construit le formulaire
	//elle doit être surchargée par l'objet fils pour la construction du formulaire
	//elle ajoute les champs systèmes (cachés) à poster obligatoires pour le fonctionnement de la classe (operation, idTravail et submit)
	protected function construitChamps() {
		//opération de gestion du formulaire en cours
		$this->createField('hidden', 'operation', array(
			'value' => $this->_operation
			));
		//id de travail du tuple en cours d'édition
		$this->createField('hidden', 'idTravail', array(
			'value' => $this->_id_travail
			));
		//valeur témoin du POST (soumission du formulaire)
		$this->createField('hidden', 'soumissionFormulaire', array(
			'value' => 'ok'
			));
	}

	//--------------------------------------
	// Getters
	//--------------------------------------

	//recupere l'info _ligneEncours (dit si une ligne est en cours de dessin
	public function ligneEncours() {
		return $this->_ligneEncours;
	}

	//recupere un message
	public function getMessage() {
		return $this->_message;
	}

	//reference au tableau collection de champs du formulaire
	//afin d'éviter d'appeler $this->_lesChamps
	protected function fields() {
		return $this->_lesChamps;
	}

	// renvoie l'objet champ identifié par $field (acces à la propriété _lesChamps (collection de champs du formulaire))
	public function field($field) {
		//renvoie l'objet "champ" dont la propriété idField vaut $field
		return $this->_lesChamps[$field];
	}

	// renvoie l'objet champ dont la propriété groupName vaut $name
	protected function getFieldObjectByGroupName($name) {
		//recherche de l'objet "champ" dont la propriété groupName vaut $name (renvoie le premier trouvé)
		foreach($this->_lesChamps as $champ) {
			if (method_exists($champ, 'groupName')) {
				if ($champ->groupName() == $name) {
					return $champ;
				}
			}
		}
	}

	//obtenir l'action à mener (consulter/valid_consulter/ajouter/valid_ajouter/etc.)
	public function getAction() {
		$this->_actionDecide();
		return $this->_action;
	}

	//obtenir l'operation demandee (consulter/ajouter/modifier/supprimer)
	public function getOperation() {
		return $this->_operation;
	}

	//obtenir le résultat du formulaire (renvoie dans un tableau)
	public function getData() {
		if (empty($this->_lesChamps)) {
			//attention si les champs ne sont pas créés, il faut le faire sinon getData() ne renverra RIEN
			//cela pouvait se produire si on appelait getData() sans avoir fait tester() auparavent.
			//on corrige donc ce disfonctionnement
			$this->initDonnees();			//initialisation des données
			$this->construitChamps();		//constuction à vide... (cad avec données d'initiation)
			$this->doThings();				//on fait des choses avant les tests
		}
		if (isset($_POST['hidSoumissionFormulaire'.'_'.$this->_idForm])) {
			//recuperation des données depuis le $_POST
			//recuperation de l'id de travail (id article mémorisé sur lequel on travaille... si il existe)
			if (!empty($_POST['hidIdTravail'.'_'.$this->_idForm]))
				$this->_id_travail = mySqlDataProtect($_POST['hidIdTravail'.'_'.$this->_idForm]);
			foreach($this->_lesChamps as $champ) {
				$champ->relever();
			}
		}
		
		//remplissage de la structure de données (ne sont renvoyés QUE les données dont les champs 'dbfield' ont été nommés)
		$donnees = array();
		foreach($this->_lesChamps as $champ) {
			//remplissage de la structure de données avec la valeur du champ
			if ($champ->dbfield() != '') {
				//initialisation à NULL de la donnée la première fois uniquement
				if (!isset($donnees[$champ->dbfield()])) {
					$donnees[$champ->dbfield()] = 'NULL';
				}
				if ($champ->enable()) {
					//le champ est enable, saisie valide
					//on prend en compte tous les champs sauf les champs radio et submit 'notposted'
					if (!((($champ->fieldType() == 'radio') || ($champ->fieldType() == 'submit')) && ($champ->value() == 'notposted'))) {
						$donnees[$champ->dbfield()] = $champ->value();
					}						
					//On enleve les masques de transformation USD, EUR et GBP (si il y en a) pour n'obtenir que les valeurs numériques
//					if (null !== $champ->testMatches()) {  
					if (!empty($champ->testMatches())) {
						foreach($champ->testMatches() as $match) {
							if (in_array($match, array('USD', 'EUR', 'GBP', 'MILLE_SPACED'))) {
								$donnees[$champ->dbfield()] = str_replace(array('$', '€', '£', ' '), '', $donnees[$champ->dbfield()]);
								break;
							}
						}
					}
				}
			}
		}
		//appel de la methode doUltimateThings() qui permet au developpeur de décrire des dernières actions avant de renvoyer les données
		$this->doUltimateThings($donnees);
		//renvoie des données
		return $donnees;
	}

	//renvoie l'id de travail
	public function getIdTravail() {
		if (isset($_POST['hidIdTravail'.'_'.$this->_idForm])) {
			$this->_id_travail = $_POST['hidIdTravail'.'_'.$this->_idForm];
		}
		return $this->_id_travail;
	}

	//--------------------------------------
	// Setters
	//--------------------------------------

	//positionnement de l'id de la donnée sur laquelle travaille le formulaire
	public function setIdTravail($valeur) {
		$this->_id_travail = $valeur;
	}

	//forcer l'operation
	public function setOperation($valeur) {
		$this->_operation = $valeur;
	}

	//positionne un message quelconque
	public function setMessage($valeur) {
		$this->_message = $valeur;
	}

	//positionne la propriété _ligneEncours
	public function setLigneEncours($valeur) {
		$this->_ligneEncours = $valeur;
	}

	//--------------------------------------
	// Méthodes publiques
	//--------------------------------------

	private function _resetAutofocus() {
		foreach($this->_lesChamps as $champ) {
			if ($champ->fieldType() == 'text') $champ->setAutofocus(false);
		}
	}

	//initialise le dessin du formulaire
	public function initDraw() {
		//reset flag ligne en cours
		$this->setLigneEncours(false);
		//recherche du premier champ en autofocus
		$this->_memAutofocus = null;
		foreach($this->fields() as $objet) {
			if (($objet->fieldType() == 'text') && ($objet->autofocus())) {
				$this->_memAutofocus = $objet;
				break;
			}
		}
		//et reset de tous les autofocus
		$this->_resetAutofocus();
		//recherche d'eventuelles erreurs
		$uneErreur = false;
		foreach($this->fields() as $objet) {
			if (($objet->erreur()) && ($objet->fieldType() == 'text')) {
				$objet->setAutofocus(true);
				$uneErreur = true;
				break;
			}
		}
		if ((!$uneErreur) && ($this->_memAutofocus !== null)) $this->_memAutofocus->setAutofocus(true);
		//DEBUG_('focus', $this->_memAutofocus->idField());
	}

	//finalize le dessin du formulaire
	public function finalizeDraw() {
		if ($this->ligneEncours() == true) {
			$this->setLigneEncours(false);
			return '</div>';
		}
	}

	//finalize le dessin du formulaire
	public function draw($enable) {
		$chaine = '';
		$this->initDraw();
		foreach($this->fields() as $objet) {
			//nouvelle ligne ?
			if ($objet->newLine() == true) {
				if ($this->ligneEncours() == true) {
					$chaine.= '</div>';
				}
				if ($objet->flexLine() !== '') 
					$chaine.= '<div class="form-group row '.$objet->flexLine().'">';
				else {
					$chaine.= '<div class="form-group row">';
				}
				$this->setLigneEncours(true);
			}
			$chaine.= $objet->draw($enable);
		}
		$chaine.= $this->finalizeDraw();
		return $chaine;
	}

	//test de l'intégrité de la construction des champs (les valeurs 'dpos' doivent être cohérentes)
	public function testDesignPos() {
		$drapeau = false;
		$firstChamp = array_keys($this->_lesChamps)[1];
		$lastChamp = array_keys($this->_lesChamps);
		$lastChamp = array_pop($lastChamp);
		foreach($this->_lesChamps as $indice => $champ) {
			if ($drapeau == false) {
				if ($champ->dpos() == 'first') {
					if ($indice == $lastChamp) {
						$champ->setErreur(true);
						$champ->setLibErreur('Paramètre dpos \''.$champ->dpos().'\' incohérent sur champ \''.$champ->idField().'\' (\'alone\' ou \'last\' attendu)');
						return true;
					}
					else $drapeau = true;
				}
				elseif (($champ->dpos() == 'last') || ($champ->dpos() == 'inter')) {
					$champ->setErreur(true);
					$champ->setLibErreur('Paramètre dpos \''.$champ->dpos().'\' incohérent sur champ \''.$champ->idField().'\' (\'alone\' ou \'first\' attendu)');
					return true;
				}
			}
			else {
				if ($champ->dpos() == 'last') {
					if ($indice == $firstChamp) {
						$champ->setErreur(true);
						$champ->setLibErreur('Paramètre dpos \''.$champ->dpos().'\' incohérent sur champ \''.$champ->idField().'\' (\'alone\' ou \'first\' attendu)');
						return true;
					}
					else $drapeau = false;
				}
				elseif (($champ->dpos() == 'alone') || ($champ->dpos() == 'first')) {
					$champ->setErreur(true);
					$champ->setLibErreur('Paramètre dpos \''.$champ->dpos().'\' incohérent sur champ \''.$champ->idField().'\' (\'inter\' ou \'last\' attendu)');
					return true;
				}
			}
		}
	}

	//enable / disable d'un élément selon l'état d'un trigger checkable (checkbox)
	public function enableOnChecked($element, $trigger) {
		if ($this->_lesChamps[$trigger]->checked()) {
			$this->_lesChamps[$element]->setEnable(true);
		} 
		else {
			$this->_lesChamps[$element]->setEnable(false);
		}
	}
	//disable / enable d'un élément selon l'état d'un trigger checkable (checkbox)
	public function disableOnChecked($element, $trigger) {
		if ($this->_lesChamps[$trigger]->checked()) {
			$this->_lesChamps[$element]->setEnable(false);
		} 
		else {
			$this->_lesChamps[$element]->setEnable(true);
		}
	}

	//positionne la propriété "visible" du champ $field si le trigger $trigger checkable (case à cocher / bouton radio) est coché
	public function setInvisibleOnChecked($field, $trigger) {
		if ($this->_lesChamps[$trigger]->checked()) {
			$this->_lesChamps[$field]->setInvisible(false);
		} 
		else {
			$this->_lesChamps[$field]->setInvisible(true);
		}
	}

	//tester la validité de la saisie
	public function tester() {
		$erreurSaisie = false;
		if(isset($_POST['hidSoumissionFormulaire'.'_'.$this->_idForm])) {

			//recuperation de l'id de travail (id article mémorisé sur lequel on travaille)
			if (!empty($_POST['hidIdTravail'.'_'.$this->_idForm]))
				$this->_id_travail = mySqlDataProtect($_POST['hidIdTravail'.'_'.$this->_idForm]); //on recupere l'id de travail dans le POST

			//constuction des champs du formulaire. Rappel : à sa construction, la valeur un champ s'initialise avec 
			//la donnée par défaut (fournie par initDonnees(), puis tente de la remplacer par sa valeur de POST si elle est présente
			$this->initDonnees();
			$this->construitChamps();	

			//appel de la méthode protégée doThings avant les tests
			$this->doThings();

			//test unitaire de chaque champ
			foreach($this->_lesChamps as $champ) {
				$champ->test();		//test du champ (rappel : la methode opère un relevé de $_POST, dont le test se fera sur la donnée postée)
				//attention : si une erreur existe déja, la methode testsSupplementaires n'est pas appelée
				$erreurSaisie = $erreurSaisie || ($champ->erreur() == true) || ($this->testsSupplementaires($champ));	//cumul des erreurs constatées
			}
			//tests supplementaires posterieurs
			//attention : la methode testsSupplementairesPosterieurs est appellée si aucune autre erreur précédente n'a été constatée
			$erreurSaisie = $erreurSaisie || ($this->testsSupplementairesPosterieurs());
		}
		return !$erreurSaisie;
	}

}