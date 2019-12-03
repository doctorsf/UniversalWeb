<?php
//-----------------------------------------------------------------------
// Classe Listing_films
//-----------------------------------------------------------------------
// éè : pour enregistrement UTF-8
// Auteur : Fabrice Labrousse
// Date : 30 novembre 2017
//-----------------------------------------------------------------------

//----------------------------------------
// fonction callback dont la responsabilité est de remplir
// la liste déroulante du filtre 'select' pour la colonne 'genres'
// Entrée :
//		defaut : l'indice de la liste déroulante sélectionné
// Retour :
//		le code HTML
//----------------------------------------
function fillGenres($defaut)
{
	$genres = array('TOUS', 'Western', 'Science-fiction', 'Drame', 'Comédie musicale', 'Horreur', 'Comédie');
	$texte = '';
	foreach($genres as $genre) {
		($defaut == $genre) ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="'.$genre.'"'.$selected.'>'.$genre.'</option>';
	}
	return $texte;
}

class Exemple_listing_films extends UniversalList {

	//=======================================
	// Méthode protégées, c'est à dire uniquement 
	// utilisable par les classes dérivées
	//=======================================

	//-------------------------------------
	// Construction des colonnes du listing
	//-------------------------------------
	protected function construitColonnes() {
		$this->createCol('titre', array(
			'order' => 1,
			'libelle' => 'Titre',	
			'size' => 35, 
			'tri' => true, 
			'triSql' => 'titre', 
			'triSqlSecondaire' => '', 
			'triLibelle' => 'sur le titre',
			'sens' => 'ASC',
			'align' => 'left',
			'title' => 'Titre du film',
			'filtre' => true,
			'filtreType' => 'text',
			'filtreActif' => 'true',
			'filtreScope' => UniversalListColonne::MENU,
			'filtreRange' => UniversalListColonne::TOUT, 
			'filtreValue' => '',
			'filtreSqlField' => 'titre',
			'filtreColor' => 'success', 
			'filtreHelp' => 'Filtre sur le titre'
		));

		$this->createCol('annee', array(
			'order' => 2,
			'libelle' => 'Année',	
			'size' => 10, 
			'tri' => true, 
			'triSql' => 'annee', 
			'triLibelle' => 'sur l\'année de production',
			'align' => 'center',
			'title' => 'Année de production'
		));

		$this->createCol('real', array(
			'order' => 3,
			'libelle' => 'Réalisateur',	
			'size' => 25, 
			'title' => 'Réalisateur du film'
		));

		$this->createCol('visuel', array(
			'order' => 4,
			'libelle' => '<span class="fas fa-tv"></span>',	
			'size' => 5, 
			'tri' => true, 
			'triSql' => 'visuel', 
			'triSqlSecondaire' => '', 
			'triLibelle' => 'sur le visuel couleur',
			'sens' => 'ASC',
			'align' => 'center',
			'title' => 'En couleur',
			'filtre' => true,
			'filtreType' => 'checkbox',
			'filtreScope' => array(UniversalListColonne::TOUT, 1),				//scope de valeurs (TOUT est valeur renvoyée si non cochée, 1 est la valeur renvoyée si cochée)(valueInverse, valeur)
			'filtreRange' => UniversalListColonne::EGAL,						//seul range possible pour un filtre de type checkbox
			'filtreValue' => UniversalListColonne::TOUT,  						//valeur par défaut (on affiche tout)
			'filtreSqlField' => 'visuel',
			'filtreCaption' => '<span class="fas fa-tv"></span>',
			'filtreColor' => 'danger', 
			'filtreHelp' => 'Filtre sur le visuel du film (couleur ?)',
			'display' => true
		));

		$this->createCol('genre', array(
			'order' => 5,
			'libelle' => 'Genre',
			'size' => 25, 
			'tri' => true, 
			'triSql' => 'genre', 
			'triLibelle' => 'sur le genre de films',
			'sens' => 'ASC',
			'align' => 'left',
			'title' => 'Genre du film',
			'filtre' => true,
			'filtreType' => 'select',
			'filtreScope' => 'fillGenres',
			'filtreRange' => UniversalListColonne::EGAL, 
			'filtreValue' => 'TOUS',
			'filtreSqlField' => 'genre',
			'filtreCaption' => 'Genre',
			'filtreColor' => 'primary', 
			'filtreHelp' => 'Filtre sur le genre',
			'display' => true
		));
	}

	//-------------------------------------
	// construction des filtres externes
	//-------------------------------------
	protected function construitFiltresExternes() {
		$this->createFiltreExterne('recherche', array(
			'filtreType' => 'multisearch',
			'filtreScope' => array(
							'titre' => 'Titre',
							'genre' => 'Genre'),
			'filtreRange' => UniversalListColonne::CONTIENT,
			'filtreValue' => array('titre', ''),
			'filtreColor' => 'primary', 
			'filtreHelp' => 'Filtre à sources multiples'
		));

		$this->createFiltreExterne('simple', array(
			'filtreType' => 'search',
			'filtreScope' => 'titre',
			'filtreRange' => UniversalListColonne::CONTIENT,
			'filtreValue' => '',
			'filtreColor' => 'danger', 
			'filtreHelp' => 'Filtre simple'
		));

		$this->createFiltreExterne('alpha', array(
			'filtreType' => 'none',
			'filtreScope' => 'titre',
			'filtreRange' => UniversalListColonne::TOUT,
			'filtreValue' => 'tous',
		));

		$this->createFiltreExterne('datation', array(
			'filtreType' => 'checkbox',
			'libelle' => 'Films à partir de 1977',
			'filtreScope' => 'annee',
			'filtreRange' => UniversalListColonne::SUPERIEURA,
			'filtreValue' => '1977',
			'actif' => true,
		));

	}

	//-------------------------------------
	// Récupération des données du listing
	//-------------------------------------
	protected function getListeNombre() {
		$requete = "SELECT count(*) nombre ";
		$requete.= "FROM films ";
		$requete.= "WHERE 1 ";
		$requete.= $this->buildFiltres();
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre != 0) {
				return $res[0]['nombre'];
			}
			else return $nombre;
		}
		return false;
	}

	protected function getListe() {
		$laListe = array();
		$requete = "SELECT titre, annee, realisateur, visuel, genre ";
		$requete.= "FROM films ";
		$requete.= "WHERE 1 ";
		$requete.= $this->buildFiltres();
		$requete.= "ORDER BY ".$this->buildTris()." ";
		if ($this->getNbLinesParPage() != 0)
			$requete.= "LIMIT ".$this->getSqlLimitStart().", ".$this->getNbLinesParPage();
		DEBUG_('$requete', $requete);
		$laListe = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($laListe !== false) {
			return $laListe;
		}
		return false;
	}

	//=======================================
	// Méthode publiques
	//=======================================

	public function Col_titre($ligne) {
		echo $ligne['titre'] ;
	}

	public function Col_annee($ligne) {
		echo $ligne['annee'] ;
	}

	public function Col_real($ligne) {
		echo $ligne['realisateur'] ;
	}

	public function Col_visuel($ligne) {
		echo $ligne['visuel'] ;
	}

	public function Col_genre($ligne) {
		echo $ligne['genre'] ;
	}


}