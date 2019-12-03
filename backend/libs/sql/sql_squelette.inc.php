<?php
//------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Fonctions SQL table Squelette
// Date : 17.01.2017
//------------------------------------------------------------------
// éè : UTF-8
//------------------------------------------------------------------

defined('_SQL_SQUELETTE_TABLE_')	|| define('_SQL_SQUELETTE_TABLE_',	_PREFIXE_TABLES_.'applications');
defined('_SQL_SQUELETTE_INDEX_')	|| define('_SQL_SQUELETTE_INDEX_',	'id_appli');
defined('_SQL_SQUELETTE_CHAMPS_')	|| define('_SQL_SQUELETTE_CHAMPS_',	'id_appli, libelle');

//----------------------------------------------------------------------
// Retourne nombre de tuples d'une table
// Entree : 
//		aucune
// Retour : 															
//		false (erreur SQL) / nombre clients sinon						
//----------------------------------------------------------------------
function sqlSquelette_getListeNombre()
{
	$requete = "SELECT count(id_fonction) nombre ";
	$requete.= "FROM "._PREFIXE_TABLES_."fonctions_serveurs";
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
// Retourne les tuples correspondants à une requete
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
function sqlSquelette_getListe($tri, $sens, $start, $nb_lignes, $lg, &$laListe)
{
	//si le tri est demandé sur plus d'1 champ il faut les concaténer pour que la requete fonctionne correctement)
	//ex si tri est "nom, prenom" il faut écrire "concat(nom, prenom)"
	if (strpos($tri, ',') !== false) $tri = 'concat('.$tri.')';
	$laListe = array();
	$requete = "SELECT id, libelle ";
	$requete.= "FROM "._PREFIXE_TABLES_."fonctions_serveurs ";
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
// Retrouver les infos d'un tuple
// Entree :
//			$id : l'id du tuple à charger
//			$tuple : tuple chargé
// Retour :
//			true (trouve) / false (erreur / pas trouve)
//----------------------------------------------------------------------
function sqlSquelette_get($id, &$tuple)
{
	$requete = "SELECT id, libelle ";
	$requete.= "FROM "._PREFIXE_TABLES_."fonctions_serveurs ";
	$requete.= "WHERE id = '".$id."';";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		if ($nombre == 1) { 
			$tuple = $res[0];
			return true;
		}
	}
	return false;
}

//----------------------------------------------------------------------
// Ajoute d'un tuple
// Entree :
//		$donnees : tableau contenant les données du tuple à créer
// Retour : 
//		true : Ok
//		false : erreur SQL
//----------------------------------------------------------------------
function sqlSquelette_add($donnees)
{
	$requete = "INSERT IGNORE INTO "._PREFIXE_TABLES_."fonctions_serveurs ";
	$requete.= "(id, libelle) VALUES (";
	$requete.= "NULL, ";
	$requete.= "'".$donnees['libelle']."' ";
	$requete.= ")";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return($res);
}

//----------------------------------------------------------------------
// Modifie un tuple
// Entree :
//		$id : l'id du tuple à modifier
//		$donnees : tableau contenant les nouvelles données
// Retour : 
//		true : modification Ok
//		false : erreur SQL
//----------------------------------------------------------------------
function sqlSquelette_update($id, $donnees)
{
	//ecriture et lancement requete
	$requete = "UPDATE "._PREFIXE_TABLES_."fonctions_serveurs SET ";
	$requete.= "libelle = '".$donnees['libelle']."' ";
	$requete.= "WHERE id = '".$id."'";
	//DEBUG_('requete', $requete);
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

//----------------------------------------------------------------------
// Supprimer un tuple
// Entree :
//		$id : l'id du tuple à supprimer
// Retour : 
//		true : suppression Ok
//		false : erreur SQL
//----------------------------------------------------------------------
function sqlSquelette_delete($id)
{
	$requete = "DELETE FROM "._PREFIXE_TABLES_."fonctions_serveurs ";
	$requete.= "WHERE id = '".$id."'";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}