<?php
//-----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// Classe de gestion d'une table de référence.
// Elle étend la classe générique de table SqlSimple.
//-----------------------------------------------------------------------
// Methodes disponibles :
//-----------------------------------------------------------------------
// getListeNombre()		: nombre de lignes du listing de la table
// getListe()			: obtenir listing de la table
// get()				: renvoie un tuple recherché
// add()				: //ajoute un tuple
// update()				: modifie un tuple
// delete()				: supprime un tuple
// 19.01.2017
//		Premiere version
// 28.03.2018
//		- Correction de l'appel aux méthodes pour compatiblité PHP 7 (le nombre de paramètre doit 
//		être identique entre la méthode et sa surcharge -> rajout paramètre $debug )
//		- Changement du nom du script en sql_squelette_references.inc.php
//		- Ajout de la fonction sqlSqueletteReference_fillSelectTous()
// 12.11.2019
//		- Modification de l'écriture des champs publiques _table (en table), _index (en index) et _champs (en champ) sans le _ (réservée aux propriétées privées)
// 26.03.2020
//		- Correction syntaxique
//		- Ajout méthode retire et fonction _existeAilleurs
//-----------------------------------------------------------------------
// éè : UTF-8
//-----------------------------------------------------------------------

class SqlSqueletteReference extends SqlSimple {
	public $table	= _PT_.'';						//saisir le nom de la table de référence (ex : "db_reference")
	public $index	= '';							//Saisir ici le champ index unique de la table (ex : "id_tuple")
	public $champs	= '';							//Saisir ici la liste des champs de la table à lister (ex : "id_tuple, libelle, famille")

	public function add($donnees, $debug = false) {
		$requete = "NULL, ";
		//ajouter le code Sql des champs nécéssaires pour l'ajout de données
//		$requete.= "'".$donnees['libelle']."', ";
//		$requete.= "'".$donnees['famille']."'";
		return parent::add($requete, $debug);
	}

	public function update($id, $donnees, $debug = false)
	{
		//ajouter le code Sql des champs nécéssaires pour la modification des données
//		$requete = "libelle = '".$donnees['libelle']."', ";
//		$requete.= "famille = '".$donnees['famille']."' ";
		return parent::update($id, $requete, $debug);
	}

	public function retire($id, $debug=false)
	{
		return SqlSimple::updateChamp($this->table, 'actif', '0', $this->index, $id, $debug);
	}

}

//----------------------------------------------------------------------
// Renvoie le code HTML de construction d'une liste
// Entree :
//		$default : id du tuple à afficher par défaut
// Retour : 
//		code HTML de remplissage de la liste ou '' si erreur SQL
//----------------------------------------------------------------------
function sqlSqueletteReference_fillSelect($defaut)
{
	$texte = '';
	$requete = "SELECT id_tuple, libelle ";
	$requete.= "FROM "._PT_."reference ";
	$requete.= "ORDER BY libelle";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		foreach($res as $ligne) {
			($defaut == $ligne['id_tuple']) ? $selected = ' selected' : $selected = '';
			$texte.= '<option value="'.$ligne['id_tuple'].'"'.$selected.'>'.$ligne['libelle'].'</option>';
		}
	}
	return $texte;
}

//----------------------------------------------------------------------
// Idem en ajoutant 'TOUS' et 'IGNORE'
//----------------------------------------------------------------------
function sqlSqueletteReference_fillSelectTous($defaut)
{
	$texte = '';
	$requete = "SELECT id_tuple, libelle ";
	$requete.= "FROM "._PT_."reference ";
	$requete.= "ORDER BY libelle";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		($defaut == UniversalListColonne::CMP_ALL) ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="'.UniversalListColonne::CMP_ALL.'"'.$selected.'>'.getLib('TOUS').'</option>';
		($defaut == UniversalListColonne::CMP_IGN) ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="'.UniversalListColonne::CMP_IGN.'"'.$selected.'>'.getLib('IGNORE').'</option>';
		foreach($res as $ligne) {
			($defaut == $ligne['id_tuple']) ? $selected = ' selected' : $selected = '';
			$texte.= '<option value="'.$ligne['id_tuple'].'"'.$selected.'>'.$ligne['libelle'].'</option>';
		}
	}
	return $texte;
}

//----------------------------------------------------------------------
// Vérifie la présence d'une clé dans les tables de la base de données
//----------------------------------------------------------------------
// Entree :
//		$id : id de la clé recherchée
//		$lesTables : tableau contenant le nombre de fois où la clé est rencontrée par table cible
//			ainsi que les mnémoniques (singulier et pluriel) de traduction des endroits (en clair) où elle est rencontré
//		Array
//		(
//			[0] => Array (
//		            [nb] => nombre de références trouvées
//				    [table] => nom_table_1
//		            [MNEMONIQUE_SINGULIER_TABLE_1] => MNEMONIQUE_SINGULIER_TABLE_1
//				    [MNEMONIQUE_PLURIEL_TABLE_1] => MNEMONIQUE_PLURIEL_TABLE_1
//				)
//			[1] => Array (
//		            [nb] => nombre de références trouvées
//				    [table] => nom_table_2
//		            [MNEMONIQUE_SINGULIER_TABLE_1] => MNEMONIQUE_SINGULIER_TABLE_2
//				    [MNEMONIQUE_PLURIEL_TABLE_1] => MNEMONIQUE_PLURIEL_TABLE_2
//				)
//			[1] => Array (
//		            [nb] => nombre de références trouvées
//				    [table] => nom_table_3
//		            [MNEMONIQUE_SINGULIER_TABLE_1] => MNEMONIQUE_SINGULIER_TABLE_3
//				    [MNEMONIQUE_PLURIEL_TABLE_1] => MNEMONIQUE_PLURIEL_TABLE_3
//				)
//		)
// Retour :
//		- false si erreur SQL
//		- 0 si aucune correspondance trouvée
//		- nombre de correspondances trouvée sur la première tables positive
//----------------------------------------------------------------------
function sqlSqueletteReference_existeAilleurs($id, &$lesTables)
{
	$lesTables = array();
	$requete = "SELECT count(id_cle_table_1) nb, 'nom_table_1', 'MNEMONIQUE_SINGULIER_TABLE_1', 'MNEMONIQUE_PLURIEL_TABLE_1' FROM "._PT_."table_1 WHERE id_cle_table_1 = '".$id."' ";
	$requete.= "UNION ALL ";
	$requete.= "SELECT count(id_cle_table_1) nb, 'nom_table_2', 'MNEMONIQUE_SINGULIER_TABLE_2', 'MNEMONIQUE_PLURIEL_TABLE_2' FROM "._PT_."table_2 WHERE id_cle_table_2 = '".$id."' ";
	$requete.= "UNION ALL ";
	$requete.= "SELECT count(id_cle_table_1) nb, 'nom_table_3', 'MNEMONIQUE_SINGULIER_TABLE_3', 'MNEMONIQUE_PLURIEL_TABLE_3' FROM "._PT_."table_3 WHERE id_cle_table_3 = '".$id."' ";
	$lesTables = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($lesTables !== false) {
		$total = 0;
		foreach($lesTables as $ligne) {
			if ($ligne['nb'] > 0) $total+= $ligne['nb'];
		}
		return $total;
	}
	return false;
}