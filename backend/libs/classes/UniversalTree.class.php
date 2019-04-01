<?php
//==============================================================
// Classe UniversalTree
//--------------------------------------------------------------
// Traitement d'arbres hiérarchiques. S'appuie sur la méthode des intervalles
// Adapté par Fabrice Labrousse
// http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
// Version 1.0.0 du 01.02.2018
//==============================================================

class UniversalTree {

	private $_table = '';		//nom de la table dans la base de données
	private $_key = '';			//nom du champ (à clé unique) de l'élément de l'arbre

	const VERSION = 'v1.0.0 (2018-02-01)';

	//----------------------------------------------
	// récupère les valeurs gauche, droite et niveau d'un élément
	// Entree : 
	//		$item : id de l'élément à recherche
	//		&$gauche : valeur gauche de l'élément
	//		&$droite : valeur droite de l'élément
	//		&$niveau : niveau hiérarchique de l'élément
	// Retour :
	//		charge les variables par adresse $gauche, $droite et $niveau
	//----------------------------------------------
	private function _getGaucheDroite($item, &$gauche, &$droite, &$niveau) {
		$requete = "SELECT gauche, droite, niveau ";
		$requete.= "FROM ".$this->_table." ";
		$requete.= "WHERE ".$this->_key." = ".$item;
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre > 0) {
				$gauche = $res[0]['gauche'];
				$droite = $res[0]['droite'];
				$niveau = $res[0]['niveau'];
				return true;
			}
			else {
				$gauche = 0;
				$droite = 0;
				$niveau = 0;
			}
		}
		return false;
	}

	//----------------------------------------------
	// Setters
	//----------------------------------------------
	public function setTable($table) {$this->_table = $table;}
	public function setKey($key) {$this->_key = $key;}

	//----------------------------------------------
	// Crée la table SQL
	// Entree : Rien
	// Retour : true / false
	//----------------------------------------------
	public function createTable() {
		if (($this->_table == '') || ($this->_key == '')) die('Classe '.get_class($this).' : Le nom de la table SQL et sa clé unique n\'ont pas été créées');
		$requete = "CREATE TABLE IF NOT EXISTS ".$this->_table." (";
		$requete.= $this->_key." int(10) UNSIGNED NOT NULL, ";
		$requete.= "gauche int(10) UNSIGNED NOT NULL, ";
		$requete.= "droite int(10) UNSIGNED NOT NULL, ";
		$requete.= "niveau int(10) UNSIGNED NOT NULL DEFAULT '0', ";
		$requete.= "PRIMARY KEY (".$this->_key."), ";
		$requete.= "UNIQUE KEY gauche (gauche) USING BTREE, ";
		$requete.= "UNIQUE KEY droite (droite)";
		$requete.= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		return executeQuery($requete, $nombre, _SQL_MODE_);
	}

	//----------------------------------------------
	// Vide la table de l'arbre
	// Entree : Rien
	// Retour : true / false
	//----------------------------------------------
	public function drop() {
		$requete = "TRUNCATE ".$this->_table;
		return executeQuery($requete, $nombre, _SQL_MODE_);
	}

	//----------------------------------------------
	// Récupérer le noeud racine
	// Entree : Rien
	// Retour : Noeud racine
	//----------------------------------------------
	public function getRoot() {
		$requete = "SELECT * ";
		$requete.= "FROM ".$this->_table." ";
		$requete.= "WHERE gauche = 1";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre == 1) {
				return $res;
			}
			else {
				return array();
			}
		}
		return false;
	}

	//----------------------------------------------
	// Récupérer toutes les feuilles de l'arbre
	// Entree : 
	//		$id_node : id du noeud à partir duquel récupérer les feuilles
	// Retour : 
	//		tableau des feuilles
	//----------------------------------------------
	public function getLeaves($id_node=null) {
		$requete = "SELECT * ";
		$requete.= "FROM ".$this->_table." ";
		$requete.= "WHERE droite - gauche = 1 ";
		if ($id_node !== null) {
			$this->_getGaucheDroite($id_node, $gauche, $droite, $niveau);
			$requete.= "AND gauche > ".$gauche." ";
			$requete.= "AND droite < ".$droite;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre > 0) {
				return $res;
			}
			else {
				return array();
			}
		}
		return false;
	}

	//----------------------------------------------
	// Récupérer toutes les noeuds de l'arbre
	// Entree : 
	//		$id_node : id du noeud à partir duquel récupérer les noeuds enfants
	// Retour : 
	//		tableau des noeuds
	//----------------------------------------------
	public function getNodes($id_node=null) {
		$requete = "SELECT * ";
		$requete.= "FROM ".$this->_table." ";
		$requete.= "WHERE droite - gauche > 1 ";
		if ($id_node !== null) {
			$this->_getGaucheDroite($id_node, $gauche, $droite, $niveau);
			$requete.= "AND gauche > ".$gauche." ";
			$requete.= "AND droite < ".$droite;
		}
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre > 0) {
				return $res;
			}
			else {
				return array();
			}
		}
		return false;
	}

	//----------------------------------------------
	// Récupérer un arbre ou sous-arbre
	// Entree : 
	//		$id_node : id du noeud à partir duquel récupérer l'arborescence
	//		$nodeToo : booléen (true ->ramener aussi le noeud parent, false -> ne pas ramener le noeud parent)
	//		byLevel : (booleen) 
	//			true -> affichage des noeuds par ordre de niveau
	//			false (défaut) -> affichage des noeuds par ordre hiérarchique
	// Retour : 
	//		tableau des noeuds
	//----------------------------------------------
	// NOTA 1 
	//	Pour un affichage par niveau on pourrait aussi utiliser la requete suivante
	//	SELECT node.libelle, node.niveau
	//	FROM table AS node, table AS parent 
	//	WHERE node.gauche BETWEEN parent.gauche AND parent.droite 
	//	AND parent.gauche = 1 
	//----------------------------------------------
	// NOTA 2 
	//	Pour un affichage hiérarchique on pourrait aussi utiliser la requete suivante
	//	SELECT node.libelle, node.niveau
	//	FROM table AS node, table AS parent 
	//	WHERE node.gauche BETWEEN parent.gauche AND parent.droite 
	//	AND parent.gauche = 1 
	//	ORDER BY node.gauche
	//----------------------------------------------
	public function getTree($id_node, $nodeToo, $byLevel=false) {
		($nodeToo) ? $operateur = "=" : $operateur = "";
		$requete = "SELECT * ";
		$requete.= "FROM ".$this->_table." ";
		$requete.= "WHERE 1 ";
		if (!empty($id_node)) {
			$this->_getGaucheDroite($id_node, $gauche, $droite, $niveau);
			$requete.= "AND gauche >".$operateur." ".$gauche." ";
			$requete.= "AND droite <".$operateur." ".$droite." ";
		}
		($byLevel) ? $requete.= " ORDER BY niveau" : $requete.= " ORDER BY gauche";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre > 0) {
				return $res;
			}
			else {
				return array();
			}
		}
		return false;
	}

	//----------------------------------------------
	// Récupère toutes les clés d'un éléments et de ses enfants
	// Entrée : 
	//		$id : id de l'élément choisi
	// Retour : 
	//		false erreur SQL sinon la liste des éléments (peut être vide)
	//----------------------------------------------
	public function getTreeKeys($id) {
		//on récupére gauche et droite de l'élément à supprimer
		$this->_getGaucheDroite($id, $gauche, $droite, $niveau);
		$requete = "SELECT ".$this->_key." ";
		$requete.= "FROM ".$this->_table." ";
		$requete.= "WHERE gauche BETWEEN ".$gauche." AND ".$droite." ";
		$requete.= "ORDER BY ".$this->_key." ASC";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre > 0) {
				return $res;
			}
			else {
				return array();
			}
		}
		return false;
	}

	//----------------------------------------------
	// Récupère toutes les clés d'un éléments et de ses enfants
	// Idem que getTreeKeys() mais renvoie juste la liste des clés 
	// et non des enregistrements
	// Entrée : 
	//		$id : id de l'élément choisi
	// Retour : 
	//		false erreur SQL sinon la liste des éléments (peut être vide)
	//----------------------------------------------
	public function getTreeKeysList($id) {
		$laListe = array();
		$res = $this->getTreeKeys($id);
		if ($res !== false) {
			foreach ($res as $entree) {
				$laListe[] = $entree[$this->_key];
			}
			return implode(',', $laListe);
		}
		return false;
	}

	//----------------------------------------------
	// Récupérer le complément (le reste) d'un arbre ou sous-arbre
	// Entree : 
	//		$id_node : id du noeud à partir duquel récupérer le complément
	//		$nodeToo : booléen (true ->ramener aussi le noeud parent, false -> ne pas ramener le noeud parent)
	//		byLevel : (booleen) 
	//			true -> affichage des noeuds par ordre de niveau
	//			false (défaut) -> affichage des noeuds par ordre hiérarchique
	// Retour : 
	//		tableau des noeuds
	//----------------------------------------------
	public function getTreeBarre($id_node, $nodeToo, $byLevel=false) {
		if (empty($id_node)) return array();
		($nodeToo) ? $operateur = "=" : $operateur = "";
		$requete = "SELECT * ";
		$requete.= "FROM ".$this->_table." ";
		if ($id_node !== null) {
			$this->_getGaucheDroite($id_node, $gauche, $droite, $niveau);
			$requete.= "WHERE droite >".$operateur." ".$droite;
		}
		($byLevel) ? $requete.= " ORDER BY niveau" : $requete.= " ORDER BY gauche";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre > 0) {
				return $res;
			}
			else {
				return array();
			}
		}
		return false;
	}

	//----------------------------------------------
	// Récupérer toute la parentée de l'arbre
	// Entree : 
	//		$id_node : id du noeud à partir duquel récupérer les noeuds parents
	// Retour : 
	//		tableau des noeuds
	//----------------------------------------------
	public function getParents($id_node=0, $nodeToo=false) {
		($nodeToo) ? $operateur = "=" : $operateur = "";
		$requete = "SELECT * ";
		$requete.= "FROM ".$this->_table." ";
		$requete.= "WHERE 1 ";
		$this->_getGaucheDroite($id_node, $gauche, $droite, $niveau);
		$requete.= "AND gauche <".$operateur." ".$gauche." ";
		$requete.= "AND droite >".$operateur." ".$droite;
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre > 0) {
				return $res;
			}
			else {
				return array();
			}
		}
		return false;
	}

	//----------------------------------------------
	// Ajoute une feuille à l'arbre
	// Entree : 
	//		$id_previous_leave : id de la feuille précédente (de gauche)
	//		$infos : tableau des informations de la feuille à entrer
	// Retour : 
	//		true si tout s'est bien passé, false sinon
	//----------------------------------------------
	// NOTA : Il faut utiliser addLeave() pour créer la racine de l'arbre et non addNode()
	//----------------------------------------------
	public function addLeave($id_previous_leave, $infos) {
		//test si la nouvelle catégorie n'existe pas déjà... car si on le fait pas on risque avoir tout chamboulé les gauche et droite pour rien
		//on pourrait utiliser les transactions, mais il faut pour ceci que le moteur de MysQL soit innoDb ... ce qui n'est pas forcément le cas
		//donc on fait la vérif en amont
		$requete = "SELECT ".$this->_key." FROM ".$this->_table." WHERE ".$this->_key." = ".$infos[$this->_key];
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($nombre == 1) return false;
		//on récupére le gauche et droite de la feuille précédente (à gauche de l'insertion)
		$this->_getGaucheDroite($id_previous_leave, $gauche, $droite, $niveau);
		//DECALLAGE DES FEUILLES ET NOEUDS
		//mise à jour des valeurs droites à décaller
		$requete = "UPDATE ".$this->_table." SET droite = droite + 2 WHERE droite > ".$droite." ORDER BY droite DESC";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return false;
		//mise à jour des valeurs gauches à décaller
		$requete = "UPDATE ".$this->_table." SET gauche = gauche + 2 WHERE gauche > ".$droite." ORDER BY gauche DESC";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return false;
		//insertion de la nouvelle feuille
		$requete = "INSERT IGNORE INTO ".$this->_table." ";
		$requete.= "(gauche, droite, niveau, ";
		$requete.= implode(', ', array_keys($infos));
		$requete.= ") VALUES (";
		$requete.= ($droite + 1).", ";
		$requete.= ($droite + 2).", ";
		$requete.= $niveau.", ";
		$requete.= "'".implode("', '", array_values($infos))."'";
		$requete.= ")";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return false;
		return true;
	}

	//----------------------------------------------
	// Ajoute un noeud (descendant) à l'arbre
	// Entree : 
	//		$id_parent_node : id du noeud parent
	//		$infos : tableau des informations du noeud à entrer
	// Retour : 
	//		true si tout s'est bien passé, false sinon
	//----------------------------------------------
	public function addNode($id_parent_node, $infos) {
		//test si la nouvelle catégorie n'existe pas déjà... car si on le fait pas on risque avoir tout chamboulé les gauche et droite pour rien
		//on pourrait utiliser les transactions, mais il faut pour ceci que le moteur de MysQL soit innoDb ... ce qui n'est pas forcément le cas
		//donc on fait la vérif en amont
		$requete = "SELECT ".$this->_key." FROM ".$this->_table." WHERE ".$this->_key." = ".$infos[$this->_key];
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($nombre == 1) return false;
		//on récupére le gauche et droite du noeud parent (au-dessus de l'insertion)
		$this->_getGaucheDroite($id_parent_node, $gauche, $droite, $niveau);
		//DECALLAGE DES FEUILLES ET NOEUDS
		//mise à jour des valeurs gauches à décaller
		$requete = "UPDATE ".$this->_table." SET gauche = gauche + 1 WHERE gauche > ".$gauche." ORDER BY gauche DESC";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return false;
		$requete = "UPDATE ".$this->_table." SET gauche = gauche + 1 WHERE gauche > ".$droite." ORDER BY gauche DESC";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return false;
		//mise à jour des valeurs droites à décaller
		$requete = "UPDATE ".$this->_table." SET droite = droite + 1 WHERE droite > ".$gauche." ORDER BY droite DESC";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return false;
		//mise à jour des valeurs droites à décaler
		$requete = "UPDATE ".$this->_table." SET droite = droite + 1 WHERE droite > ".$droite." ORDER BY droite DESC";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return false;
		//insertion de la nouvelle feuille
		$requete = "INSERT IGNORE INTO ".$this->_table." ";
		$requete.= "(gauche, droite, niveau, ";
		$requete.= implode(', ', array_keys($infos));
		$requete.= ") VALUES (";
		$requete.= ($gauche + 1).", ";
		$requete.= ($droite + 1).", ";
		$requete.= ($niveau + 1).", ";
		$requete.= "'".implode("', '", array_values($infos))."'";
		$requete.= ")";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return false;
		return true;
	}


	//----------------------------------------------
	// Supprime un élément de l'arbre
	// Entree : 
	//		$id : id de l'élément à supprimer
	//		$delChildren : 
	//			true (defaut) -> suppression aussi des éléments enfants s'il y en a
	//			false -> conservation des éléments enfants s'il y en a
	// Retour : 
	//		true si tout s'est bien passé, false sinon
	//----------------------------------------------
	// NOTA : Si un élément supprimé contient un sous-arbre, les sous-niveaux 
	// sont automatiquement surélevés de 1
	//----------------------------------------------
	public function delItem($id, $delChildren=true) {
		//on récupére gauche et droite de l'élément à supprimer
		$this->_getGaucheDroite($id, $gauche, $droite, $niveau);
		$width = $droite - $gauche + 1;
		if ($delChildren) {
			//suppression du noeud et de toutes les feuilles de l'arbre enfant s'il y en a
			$requete = "DELETE FROM ".$this->_table." WHERE gauche BETWEEN ".$gauche." AND ".$droite;
			$res = executeQuery($requete, $nombre, _SQL_MODE_);
			if ($res === false) return false;
		}
		else {
			//suppression de l'élément seulement (pas de son éventuel sous-arbre)
			$requete = "DELETE FROM ".$this->_table." WHERE gauche = ".$gauche;
			$res = executeQuery($requete, $nombre, _SQL_MODE_);
			if ($res === false) return false;
			//mise à jour des valeurs droites et gauches du sous-arbre enfant s'il existe
			$requete = "UPDATE ".$this->_table." SET droite = droite - 1 WHERE droite BETWEEN ".$gauche." AND ".$droite." ORDER BY droite ASC";
			$res = executeQuery($requete, $nombre, _SQL_MODE_);
			if ($res === false) return false;
			$requete = "UPDATE ".$this->_table." SET gauche = gauche - 1 WHERE gauche BETWEEN ".$gauche." AND ".$droite." ORDER BY gauche ASC";
			$res = executeQuery($requete, $nombre, _SQL_MODE_);
			if ($res === false) return false;
			//mise à jour des niveaux pour les éléments d'un éventuels sous-arbre
			$requete = "UPDATE ".$this->_table." SET niveau = niveau - 1 WHERE gauche BETWEEN ".$gauche." AND ".$droite;
			$res = executeQuery($requete, $nombre, _SQL_MODE_);
			if ($res === false) return false;
			$width = 2;
		}
		//mise à jour des valeurs droites à décaler
		$requete = "UPDATE ".$this->_table." SET droite = droite - ".$width." WHERE droite > ".$droite." ORDER BY droite ASC";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return false;
		//mise à jour des valeurs gauches à décaler
		$requete = "UPDATE ".$this->_table." SET gauche = gauche - ".$width." WHERE gauche > ".$droite." ORDER BY gauche ASC";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return false;
		return true;
	}

	//----------------------------------------------
	// Affichage de debuggage du contenu de l'arbre
	// Entree : 
	//		$libelle : libellé libre à afficher
	//		$tableau : tableau d'éléments (feuilles et noeuds) de l'arbre
	// Retour : écriture sur la sortie standard
	//----------------------------------------------
	// Cette méthode permet de débugger mais il faut la surcharger
	// par une classe héritéepour afficher les contenus des feuilles
	// car cette classe étant standard (elle ne fait que gérer les arbres
	// hiérarchisés), elle ne contient aucune valeur applicatives.
	//----------------------------------------------
	public function display($libelle, $tableau) {
		echo '<p><u>'.$libelle.'</u><br />';
		foreach($tableau as $element) {
			for($i = 0; $i < $element['niveau']; $i++) echo '&hellip;';
			echo ' '.$element[$this->_key].' ('.$element[$this->_key].')<br />';
		}
		echo '</p>';
	}

}