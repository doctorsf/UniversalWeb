<?php
//------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Classe qui gère les données d'une table MySQL générique
// Date : 19.01.2017
//-----------------------------------------------------------------------
// mise à jour : 
// 29.11.2017
//		modification des paramètres de getListe (enlevé le param de langue)
// 05.02.2019
//		ajout de la méthode importMany
// 12.02.2019
//		test de la méthode importMany et utilisation dans exemple fourni avec UniversalWeb
// 04.04.2019
//		correction bug importMany()
// 12.11.2019
//		modification de l'écriture des champs publiques _table (en table), _index (en index) et _champs (en champ) sans le _ (réservée aux propriétées privées)
// 06.12.2019
//		ajout de la méthode statique updateChamp
// 13.12.2019
//		ajout du paramètre debug à la classe existValeur et existValeurAilleurs
// 23.12.2019
//		ajout des méthodes statiques getMin, getMax, getGap
// 13.01.2020
//		ajout de la méthode statique catalog
//-----------------------------------------------------------------------
// Cette classe comporte des méthodes d'acces génériques à une table
// -------------------------------------
// getListeNombre() : renvoie le nombre de tuple dans une liste
// getListe()		: renvoie une liste de tuples de la table
// get()			: recupere un tuple
// add()			: ajoute une tuple à la table
// addMany()		: ajoute plusieurs tuples en une seule fois
// importMany()		: import en masse multi requetes
// update()			: modifie un tuple de la table
// delete()			: supprime un tuple de la table
// Ainsi que quelques méthodes statiques
// -------------------------------------
// fillSelect()		: remplissage d'une liste déroulante
// existValeur()	: test de l'existence d'une valeur dans la table
// existValeurAilleurs() : test de l'existence d'une valeur	autre que..
// getValeurForKey(): trouve la valeur d'un champ pour une clé donnée
// swapBool()		: swap d'un champ booléan
//-----------------------------------------------------------------------
// Mise en oeuvre : créer une classe qui hérite de SqlSimple dans
// laquelle	on surchargerea à minima les mathodes add() et update() car
// celles-ci font appel a des champs propres à chaque table.
// éè : UTF-8
//-----------------------------------------------------------------------

class SqlSimple {

	//************************ METHODES STATIQUES ****************************

	//----------------------------------------------------------------------
	// Obtenir une liste index / champ de toute la table
	// Entree :
	//		$table : nom de la table concernée
	//		$index : index a prendre en compte
	//		$champ : champ à afficher
	// Retour : 
	//		tableau si ok / false si erreur SQL
	//----------------------------------------------------------------------
	static function catalog($table, $index, $champ) {
		$laListe = array();
		$requete = "SELECT ".$index.", ".$champ." ";
		$requete.= "FROM ".$table." ";
		$requete.= "ORDER BY ".$index;
		$laListe = executeQuery($requete, $nombre, _SQL_MODE_);
		return $laListe;
	}

	//----------------------------------------------------------------------
	// Remplissage d'une liste
	// Entree :
	//		$default : id de l'item sélectionné par défaut
	//		$table : nom de la table concernée
	//		$index : indice a prendre en compte
	//		$champ : valeur du champ à afficher
	// Retour : 
	//		Le code HTML pour la liste déroulante
	//----------------------------------------------------------------------
	static function fillSelect($defaut, $table, $index, $champ)
	{
		$texte = '';
		$requete = "SELECT ".$index.", ".$champ." ";
		$requete.= "FROM ".$table." ";
		$requete.= "ORDER BY ".$champ;
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			foreach($res as $ligne) {
				($defaut == $ligne[$index]) ? $selected = ' selected' : $selected = '';
				$texte.= '<option value="'.$ligne[$index].'"'.$selected.'>'.$ligne[$champ].'</option>';
			}
		}
		return $texte;
	}

	//----------------------------------------------------------------------
	// Renvoie la plus grande valeur du champ numérique $champ de la $table,
	// puis on y ajoute la valeur numérique $offset
	// Entree :
	//		$table : nom de la table concernée
	//		$champ : nom du champ à explorer
	//		$offset : valeur à ajouter sur la valeur retournée (défaut 0)
	// Retour : 
	//		La valeur maximum augmentée de $offset
	//----------------------------------------------------------------------
	static function getMax($table, $champ, $offset=0)
	{
		$requete = "SELECT MAX(".$champ.") max ";
		$requete.= "FROM ".$table;
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre == 1) return $res[0]['max'] + $offset;
		}
		return 0;
	}

	//----------------------------------------------------------------------
	// Renvoie la plus petite valeur du champ numérique $champ de la $table,
	// puis on y ajoute la valeur numérique $offset
	// Entree :
	//		$table : nom de la table concernée
	//		$champ : nom du champ à explorer
	//		$offset : valeur à ajouter sur la valeur retournée (défaut 0)
	// Retour : 
	//		La valeur minimum augmentée de $offset
	//----------------------------------------------------------------------
	static function getMin($table, $champ, $offset=0)
	{
		$requete = "SELECT MIN(".$champ.") min ";
		$requete.= "FROM ".$table;
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre == 1) return $res[0]['min'] + $offset;
		}
		return 0;
	}

	//----------------------------------------------------------------------
	// Renvoie un trou numérique dans le $champ de la $table.
	// Entree :
	//		$table : nom de la table concernée
	//		$champ : nom du champ à explorer
	//		$sauf : valeur à ne pas proposer (souvent le 0)
	// Retour : 
	//		Le trou proposé
	//----------------------------------------------------------------------
	// - En réalité renvoie la valeur directement inférieure au mini (champ -1)
	// - Limite de la valeur renvoyée [0 .. MAX + 1] (pas de chiffre négatif)
	//----------------------------------------------------------------------
	static function getGap($table, $champ, $sauf=999999)
	{
		if ((self::getMin($table, $champ)) > 0) {
			$requete = "SELECT (".$champ." - 1) numero FROM ".$table." ";
			$requete.= "WHERE (".$champ." - 1) NOT IN ";
			$requete.= "(SELECT ".$champ." FROM ".$table.") ";
			if ($sauf != 999999) {
				$requete.= "AND (".$champ." - 1) <> ".(int)$sauf." ";
			}
			$requete.= "LIMIT 0, 1";
			$res = executeQuery($requete, $nombre, _SQL_MODE_);
			if ($res !== false) {
				if ($nombre == 1) return $res[0]['numero'];
			}
		}
		return self::getMax($table, $champ, 1);
	}

	//----------------------------------------------------------------------
	// Test si la table $table possède un champ $field à la valeur $valeur
	// Entree :
	//		$table : nom de la table
	//		$field : nom du champ de la table à tester
	//		$valeur : valeur du champ $field recherchée
	// Retour : 
	//		Nombre de fois valeur trouvée (0 .. x)
	//		false : erreur SQL
	//----------------------------------------------------------------------
	static function existValeur($table, $field, $valeur, $debug=false)
	{
		$requete = "SELECT COUNT(*) nombre FROM ".$table." ";
		$requete.= "WHERE ".$field." = '".$valeur."'";
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			return $res[0]['nombre'];
		}
		return false;
	}

	//----------------------------------------------------------------------
	// Test si la table $table possède un champ $field à la valeur $valeur
	// pour des tuples autres que celui dont la clé $id à la valeur $valeurId
	// Entree :
	//		$table : nom de la table
	//		$field : nom du champ de la table à tester
	//		$valeur : valeur du champ $field recherchée
	//		$id : clé unique de la table
	//		$valeurId : valeur de la clé unique
	// Retour : 
	//		Nombre de fois valeur trouvée (0 .. x)
	//		false : erreur SQL
	//----------------------------------------------------------------------
	static function existValeurAilleurs($table, $field, $valeur, $id, $valeurId, $debug=false)
	{
		$requete = "SELECT COUNT(*) nombre FROM ".$table." ";
		$requete.= "WHERE ".$field." = '".$valeur."' ";
		$requete.= "AND ".$id." != '".$valeurId."' ";
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			return $res[0]['nombre'];
		}
		return false;
	}

	//----------------------------------------------------------------------
	// Récupere un champ $field de la $table pour une un champ $key ayant la valeur $valeur
	// Entree :
	//		$table : nom de la table
	//		$field : nom du champ duquel ramener la valeur pour $key = $valeur
	//		$key : champ sur lequel rechercher la valeur $valeur (doit être une clé unique)
	//		$valeur : valeur du champ $index recherchée
	// Retour : 
	//		valeur du champ $field si trouvé
	//		false : aucune valeur trouvée ou $key n'est pas une clé unique ou erreur SQL
	//----------------------------------------------------------------------
	static function getValeurForKey($table, $field, $key, $valeur, $debug=false)
	{
		$requete = "SELECT ".$field." FROM ".$table." ";
		$requete.= "WHERE ".$key." = '".$valeur."'";
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre == 1) { 
				return $res[0][''.$field.''];
			}
		}
		return false;
	}

	//----------------------------------------------------------------------
	// Swap un champ booléen
	// Si le $champ correspondant au tuple de l'id $id = '0' -> '1'
	// Si le $champ correspondant au tuple de l'id $id = '1' -> '0'
	// Entree :
	//		$table : nom de la table
	//		$champ : nom du champ booléan concerné à swapper
	//		$idField : nom du champ clé (id)
	//		$id : l'id du tuple concerné
	// Retour : 
	//		nombre de modification effectué (forcément 1)
	//		false : erreur SQL
	//----------------------------------------------------------------------
	static function swapBool($table, $champ, $idField, $id, $debug=false)
	{
		$requete = "UPDATE ".$table." ";
		$requete.= "SET ".$champ." = IF(".$champ." = '1', '0', '1') ";
		$requete.= "WHERE ".$idField." = '".$id."';";
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			return $nombre;
		}
		return $res;
	}

	//----------------------------------------------------------------------
	// Update d'un champ unique
	//----------------------------------------------------------------------
	// Entree :
	//		$table : nom de la table
	//		$champ : nom du champ à modifier
	//		$valeur : valeur que doit recevoir $champ
	//		$idField : champ de condition WHERE
	//		$id : valeur de condition WHERE
	// Retour : 
	//		nombre de modification effectuées
	//		false : erreur SQL
	//----------------------------------------------------------------------
	static function updateChamp($table, $champ, $valeur, $idField, $id, $debug=false)
	{
		$requete = "UPDATE ".$table." ";
		$requete.= "SET ".$champ." = '".$valeur."' ";
		$requete.= "WHERE ".$idField." = '".$id."';";
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			return $nombre;
		}
		return $res;
	}

	//************************ METHODES PUBLIQUES ****************************

	//----------------------------------------------------------------------
	// Retourne nombre de tuples d'une table
	// Entree : Rien
	// Retour : false (erreur SQL) / nombre de tuples sinon						
	//----------------------------------------------------------------------
	public function getListeNombre($debug=false) {
		$requete = "SELECT count(".$this->index.") nombre ";
		$requete.= "FROM ".$this->table;
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre != 0) {
				return $res[0]['nombre'];
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
	//		$laListe : liste des articles trouvés
	//		$debug : true (affiche la requete sans l'executer) / false execute la requete
	// Retour :
	//		false (erreur SQL) / nombre articles sinon
	//----------------------------------------------------------------------
	public function getListe($tri, $sens, $start, $nb_lignes, &$laListe, $debug=false) {
		//si le tri est demandé sur plus d'1 champ il faut les concaténer pour que la requete fonctionne correctement)
		//ex si tri est "nom, prenom" il faut écrire "concat(nom, prenom)"
		if (strpos($tri, ',') !== false) $tri = 'concat('.$tri.')';
		$laListe = array();
		$requete = "SELECT ".$this->champs." ";
		$requete.= "FROM ".$this->table." ";
		$requete.= "ORDER BY ".$tri." ".$sens." ";
		$requete.= "LIMIT ".$start.", ".$nb_lignes;
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$laListe = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($laListe !== false) {
			return $nombre;
		}
		$laListe = null;
		return false;
	}

	//----------------------------------------------------------------------
	// Retrouver les infos d'un tuple
	// Entree :
	//		$id : l'id du tuple à charger
	//		$tuple : tuple chargé
	// Retour :
	//		true (trouve) / false (erreur / pas trouve)
	//----------------------------------------------------------------------
	public function get($id, &$tuple, $debug=false)
	{
		$requete = "SELECT ".$this->champs." ";
		$requete.= "FROM ".$this->table." ";
		$requete.= "WHERE ".$this->index." = '".$id."';";
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
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
	//		$chaine : chaine de caractère contenant le code SQL d'ajout des données
	// Retour : 
	//		nombre de tuples insérés si OK
	//		false : erreur SQL
	//----------------------------------------------------------------------
	public function add($chaine, $debug=false) {
		$requete = "INSERT IGNORE INTO ".$this->table." ";
		$requete.= "(".$this->champs.") VALUES (";
		$requete.= $chaine;
		$requete.= ")";
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			return $nombre;
		}
		return $res;
	}

	//----------------------------------------------------------------------
	// Ajout de plusieurs tuples en une seule requete
	// Entree :
	//		$tabDonnees : tableau des données
	// Retour : 
	//		nombre de tuples insérés si OK
	//		false : erreur SQL
	//----------------------------------------------------------------------
	public function addMany($tabDonnees, $debug=false) {
		if (empty($tabDonnees)) return 0;
		$requete = "INSERT IGNORE INTO ".$this->table." ";
		$requete.= "(".$this->champs.") VALUES ";
		foreach ($tabDonnees as $indice => $donnee) {
			$requete.= "(";
			if (is_array($donnee)) {
				foreach ($donnee as $indiceDonnee => $champ) {
					$requete.= "'".$champ."'";
					if ($indiceDonnee < (count($donnee) - 1)) $requete.= ", ";
				}
			}
			else {
				$requete.= "'".$donnee."'";
			}
			$requete.= ")";
			if ($indice < (count($tabDonnees) - 1)) $requete.= ", ";
		}
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			return $nombre;
		}
		return $res;
	}

	//----------------------------------------------------------------------
	// Ajout de plusieurs tuples en plusieurs requetes (Import en masse)
	// Même fonction que addMany sauf que la requete est répétée toutes les
	// $nb_tuple_par_requete lignes de données
	// Entree :
	//		$masqueSql : masque SQL possedant des placeholders à la place des données à insérer
	//		$donnees : tableau des données
	//		$nb_tuple_par_requete : nombre de lignes par requete (defaut 10)
	// Retour : 
	//		nombre de tuples insérés si OK
	//		false : erreur SQL
	//----------------------------------------------------------------------
	// Le principe
	// - Dans $masqueSql donner la requete SQL de remplissage des champs avec pour 
	//		chaque valeur de champ un placeholder de la forme '[numero]' (voir exemples ci-dessous)
	// - Dans $donnees placer les enregistrements à importer. Les champs de chaque enregistrement doivent être 
	//		proposés dans l'ordre des placeholders du masque. Les $données seront automatiquement échappées. 
	//		Il n'est pas besoin de la faire à l'avance.
	// - Definir dans $nb_tuple_par_requete à partir de combien de ligne on execute 
	//		la requete et on en crée une nouvelle
	//----------------------------------------------------------------------
	// Exemple de masque :
	// $masqueSQL = "NULL, '[0]', '[1]', (SELECT id_chose FROM chose WHERE truc = '[2]'), '[3]'" 
	//----------------------------------------------------------------------
	// Cette méthode fonctionne seulement pour des requetes STATIQUES. Elle ne fonctionne pas 
	// pour des requêtes qui varient en fonction des données (genre NULL si valeur 1 et autre si valeur 2)
	//----------------------------------------------------------------------
	function importMany($masqueSql, $donnees, $nb_tuple_par_requete=10, $debug=false) {
		//construction de la liste des placeholders attendus dans le masque SQL
		//on cree 1 placeholder par champ attendu
		$placeholders = array();
		$nbChamps = count($donnees[0]);
		for ($i = 0; $i < $nbChamps; $i++) {
			$placeholders[$i] = '['.$i.']';
		}
		//initialisation du compteur de résultat
		$nombre_entrees = 0;
		//numero de tuple encours (attention ce n'est pas l'indice du tuple dans le tableau de données mais juste un compteur)
		$numTuple = 0;
		foreach($donnees as $enreg) {
			if (mod($numTuple, $nb_tuple_par_requete) == 0) {
				$requete = "INSERT IGNORE INTO ".$this->table." ";
				$requete.= "(".$this->champs.") VALUES ";
			}
			$requete.= "(";
			//remplacement des placeholders du masque SQL par les valeurs disponibles dans $donnees
			//attention, les données sont échappées
			$requete.= str_replace($placeholders, mySqlDataProtect($enreg), $masqueSql);
			$requete.= ")";
			if ($numTuple != count($donnees)- 1) {
				//il existe encore donnée...
				if (mod(($numTuple + 1), $nb_tuple_par_requete) != 0) {
					//on est pas arrivé à la limite $nb_tuple_par_requete tuples à insérer dans la requete -> on ajoute 
					//une virgule pour préparer l'insertion du tuple suivant.
					$requete.= ", ";
				}
				if (mod(($numTuple + 1), $nb_tuple_par_requete) == 0) {
					//on est arrivé à la limite $nb_tuple_par_requete tuples à insérer dans la requete -> on exécute la requete !
					if ($debug) {
						DEBUG_('Requete', $requete); 
					}
					else {
						$res = executeQuery($requete, $nombre, _SQL_MODE_);
						if ($res !== false) {
							$nombre_entrees += $nombre;
						}
						else return false;
					}
				}
			}
			else {
				//il n'y a plus de données... on execute la requete !
				if ($debug) {
					DEBUG_('Requete', $requete); 
				}
				else {
					$res = executeQuery($requete, $nombre, _SQL_MODE_);
					if ($res !== false) {
						$nombre_entrees += $nombre;
					}
					else return false;
				}
			}
			$numTuple++;
		}
		if ($debug) {
			return true;
		}
		return $nombre_entrees;
	}

	//----------------------------------------------------------------------
	// Modifie un tuple
	// Entree :
	//		$id : l'id du tuple à modifier
	//		$chaine : chaine de caractère contenant le code SQL de modification des données
	// Retour : 
	//		nombre de tuples modifiés si modification Ok
	//		false : erreur SQL
	//----------------------------------------------------------------------
	public function update($id, $chaine, $debug=false)
	{
		//ecriture et lancement requete
		$requete = "UPDATE IGNORE ".$this->table." SET ";
		$requete.= $chaine;
		$requete.= "WHERE ".$this->index." = '".$id."'";
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			return $nombre;
		}
		return $res;
	}

	//----------------------------------------------------------------------
	// Supprimer un tuple
	// Entree :
	//		$id : l'id du tuple à supprimer
	// Retour : 
	//		nombre de tuples supprimés si suppression Ok
	//		false : erreur SQL
	//----------------------------------------------------------------------
	public function delete($id, $debug=false)
	{
		$requete = "DELETE IGNORE FROM ".$this->table." ";
		$requete.= "WHERE ".$this->index." = '".$id."'";
		if ($debug) {
			DEBUG_('Requete', $requete); 
			return true;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			return $nombre;
		}
		return $res;
	}

}