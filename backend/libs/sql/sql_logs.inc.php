<?php
/*-----------------------------------------------------------------------
Auteur : Fabrice Labrousse
Classe de gestion de la table des logs
Elle étend la classe générique de table SqlSimple.
éè : UTF-8
-------------------------------------------------------------------------
Methodes disponibles :
-------------------------------------------------------------------------
getListeNombre()	: nombre de lignes du listing de la table
getListe()			: obtenir listing de la table
get()				: renvoie un tuple recherché
add()				: //ajoute un tuple
update()			: modifie un tuple
delete()			: supprime un tuple
19.01.2017
	Premiere version
28.03.2018
	- Correction de l'appel aux méthodes pour compatiblité PHP 7 (le nombre de paramètre doit 
	être identique entre la méthode et sa surcharge -> rajout paramètre $debug )
	- Changement du nom du script en sql_squelette_references.inc.php
	- Ajout de la fonction sqlSqueletteReference_fillSelectTous()
16.04.2018
	- Ajout des fonctions sqlLogs_createTableLogs() et sqlLogs_createTableLogsTypes() de création 
	des tables de logs. Lancées par active_application.php
27.11.2018
	- Correction bug déclaration publique $_table (le préfixe base était en dur et erroné)
30.01.2019
	- Correction creation table : remplacé 'datetime' par 'timestamp' dans la création du champ 'quand'
	sinon sela ne marchait pas
11.04.2019
	- Correction méthode purge() : contrairement à DELETE FROM, TRUNCATE TABLE ne retourne pas le nombre de lignes supprimées
	l'ancienne méthode renvoyait toujours 0
12.11.2019
	- Modification de l'écriture des champs publiques _table (en table), _index (en index) et _champs (en champ) sans le _ (réservée aux propriétées privées)
------------------------------------------------------------------------*/

//- CREATION tables logs --------------------------------------
function sqlLogs_createTableLogs()
{
	$requete = "CREATE TABLE IF NOT EXISTS `"._PREFIXE_TABLES_."logs` (";
	$requete.= "`id_log` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, ";
	$requete.= "`id_log_type` tinyint(2) UNSIGNED NOT NULL, ";
	$requete.= "`id_user` varchar(100) NOT NULL, ";
	$requete.= "`operation` varchar(255) NOT NULL, ";
	$requete.= "`quand` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, ";
	$requete.= "PRIMARY KEY (`id_log`), ";
	$requete.= "KEY `id_user` (`id_user`), ";
	$requete.= "KEY `quand` (`quand`)";
	$requete.= ") ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	return $res;
}

function sqlLogs_createTableLogsTypes()
{
	$requete = "CREATE TABLE IF NOT EXISTS `"._PREFIXE_TABLES_."logs_types` (";
	$requete.= "`id_log_type` tinyint(2) UNSIGNED NOT NULL, ";
	$requete.= "`libelle` varchar(128) NOT NULL, ";
	$requete.= "PRIMARY KEY (`id_log_type`)";
	$requete.= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		//Contenu minimum de la table `profils`
		$requete = "INSERT INTO `"._PREFIXE_TABLES_."logs_types` (`id_log_type`, `libelle`) VALUES ";
		$requete.= "("._LOG_CONNEXION_.", 'Connexion');";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
	return $res;
}

//-----------------------------------------------------------------------

class sqlLogs extends SqlSimple {
	public $table	= _PREFIXE_TABLES_.'logs';							//saisir le nom de la table de référence (ex : "db_reference")
	public $index	= 'id_log';											//Saisir ici le champ index unique de la table (ex : "id_tuple")
	public $champs	= 'id_log, id_log_type, id_user, operation';		//Saisir ici la liste des champs de la table à lister (ex : "id_tuple, libelle, famille")

	public function add($donnees, $debug = false) {
		$requete = "NULL, ";
		//ajouter le code Sql des champs nécéssaires pour l'ajout de données
		$requete.= "'".$donnees['id_log_type']."', ";
		$requete.= "'".$donnees['id_user']."', ";
		$requete.= "'".$donnees['operation']."' ";
		return parent::add($requete, $debug);
	}

	//purge les logs de plus de 3 mois
	public function epure() {
		$requete.= "DELETE FROM ".$this->table." WHERE quand < DATE_SUB(NOW(), INTERVAL 3 MONTH)";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			return $nombre;
		}
		return false;
	}

	//purge entièrement les logs
	//Contrairement à DELETE FROM, TRUNCATE TABLE ne retourne pas le nombre de lignes supprimées
	//la méthode retourne donc true ou false
	public function purge() {
		$requete.= "TRUNCATE TABLE ".$this->table;
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			return true;
		}
		return false;
	}
}

//--------------------------------------
// Log
//--------------------------------------
function sqlLogs_log($id_log_type, $operation) {
	$log = new sqlLogs();
	$donnees['id_log_type'] = $id_log_type;
	$donnees['id_user'] = $_SESSION[_APP_LOGIN_]->getId();
	$donnees['operation'] = addslashes($operation);
	$log->add($donnees);
}

//----------------------------------------------------------------------
// Renvoie le code HTML de construction de la liste des types de journeaux
// Entree :
//		$default : id du type de journal par défaut
// Retour : 
//		code HTML de remplissage de la liste ou '' si erreur SQL
//----------------------------------------------------------------------
function sqlLogs_fillSelectTypesTous($defaut)
{
	$texte = '';
	$requete = "SELECT id_log_type, libelle ";
	$requete.= "FROM "._PREFIXE_TABLES_."logs_types ";
	$requete.= "ORDER BY libelle";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		($defaut == 'TOUS') ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="TOUS"'.$selected.'>TOUS</option>';
		foreach($res as $ligne) {
			($defaut == $ligne['id_log_type']) ? $selected = ' selected' : $selected = '';
			$texte.= '<option value="'.$ligne['id_log_type'].'"'.$selected.'>'.$ligne['libelle'].'</option>';
		}
	}
	return $texte;
}

//----------------------------------------------------------------------
// Renvoie le code HTML de construction de la liste des types de journeaux
// Entree :
//		$default : id du type de journal par défaut
// Retour : 
//		code HTML de remplissage de la liste ou '' si erreur SQL
//----------------------------------------------------------------------
function sqlLogs_fillSelectUtilisateursTous($defaut)
{
	$texte = '';
	$requete = "SELECT DISTINCT "._PREFIXE_TABLES_."users.id_user, nom, prenom ";
	$requete.= "FROM "._PREFIXE_TABLES_."users, "._PREFIXE_TABLES_."logs ";
	$requete.= "WHERE "._PREFIXE_TABLES_."logs.id_user = "._PREFIXE_TABLES_."users.id_user";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		($defaut == 'TOUTES') ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="TOUS"'.$selected.'>TOUS</option>';
		($defaut == 'IGNORE') ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="IGNORE"'.$selected.'>IGNORE</option>';
		foreach($res as $ligne) {
			($defaut == $ligne['id_user']) ? $selected = ' selected' : $selected = '';
			$texte.= '<option value="'.$ligne['id_user'].'"'.$selected.'>'.$ligne['nom'].' '.$ligne['prenom'].'</option>';
		}
	}
	return $texte;
}