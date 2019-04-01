<?php
//------------------------------------------------------------------
// Classe UniversalDatabase
// A utiliser à la place des scripts 
//		- db.connexion.pdo.php
//		- db.connexion.pdo.oracle.php
//------------------------------------------------------------------
// éè : pour enregistrement UTF-8
// Auteur : Fabrice Labrousse
// Date : 25 janvier 2019
//------------------------------------------------------------------
// Classe permettant d'exécuter des requetes SQL
//------------------------------------------------------------------
// Afin de conserver la compatiblité ascendante avec la fonction 
// executeQuery des bibliothèques "db.connexion.pdo.php" et "db.connexion.pdo.oracle.php"
// on pourra faire appel à cette fonction émulatrice
//	function executeQuery($requete, &$nbenr, $mode=SQL_MODE_NORMAL) {
//		global $dbServer;
//		global $dbLogin;
//		global $dbPassword;
//		global $dbDatabase;
//		$db = new UniversalDatabase();
//		$db->setServeur($dbServer);
//		$db->setDatabase($dbDatabase);
//		$db->setLogin($dbLogin);
//		$db->setPassword($dbPassword);
//		$db->connect();
//		$res = $db->executeQuery($requete, $nombre, $mode);
//		return $res;
//	}
//------------------------------------------------------------------

// constantes pour la fonction executeQuery
defined('SQL_MODE_NORMAL') || define('SQL_MODE_NORMAL', 0);
defined('SQL_MODE_SILENT') || define('SQL_MODE_SILENT', 1);
defined('SQL_MODE_DEBUG')  || define('SQL_MODE_DEBUG', 2);

//------------------------------------------------------------------
class UniversalDatabase {

	private $_moteur = 'mysql';						//moteur de la base de données
	private $_codage = 'utf8';						//codage de la base de données (dans lequel récupérer les tuples)
	private $_serveur = '';							//serveur
	private $_database = '';						//base de données
	private $_login = '';							//login
	private $_password = '';						//mot de passe
	private $_connexion = null;						//objet PDO 
	private $_erreurCode = 0;						//code d'erreur en cours
	private $_erreurMsg = 'Aucune erreur';			//message d'erreur en cours

	private $_lastInsertId = null;					//dernier id inséré

	private $_preparedQuerySet = array();			//set (array) des requetes préparées

	const VERSION = 'v1.0.0 (2019-01-24)';
	const COPYRIGHT = '&copy;2019 Fabrice Labrousse';
	const MYSQL = 'mysql';
	const ORACLE = 'oci';

	const CODAGE_UTF8 = 'utf8';
	
	//codes d'erreur
	const ERROR_UNKNOW_ENGINE = 1;
	const ERROR_UNKNOW_CODAGE = 2;
	const ERROR_PDO = 3;

	//messages d'erreur
	const MSG_UNKNOW_ENGINE = 'Moteur inconnu';
	const MSG_UNKNOW_CODAGE = 'Codage inconnu';

	//=======================================
	// Méthodes privées
	//=======================================

	//--------------------------------------------------------------------------------
	// Execution d'une requête MySQL préparée
	//--------------------------------------------------------------------------------
	// Entrée : 
	//		$requete : la requete SQL sous forme préparée (avec des placeholders ? à la place des valeurs)
	//		$nbenr : renvoie le nombre de lignes impactées
	//		$mode : le mode d'exécution de la requete préparée
	//			SQL_MODE_SILENT (1) : aucune info affichée (prod)
	//			SQL_MODE_NORMAL (0) : infos ligne, code erreur SQL
	//			SQL_MODE_DEBUG (2) : infos complete + rappel requete
	//		$param : tableau sérialisé contenant les valeurs destinées à la requete préparée (array de valeur définies dans l'ordre des demandes)
	// Retour : 
	//		- pour les requetes en : SELECT, SHOW, DESCRIBE, EXPLAIN
	//			- tableau associatif ou un tableau vide si ok 
	//			- false si une erreur
	//		- pour les requetes de modification en : INSERT, UPDATE, DELETE
	//			- true si l'opération s'est bien passée
	//			- false si une erreur
	//--------------------------------------------------------------------------------
	// Fonctionnement
	// - les requetes préparées sont stockées dans la propriété privée _preparedQuerySet
	//   Chaque requete est identifiée (indivé) par sa représentation MD5 qui sert d'index 
	//	 afin de la retrouver rapidement.
	//		exemple : Array(
	//			[5f66c1a382eef76adf2c0beddf5efe20] => Array
    //			(
	//				[prepId] => PDOStatement Object
	//				(
	//					[queryString] => SELECT nom, prenom FROM users WHERE id_user = ?
	//				)
	//				[queryType] => SEL
	//			)
	// - Si la requête ne se trouve pas déjà dans le tableau alors on l'y ajoute
	// - Si la requete s'y trouve déjà, on l'utilise
	// - L'association des paramètres est uniquement réalisés avec les
	//   placeholders '?'. Il est donc impératif qu'à chaque ? corresponde une
	//	 valeur. Dans la requete SQL, les placeholders doivent s'écrire ? sans
	//   quote pour les entourer ('?' = interdit) sinon pas de résultat.
	// - Le résultat de la requete (qu'il soit valable ou qu'il donne une erreur) est renvoyé 
	//	 à la fonction executeQuery appelante.
	// - pour cette fonction, le placeholder pour LIMIT ne fonctionne pas car les valeurs envoyées
	//   par défaut sont des chaines de caractères et LIMIT attend obligatoirement un entier
	//--------------------------------------------------------------------------------
	private function _executePreparedQuery($requete, &$nbenr, $mode, $params)
	{
		$nbenr = -1;

		try {
			//recherche si la requete a déjà été préparée (objet PDOStatement contenu dans $this->_preparedQuerySet)
			$md5 = md5($requete);
			if (array_key_exists($md5, $this->_preparedQuerySet)) {
				//cette requete a déjà été preparée
				$prepId	= $this->_preparedQuerySet[$md5]['prepId'];
				$queryType = $this->_preparedQuerySet[$md5]['queryType'];
			}
			else {
				//cette requete n'a pas encore été préparée
				$queryType = strtoupper(substr($requete, 0, 3));		//enregistre le type de requete 'SEL', 'SHO', 'DES', 'EXP', 'INS', 'DEL', 'UPD'... etc
				$prepId = $this->_connexion->prepare($requete);			//enregistre la requete
				$this->_preparedQuerySet[$md5] = array('prepId' => $prepId, 'queryType' => $queryType);
			}

			//associer les valeurs aux place holders '?' de la requete préparée
			$lesParams = unserialize($params);
			for ($i = 0; $i < count($lesParams); $i++) {
				$prepId->bindValue($i+1, $lesParams[$i]);
			}

			//execution
			$prepId->execute();
			$nbenr = $prepId->rowCount();					//demande le nombre de lignes retournées
			if (in_array($queryType, array('SEL', 'SHO', 'DES', 'EXP'))) {
				$prepId->setFetchMode(PDO::FETCH_ASSOC);	//choix de récupération des resultats sous forme de tableau associatif
				$tableauRes = array();						//creation du tableau de résultats
				$tableauRes = $prepId->fetchAll();			//recupération des résultats - fetchAll() retournera un tableau vide si pas de résultat. fetch() retournera FALSE
				$prepId->closeCursor();						//liberation du curseur associé au jeu de resultats
				return $tableauRes;
			}
			else {
				if ($queryType == 'INS')					//recupere l'eventuel id inséré sur une requete INSERT
					$this->_lastInsertId = $this->_connexion->lastInsertId();
				return true;
			}
		} 
		catch (Exception $e) {
			if ($mode == SQL_MODE_NORMAL) die('Echec lors de la requête préparée : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage());
			else if ($mode == SQL_MODE_DEBUG) die('Echec lors de la requête : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage().'<br />'.$requete);
			return false;
		}		
		return false; //renvoie false en cas de syntaxe SQL incorrecte
	}


	//=======================================
	// SETTERS
	//=======================================	

	// positionne le choix du modteur de base de données
	public function setMoteur($moteur) {
		if (!in_array($moteur, array(self::MYSQL, self::ORACLE))) {
			$this->_erreurCode = self::ERROR_UNKNOW_ENGINE;
			$this->_erreurMsg = self::MSG_UNKNOW_ENGINE;
			return false;
		}
		$this->_moteur = $moteur;
		return true;
	}

	// positionne le choix du codage de la base de données
	public function setCodage($codage) {
		if (!in_array($codage, array(self::CODAGE_UTF8))) {
			$this->_erreurCode = self::ERROR_UNKNOW_CODAGE;
			$this->_erreurMsg = self::MSG_UNKNOW_CODAGE;
			return false;
		}
		$this->_codage = $codage;
		return true;
	}

	public function setServeur($serveur) {$this->_serveur = $serveur;}				// positionne le serveur de la base de données
	public function setDatabase($database) {$this->_database = $database;}			// positionne la base de données
	public function setLogin($login) {$this->_login = $login;}						//positionne le login utilisé pour la base de donnée
	public function setPassword($password) {$this->_password = $password;}			//positionne le mot de passe pour accès à la base de données

	//=======================================
	// GETTERS
	//=======================================	
	
	public function getLastInsertId() {return $this->_lastInsertId;}				//retourne l'id du dernier élément insérer (par INSERT)


	//=======================================
	// Méthodes publiques
	//=======================================	

	//---------------------------------------------------
	// Connexion à la base de données
	//---------------------------------------------------
	public function connect() {
		try {
			$arrExtraParam = array();
			if ($this->_codage == self::CODAGE_UTF8) {
				// mise en place de paramètres supplémentaires à l'initialisation de la connexion
				// en particulier on oblige à travailler sur un jeu de caractère en UTF-8
				$arrExtraParam = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
			}

			// initialisation de la connexion
			$this->_connexion = new PDO($this->_moteur.':host='.$this->_serveur.';dbname='.$this->_database, $this->_login, $this->_password, $arrExtraParam);

			// positionnement des erreurs
			// PDO::ERRMODE_SILENT - ne rapporte pas d'erreur (rens. codes erreur)
			// PDO::ERRMODE_WARNING - émet un warning
			// PDO::ERRMODE_EXCEPTION - lance une exception
			$this->_connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (Exception $e) {
			$this->_connexion = null;
			$this->_erreurCode = self::ERROR_PDO;
			$this->_erreurMsg = 'Erreur PDO dans : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage();
			die($this->_erreurMsg);
		}
	}

	//------------------------------------------------------------------------
	// Execution d'une requête MySQL
	//------------------------------------------------------------------------
	// Entrée : 
	//		$requete : la requete SQL avec ou sans paramètre (avec paramètre sous la forme "requete¤params")
	//		$nbenr : renvoie 
	//			- nombre de lignes retournées pour les requête SELECT, 
	//			- nombre de lignes affectées pour UPDATE, REPLACE ou DELETE
	//			- -1 si erreur sur la requete.
	//		$mode : le mode d'exécution de la requete préparée
	//			SQL_MODE_SILENT (1) : aucune info affichée (prod)
	//			SQL_MODE_NORMAL (0) : infos ligne, code erreur SQL
	//			SQL_MODE_DEBUG (2) : infos complete + rappel requete
	// Retour : 
	//		- pour les requetes en : SELECT, SHOW, DESCRIBE, EXPLAIN
	//			- tableau associatif ou un tableau vide si ok 
	//			- false si une erreur
	//		- pour les requetes de modification en : INSERT, UPDATE, DELETE
	//			- true si l'opération s'est bien passée
	//			- false si une erreur
	//------------------------------------------------------------------------
	// - Un INSERT met à jour la propriétre _lastInsertId qui contient le dernier 
	//		numéro d'enregistrement créé. Info accessible via le getter getLastInsertId()
	//------------------------------------------------------------------------
	// RAPPEL SQL : Pour requêtes du type SELECT, SHOW, DESCRIBE, EXPLAIN et
	// les autres requêtes retournant un jeu de résultats, mysql_query()
	// retournera une ressource en cas de succès, ou FALSE en cas d'erreur.
	// Pour les autres types de requêtes, INSERT, UPDATE, DELETE, DROP, etc.,
	// mysql_query() retourne TRUE en cas de succès ou FALSE en cas d'erreur.
	//------------------------------------------------------------------------
	public function executeQuery($requete, &$nbenr, $mode=SQL_MODE_NORMAL) {
		
		//augmentation du nombre de requete executées
		global $_NB_QUERY;
		$_NB_QUERY += 1;

		//la structure attendue de $requete est de la forme : "requete SQL¤params" (séparateur = ¤)
		//si il existe des paramètres, on considère qu'il s'agit d'une requete préparée
		$dummy = explode('¤', $requete);
		$requete = $dummy[0];
		if (isset($dummy[1])) 
			return $this->_executePreparedQuery($requete, $nbenr, $mode, $dummy[1]);

		$nbenr = -1;

		if (in_array(substr($requete, 0, 3), array('SEL', 'SHO', 'DES', 'EXP'))) {
			//----------------------------
			// requete de résultat SELECT
			//----------------------------
			try {
				$this->_connexion->quote($requete);			//protege la chaine contre les injections SQL
				$res = $this->_connexion->query($requete);	//execute la requete
				$nbenr = $res->rowCount();					//demande le nombre de lignes retournées
				$res->setFetchMode(PDO::FETCH_ASSOC);		//choix de récupération des resultats sous forme de tableau associatif
				$tableauRes = array();						//creation du tableau de résultats
				$tableauRes = $res->fetchAll();				//recupération des résultats - fetchAll() retournera un tableau vide si pas de résultat. fetch() retournera FALSE
				$res->closeCursor();						//liberation du curseur associé au jeu de resultats
				return $tableauRes;
			} 
			catch (Exception $e) {
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
				$this->_connexion->quote($requete);			//protege la chaine contre les injections SQL
				$nbenr = $this->_connexion->exec($requete);	//execute la requete et récupere le nombre de lignes impactées
				if (substr($requete, 0, 3) == 'INS')		//recupere l'eventuel id inséré sur une requete INSERT
					$this->_lastInsertId = $this->_connexion->lastInsertId();
				return true;
			}
			catch (Exception $e) {
				if ($mode == SQL_MODE_NORMAL) die('Echec lors de la requête : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage());
				else if ($mode == SQL_MODE_DEBUG) die('Echec lors de la requête : '.$e->getFile().' ligne : '.$e->getLine().' : '.$e->getMessage().'<br />'.$requete);
				return false;
			}
		}
		return false; //renvoie false en cas de syntaxe SQL non prise en compte
	}

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