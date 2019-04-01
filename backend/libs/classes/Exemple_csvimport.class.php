<?php
//----------------------------------------------------------------------
// Classe d'import CSV pour la videothèque
//----------------------------------------------------------------------

// classe permettant d'utiliser la classe SqlSimple
class SqlTableFilms extends SqlSimple {
	public $_table = 'films';
	public $_index = 'titre';
	public $_champs = 'titre, annee, realisateur, visuel, genre';
}

class Exemple_csvimport extends UniversalCsvImport {

	// tests personnalisés pour l'importation
	public $PARMI_VISUEL = array('couleur', 'noir & blanc');
	public $PARMI_GENRES = array('drame', 'science-fiction', 'comédie', 'western', 'fantastique');

	// creation du modèle d'importation
	public function buildModele() {
		$this->createColonne('titre', 0, 'Titre', 'titre', array('REQUIRED'));
		$this->createColonne('annee', 1, 'Année', 'annee', array('REQUIRED', 'CHECK_INTEGER_4'));
		$this->createColonne('realisateur', 2, 'Réalisateur', 'realisateur', array('REQUIRED'));
		$this->createColonne('visuel', 3, 'Visuel', 'visuel', array('REQUIRED', 'PARMI_VISUEL'));
		$this->createColonne('genre', 4, 'Genre cinématographique', 'genre', array('REQUIRED', 'PARMI_GENRES'));
	}

	// importation effective dans la base de données
	public function import($data) {
		$sqlFilms = new SqlTableFilms();
		$masqueSql = "'[0]', '[1]', '[2]', '[3]', '[4]'";
		$nbInsertions = $sqlFilms->importMany($masqueSql, $data, 3, false);
		return $nbInsertions;
	}

}