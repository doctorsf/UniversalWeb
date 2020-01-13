<?php
//------------------------------------------------------------------
// Classe Listing_films
//------------------------------------------------------------------
// éè : pour enregistrement UTF-8
// Auteur : Fabrice Labrousse
// Date : 30 novembre 2017 - 14 mars 2019
//------------------------------------------------------------------

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
	$genres = array(
			UniversalListColonne::CMP_ALL => 'Tous les genres' , 
			'Western' => 'Western', 
			'Science-fiction' => 'Science-fiction', 
			'Drame' => 'Drame', 
			'Comédie musicale' => 'Comédie musicale', 
			'Horreur' => 'Horreur', 
			'Comédie' => 'Comédie');
	$texte = '';
	foreach($genres as $key => $genre) {
		($defaut == $key) ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="'.$key.'"'.$selected.'>'.$genre.'</option>';
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
			'header' => true,
			'libelle' => 'Titre',	
			'size' => 35, 
			'align' => 'left',
			'title' => 'Titre du film',
			'titlePos' => '',
			'tri' => true, 
			'triSql' => 'titre', 
			'triSqlSecondaire' => '', 
			'triLibelle' => 'sur le titre',
			'triSens' => 'ASC',
			'filtre' => true,
			'filtreType' => 'text',
			'filtreActif' => 'true',
			'filtreScope' => UniversalListColonne::MENU,
			'filtreRange' => UniversalListColonne::CMP_ALL, 
			'filtreValue' => '',
			'filtreSqlField' => 'titre',
			'filtreColor' => 'success', 
			'filtreHelp' => 'Filtre sur le titre',
			'display' => true
		));

		$this->createCol('annee', array(
			'order' => 2,
			'libelle' => 'Année',
			'size' => 10, 
			'align' => 'center',
			'title' => 'Année de production', 
			'tri' => true, 
			'triSql' => 'annee', 
			'triLibelle' => 'sur l\'année de production',
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
			'align' => 'center',
			'title' => 'En couleur',
			'tri' => true, 
			'triSql' => 'visuel', 
			'triSqlSecondaire' => '', 
			'triLibelle' => 'sur le visuel couleur',
			'triSens' => 'ASC',
			'filtre' => true,
			'filtreType' => 'checkbox',
			'filtreScope' => array(UniversalListColonne::CMP_ALL, 1),			//scope de valeurs (CMP_ALL est valeur renvoyée si non cochée, 1 est la valeur renvoyée si cochée)(valueInverse, valeur)
			'filtreRange' => UniversalListColonne::CMP_EQUAL,					//seul range possible pour un filtre de type checkbox
			'filtreValue' => UniversalListColonne::CMP_ALL,  					//valeur par défaut (on affiche tout)
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
			'align' => 'left',
			'title' => 'Genre du film',
			'tri' => true, 
			'triSql' => 'genre', 
			'triLibelle' => 'sur le genre de films',
			'triSens' => 'ASC',
			'filtre' => true,
			'filtreType' => 'select',
			'filtreScope' => 'fillGenres',
			'filtreRange' => UniversalListColonne::CMP_EQUAL, 
			'filtreValue' => UniversalListColonne::CMP_ALL,
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
			'filtreRange' => UniversalListColonne::CMP_CONTENDS,
			'filtreValue' => array('titre', ''),
			'filtreColor' => 'primary', 
			'filtreHelp' => 'Filtre à sources multiples'
		));

		$this->createFiltreExterne('simple', array(
			'filtreType' => 'search',
			'filtreScope' => 'titre',
			'filtreRange' => UniversalListColonne::CMP_CONTENDS,
			'filtreValue' => '',
			'filtreColor' => 'danger', 
			'filtreHelp' => 'Filtre simple'
		));

		$this->createFiltreExterne('alpha', array(
			'filtreType' => 'none',
			'filtreScope' => 'titre',
			'filtreRange' => UniversalListColonne::CMP_ALL,
			'filtreValue' => 'all',
		));

		$this->createFiltreExterne('datation', array(
			'filtreType' => 'checkbox',
			'libelle' => 'Films à partir de 1977',
			'filtreScope' => 'annee',
			'filtreRange' => UniversalListColonne::CMP_GREATER_THAN,
			'filtreValue' => '1977',
			'actif' => false,
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
		//DEBUG_('$requete', $requete);
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