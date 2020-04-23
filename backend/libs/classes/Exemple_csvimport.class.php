<?php
//----------------------------------------------------------------------
// Classe d'import CSV pour la videothèque
//----------------------------------------------------------------------
// 12.11.2019
//		modification de l'écriture des champs publiques _table (en table), _index (en index) et _champs (en champ) sans le _ (réservée aux propriétées privées)
//----------------------------------------------------------------------

// classe permettant d'utiliser la classe SqlSimple
class SqlTableFilms extends SqlSimple {
	public $table = 'films';
	public $index = 'titre';
	public $champs = 'titre, annee, realisateur, visuel, genre';
}

class Exemple_csvimport extends UniversalCsvImport {

	// tests personnalisés pour l'importation
	public $PARMI_VISUEL = array('couleur', 'noir & blanc');
	public $PARMI_GENRES = array('drame', 'science-fiction', 'comédie', 'western', 'fantastique');

	//---------------------------------------
	// Tests personnalisé sur la colonne imdb
	//---------------------------------------	
	public function testVisuel($valeur) {
		return in_array($valeur, $this->PARMI_VISUEL);
	}

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
/*
		$this->createColonne('annee', array(
			'colonne' => 1, 
			'libelle' => 'Année', 
			'sqlField' => 'annee', 
			'match' => array('REQUIRED', 'CHECK_INTEGER_4'), 
			'commentaire' => 'Année de sortie du film (4 chiffres obligatoires)',
			'css' => 'text-danger',
			'active' => true
		));
*/
		//autre possibilité : test avec un REGEX personnalisé
		$this->createColonne('annee', array(
			'colonne' => 1, 
			'libelle' => 'Année', 
			'sqlField' => 'annee', 
			'match' => array('REQUIRED', 'REGEX'), 
			'matchValue' => '#^[0-9]{4}$#', 
			'commentaire' => 'Année de sortie du film (4 chiffres obligatoires)',
			'css' => 'text-danger',
			'active' => true
		));

		$this->createColonne('realisateur', array(
			'colonne' => 2, 
			'libelle' => 'Réalisateur', 
			'sqlField' => 'realisateur', 
			'match' => array('DEFAULT'), 
			'defaut' => 'Inconnu',
			'commentaire' => 'Nom du réalisateur (saisie obligatoire)',
			'css' => 'text-danger',
			'active' => true
		));

/*
		$this->createColonne('visuel', array(
			'colonne' => 3, 
			'libelle' => 'Visuel', 
			'sqlField' => 'visuel', 
			'match' => array('REQUIRED', 'PARMI_VISUEL'), 
			'commentaire' => 'Type de visuel (saisie obligatoire)',
			'css' => 'text-danger',
			'active' => true
		));
*/
		//autre possibilité : test avec une méthode personnalisée
		$this->createColonne('visuel', array(
			'colonne' => 3, 
			'libelle' => 'Visuel', 
			'sqlField' => 'visuel', 
			'match' => array('REQUIRED', 'CUSTOM'), 
			'matchValue' => 'testVisuel', 
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
		if (empty($data)) return 0;
		$sqlFilms = new SqlTableFilms();
		$masqueSql = "'[1]', '[2]', '[3]', '[4]', '[5]'";
		$nbInsertions = $sqlFilms->importMany($masqueSql, $data, 3, false);
		return $nbInsertions;
	}

}