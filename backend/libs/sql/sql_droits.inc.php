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
// 24.05.2019
//		Ajout de la notion de groupes de fonctionnalités avec gestion complète
// 27.05.2019
//		Ajout de la fonction sqlDroits_swapAutorisationProfil() qui swappe un droit
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

//- CREATION table groupes de fonctionnalites basique --------------------------
function sqlDroits_createTableGroupesFonctionnalites()
{
	$requete = "CREATE TABLE IF NOT EXISTS `"._PREFIXE_TABLES_."groupes_fonctionnalites` (";
	$requete.= "`id_groupe_fonctionnalite` tinyint(3) UNSIGNED NOT NULL, ";
	$requete.= "`libelle` varchar(128) NOT NULL, ";
	$requete.= "`ordre` tinyint(3) UNSIGNED NOT NULL DEFAULT '1', ";
	$requete.= "PRIMARY KEY (`id_groupe_fonctionnalite`)";
	$requete.= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		//Contenu de la table `fonctionnalites`
		$requete = "INSERT IGNORE INTO `"._PREFIXE_TABLES_."groupes_fonctionnalites` (`id_groupe_fonctionnalite`, `libelle`, `ordre`) VALUES ";
		$requete.= "(1, 'Non classée', 1),";
		$requete.= "(2, 'Administration', 2)";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
	return $res;
}

//- CREATION table fonctionnalites basique --------------------------------------
function sqlDroits_createTableFonctionnalites()
{
	$requete = "CREATE TABLE IF NOT EXISTS `"._PREFIXE_TABLES_."fonctionnalites` (";
	$requete.= "`id_fonctionnalite` tinyint(3) UNSIGNED NOT NULL, ";
	$requete.= "`id_groupe_fonctionnalite` tinyint(3) UNSIGNED NOT NULL DEFAULT '1', ";
	$requete.= "`libelle` varchar(128) NOT NULL, ";
	$requete.= "`code` varchar(30) NOT NULL, ";
	$requete.= "PRIMARY KEY (`id_fonctionnalite`), ";
	$requete.= "UNIQUE KEY `code` (`code`)";
	$requete.= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		//Contenu de la table `fonctionnalites`
		$requete = "INSERT IGNORE INTO `"._PREFIXE_TABLES_."fonctionnalites` (`id_fonctionnalite`, `id_groupe_fonctionnalite`, `libelle`, `code`) VALUES ";
		$requete.= "(1, 2, 'Administrer l\'application', 'FONC_ADM_APP'), ";
		$requete.= "(2, 2, 'Gérer les utilisateurs', 'FONC_ADM_GERER_USERS');";
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
		$requete.= "(1, 1, 1), ";
		$requete.= "(2, 0, 0), ";
		$requete.= "(2, 1, 1);";
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
// Swap l'autorisation d'un profil d'accès pour une fonctionalité particulière
// Entree :
//		$fonctionnalite : id de la fonctionalité
//		$profil : id du profil concerné
// Retour :
//		true : modification effectuée
//		false : erreur SQL
//----------------------------------------------------------------------
function sqlDroits_swapAutorisationProfil($fonctionnalite, $profil)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."droits ";
	$requete.= "SET autorisation = IF(autorisation = '1', '0', '1') ";
	$requete.= "WHERE id_fonctionnalite = '".$fonctionnalite."' AND id_profil = '".$profil."';";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Charge les droits administratifs de l'application
// Charge les fonctionnalites, profils et droits
// Entree :
//		$laListe : tableau des droits chargés
//		$notionGroupes : true (on utilise la notion de groupes) / false (pas de notion de groupe)
// Retour : 
//		true / false
//----------------------------------------------------------------------
function sqlDroits_loadFonctionnalites(&$laListe, $notionGroupes)
{
	$laListe = array();
	$requete = "SELECT id_fonctionnalite, "._PREFIXE_TABLES_."fonctionnalites.id_groupe_fonctionnalite, "._PREFIXE_TABLES_."groupes_fonctionnalites.libelle groupe, ";
	$requete.= _PREFIXE_TABLES_."fonctionnalites.libelle, code ";
	$requete.= "FROM "._PREFIXE_TABLES_."fonctionnalites, "._PREFIXE_TABLES_."groupes_fonctionnalites ";
	$requete.= "WHERE "._PREFIXE_TABLES_."fonctionnalites.id_groupe_fonctionnalite = "._PREFIXE_TABLES_."groupes_fonctionnalites.id_groupe_fonctionnalite ";
	if ($notionGroupes) {
		$requete.= "ORDER BY ordre, id_fonctionnalite";
	}
	else {
		$requete.= "ORDER BY id_fonctionnalite";
	}
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		foreach($res as $ligne) {
			$laListe[$ligne['code']]['id_fonctionnalite'] = $ligne['id_fonctionnalite'];
			$laListe[$ligne['code']]['id_groupe_fonctionnalite'] = $ligne['id_groupe_fonctionnalite'];
			$laListe[$ligne['code']]['groupe'] = $ligne['groupe'];
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
//		$id_fonctionnalite : id de la nouvelle fonctionnalite. option. Si non fourni ou
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
	if ($code == '') {		//proposer un code de fonctionnalite par défaut
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

//----------------------------------------------------------------------
// Récupération des informations pour l'affichage du listing des droits
// Ici on récupère toutes les fonctionnalités de tous les groupes, y 
// compris les groupes qui ne possèdent aucune fonctionnalité
// Entree : 
//		$laListe : tableau en retour des informations
//		$notionGroupes : true (on utilise la notion de groupes) / false (pas de notion de groupe)
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_getInfosListing(&$laListe, $notionGroupes)
{
	$requete = "SELECT ";
	$requete.= "A.id_groupe_fonctionnalite, A.libelle groupe, A.ordre, B.id_fonctionnalite, B.libelle, B.code ";
	$requete.= "FROM "._PREFIXE_TABLES_."groupes_fonctionnalites A ";
	$requete.= "LEFT OUTER JOIN "._PREFIXE_TABLES_."fonctionnalites B ON B.id_groupe_fonctionnalite = A.id_groupe_fonctionnalite ";
	if ($notionGroupes) {
		$requete.= "ORDER BY A.ordre, B.id_fonctionnalite";
	}
	else {
		$requete.= "ORDER BY B.id_fonctionnalite";
	}
	$laListe = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($laListe !== false) {
		return true;
	}
	$laListe = null;
	return false;
}

//----------------------------------------------------------------------
// Ajoute un nouveau groupe de fonctionnalités. 
// Le groupe ajouté est positionné en dernier.
// Entrée : rien
// Retour : true (groupe ajoutée) / false (erreur)
//----------------------------------------------------------------------
function sqlDroits_addGroupeFonctionnalites()
{
	//proposition id_groupe_fonctionnalite et ordre
	$requete = "SELECT MAX(id_groupe_fonctionnalite) + 1 id, MAX(ordre) + 1 ordre FROM "._PREFIXE_TABLES_."groupes_fonctionnalites";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		$id_groupe_fonctionnalite = $res[0]['id'];
		$ordre = $res[0]['ordre'];
	}
	else return false;
	//proposition libellé
	$libelle = 'Groupe n°'.$id_groupe_fonctionnalite;
	//creation
	$requete = "INSERT IGNORE INTO "._PREFIXE_TABLES_."groupes_fonctionnalites (id_groupe_fonctionnalite, libelle, ordre) VALUES ";
	$requete.= "('".$id_groupe_fonctionnalite."', '".$libelle."', '".$ordre."')";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Supprime un groupe de fonctionnalités (attention ne fait aucun test sur 
// les fonctionnalités qu'il pouvait contenir).
//----------------------------------------------------------------------
function sqlDroits_deleteGroupeFonctionnalites($id_groupe)
{
	$requete = "DELETE FROM "._PREFIXE_TABLES_."groupes_fonctionnalites WHERE id_groupe_fonctionnalite = '".$id_groupe."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Positionne la fonctionnalité $id_fonctionnalite dans le groupe de 
// fonctionnalités $id_groupe
// Entree :
//		$id_fonctionnalite : id de la fonctionnalite à déplacer
//		$id_groupe : id fu groupe de fonctionnalités cible
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_setFonctionnaliteToGroupe($id_fonctionnalite, $id_groupe)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."fonctionnalites SET id_groupe_fonctionnalite = '".$id_groupe."' WHERE id_fonctionnalite = '".$id_fonctionnalite."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Renomme le libellé d'un groupe de fonctionnalités
// Entree :
//		$id : id du gorupe de fonctionnalité à renommer
//		$newLibelle : nouveau libelle
// Retour : true / false
//----------------------------------------------------------------------
function sqlDroits_renameGroupeFonctionnalites($id, $newLibelle)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."groupes_fonctionnalites SET libelle = '".$newLibelle."' WHERE id_groupe_fonctionnalite = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Récupere les informations de groupes de fonctionnalités. En retour la fonction
// renvoie les groupes des fonctionnalité avec comme clé l'id du groupe
// Entree : Rien
// Retour : tableau des groupes de fonctionnalités avec clé = id groupe / false (si erreur)
//----------------------------------------------------------------------
function sqlDroits_getGroupes()
{
	$requete = "SELECT * FROM "._PREFIXE_TABLES_."groupes_fonctionnalites";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		return array_flip_key($res, 'id_groupe_fonctionnalite');
	}
	return $res;
}

//----------------------------------------------------------------------
// Cette fonction réarrange l'odre d'affichage des groupes de fonctionnalités.
// Il positionne le groupe $grSource en dessous du groupe $grCible
// Entree : 
//		$grSource : id du groupe de fonctionnalité à déplacer
//		$grCible : id du groupe de fonctionnalité sous lequel placer le groupe source
// Sortie
//		false (erreur SQL)
//----------------------------------------------------------------------
function sqlDroits_rearrangeGroupes($grSource, $grCible)
{
	$groupes = sqlDroits_getGroupes();
	if ($groupes !== false) {
		//DEBUG_('groupes', $groupes);
		//placer $grSource après $grCible
		$mem = $groupes[$grCible]['ordre'] * 10 + 5;
		//DEBUG_('mem', $mem); 
		//renummérotation de l'ordre de tous les groupes en multipliant par 10
		foreach($groupes as $id_groupe => $groupe) {
			$groupes[$id_groupe]['ordre'] = $groupes[$id_groupe]['ordre'] * 10;
		}
		//DEBUG_('groupes', $groupes);
		//insertion de la source à la position memorisée (mem)
		$groupes[$grSource]['ordre'] = $mem;
		//DEBUG_('groupes', $groupes);
		//renummérotation des ordres de 1 en 1
		$groupes = array_sort($groupes, 'ordre');
		$compteur = 1;
		foreach($groupes as $id_groupe => $groupe) {
			$groupes[$id_groupe]['ordre'] = $compteur++;
		}
		//DEBUG_('groupes', $groupes);
		//réécriture dans la base de données
		$requetes = '';
		foreach($groupes as $id_groupe => $groupe) {
			$requetes.= "UPDATE "._PREFIXE_TABLES_."groupes_fonctionnalites SET ordre = '".$groupe['ordre']."' WHERE id_groupe_fonctionnalite = '".$id_groupe."';";
		}
		//DEBUG_('requetes', $requetes);
		$res = executeQuery($requetes, $nombre, _SQL_MODE_);
		return $res;
	}
	return false;
}