<?php
//-----------------------------------------------------------------------
// Auteur : Fabrice Labrousse
// éè : UTF-8
//-----------------------------------------------------------------------
// Explication des champs : 
// id : identifiant unique du tuple (AUTO_INCREMENT)
// parametre : chaine représentant le paramètre directement utilisable dans le code
// ordre : ordre d'affichage du paramètre
// valeur : valeur donnée au paramètre
// reglable : booleen. Si true, il s'agit d'un reglage de l'application. le paramètre 
//		peut être mis à disposition du webmaster pour régler l'application
// type : type de paramètre utilisé pour façonner son champ de saisie (text*, number, boolean, date)
// libelle : libellé afficher pour façonner le champ de saisie (Peut être un mnémonique)
// min : valeur minimale de la saisie pour un paramètre "number"
// max : valeur maximale de la saisie pour un paramètre "number"
// step : pas de modification de la saisie pour un paramètre "number"
// comment : commentaire explicatif sur le role du paramètre
//-----------------------------------------------------------------------

//- CREATION table params ----------------------------------------------
function sqlParams_createTableParams() {	
	$requete = "CREATE TABLE IF NOT EXISTS "._PT_."params (";
	$requete.= "id int(10) UNSIGNED NOT NULL AUTO_INCREMENT, ";
	$requete.= "ordre tinyint(3) UNSIGNED NOT NULL DEFAULT '0', ";
	$requete.= "parametre varchar(32) NOT NULL, ";
	$requete.= "valeur varchar(255) NOT NULL, ";
	$requete.= "reglable tinyint(1) UNSIGNED NOT NULL DEFAULT '0', ";
	$requete.= "type varchar(10) NOT NULL DEFAULT 'text', ";
	$requete.= "libelle varchar(64) NOT NULL, ";
	$requete.= "min varchar(32) DEFAULT NULL, ";
	$requete.= "max varchar(32) DEFAULT NULL, ";
	$requete.= "step varchar(32) DEFAULT NULL, ";
	$requete.= "comment varchar(255) NOT NULL, ";
	$requete.= "PRIMARY KEY (id)";
	$requete.= ") ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;";
	$res = executeQuery($requete, $nombre, _SQL_MODE_);
	if ($res !== false) {
		//Contenu minimum de la table `params`
		$requete = "INSERT INTO "._PT_."params (id, ordre, parametre, valeur, reglable, `type`, libelle, `min`, `max`, step, `comment`) VALUES ";
		$requete.= "(1, 1, '_PARAM_EMAIL_WEBMASTER_', 'webmaster@application.com', 1, 'text', 'WEBMASTER_EMAIL', NULL, NULL, NULL, 'WEBMASTER_EMAIL_HELP')";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
	}
}

class SqlParams extends SqlSimple {
	public $table	= _PT_.'params';
	public $index	= 'id';
	public $champs	= 'id, ordre, parametre, valeur, reglable, type, libelle, min, max, step, comment';

	//-------------------------------------------------------
	// Modifie l'ordre d'affichage des paramètres (champ 'ordre')
	// Entrée : 
	//		$source : n° d'ordre actuel du paramètre à réordonner
	//		$cible : n° d'ordre final à donner au paramètre
	// Retour :
	//		true / false
	//-------------------------------------------------------
	static function paramSort($source, $cible) {
		$requete = "SELECT id, parametre, ordre FROM "._PT_."params";
		$origine = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res === false) return $false;
		$parametres = array_flip_key($origine, 'ordre');

		//DEBUG_('parametres', $parametres);
		//placer $source après $cible
		$mem = $parametres[$cible]['ordre'] * 10 + 5;
		//DEBUG_('mem', $mem); 
		//renummérotation de l'ordre de tous les groupes en multipliant par 10
		foreach($parametres as $id => $dummy) {
			$parametres[$id]['ordre'] = $parametres[$id]['ordre'] * 10;
		}
		//DEBUG_('parametres', $parametres);
		//insertion de la source à la position memorisée (mem)
		$parametres[$source]['ordre'] = $mem;
		//DEBUG_('parametres', $parametres);
		//renummérotation des ordres de 1 en 1
		$parametres = array_sort($parametres, 'ordre');
		$compteur = 1;
		foreach($parametres as $id => $dummy) {
			$parametres[$id]['ordre'] = $compteur++;
		}
		//DEBUG_('parametres', $parametres);
		//réécriture dans la base de données
		$requetes = '';
		foreach($parametres as $id => $parametre) {
			if ($id != $parametres[$id]['ordre']) {
				$requetes.= "UPDATE IGNORE "._PT_."params SET ordre = '".$parametre['ordre']."' WHERE id = '".$parametre['id']."';";
			}
		}
		//DEBUG_('requetes', $requetes);
		$res = executeQuery($requetes, $nombre, _SQL_MODE_);
		if ($res) riseMessage(getLib('MODIFICATION_PRISE_EN_COMPTE'));
		return $res;
	}

	public function add($donnees, $debug = false) {
		$requete = "NULL, ";
		$requete.= "0, ";
		$requete.= "'".$donnees['parametre']."', ";
		$requete.= "'".$donnees['valeur']."', ";
		$requete.= "'".$donnees['reglable']."', ";
		$requete.= "'".$donnees['type']."', ";
		$requete.= "'".$donnees['libelle']."', ";
		($donnees['min'] == '') ?  $requete.= 'NULL, ' : $requete.= "'".$donnees['min']."', ";
		($donnees['max'] == '') ?  $requete.= 'NULL, ' : $requete.= "'".$donnees['max']."', ";
		($donnees['step'] == '') ?  $requete.= 'NULL, ' : $requete.= "'".$donnees['step']."', ";
		$requete.= "'".$donnees['comment']."'";
		return parent::add($requete, $debug);
	}

	public function update($id, $donnees, $debug = false) {
		$requete = "ordre = '".$donnees['ordre']."', ";
		$requete.= "parametre = '".$donnees['parametre']."', ";
		$requete.= "valeur = '".$donnees['valeur']."', ";
		$requete.= "reglable = '".$donnees['reglable']."', ";
		$requete.= "type = '".$donnees['type']."', ";
		$requete.= "libelle = '".$donnees['libelle']."', ";
		($donnees['min'] == '') ?  $requete.= 'min = NULL, ' : $requete.= "min = '".$donnees['min']."', ";
		($donnees['max'] == '') ?  $requete.= 'max = NULL, ' : $requete.= "max = '".$donnees['max']."', ";
		($donnees['step'] == '') ?  $requete.= 'step = NULL, ' : $requete.= "step = '".$donnees['step']."', ";
		$requete.= "comment = '".$donnees['comment']."'";
		return parent::update($id, $requete, $debug);
	}

}