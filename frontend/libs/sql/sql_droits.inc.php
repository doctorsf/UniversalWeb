<?php
//--------------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Requetes SQL de gestion des droits
// Date : 03.11.2014
//--------------------------------------------------------------------------
// 06.02.2018 :
//		Modification sqlDroits_addProfil() et sqlDroits_addFonctionnalite() : 
//		Maintenant à chaque création de profil ou de fonctionnalité, les droits 
//		sont automatiquement ajouté dans la table "droits" avec interdiction (0)
//--------------------------------------------------------------------------
// éè : UTF-8
//--------------------------------------------------------------------------

//- CREATION table profils basique --------------------------------------
function sqlDroits_createTableProfils()
{
	$requete = "CREATE TABLE IF NOT EXISTS `"._PREFIXE_TABLES_."profils` (";
	$requete.= "`id_profil` tinyint(3) unsigned NOT NULL, ";
	$requete.= "`libelle` varchar(30) NOT NULL, ";
	$requete.= "`code` varchar(30) NOT NULL, ";
	$requete.= "PRIMARY KEY (`id_profil`), ";
	$requete.= "UNIQUE KEY `code` (`code`)";
	$requete.= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		//Contenu minimum de la table `profils`
		$requete = "INSERT IGNORE INTO `"._PREFIXE_TABLES_."profils` (`id_profil`, `libelle`, `code`) VALUES ";
		$requete.= "(1, 'Administrateur', 'PROFIL_ADMIN'), ";
		$requete.= "(0, 'Visiteur', 'PROFIL_VISITEUR');";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
	return $res;
}

//- CREATION table fonctionnalites basique --------------------------------------
function sqlDroits_createTableFonctionnalites()
{
	$requete = "CREATE TABLE IF NOT EXISTS `"._PREFIXE_TABLES_."fonctionnalites` (";
	$requete.= "`id_fonctionnalite` tinyint(3) unsigned NOT NULL, ";
	$requete.= "`libelle` varchar(128) NOT NULL, ";
	$requete.= "`code` varchar(30) NOT NULL, ";
	$requete.= "PRIMARY KEY (`id_fonctionnalite`), ";
	$requete.= "UNIQUE KEY `id_fontionnalite` (`id_fonctionnalite`), ";
	$requete.= "UNIQUE KEY `code` (`code`)";
	$requete.= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		//Contenu de la table `fonctionnalites`
		$requete = "INSERT IGNORE INTO `"._PREFIXE_TABLES_."fonctionnalites` (`id_fonctionnalite`, `libelle`, `code`) VALUES ";
		$requete.= "(1, 'Administrer l''application', 'FONC_ADM_APP');";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
	return $res;
}

//- CREATION table droits basique --------------------------------------
function sqlDroits_createTableDroits()
{
	$requete = "CREATE TABLE IF NOT EXISTS `"._PREFIXE_TABLES_."droits` (";
	$requete.= "`id_fonctionnalite` tinyint(3) unsigned NOT NULL, ";
	$requete.= "`id_profil` tinyint(3) unsigned NOT NULL, ";
	$requete.= "`autorisation` tinyint(1) unsigned NOT NULL, ";
	$requete.= "UNIQUE KEY `fonctionalite` (`id_fonctionnalite`,`id_profil`)";
	$requete.= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		//Contenu de la table `droits`
		$requete = "INSERT IGNORE INTO `"._PREFIXE_TABLES_."droits` (`id_fonctionnalite`, `id_profil`, `autorisation`) VALUES ";
		$requete.= "(1, 1, 1);";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
	return $res;
}

//----------------------------------------------------------------------
// Modifie l'autorisation d'un profil d'accès pour une fonctionalité
// particulière
// Entree :
//		$fonctionnalite : id de la fonctionalité
//		$profil : id du profil concerné
//		$valeur : nouvelle valeur (0/1)
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_updateAutorisationProfil($fonctionnalite, $profil, $valeur)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."droits SET autorisation = '".$valeur."' WHERE id_fonctionnalite = '".$fonctionnalite."' AND id_profil = '".$profil."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		if ($nombre == 0) {
			//l'autorisation de cette fonctionnalité pour ce niveau n'existe pas dans la base de données -> on la cree (valeur défaut = 0)
			$requete = "INSERT INTO "._PREFIXE_TABLES_."droits (id_fonctionnalite, id_profil, autorisation) VALUES ";
			$requete.= "('".$fonctionnalite."', '".$profil."', '0')";
			$res = executeQuery($requete, $nombre, _SQL_MODE_);
		}
	}
	return $res;
}

//----------------------------------------------------------------------
// Charge les droits administratifs de l'application
// Charge les fonctionnalites, profils et droits
// Entree :
//		$laListe : tableau des droits chargés
// Retour : 
//		true / false
//----------------------------------------------------------------------
function sqlDroits_loadFonctionnalites(&$laListe)
{
	$laListe = array();
	$requete = "SELECT id_fonctionnalite, libelle, code ";
	$requete.= "FROM "._PREFIXE_TABLES_."fonctionnalites ORDER BY id_fonctionnalite";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		foreach($res as $ligne) {
			$laListe[$ligne['code']]['id_fonctionnalite'] = $ligne['id_fonctionnalite'];
			$laListe[$ligne['code']]['code'] = $ligne['code'];
			$laListe[$ligne['code']]['libelle'] = $ligne['libelle'];
		}
		return true;
	}
	$laListe = null;
	return false;
}

function sqlDroits_loadProfils(&$laListe)
{
	$laListe = array();
	$requete = "SELECT id_profil, libelle, code ";
	$requete.= "FROM "._PREFIXE_TABLES_."profils ORDER BY id_profil";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		foreach($res as $ligne) {
			$laListe[$ligne['code']]['id_profil'] = $ligne['id_profil'];
			$laListe[$ligne['code']]['code'] = $ligne['code'];
			$laListe[$ligne['code']]['libelle'] = $ligne['libelle'];

		}
		return true;
	}
	$laListe = null;
	return false;
}

function sqlDroits_loadDroits(&$laListe)
{
	$laListe = array();
	$requete = "SELECT id_fonctionnalite, id_profil, autorisation ";
	$requete.= "FROM "._PREFIXE_TABLES_."droits ORDER BY id_fonctionnalite, id_profil";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		foreach($res as $ligne) {
			$laListe[$ligne['id_fonctionnalite'].'-'.$ligne['id_profil']] = $ligne['autorisation'];
		}
		return true;
	}
	$laListe = null;
	return false;
}

//----------------------------------------------------------------------
// Ajouter un profil
// Entree :
//		$id_profil : 
//			id du nouveau profil. option. Si non fourni ou vide, alors 
//			on donne id le plus grand + 1
//		$libelle : 
//			libelle du nouveau profil. option. Si non fourni ou	vide, 
//			alors 'Profil n°x'
//		$code : 
//			code de profil à utilisable dans le code source.
//			option. Si non fourni ou vide, alors 'PROFIL_X'
// Retour : true (profil ajouté) / false (erreur)
//----------------------------------------------------------------------
function sqlDroits_addProfil($id_profil='', $libelle='', $code='')
{
	if ($id_profil == '') {		//recherche du prochain id_profil
		$requete = "SELECT MAX(id_profil) + 1 nouveau FROM "._PREFIXE_TABLES_."profils";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			$id_profil = $res[0]['nouveau'];
		}
		else return false;
	}
	if ($libelle == '') {	//proposer un libelle par défaut
		$libelle = 'Profil n°'.$id_profil;
	}
	if ($code == '') {	//proposer un code de fonctionnalite par défaut
		$code = 'PROFIL_'.$id_profil;
	}
	$requete = "INSERT INTO "._PREFIXE_TABLES_."profils (id_profil, libelle, code) VALUES (".$id_profil.", '".$libelle."', '".$code."')";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);

	if ($res !== false) {
		//remplissage (à 0) des droits du nouveau profil pour chaque fonctionnalité
		$requete = "SELECT id_fonctionnalite FROM "._PREFIXE_TABLES_."fonctionnalites";
		$fonctionnalites = executeQuery($requete, $nombre, _SQL_MODE_);
		if (!empty($fonctionnalites)) {
			$requete = "INSERT INTO "._PREFIXE_TABLES_."droits (id_fonctionnalite, id_profil, autorisation) VALUES ";
			foreach ($fonctionnalites as $indice => $fonctionnalite) {
				$requete.= "('".$fonctionnalite['id_fonctionnalite']."', '".$id_profil."', '0')";
				if (($indice < (count($fonctionnalites) - 1))) $requete.=", ";
			}
			$res = executeQuery($requete, $nombre, _SQL_MODE_);
		}
	}
	return $res;
}

//----------------------------------------------------------------------
// Supprime un profil de droits complet. Supprimer également tous les
// droits (couple profil/fonctionnalite)
// Entree :
//		$profil : id du profil à supprimer
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_deleteProfil($profil)
{
	$requete = "DELETE FROM "._PREFIXE_TABLES_."profils WHERE id_profil = '".$profil."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		$requete = "DELETE FROM "._PREFIXE_TABLES_."droits WHERE id_profil = '".$profil."'";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
	return $res;
}

//----------------------------------------------------------------------
// Renomme le libellé d'un profil
// Entree :
//		$profil : id du profil à renommer
//		$newLibelle : nouveau libelle
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_renameProfil($profil, $newLibelle)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."profils SET libelle = '".$newLibelle."' WHERE id_profil = '".$profil."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Renomme le code d'un profil
// Entree :
//		$profil : id du profil à renommer
//		$newLibelle : nouveau libelle
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_renameCodeProfil($profil, $newLibelle)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."profils SET code = '".$newLibelle."' WHERE id_profil = '".$profil."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Modifie le code profil d'un profil
// Attention, ce traitement ne concerne que la table _profils et ne
// modifie aucunement les tables faisant référence à cette valeur
// Entree :
//		$profil : id du profil à renommer
//		$newLibelle : nouvel id
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_renameIdProfil($profil, $newLibelle)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."profils SET id_profil = '".$newLibelle."' WHERE id_profil = '".$profil."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		$requete = "UPDATE "._PREFIXE_TABLES_."droits SET id_profil = '".$newLibelle."' WHERE id_profil = '".$profil."'";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
	return $res;
}

//----------------------------------------------------------------------
// Ajouter une fonctionnalite
// Entree :
//		$id : id de la nouvelle fonctionnalite. option. Si non fourni ou
//				vide, alors on donne id le plus grand + 1
//		$libelle : libelle de la nouvelle fonctionnalite. option. Si non
//				fourni ou vide, alors 'Fonctionnalité n°x'
//		$code : code de fonctionnalité utilisable dans le code source.
//				option. Si non fourni ou vide, alors 'FONCTIONNALITE_X'
// Retour : true (fonctionnalite ajoutée) / false (erreur)
//----------------------------------------------------------------------
function sqlDroits_addFonctionnalite($id_fonctionnalite='', $libelle='', $code='')
{
	if ($id_fonctionnalite == '') {		//recherche du prochain id_fonctionnalité
		$requete = "SELECT MAX(id_fonctionnalite) + 1 nouveau FROM "._PREFIXE_TABLES_."fonctionnalites";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			$id_fonctionnalite = $res[0]['nouveau'];
		}
		else return false;
	}
	if ($libelle == '') {	//proposer un libelle par défaut
		$libelle = 'Fonctionnalite n°'.$id_fonctionnalite;
	}
	if ($code == '') {	//proposer un code de fonctionnalite par défaut
		$code = 'FONCTIONNALITE_'.$id_fonctionnalite;
	}
	//creation
	$requete = "INSERT IGNORE INTO "._PREFIXE_TABLES_."fonctionnalites (id_fonctionnalite, libelle, code) VALUES ";
	$requete.= "('".$id_fonctionnalite."', '".$libelle."', '".$code."')";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);

	if ($res !== false) {
		//remplissage (à 0) des droits de la nouvelle fonctionnalité pour chaque profil
		$requete = "SELECT id_profil FROM "._PREFIXE_TABLES_."profils";
		$profils = executeQuery($requete, $nombre, _SQL_MODE_);
		if (!empty($profils)) {
			$requete = "INSERT INTO "._PREFIXE_TABLES_."droits (id_fonctionnalite, id_profil, autorisation) VALUES ";
			foreach ($profils as $indice => $profil) {
				$requete.= "('".$id_fonctionnalite."', '".$profil['id_profil']."', '0')";
				if (($indice < (count($profils) - 1))) $requete.=", ";
			}
			$res = executeQuery($requete, $nombre, _SQL_MODE_);
		}
	}
	return $res;
}

//----------------------------------------------------------------------
// Supprime une fonctionnalite. Supprime également tous les droits
// (couple profil/fonctionnalite) correspondants
// Entree :
//		$id_fonctionnalite : id de la fonctionnalité supprimer
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_deleteFonctionnalite($id_fonctionnalite)
{
	$requete = "DELETE FROM "._PREFIXE_TABLES_."fonctionnalites WHERE id_fonctionnalite = '".$id_fonctionnalite."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		$requete = "DELETE FROM "._PREFIXE_TABLES_."droits WHERE id_fonctionnalite = '".$id_fonctionnalite."'";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
	return $res;
}

//----------------------------------------------------------------------
// Renomme le libellé d'une fonctionnalite
// Entree :
//		$id : id de la fonctionnalite à renommer
//		$newLibelle : nouveau libelle
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_renameFonctionnalite($id, $newLibelle)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."fonctionnalites SET libelle = '".$newLibelle."' WHERE id_fonctionnalite = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Renomme le code d'une fonctionnalite
// Entree :
//		$id : id de la fonctionnalite à renommer
//		$newLibelle : nouveau libelle
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_renameCodeFonctionnalite($id, $newLibelle)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."fonctionnalites SET code = '".$newLibelle."' WHERE id_fonctionnalite = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Modifie l'id d'une fonctionnalite
// Entree :
//		$id : id de la fonctionnalite à renommer
//		$newLibelle : nouvel id
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_renameIdFonctionnalite($id, $newLibelle)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."fonctionnalites SET id_fonctionnalite = '".$newLibelle."' WHERE id_fonctionnalite = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		//modifier le nouveau code fonctionnalité dans la table droits
		$requete = "UPDATE "._PREFIXE_TABLES_."droits SET id_fonctionnalite = '".$newLibelle."' WHERE id_fonctionnalite = '".$id."'";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
	return $res;
}

//----------------------------------------------------------------------
// Construit une liste déroulante des niveaux d'accès
// Entree : 
//		$defaut : id du profil à afficher par défaut dans la liste déroulante
// Retour : 
//		liste des imprimeurs sous forme de liste déroulante
//----------------------------------------------------------------------
function sqlDroits_buildProfilesList($defaut)
{
	$texte = '';
	$requete = "SELECT libelle, id_profil FROM "._PREFIXE_TABLES_."profils ORDER BY libelle";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		foreach($res as $ligne) {
			($defaut == $ligne['id_profil']) ? $selected = ' selected' : $selected = '';
			$texte.= '<option value="'.$ligne['id_profil'].'"'.$selected.'>'.$ligne['libelle'].'</option>';
		}
	}
	return $texte;
}