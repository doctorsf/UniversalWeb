<?php
//-----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Fonction bases de données diverses
// Date : 30.07.2014
//-----------------------------------------------------------------------
// éè : UTF-8
//-----------------------------------------------------------------------

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
// Entree : 
//		$defaut : id du profil à afficher par défaut dans la liste déroulante
// Retour : 
//		liste des imprimeurs sous forme de liste déroulante
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