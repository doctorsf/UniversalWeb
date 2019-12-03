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
//-----------------------------------------------------------------------
// éè : UTF-8
//-----------------------------------------------------------------------

class sqlSqueletteReference extends SqlSimple {
	public $table	= '';						//saisir le nom de la table de référence (ex : "db_reference")
	public $index	= '';						//Saisir ici le champ index unique de la table (ex : "id_tuple")
	public $champs	= '';						//Saisir ici la liste des champs de la table à lister (ex : "id_tuple, libelle, famille")

	public function add($donnees, $debug = false) {
		$requete = "NULL, ";
		//ajouter le code Sql des champs nécéssaires pour l'ajout de données
//		$requete.= "'".$donnees['libelle']."', ";
//		$requete.= "'".$donnees['famille']."' ";
		return parent::add($requete, $debug);
	}

	public function update($id, $donnees, $debug = false)
	{
		//ajouter le code Sql des champs nécéssaires pour la modification des données
//		$requete = "libelle = '".$donnees['libelle']."', ";
//		$requete.= "famille = '".$donnees['famille']."' ";
		return parent::update($id, $requete, $debug);
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
	$requete.= "FROM "._PREFIXE_TABLES_."reference ";
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
	$requete.= "FROM "._PREFIXE_TABLES_."reference ";
	$requete.= "ORDER BY libelle";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		($defaut == 'TOUS') ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="TOUS"'.$selected.'>TOUS</option>';
		($defaut == 'IGNORE') ? $selected = ' selected' : $selected = '';
		$texte.= '<option value="IGNORE"'.$selected.'>IGNORE</option>';
		foreach($res as $ligne) {
			($defaut == $ligne['id_tuple']) ? $selected = ' selected' : $selected = '';
			$texte.= '<option value="'.$ligne['id_tuple'].'"'.$selected.'>'.$ligne['libelle'].'</option>';
		}
	}
	return $texte;
}