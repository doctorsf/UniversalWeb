<?php
//-----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// éè : UTF-8
//-----------------------------------------------------------------------

class Params {

	static function load() {
		$laListe = array();
		$requete = "SELECT parametre, valeur, type, min, max FROM "._PT_."params ORDER BY id";
		//Mode silencieux obligatoire pour cette requete puisque le parametre _SQL_MODE_ va etre récupéré avec (donc encore inconnu !)
		$liste = executeQuery($requete, $nombre, SQL_MODE_SILENT);
		if ($liste !== false) {
			foreach($liste as $param) {
				if (($param['type'] == 'boolean') && ($param['min'] == '') && ($param['max'] == '')) {
					//on force la prise en compte booléène (true / false)
					defined($param['parametre']) || define($param['parametre'],	(bool)$param['valeur']);
					//DEBUG_($param['parametre'].' (boolean)', $param['valeur']);
				}
				else {
					defined($param['parametre']) || define($param['parametre'],	$param['valeur']);
					//DEBUG_($param['parametre'], $param['valeur']);
				}
			}
		}
		else die('Le chargement des paramètres a échoué');
	}

}