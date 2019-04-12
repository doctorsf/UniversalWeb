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

	//---------------------------------------
	// creation du modèle d'importation
	//---------------------------------------
	public function buildModele() {

		$this->createColonne('titre', array(
			'colonne' => 0, 
			'libelle' => 'Titre', 
			'sqlField' => 'titre', 
			'match' => array('REQUIRED'), 
			'commentaire' => 'Titre du film (saisie obligatoire)',
			'css' => 'text-danger',
			'active' => true
		));

		$this->createColonne('annee', array(
			'colonne' => 1, 
			'libelle' => 'Année', 
			'sqlField' => 'annee', 
			'match' => array('REQUIRED', 'CHECK_INTEGER_4'), 
			'commentaire' => 'Année de sortie du film (4 chiffres obligatoires)',
			'css' => 'text-danger',
			'active' => true
		));

		$this->createColonne('realisateur', array(
			'colonne' => 2, 
			'libelle' => 'Réalisateur', 
			'sqlField' => 'realisateur', 
			'match' => array('DEFAULT'), 
			'commentaire' => 'Nom du réalisateur (saisie obligatoire)',
			'defaut' => 'Inconnu',
			'css' => 'text-danger',
			'active' => true
		));

		$this->createColonne('visuel', array(
			'colonne' => 3, 
			'libelle' => 'Visuel', 
			'sqlField' => 'visuel', 
			'match' => array('REQUIRED', 'PARMI_VISUEL'), 
			'commentaire' => 'Type de visuel (saisie obligatoire)',
			'css' => 'text-danger',
			'active' => true
		));

		$this->createColonne('genre', array(
			'colonne' => 4, 
			'libelle' => 'Genre', 
			'sqlField' => 'genre', 
			'match' => array('REQUIRED', 'PARMI_GENRES'), 
			'commentaire' => 'Genre cinématographique (saisie obligatoire)',
			'css' => 'text-danger',
			'active' => true
		));

	}

	//---------------------------------------
	// Importation effective dans la base de données
	// Entrée : 
	//		$data : jeu de données à importer
	// Retour : 
	//		Nombre d'insertions dnas la base de données
	//---------------------------------------
	public function import($data) {
		$sqlFilms = new SqlTableFilms();
		$masqueSql = "'[0]', '[1]', '[2]', '[3]', '[4]'";
		$nbInsertions = $sqlFilms->importMany($masqueSql, $data, 3, false);
		return $nbInsertions;
	}

}