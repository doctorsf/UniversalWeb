<?php
/*----------------------------------------------------------------------
Auteur : Fabrice Labrousse
Fonction bases de données diverses
Date : 30.07.2014
------------------------------------------------------------------------
éè : UTF-8
------------------------------------------------------------------------
16.04.2018
	- Ajout de la fonction sqlDivers_createTableExemplesFilms() de création de la table d'exemples
	"films" pour les exemples d'utilisation UniversalList. Lancé par active_application.php
27.11.2018
	- Correction syntaxique après premier essai sqlDivers_createTableExemplesFilms
18.03.2020
	- Ajout fonction sqlDivers_updateTableAutoIncrement()
------------------------------------------------------------------------*/


//----------------------------------------------------------------------
// Modification de a valeur de l'auto-incrément d'une table
// A utiliser avec trés grande précaution !!
//----------------------------------------------------------------------
function sqlDivers_updateTableAutoIncrement($table, $value)
{
	$requete = "ALTER TABLE ".$table." AUTO_INCREMENT = ".$value;
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//- CREATION table profils basique --------------------------------------
function sqlDivers_createTableExemplesFilms()
{
	$requete = "CREATE TABLE IF NOT EXISTS `films` (";
	$requete.= "`titre` varchar(255) NOT NULL, ";
	$requete.= "`annee` int(4) UNSIGNED NOT NULL, ";
	$requete.= "`realisateur` varchar(255) NOT NULL, ";
	$requete.= "`visuel` tinyint(1) UNSIGNED NOT NULL DEFAULT '1', ";
	$requete.= "`genre` varchar(64) NOT NULL, ";
	$requete.= "UNIQUE KEY `titre` (`titre`), ";
	$requete.= "KEY `annee` (`annee`), ";
	$requete.= "KEY `genre` (`genre`)";
	$requete.= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		//Contenu minimum de la table `profils`
		$requete = "INSERT INTO `films` (`titre`, `annee`, `realisateur`, `visuel`, `genre`) VALUES ";
		$requete.= "('2001: l\'odyssée de l\'espace', 1968, 'Stanley Kubrick', 1, 'Science-fiction'), ";
		$requete.= "('Amityville, la maison du diable', 1979, 'Stuart Rosenberg', 1, 'Horreur'), ";
		$requete.= "('Chantons sous la pluie', 1952, 'Stanley Donen', 1, 'Comédie musicale'), ";
		$requete.= "('Il était une fois dans l\'Ouest', 1968, 'Sergio Leone', 1, 'Western'), ";
		$requete.= "('L\'Empire contre-attaque', 1980, 'Irvin Kershner', 1, 'Science-fiction'), ";
		$requete.= "('La Guerre des étoiles', 1977, 'George Lucas', 1, 'Science-fiction'), ";
		$requete.= "('La planète des singes', 1968, 'Franklin J. Schaffner', 1, 'Science-fiction'), ";
		$requete.= "('La prisonnière du désert', 1956, 'John Ford', 1, 'Western'), ";
		$requete.= "('Le bon, la brute et le truand', 1966, 'Sergio Leone', 1, 'Western'), ";
		$requete.= "('Les parapluies de Cherbourg', 1964, 'Jacques Demy', 1, 'Comédie musicale'), ";
		$requete.= "('Quo Vadis', 1951, 'Mervyn LeRoy', 1, 'Drame'), ";
		$requete.= "('Autant en emporte le vent', 1939, 'Victor Fleming', 0, 'Drame'), ";
		$requete.= "('Le jour le plus long', 1962, 'Ken Annakin', 0, 'Guerre'), ";
		$requete.= "('La bête humaine', 1938, 'Jean Renoir', 0, 'Drame');";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
	return $res;
}

//----------------------------------------------------------------------
// construit une liste déroulante des profils utilisateurs							
// Entree : 
//		$defaut : id du profil par défaut à afficher														
// Retour : 
//		une chaine de caractère comportant le code HTML
//----------------------------------------------------------------------
function sqlDivers_buildUserProfilsList($defaut)
{
	$texte = '';
	$requete = "SELECT id_profil, libelle FROM "._PREFIXE_TABLES_."profils ORDER BY id_profil";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		foreach($res as $ligne) {
			($defaut == $ligne['id_profil']) ? $selected = ' selected' : $selected = '';
			$texte.= '<option value="'.$ligne['id_profil'].'"'.$selected.'>'.$ligne['libelle'].'</option>';
		}
	}
	return $texte;
}

//----------------------------------------------------------------------
// Fonction qui permet de déterminer le nombre d'administrateur dans la 
// base de données. 
// Entree : 
//		$uid : contient l'id de l'admin en retour si unique (1 seul)
// Retour : 
//		nombre d'administrateurs dans la base
//		false si erreur SQL ou il s'il n'y a aucun administrateur dans la base
//----------------------------------------------------------------------
function sqlDivers_testMinimumAdmin(&$id)
{
	$requete = "SELECT id_user ";
	$requete.= "FROM "._PREFIXE_TABLES_."users, "._PREFIXE_TABLES_."profils ";
	$requete.= "WHERE "._PREFIXE_TABLES_."users.id_profil = "._PREFIXE_TABLES_."profils.id_profil ";
	$requete.= "AND "._PREFIXE_TABLES_."profils.code = 'PROFIL_ADMIN' ";	
	$requete.= "AND "._PREFIXE_TABLES_."users.active = '1'";	
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		if ($nombre == 1) $id = $res[0]['id_user'];
		return $nombre;
	}
	return false;
}

//----------------------------------------------------------------------
// Construit une liste déroulante des niveaux d'accès
// Entree : $defaut : id du profil à afficher par défaut dans la liste déroulante
// Retour : liste des imprimeurs sous forme de liste déroulante
//----------------------------------------------------------------------
function sqlDivers_buildLanguesDispo($defaut)
{
	$texte = '';
	$res = array(array('code' => 'fr', 'libelle' => getLib('FRANCAIS')),
		 		 array('code' => 'en', 'libelle' => getLib('ANGLAIS')));
	foreach($res as $ligne) {
		($defaut == $ligne['code']) ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="'.$ligne['code'].'"'.$selected.'>'.$ligne['libelle'].'</option>';
	}
	return $texte;
}