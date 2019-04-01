<?php
//------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Classe PageNavigator
// Date : 13.10.2017
//------------------------------------------------------------------
// éè : UTF-8				
// Cette classe met en oeuvre un navigateur inter-pages
//------------------------------------------------------------------
// 22.03.2013 : création
// 07.05.2013 : ajout notion de schémas
// 19.05.2014 : prise en compte chemin source
// 13.10.2017 : transformation de la fonction procédurale en classe objet
// 15.11.2017 : Correction bug si $nb_lignes_par_page == 0 on avait une division par zéro
// 22.11.2017 : Modification du constructeur. Il fait maintenant appel à des setters puis à la méthode privée _build()
// 10.01.2018 : correction _drawStandard() pour compatibilité PHP 7.2.0 fonction count()
//------------------------------------------------------------------
// Affiche et gére une navigation par pages
//   de la forme << Prec. | Page : 1 2 3 .. 9 | Suiv. >>
//------------------------------------------------------------------
// Schémas d'url que sait traiter la fonction en entrée :
// 1 : _PAGE_NAVIGATOR_SCHEMA_STANDARD_ (standard)
//		/fr/photos.php?data=xxx&page=y (ici indicePage y = null)
// 2 : _PAGE_NAVIGATOR_SCHEMA_REWRITTE_ (rewritte)
//		/fr/photos-xxx-y-blabla.htm	 (ici indicePage y = 3)
// 3 : _PAGE_NAVIGATOR_SCHEMA_REPERTOIRE_ (repertoire)
//		/fr/photos/xxx/y/blabla.htm	 (ici indicePage y = 4)
//------------------------------------------------------------------
// paramètres d'entrée au constructeur :
//		$total_data : nombre total de lignes de données
//		$nb_lignes_par_page : nombre de lignes à afficher par page
//		$nb_item_menu : nb item dans menu (< 1 .. 234567 .. 10 > ici 6)
//			Si '0' alors le navigateur sera réduit Ã  son strict minimum sous la forme << < page > >>
//		$page_encours : numéro de la page en cours
//		$schema : schéma d'url en entrée choisi (1..3)(constantes)
//		$indicePage : position ou trouver l'information de 'Page' dans
//			l'url en entrée et selon le schéma d'url en entrée.
//      $lg : langue. argument facultatif. Par défaut en français ('fr')
// Exemple d'appel :
// $pg = new pageNavigator($nb, $nbparpage, 8, $page, _PAGE_NAVIGATOR_SCHEMA_REPERTOIRE_, 3, $lg);
//- METHODES IMPORTANTES -------------------------------------------
// getItemDebut() : renvoie l'id de la 1ère donnée à afficher sur la page
// getItemFin() : renvoie l'id de la dernière donnée à afficher sur la page
//- METHODES ACCESSOIRES -------------------------------------------
//	setMinimaliste($valeur)	: affiche un navigateur minimaliste (true / false*)
//	setSmallButtons()		: affiche des petits boutons
//	setLargeButtons()		: affiche des boutons standards
//	setPageOn()				: affiche le mot 'Page : '
//	setPageOff()			: n'affiche pas le mot 'Page : '
//	setPrec($valeur)		: défini le libellé du bouton précédent
//	setSuiv($valeur)		: défini le libellé du bouton suivant
//	setSeparator($valeur)	: affiche chaque bouton de manière séparée (true* / false)
//	setColor($valeur)		: modifie la couleur primaire des boutons (couleurs bootstrap)
//------------------------------------------------------------------

class PageNavigator {

	private $_total_data = 0;								//nombre total de lignes de données à représenter 
	private $_nb_lignes_par_page = 0;						//nombre maximal de lignes à représenter dans une page
	private $_nb_item_menu = 0;								//nombre d'item dans le menu entre l'item de gauche et l'item de droite 
	private $_page_encours = 0;								//numéro de la page encours affichée
	private $_itemdebut = 0;								//indice de la première données de la page visible
	private $_itemfin = 0;									//indice de la dernière données de la page visible
	private $_schema = self::SCHEMA_STANDARD;				//type d'url utilisée par le site
	private $_indicePage = null;							//indice de la particule dans l'uri ou se trouve l'information de page
	private $_lg = 'fr';									//langue dans laquelle il faut afficher le navigateur

	private $_nb_pages;										//nombre de pages gérées par le navigateur (calcul)
	private $_prec;											//texte signignant l'item "précédent"
	private $_suiv;											//texte signignant l'item "suivant"
	private $_url;											//url de lien à poser sur les items
	private $_pageDebut;									//numéro de la page de début dans le linéaire
	private $_pageFin;										//numéro de la page de fin dans le linéaire

	private $_small = false;								//affichage de petite boutons (true / false*)
	private $_displayPage = true;							//affichage de 'Page : ' devant la liste des pages (true* / false)
	private $_minimaliste = false;							//affiche un navigateur minimaliste (idéla pour téléphones) (true / false*)
	private $_separator = true;								//affiche un séparateur '|' entre les boutons de début et de fin (true* / false)
	private $_color = 'primary';							//couleur (bootstrap) par défaut 
	private $_nodraw = false;								//ne dessine pas le navigateur (true / false*)

	private $_bootstrapColors = array(						//couleurs de base Boostrap
								'success' => '#5cb85c', 
								'primary' => '#0275d8', 
								'warning' => '#eb9316', 
								'danger' => '#c12e2a', 
								'info' => '#2aabd2',
								'link' => 'transparent',
								'secondary' => '#ccc');

	const SCHEMA_STANDARD = 1;								//ex : /fr/photos.php?data=xxx&page=y (ici indicePage y = null)
	const SCHEMA_REWRITTE = 2;								//ex : /fr/photos-xxx-y-blabla.htm	 (ici indicePage y = 3)
	const SCHEMA_REPERTOIRE = 3;							//ex : /fr/photos/xxx/y/blabla.htm	 (ici indicePage y = 4)

	//=======================================
	// Constructeur
	//=======================================

	public function setTotalData($valeur) {$this->_total_data = $valeur; 		$this->_build();}				//nombre total de lignes de données à représenter 
	public function setNbLignesParPage($valeur) {$this->_nb_lignes_par_page = $valeur; 		$this->_build();}	//nombre maximal de lignes à représenter dans une page
	public function setNbItemMenu($valeur) {$this->_nb_item_menu = $valeur; 		$this->_build();}			//nombre d'item dans le menu entre l'item de gauche et l'item de droite 
	public function setPageEncours($valeur) {$this->_page_encours = $valeur; 		$this->_build();}			//numéro de la page encours affichée
	public function setSchema($valeur) {$this->_schema = $valeur;}						//type d'url utilisée par le site
	public function setIndicePage($valeur) {$this->_indicePage = $valeur;}				//indice de la particule dans l'uri ou se trouve l'information de page
	public function setLangue($valeur) {$this->_lg = $valeur;}							//langue dans laquelle il faut afficher le navigateur

	public function __construct(
						$total_data, 
						$nb_lignes_par_page, 
						$nb_item_menu, 
						$page_encours, 
						$schema=self::SCHEMA_STANDARD, 
						$indicePage=null, //facultatifs
						$lg='fr')										//facultatif
	{
		$this->setTotalData($total_data);
		$this->setNbLignesParPage($nb_lignes_par_page);
		$this->setNbItemMenu($nb_item_menu);
		$this->setPageEncours($page_encours);
		$this->setSchema($schema);
		$this->setIndicePage($indicePage);
		$this->setLangue($lg);
		//on lance la construction
		$this->_build();
	}

	private function _build() {		
		//initialisation des propriétés de l'objet
//		$this->_total_data = $total_data;
//		$this->_nb_lignes_par_page = $nb_lignes_par_page;
//		$this->_nb_item_menu = $nb_item_menu;
//		$this->_page_encours = $page_encours;
//		$this->_schema = $schema;
//		$this->_indicePage = $indicePage;
//		$this->_lg = $lg;

		//si l'on passe $nb_item_menu à 0 cela revient à demander un navigateur minimaliste
//		if ($nb_item_menu == 0) $this->_minimaliste = true;
//		if ($this->_nb_item_menu == 0) $this->_minimaliste = true;
		$this->_minimaliste = ($this->_nb_item_menu == 0);
		
		//calcul du nombre de pages de résultat
		if ($this->_nb_lignes_par_page == 0) {
			$this->_nb_pages = 0;
		}
		else {
			if (mod($this->_total_data, $this->_nb_lignes_par_page) == 0)
				$this->_nb_pages = div($this->_total_data, $this->_nb_lignes_par_page);
			else
				$this->_nb_pages = div($this->_total_data, $this->_nb_lignes_par_page) + 1;
		}

		//modification du paramètre page pour plus de sécurite
		if ($this->_page_encours > $this->_nb_pages) $this->_page_encours = $this->_nb_pages;
		if ($this->_page_encours < 1) $this->_page_encours = 1;

		//calcul des numéros de ligne de début et de fin pour traitement par programme principal
		$this->_itemdebut = ($this->_nb_lignes_par_page * ($this->_page_encours - 1));
		$this->_itemfin = min(($this->_itemdebut + $this->_nb_lignes_par_page - 1), ($this->_total_data - 1));

		//si une seule page, on s'arrete là 
		if ($this->_nb_pages <= 1) {
			$this->_nodraw = true;
			return;
		}
		else $this->_nodraw = false;

		//prise en compte de la langue pour les Prec/Prev et Suiv/Next
		if ($this->_lg == 'fr')	{
			$this->_prec = '« Préc.';
			$this->_suiv = 'Suiv. »';
		}
		else {
			$this->_prec = '« Prev.';
			$this->_suiv = 'Next »';
		}

		//construction de l'url d'appel pour les pages suivantes
		//dans l'url en cours, on conserve tout sauf le parametre 'page'
		if (isset($_SERVER['REQUEST_URI'])) {
			$this->_url = $_SERVER['REQUEST_URI'];
		} 
		else {
			//php v4.3 par exemple ne connait pas la superglobale $_SERVER['REQUEST_URI']
			$this->_url = $_SERVER['PHP_SELF'];
			($_SERVER['QUERY_STRING'] != '') ? $this->_url.='?'.$_SERVER['QUERY_STRING'] : $this->_url.=$_SERVER['QUERY_STRING'];
		}

		//Au cas où l'URI est rewrittée alors qu'on attend une URI "à rewritter", on force
		//$indicePage à null pour obtenir le bon comportement de la fonction
		if (strpos($this->_url, '.php') !== false) {
			$this->_schema = 1;
			$this->_indicePage = null;
		}

		// Calcul de la page du début et de fin du menu linéaire 
		$this->_calculeDebutFin();
	}

	//=======================================
	// Méthodes privées
	//=======================================

	private function _calculeDebutFin() {
		//Calcul de la page du début et de fin du menu linéaire 
		if ($this->_nb_pages <= $this->_nb_item_menu) {
			$this->_pageDebut = 1;
			$this->_pageFin = $this->_nb_pages;
		}
		else {
			if ($this->_page_encours <= (div($this->_nb_item_menu,2) - 1)) 
				$this->_pageDebut = 1; 
			else 
				$this->_pageDebut = $this->_page_encours - ((div($this->_nb_item_menu,2) - 1));
			$this->_pageFin = $this->_pageDebut + ($this->_nb_item_menu - 1);
			if ($this->_pageFin > $this->_nb_pages) {
				$this->_pageFin = $this->_nb_pages;
				$this->_pageDebut = $this->_nb_pages - ($this->_nb_item_menu - 1);
			}
		}
	}

	//------------------------------------------------------------------
	// SCHEMA STANDARD
	// Traitement d'URI "standard" (ex : "/francais/photos.php?data=0000318&page=6")		
	// $indicePage doit être null. C'est l'information 'page=' qui est prise en compte		
	//------------------------------------------------------------------
	private function _drawStandard() {
		$tabUrl = parse_url($this->_url);
		$path = $tabUrl['path'];
		$query = (isset($tabUrl['query'])) ? $tabUrl['query'] : '';
		$query = explode('&', $query);

		if ($query[0] == '') $query = array();
		$this->_url = $path.'?';
		$premier = true;
		for($i = 0; $i <= (count($query) - 1); $i++) {
			$mot = mb_substr($query[$i], 0, 4);
			if ($mot != 'page')	{
				if ($premier == false) 
					$this->_url.= '&amp;'; 
				$this->_url.= $query[$i]; 
				$premier = false;
			}
			else {
		    	if ($premier == false) 
					$this->_url.= '&amp;';
				$premier = true;
			}
		}
	   	if ($premier == false) 
			$this->_url.= '&amp;';

		//shéma des url
		$shemaUrl = $this->_url.'page=[xx]';

		//Affichage
		return $this->_affichage($shemaUrl);
	}

	//------------------------------------------------------------------
	// SCHEMA REWRITTE
	// Traitement d'URI "à rewritter" (ex : "/francais/photos-0000318-6-blade-runner.htm")
	// $indicePage (!= null) contient la position de l'information "PAGE" dans l'URI (ici:3 car le '6' est à la 3ème position)
	//------------------------------------------------------------------
	private function _drawRewrite() {
		//on recupere la derniere partie de l'url (ici "photos-0000318-6-blade-runner.htm")
		$this->_url = htmlentities(strip_tags($this->_url));
		$parts = explode('/', $this->_url);
		$htmlPart = end($parts);
		$chemin = '';

		//on fragmente notre information dans le tableau $urlParts (séparateur '-')
		$urlParts = explode('-', $htmlPart);

		//prise en compte du cas où l'élément contenant l'indice de la page ($indicePage) est 
		//le dernier du tableau $urlParts. Exemple : meilleurs-films-1.htm 
		$extention = '';
		if($this->_indicePage == count($urlParts)) {
			$pathinfo = pathinfo($htmlPart);
			if($pathinfo['extension'] != '') $extention = '.'.$pathinfo['extension'];
		}

		//shéma des url
		$urlParts[$this->_indicePage - 1] = '[xx]';
		$shemaUrl = $chemin.implode('-', $urlParts).$extention;

		//Affichage
		return $this->_affichage($shemaUrl);
	}

	//------------------------------------------------------------------
	// SCHEMA REPERTOIRE
	// Traitement d'URI "repertoire" (ex : "/francais/photos/0000318/6/blade-runner")		
	// $indicePage (!= null) contient la position de l'information "PAGE" dans l'URI (ici:4)
	//------------------------------------------------------------------
	private function _drawRepertoire() {
		//on fragmente notre information dans le tableau $urlParts (séparateur '/')
		$urlParts = explode('/', $this->_url);

		//shéma des url
		$urlParts[$this->_indicePage] = '[xx]';
		$shemaUrl = implode('/', $urlParts);

		//Affichage
		return $this->_affichage($shemaUrl);
	}

	//------------------------------------------------------------------
	// Affichage
	//------------------------------------------------------------------
	private function _affichage($shemaUrl) {
		$retour = '';
		($this->_small) ? $small = ' btn-group-sm' : $small = '';
		if ($this->_minimaliste) {
			($this->_small) ? $small = ' btn-group-sm' : $small = '';
			$retour.= '<div class="btn-group'.$small.'" role="group">';
			$retour.= $this->_affichageFlecheGaucheMinimaliste($shemaUrl);
			$retour.= $this->_affichagePaginationMinimaliste($shemaUrl);
			$retour.= $this->_affichageFlecheDroiteMinimaliste($shemaUrl);
			$retour.= '</div>';
		}
		else {
			if ($this->_separator) {
				$retour.= '<div class="'.$small.'" role="group">';
			}
			else {
				$retour.= '<div class="btn-group'.$small.'" role="group">';
			}
			$retour.= $this->_affichageFlecheGauche($shemaUrl);
			$retour.= $this->_affichagePagination($shemaUrl);
			$retour.= $this->_affichageFlecheDroite($shemaUrl);
			$retour.= '</div>';
		}
		return $retour;
	}

	//----------------------------
	// Affichage fleche de gauche 
	//----------------------------
	private function _affichageFlecheGauche($shemaUrl) {
		$retour = '';
		if ($this->_page_encours == 1)	{
			$retour.= '<button type="button" class="btn btn-'.$this->_color.'" disabled>';
			$retour.= $this->_prec;
			$retour.= '</button>';
			if ($this->_separator) $retour.= ' | ';
			if ($this->_displayPage) $retour.= 'Page : ';
		}
		else {
			$url = str_replace('[xx]', ($this->_page_encours - 1), $shemaUrl);
			$retour.= '<a href="'.$url.'" class="btn btn-'.$this->_color.'" role="button">';
			$retour.= $this->_prec;
			$retour.= '</a>';
			if ($this->_separator) $retour.= ' | ';
			if ($this->_displayPage) $retour.= 'Page : ';
		}
		return $retour;
	}

	//----------------------------
	// Affichage fleche de gauche 
	// en mode minimaliste
	//----------------------------
	private function _affichageFlecheGaucheMinimaliste($shemaUrl) {
		$retour = '';
		if ($this->_page_encours == 1)	{
			$retour.= '<button type="button" class="btn btn-secondary" disabled>';
			$retour.= '<span class="fas fa-fast-backward"></span>';
			$retour.= '</button>';
			$retour.= '<button type="button" class="btn btn-secondary" disabled>';
			$retour.= '<span class="fas fa-step-backward"></span>';
			$retour.= '</button>';
		}
		else {
			$url = str_replace('[xx]', 1, $shemaUrl);
			$retour.= '<a href="'.$url.'" class="btn btn-secondary" role="button">';
			$retour.= '<span class="fas fa-fast-backward"></span>';
			$retour.= '</a>';
			$url = str_replace('[xx]', ($this->_page_encours - 1), $shemaUrl);
			$retour.= '<a href="'.$url.'" class="btn btn-secondary" role="button">';
			$retour.= '<span class="fas fa-step-backward"></span>';
			$retour.= '</a>';
		}
		return $retour;
	}

	//---------------------------------------------
	// Affichage de la pagination du menu linéaire 
	//---------------------------------------------
	private function _affichagePagination($shemaUrl) {
		$retour = '';
		//premier item
		if ($this->_pageDebut != 1) {
			$url = str_replace('[xx]', '1', $shemaUrl);
			$retour.= '<a href="'.$url.'" class="btn btn-secondary" role="button">1&hellip;</a>';
			if ($this->_separator) $retour.= '&nbsp;';
		}
		//items intermédiaires
		for ($j = $this->_pageDebut; $j <= $this->_pageFin; $j++) {
			if ($j == $this->_page_encours) {
				$retour.= '<button type="button" class="btn btn-secondary active">'.$this->_page_encours.'</button>';
				if ($this->_separator) $retour.= '&nbsp;';
			}
			else {
				$url = str_replace('[xx]', $j, $shemaUrl);
				$retour.= '<a href="'.$url.'" class="btn btn-secondary" role="button">'.$j.'</a>';
				if ($this->_separator) $retour.= '&nbsp;';
			}
		}
		//dernier item
		if ($this->_pageFin != $this->_nb_pages) {
			$url = str_replace('[xx]', $this->_nb_pages, $shemaUrl);
			$retour.= '<a href="'.$url.'" class="btn btn-secondary" role="button">&hellip;'.$this->_nb_pages.'</a>';
			if ($this->_separator) $retour.= '&nbsp;';
		}
		return $retour;
	}

	//---------------------------------------------
	// Affichage de la pagination du menu linéaire 
	// en mode minimaliste
	// Ici on affiche aussi le nombre de pages pour que l'utilisateur ne soit pas déstabilisé
	//---------------------------------------------
	private function _affichagePaginationMinimaliste($shemaUrl) {
		$retour = '';
		$retour.= '<button type="button" class="btn btn-secondary active">';
		$retour.= '<strong>'.$this->_page_encours.'</strong>';
		$retour.= '/'.$this->_nb_pages;
		$retour.= '</button>';
		return $retour;
	}

	//----------------------------
	// Affichage flèche de droite 
	//----------------------------
	private function _affichageFlecheDroite($shemaUrl) {
		$retour = '';
		if ($this->_page_encours == $this->_nb_pages) {
			if ($this->_separator) $retour.= '| ';
			$retour.= '<button type="button" class="btn btn-'.$this->_color.'" disabled>';
			$retour.= $this->_suiv;
			$retour.= '</button>';
		}
		else {
			$url = str_replace('[xx]', ($this->_page_encours + 1), $shemaUrl);
			if ($this->_separator) $retour.= '| ';
			$retour.= '<a href="'.$url.'" class="btn btn-'.$this->_color.'" role="button">';
			$retour.= $this->_suiv;
			$retour.= '</a>';
		}
		return $retour;
	}

	//----------------------------
	// Affichage flèche de droite 
	// en mode minimaliste
	//----------------------------
	private function _affichageFlecheDroiteMinimaliste($shemaUrl) {
		$retour = '';
		if ($this->_page_encours == $this->_nb_pages) {
			$retour.= '<button type="button" class="btn btn-secondary" disabled>';
			$retour.= '<span class="fas fa-step-forward"></span>';
			$retour.= '</button>';
			$retour.= '<button type="button" class="btn btn-secondary" disabled>';
			$retour.= '<span class="fas fa-fast-forward"></span>';
			$retour.= '</button>';
		}
		else {
			$url = str_replace('[xx]', ($this->_page_encours + 1), $shemaUrl);
			$retour.= '<a href="'.$url.'" class="btn btn-secondary" role="button">';
			$retour.= '<span class="fas fa-step-forward"></span>';
			$retour.= '</a>';
			$url = str_replace('[xx]', ($this->_nb_pages), $shemaUrl);
			$retour.= '<a href="'.$url.'" class="btn btn-secondary" role="button">';
			$retour.= '<span class="fas fa-fast-forward"></span>';
			$retour.= '</a>';
		}
		return $retour;
	}

	//=======================================
	// GETTERS
	//=======================================

	public function getItemDebut()		{return $this->_itemdebut;}
	public function getItemFin()		{return $this->_itemfin;}
	public function getSmall()			{return $this->_small;}
	public function getDisplayPage()	{return $this->_displayPage;}

	//=======================================
	// SETTERS
	//=======================================

	//=======================================
	// Methodes publiques
	//=======================================

	public function setMinimaliste($valeur)	{$this->_minimaliste = $valeur;}
	public function setSmallButtons()		{$this->_small = true;}
	public function setLargeButtons()		{$this->_small = false;}
	public function setPageOn()				{$this->_displayPage = true;}
	public function setPageOff()			{$this->_displayPage = false;}
	public function setPrec($valeur)		{$this->_prec = $valeur;}
	public function setSuiv($valeur)		{$this->_suiv = $valeur;}
	public function setSeparator($valeur)	{$this->_separator = $valeur;}
	public function setColor($valeur)		{$this->_color = $valeur;}

	//------------------------------------------------------------------
	//dessin du navigateur de pages
	//------------------------------------------------------------------
	public function draw() {
		//si on ne doit pas dessiner
		if ($this->_nodraw) return;
		//styles
		$retour = '<div class="page-navigator">';
		$retour.= '<style>';
		$retour.= '.page-navigator .btn {';
			$retour.= 'margin-bottom: 2px;';
		$retour.= '}';
		$retour.= '.page-navigator a.btn.btn-secondary:hover {';
			$retour.= 'color: white;';
			$retour.= 'background-color: '.$this->_bootstrapColors[$this->_color].';';
		$retour.= '}';
		$retour.= '</style>';
		//affichage selon schéma
		if ($this->_schema == self::SCHEMA_STANDARD) {
			$retour.= $this->_drawStandard();
		}
		elseif (($this->_schema == self::SCHEMA_REWRITTE) && ($this->_indicePage != null)) {
			$retour.= $this->_drawRewrite();
		}
		elseif (($this->_schema == self::SCHEMA_REPERTOIRE) && ($this->_indicePage != null)) {
			$retour.= $this->_drawRepertoire();
		}
		$retour.= '</div>';
		return $retour;
	}
}