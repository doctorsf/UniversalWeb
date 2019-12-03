<?php
//-----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Date : 22.11.2012
// Version :
// 11.01.2018 : mySqlDataProtect déclaré si pas encore déclaré
//-----------------------------------------------------------------------
// db.connexion.pdo.php
// permet de faire la connexion à une base de données quelconque
// les informations spécifiques à renseigner pour la base de données
// à ouvrir sont à renseigner dans les variables globales suivantes :
//		$dbServer : nom du serveur d'hébergement ex: "localhost"
//		$dbLogin : nom de l'utilisateur ex "root"
//		$dbPassword : mot de passe d'accès à la base ex ""
//		$dbDatabase : nom de la base de données ex "test"
//-----------------------------------------------------------------------
// Prend en charge PDO (PHP Database Object)
// Pour lancer une requete SQL simple :
//		executeQuery($requete, &$nbenr, $mode)
// Pour lancer une suite de requetes simples (gain de temps / acces db)
//		$con = getConnexion();
//		executeQuery($requete, &$nbenr, $mode, $con);
//		executeQuery($requete, &$nbenr, $mode, $con);
//		...
//		CloseConnexion($con);
//		Attention : gain de temps mais risque de voir le nombre de
//      connexion max admises par le serveur SQL saturé si le script
//		est trop long entre debut et fin de connexion (on est pas seul)
// Pour lancer une suite de requetes préparées (gain de temps) :
//		StartPreparedQuerySet()
//		executeQuery($requete, &$nbenr, $mode)
//		executeQuery($requete, &$nbenr, $mode)
//		...
//		StopPreparedQuerySet()
//		Attention : gain de temps encore supérieur mais risques
//      identiques de saturation du nombre de connexion au serveur SQL
//		N'est efficace que lors de répétition de requetes identiques.
//-----------------------------------------------------------------------

global $dbConnexion_lastInsertId;		// id de connexion

global $dbPreparedQuerySetFlag;			// drapeau (true/false) qui indique si l'execution d'un jeu de requetes préparées est en cours
global $dbPreparedQuerySetConnexion;	// id de connexion d'un jeu de requetes preparees en cours
global $dbPreparedQuerySet;				// set (array) des requetes préparées

// constantes pour la fonction executeQuery
defined('SQL_MODE_NORMAL') || define('SQL_MODE_NORMAL', 0);
defined('SQL_MODE_SILENT') || define('SQL_MODE_SILENT', 1);
defined('SQL_MODE_DEBUG')  || define('SQL_MODE_DEBUG', 2);

//----------------------------------------------------------------------
// Ouverture de la base de données
// PDO::ERRMODE_SILENT - ne rapporte pas d'erreur (rens. codes erreur)
// PDO::ERRMODE_WARNING - émet un warning
// PDO::ERRMODE_EXCEPTION - lance une exception
//----------------------------------------------------------------------
function getConnexion()
{
	global $dbServer;
	global $dbLogin;
	global $dbPassword;
	global $dbDatabase;
	try {
		// mise en place de paramètres supplémentaires à l'initialisation de la connexion
		//$arrExtraParam = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
		// initialisation de la connexion
		//$dbConnexion = new PDO('mysql:host='.$dbServer.';dbname='.$dbDatabase, $dbLogin, $dbPassword, $arrExtraParam);
		$dbConnexion = new PDO('oci:host='.$dbServer.';dbname='.$dbDatabase, $dbLogin, $dbPassword);
		// positionnement des erreurs
		$dbConnexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbConnexion;
	}
	catch (Exception $e) {
		$dbConnexion = null;
		$msg = 'Erreur PDO dans : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage();
	    die($msg);
	}
}

//----------------------------------------------------------------------
// Fermeture du lien de connexion à la base de données MySQL
//----------------------------------------------------------------------
function CloseConnexion($dbConnexion)
{
	$dbConnexion = null;
	return true;
}

//----------------------------------------------------------------------
// Initialise le lancement d'un prochain jeu de requetes préparées
//----------------------------------------------------------------------
function StartPreparedQuerySet()
{
	global $dbPreparedQuerySetFlag;
	global $dbPreparedQuerySetConnexion;
	global $dbPreparedQuerySet;

	if ((!empty($dbPreparedQuerySetFlag)) || (!empty($dbPreparedQuerySet))) {
		die('Erreur : un jeu de requêtes préparées est déjà en cours d\'utilisation...');
		return false;
	}
	$dbPreparedQuerySetConnexion = getConnexion();	// on cree une connexion à la base de données
	$dbPreparedQuerySet = array();					// on cree le tableau de requetes préparées
	$dbPreparedQuerySetFlag = true;					// jeu de requetes préparées prêt
	return true;
}

//----------------------------------------------------------------------
// Termine l'execution d'un jeu de requetes préparées
//----------------------------------------------------------------------
function StopPreparedQuerySet()
{
	global $dbPreparedQuerySetFlag;
	global $dbPreparedQuerySetConnexion;
	global $dbPreparedQuerySet;

	$dbPreparedQuerySetFlag = false;						// fin du jeu de requetes préparées
	foreach($dbPreparedQuerySet as $index => $id)
		$dbPreparedQuerySet[$index]['prepId'] = null;		// on libere les requetes préparées
	$dbPreparedQuerySet = null;								// on supprime le tableau de requetes préparées ou unset($GLOBALS['dbPreparedQuerySet']);
	$dbPreparedQuerySetConnexion = null;					// on ferme la connexion à la base de données
	return true;
}

//-------------------------------------------------------------------------
// Execution d'une requête MySQL
//-------------------------------------------------------------------------
// - les enregistrement retournés sont stockés dans une Array $tableauRes
// - la fonction renvoie dans son argument ($nbenr) le nombre de lignes
//   retournées pour les requête SELECT, ou le nombre de lignes affectées
//   pour UPDATE, REPLACE ou DELETE et -1 si erreur sur la requete.
// - Un INSERT met à jour la variable globale $dbConnexion_lastInsertId
//   qui contient le dernier numéro d'enregistrement créé.
// - $mode (SQL_MODE_SILENT / SQL_MODE_NORMAL (défaut) / SQL_MODE_DEBUG
//		Si SQL_MODE_SILENT : aucune info affichée (prod)
//		Si SQL_MODE_NORMAL : infos ligne, code erreur SQL
//		Si SQL_MODE_DEBUG  : infos complete + rappel requete
// - $dbConnexion : si cette valeur est (null) alors la fonction est
//   autonome et se charge d'ouvrir et de fermer elle-même une nouvelle
//   connexion à la base de données.
//   si cette valeur n'est pas null, alors cela signifie qu'une connexion
//   à la base de données a été (doit être) prise en charge manuellement
//   par le programmeur via getConnexion() (et devra être fermée par la
//   fonction CloseConnexion()). $dbConnexion est alors l'identifiant de
//   connexion renvoyé par getConnexion(). Cette methode sert par exemple
//   pour favorier une suite de requetes sans ouvrir/fermer la connexion
//   à la base de donnée systématiquement et ainsi gagner du temps. Mais
//   attention à ne pas garder une connexion active trop longtemps sous
//   peine d'atteindre le nombre maxi de connexions autorisées au serveur
// - NE PAS lancer de requete classique pendant une phase de requetes
//   préparées
//-------------------------------------------------------------------------
// RETOUR : requetes de resultat (SELECT, SHOW, DESCRIBE, EXPLAIN)
//	- un tableau associatif ou un tableau vide si ok
//	- false si une erreur
// RETOUR : pour les requetes de modification (INSERT, UPDATE, DELETE)
//  - true si l'opération s'est bien passée
//	- false si une erreur
//	- $nbenr renvoie le nombre de lignes impactées
//-------------------------------------------------------------------------
// RAPPEL SQL : Pour requêtes du type SELECT, SHOW, DESCRIBE, EXPLAIN et
// les autres requêtes retournant un jeu de résultats, mysql_query()
// retournera une ressource en cas de succès, ou FALSE en cas d'erreur.
// Pour les autres types de requêtes, INSERT, UPDATE, DELETE, DROP, etc.,
// mysql_query() retourne TRUE en cas de succès ou FALSE en cas d'erreur.
//-------------------------------------------------------------------------
function executeQuery($requete, &$nbenr, $mode=SQL_MODE_NORMAL, $dbConnexion=null)
{
	global $dbConnexion_lastInsertId;
	global $dbPreparedQuerySetFlag;
	global $_NB_QUERY;
	$_NB_QUERY += 1;

	// on extrait de $requete la requete SQL et les parametres enventuels présents à la suite
	// la structure attendue de $requete est de la forme : "requete SQL|params"
	$dummy = explode('¤', $requete);
	$requete = $dummy[0];

	// test si tentative d'envoi d'une requete classique pendant phase de requetes préparées
	if ((!empty($dbPreparedQuerySetFlag)) && (!isset($dummy[1]))) {
		return false;
	}

	// test si on doit gérer une requete préparée ou pas
	if (!empty($dbPreparedQuerySetFlag))
		return ExecutePreparedQuery($requete, $nbenr, $mode, $dummy[1]);

	$nbenr = -1;
	// l'ouverture et la fermeture de la base de données sera fait si $dbConnexion existe (pas null)
	$OpenClose = ($dbConnexion == null);

	if (in_array(substr($requete, 0, 3), array('SEL', 'SHO', 'DES', 'EXP'))) {
		//----------------------------
		// requete de résultat SELECT
		//----------------------------
		try {
			if($OpenClose)
				$dbConnexion = getConnexion();
			$dbConnexion->quote($requete);			//protege la chaine contre les injections SQL
			$res = $dbConnexion->query($requete);	//execute la requete
			$nbenr = $res->rowCount();				//demande le nombre de lignes retournées
			$res->setFetchMode(PDO::FETCH_ASSOC);	//choix de récupération des resultats sous forme de tableau associatif
			$tableauRes = Array();					//creation du tableau de résultats
			$tableauRes = $res->fetchAll();			//recupération des résultats - fetchAll() retournera un tableau vide si pas de résultat. fetch() retournera FALSE
			$res->closeCursor();					//liberation du curseur associé au jeu de resultats
			if ($OpenClose)
				CloseConnexion($dbConnexion);
			return $tableauRes;
		} 
		catch (Exception $e) {
			CloseConnexion($dbConnexion);
			if ($mode == SQL_MODE_NORMAL) die('Echec lors de la requête : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage());
			else if ($mode == SQL_MODE_DEBUG) die('Echec lors de la requête : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage().'<br />'.$requete);
			return false;
		}		
	}	
	else {
		//---------------------------------------------------
		// requetes de modification INSERT / DELETE / UPDATE
		//---------------------------------------------------
		try {
			if($OpenClose)
				$dbConnexion = getConnexion();
			$dbConnexion->quote($requete);			//protege la chaine contre les injections SQL
			$nbenr = $dbConnexion->exec($requete);	//execute la requete et récupere le nombre de lignes impactées
			if (substr($requete, 0, 3) == 'INS')	//recupere l'eventuel id inséré sur une requete INSERT
				$dbConnexion_lastInsertId = $dbConnexion->lastInsertId();
			if ($OpenClose)
				CloseConnexion($dbConnexion);
			return true;
		}
		catch (Exception $e) {
			CloseConnexion($dbConnexion);
			if ($mode == SQL_MODE_NORMAL) die('Echec lors de la requête : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage());
			else if ($mode == SQL_MODE_DEBUG) die('Echec lors de la requête : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage().'<br />'.$requete);
			return false;
		}
	}
	return false; //renvoie false en cas de syntaxe SQL incorrecte
}

//------------------------------------------------------------------------------
// Execution d'une requête MySQL préparée
// Cette fonction ne doit jamais être appelée directement. C'est la fonction
// executeQuery qui s'en charge si besoin
//------------------------------------------------------------------------------
// - Ne pas abuser des requetes préparées.
// - Afin de ne pas atteindre le nombre maximum de connexion simu. à la base de
//   données ne pas mettre trop de code entre StartPreparedQuerySet() et
//   StopPreparedQuerySet() car cette méthode de requetage garde une connexion
//   active à MySQL pour chaque utilisateur. Le principe étant de libérer une
//   connexion le plus vite possible.
// - NE PAS lancer de requete classique pendant une phase de requetes
//   préparées (renvoie false)
//------------------------------------------------------------------------------
// PRINCIPE : $requete est de la forme "requete¤paramètres"
// - paramètres est en fait un tableau sérialisé de valeurs
// - L'execution des requetes préparées ne vaut que si il existe une
//   connexion à MySQL initialisé depuis l'extérieur. Cela se fait par la
//   fonction StartPreparedQuerySet(). Si ce n'est pas le cas (flag
//   $dbPreparedQuerySetFlag), la fonction renvoie false.
// - La fin d'execution d'un jeu de requete préparées doit être fait avec la
//	 fonction StopPreparedQuerySet() pour déconnecter MySQL et libérer les res.
// - les requetes préparées sont stockées dans le tableau global
//   $dbPreparedQuerySet. Chaque requete est identifiée par sa représentation
//   MD5 qui sert d'index afin de la retrouver rapidement
// - Si la requête ne se trouve pas déjà dans le tableau alors on l'y ajoute
// - Si la requete s'y trouve déjà, on l'utilise
// - L'association des paramètres est uniquement réalisés avec les
//   placeholders '?'. Il est donc impératif qu'à chaque ? corresponde une
//	 valeur. Dans la requete SQL, les placeholders doivent s'écrire ? sans
//   quote pour les entourer ('?' = interdit) sinon pas de résultat.
// - Le résultat de la requete (qu'il soit valable ou qu'il donne une erreur)
//   est renvoyé à la fonction executeQuery appelante.
//------------------------------------------------------------------------------
function ExecutePreparedQuery($requete, &$nbenr, $mode, $params)
{
	global $dbConnexion_lastInsertId;
	global $dbPreparedQuerySetFlag;
	global $dbPreparedQuerySetConnexion;
	global $dbPreparedQuerySet;

	// si la connexion n'a pas été etablie à partir de StartPreparedQuerySet() on renvoie false
	if ($dbPreparedQuerySetFlag == false) return false;
	$dbConnexion = $dbPreparedQuerySetConnexion;

	$nbenr = -1;

	try {
		// recherche si la requete a déjà été préparée
		$md5 = md5($requete);
		if (array_key_exists($md5, $dbPreparedQuerySet))	{
			// cette requete a déjà été preparée
			$prepId	= $dbPreparedQuerySet[$md5]['prepId'];
			$queryType = $dbPreparedQuerySet[$md5]['queryType'];
		}
		else {
			// cette requete n'a pas encore été préparée
			$queryType = strtoupper(substr($requete, 0, 3));			//enregistre le type de requete 'SEL', 'SHO', 'DES', 'EXP', 'INS', 'DEL', 'UPD'... etc
			$prepId = $dbPreparedQuerySetConnexion->prepare($requete);	//enregistre la requete
			$dbPreparedQuerySet[$md5] = array('prepId' => $prepId, 'queryType' => $queryType);
		}
	
		// associer les valeurs aux place holders '?' de la requete préparée
		$lesParams = unserialize($params);
		for ($i = 0; $i < count($lesParams); $i++) {
			$prepId->bindValue($i+1, $lesParams[$i]);
		}

		// execution
		$prepId->execute();
		$nbenr = $prepId->rowCount();					//demande le nombre de lignes retournées
		if (in_array($queryType, array('SEL', 'SHO', 'DES', 'EXP'))) {
			$prepId->setFetchMode(PDO::FETCH_ASSOC);	//choix de récupération des resultats sous forme de tableau associatif
			$tableauRes = Array();						//creation du tableau de résultats
			$tableauRes = $prepId->fetchAll();			//recupération des résultats - fetchAll() retournera un tableau vide si pas de résultat. fetch() retournera FALSE
			$prepId->closeCursor();						//liberation du curseur associé au jeu de resultats
			return $tableauRes;
		}
		else {
			if ($queryType == 'INS')						//recupere l'eventuel id inséré sur une requete INSERT
				$dbConnexion_lastInsertId = $dbPreparedQuerySetConnexion->lastInsertId();
			return true;
		}
	} 
	catch (Exception $e) {
		StopPreparedQuerySet();
		if ($mode == SQL_MODE_NORMAL) die('Echec lors de la requête préparée : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage());
		else if ($mode == SQL_MODE_DEBUG) die('Echec lors de la requête : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage().'<br />'.$requete);
		return false;
	}		
	return false; //renvoie false en cas de syntaxe SQL incorrecte
}

//-------------------------------------------------------------------------
// Protège des données en vue de les injecter dans une base MySQL
// Attention la fonction supprime le caractère tout de qui se trouve après un 
// caractère '<' immédiatement suivi de text (ex : <24)
// Voir http://stackoverflow.com/questions/17650623/php-strip-tags-not-allowing-less-than-in-string
// pour plus d'infos
//-------------------------------------------------------------------------
if (!function_exists('mySqlDataProtect')) {
	function mySqlDataProtect($data) {
		if (is_array($data)) {
			foreach($data as $index => $dummy) {
				$data[$index] = addslashes(strip_tags($data[$index]));
				$data[$index] = str_replace(chr(13).chr(10), '\r\n', $data[$index]);
			}
		}
		else {
			$data = addslashes(strip_tags($data));
			$data = str_replace(chr(13).chr(10), '\r\n', $data);
		}
		return $data;
	}
}