<?php
//----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Requetes SQL de gestion des utilisateurs
// La structure de la table des utilisateurs est proposée par défaut.
// Libre à vous de l'étoffer
// Date : 28.07.2014
//----------------------------------------------------------------------
// éè : UTF-8
//----------------------------------------------------------------------

//----------------------------------------------------------------------
// dit si user existe dans la base de donnees
// Entree
//		$id : id de l'utilisateur
// Retour
//		true ou false
//----------------------------------------------------------------------
function sqlUsers_userExists($id)
{
	$requete = "SELECT id_user FROM "._PREFIXE_TABLES_."users WHERE id_user = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return ($nombre == 1);
}

//----------------------------------------------------------------------
// Récupere le mot de passe crypté de l'utilisateur id
// Entree
//      $id : id de l'utilisateur
// Retour
//		le SHA1 du mot de passe de l'utilisateur ou false si erreur
//----------------------------------------------------------------------
function sqlUsers_getCryptedPassword($id)
{
	$requete = "SELECT password FROM "._PREFIXE_TABLES_."users WHERE id_user = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res[0]['password'];
}

//----------------------------------------------------------------------
// Test mot de passe utilisateur. On considère que l'utilisateur est
// connu de la base.
// Entree
//      $id : id de l'utilisateur
//      $pass : mot de passe fourni
// Retour
//		true (mot de passe ok) ou false (mot de passe mauvais)
//----------------------------------------------------------------------
function sqlUsers_testUserPassword($id, $pass)
{
	$requete = "SELECT password FROM "._PREFIXE_TABLES_."users WHERE id_user = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return ($res[0]['password'] == sha1($pass));
}

//----------------------------------------------------------------------
// dit si le compte de l'utilisateur est actif
// Entree
//      $id : id de l'utilisateur
// Retour
//		true ou false
//----------------------------------------------------------------------
function sqlUsers_userIsActive($id)
{
	$requete = "SELECT active FROM "._PREFIXE_TABLES_."users WHERE id_user = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return (($nombre == 1) && ($res[0]['active'] == 1));
}

//----------------------------------------------------------------------
// Met à jour l'heure de dernier accès d'un utilisateur
// Entree : 
//		$id : l'id de l'utilisateur
//		$ip : adresse ip de l'utilisateur
// Retour : 
//		l'heure mise à jour au format 'Y-m-d H:i:s'	
//		false sinon															
//----------------------------------------------------------------------
function sqlUsers_updateLastAccess($id, $ip)
{
	$objDate = new DateTime();
	$chaineDate = $objDate->format('Y-m-d H:i:s');
	$requete = "UPDATE "._PREFIXE_TABLES_."users SET ";
	$requete.= "dernier_acces = '".$chaineDate."', ";
	$requete.= "ip = '".$ip."' ";
	$requete.= "WHERE id_user = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res) return $chaineDate;
	else return false;
}

//----------------------------------------------------------------------
// Retrouver les infos utilisateur depuis la base de données
// Entree :
//		$id : l'id de l'utilisateur à rechercher
//		$user :	tableau des infos utilisateur chargées
// Retour :
//		true (trouve) / false (erreur / pas trouve)
//----------------------------------------------------------------------
function sqlUsers_getInfosUser($id, &$user)
{
	$requete = "SELECT id_user, nom, prenom, email, password, langue, profil, testeur, date_creation, dernier_acces, ";
	$requete.= "action_demandee, code_validation, active, autolog, ip, notes_privees ";
	$requete.= "FROM "._PREFIXE_TABLES_."users ";
	$requete.= "WHERE id_user = '".$id."';";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		if ($nombre == 1) { 
			$user = $res[0];
			return true;
		}
	}
	return false;
}

//----------------------------------------------------------------------
// Retourne le nombre d'utilisateur				
// Entree : 
//		aucune
// Retour : 															
//		false (erreur SQL) / nombre clients sinon						
//----------------------------------------------------------------------
function sqlUsers_getListingNombre()
{
	$requete = "SELECT count(id_user) nombre ";
	$requete.= "FROM "._PREFIXE_TABLES_."users";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		if ($nombre != 0) {
			return($res[0]['nombre']);
		}
		else return $nombre;
	}
	return false;
}

//----------------------------------------------------------------------
// Retourne les articles correspondant à une requete
// Entree :
//		$tri : champ de tri
//		$sens : sens de l'affichage (ASC / DESC)
//		$start : ligne de début de recherche (on ne lit pas tout)
//		$nb_lignes : nombre de lignes max à ramener
//		$lg : langue de recherche (_FR_ / _EN_)
//		$laListe : liste des articles trouvés
// Retour :
//		false (erreur SQL) / nombre articles sinon
//----------------------------------------------------------------------
function sqlUsers_getListing($tri, $sens, $start, $nb_lignes, $lg, &$laListe)
{
	//si le tri est demandé sur plus d'1 champ il faut les concaténer pour que la requete fonctionne correctement
	//ex si tri est "nom, prenom" il faut écrire "concat(nom, prenom)"
	if (strpos($tri, ',') !== false) $tri = 'concat('.$tri.')';
	$laListe = array();
	$requete = "SELECT id_user, nom, prenom, email, password, langue, profil, libelle libelle_profil, date_creation, dernier_acces, ";
	$requete.= "action_demandee, code_validation, active, autolog, ip, notes_privees ";
	$requete.= "FROM "._PREFIXE_TABLES_."users a, "._PREFIXE_TABLES_."profils b ";
	$requete.= "WHERE a.profil = b.id_profil ";
	$requete.= "ORDER BY ".$tri." ".$sens." ";
	$requete.= "LIMIT ".$start.", ".$nb_lignes;
	$laListe = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($laListe !== false) {
		return($nombre);
	}
	$laListe = null;
	return false;
}

//----------------------------------------------------------------------
// Ajoute un nouvel utilisateur à la base de données
// Entree :
//		$donnees : tableau contenant les données de l'utilisateur
// Retour : 
//		true : Ok
//		false : erreur SQL
//----------------------------------------------------------------------
function sqlUsers_addUser($donnees)
{
	$requete = "INSERT INTO "._PREFIXE_TABLES_."users ";
	$requete.= "(id_user, nom, prenom, email, password, ";
	$requete.= "langue, profil, testeur, ";
	$requete.= "date_creation, dernier_acces, ";
	$requete.= "action_demandee, code_validation, active, autolog, ip, ";
	$requete.= "notes_privees) VALUES (";
	$requete.= "'".utf8_strtolower($donnees['id_user'])."', ";
	$requete.= "'".ucwords(utf8_strtolower($donnees['nom']))."', ";
	$requete.= "'".ucwords(utf8_strtolower($donnees['prenom']))."', ";
	$requete.= "'".$donnees['email']."', SHA1('".$donnees['password']."'), ";
	$requete.= "'".$donnees['langue']."', '".$donnees['profil']."', '".$donnees['testeur']."', ";
	$requete.= "NOW(), '00000-00-00 00:00:00', ";
	$requete.= "'0', '', '".$donnees['active']."', '".$donnees['autolog']."', '000.000.000.000', ";
	$requete.= "'".$donnees['notes_privees']."'";
	$requete.= ")";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return($res);
}

//----------------------------------------------------------------------
// Modifie les données d'un utilisateur (toutes les données, y compris les
// plus délicates peuvent être modifiées)
// Entree :
//		$id : l'id de l'utilisateur à modifier
//		$donnees : tableau contenant les nouvelles données de l'utilisateur
// Retour : 
//		true : modification Ok
//		false : erreur SQL
//----------------------------------------------------------------------
function sqlUsers_updateUserGlobal($id, $donnees)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."users SET ";
	$requete.= "id_user = '".utf8_strtolower($donnees['id_user'])."', ";
	$requete.= "nom = '".ucwords(utf8_strtolower($donnees['nom']))."', ";
	$requete.= "prenom = '".ucwords(utf8_strtolower($donnees['prenom']))."', ";
	$requete.= "email = '".$donnees['email']."', ";
	//test si le mot de passe a été modifié. dans le cas contraire, il ne faut pas prendre en compte le champ
	//car on risque de surcrypter SHA1 le cryptage SHA1 du mot de passe
	if (isset($donnees['password'])) {
		if ($donnees['password'] != sqlUsers_getCryptedPassword($id)) {
			//le mot de passe a été changé
			$requete.= "password = SHA1('".$donnees['password']."'), ";
		}
	}
	if (isset($donnees['langue'])) $requete.= "langue = '".$donnees['langue']."', ";
	$requete.= "profil = '".$donnees['profil']."', ";
	if (isset($donnees['testeur'])) $requete.= "testeur = '".$donnees['testeur']."', ";
	$requete.= "date_creation = '".changeDateTimeFormat($donnees['date_creation'], _FORMAT_DATE_TIME_, _FORMAT_DATE_TIME_SQL_)."', ";
	if (isset($donnees['action_demandee'])) $requete.= "action_demandee = '".$donnees['action_demandee']."', ";
	if (isset($donnees['code_validation'])) $requete.= "code_validation = '".$donnees['code_validation']."', ";
	$requete.= "active = '".$donnees['active']."', ";
	if (isset($donnees['autolog'])) $requete.= "autolog = '".$donnees['autolog']."', ";
	$requete.= "notes_privees = '".$donnees['notes_privees']."' ";
	$requete.= "WHERE id_user = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Modifie les données d'un utilisateur (seules les données non sensibles
// peuvent être modifiées)
// Entree :
//		$id : l'id de l'utilisateur à modifier
//		$donnees : tableau contenant les nouvelles données de l'utilisateur
// Retour : 
//		true : modification Ok
//		false : erreur SQL
//----------------------------------------------------------------------
function sqlUsers_updateUser($id, $donnees)
{
	$requete = "UPDATE "._PREFIXE_TABLES_."users SET ";
	$requete.= "nom = '".ucwords(utf8_strtolower($donnees['nom']))."', ";
	$requete.= "prenom = '".ucwords(utf8_strtolower($donnees['prenom']))."', ";
	$requete.= "email = '".$donnees['email']."', ";
	//test si le mot de passe a été modifié. dans le cas contraire, il ne faut pas prendre en compte le champ
	//car on risque de surcrypter SHA1 le cryptage SHA1 du mot de passe
	if (isset($donnees['password'])) {
		if ($donnees['password'] != sqlUsers_getCryptedPassword($id)) {
			//le mot de passe a été changé
			$requete.= "password = SHA1('".$donnees['password']."'), ";
		}
	}
	if (isset($donnees['langue'])) $requete.= "langue = '".$donnees['langue']."', ";
	if (isset($donnees['autolog'])) $requete.= "autolog = '".$donnees['autolog']."', ";
	$requete.= "id_user = '".utf8_strtolower($donnees['id_user'])."' ";
	$requete.= "WHERE id_user = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Supprimer un utilisateur
// Entree :
//		$id : l'id de l'utilisateur à supprimer
// Retour : 
//		true : suppression Ok
//		false : erreur SQL
//----------------------------------------------------------------------
function sqlUsers_deleteUser($id)
{
	$requete = "DELETE FROM "._PREFIXE_TABLES_."users ";
	$requete.= "WHERE id_user = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}