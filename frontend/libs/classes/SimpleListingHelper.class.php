<?php
//--------------------------------------------------------------------------
// Classe SimpleListingHelper
// Rassemble des outils pour la création rapide de listings basique
// Les méthode de cette classe sont la plupart du temps accédées de manière statique
//--------------------------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
//--------------------------------------------------------------------------
// 05.04.2018
//		Rend les classe statiques car version PHP 7.1.8 est stricte et évite ainsi 
//		ce genre de message : Deprecated: Non-static method SimpleListingHelper::getParams() should not be called statically 
// 07.03.2019
//		Changement de paramètres pour la méthode getParams()
//		On passe maintenant l'identifiant de la colonne par défaut et non pas la liste des champs triables
//		On ajoute aussi un die() si la colonne par défaut n'existe pas
// 14.03.2019
//		Ajout du paramètre "header" dans la construction de la colonne de la table. true (la colonne est une entete pour la ligne), false sinon (valeur par défaut)
//			ce paramètre indique si la donnée de la colonne doit servir d'entête pour la ligne.
//		Ajout du paramètre $css à la methode drawHead (personnalisation de l'entête)
// 17.05.2019
//		- Correction manque </th> méthode drawHead
//		- Ajout du paramètre 'css' aux colonnes
// 04.12.2019
//		- Appliqué le CSS aux tags <th> et <td> (drawBody)
//		- Viré les propriétés HTML "width" et "align" depréciés en HTML 5 et remplacé par du CSS
//--------------------------------------------------------------------------

class SimpleListingHelper {

	//--------------------------------------------------------------------------
	// Création d'une colonne de listing. Cette fonction permet d'initialiser les
	// données non fournies d'une colonne de listing (pour lecture plus facile dans les scripts "listings")
	// Entree : tableau des informations connues de la colonne
	// Sortie : tableau complet de toutes les informations nécessaires à la colonne
	//--------------------------------------------------------------------------
	public static function createCol($tableau) {
		if (!isset($tableau['name'])) $tableau['name'] = 'Nom Colonne ?';					//nom de la colonne obligatoire
		if (!isset($tableau['size'])) $tableau['size'] = '10';								//taille en % (10 si non renseigné)
		if (!isset($tableau['align'])) $tableau['align'] = 'left';							//alignement de la donnée (left / center / right)(left par féfaut)
		if (!isset($tableau['tri'])) $tableau['tri'] = '';									//si la colonne est triée, donner le champ de la base de données concerné
		if (!isset($tableau['sens'])) $tableau['sens'] = '';								//sens du tri
		if (($tableau['tri'] != '') && ($tableau['sens'] == '')) $tableau['sens'] = 'ASC';	//sens du tri souhaité par défaut (ASC par defaut) si tri demandé
		if (!isset($tableau['title'])) $tableau['title'] = '';								//info bulle sur la colonne (vide par défaut)
		if (!isset($tableau['header'])) $tableau['header'] = false;							//booléen indique si la colonne contient l'information d'entete pour la ligne (scope="row")
		if (!isset($tableau['css'])) $tableau['css'] = '';									//css du libellé de la colonne
		return $tableau;
	}

	//-------------------------------------
	// Renvoie la taille totale du tableau en %
	//-------------------------------------
	public static function getSize($tableau) {
		$total = 0;
		foreach($tableau as $colonne) {
			$total+= $colonne['size'];
		}
		return $total;
	}

	public static function sizeAlarm($tableau) {
		$taille = self::getSize($tableau);
		if (($taille < 100) || ($taille > 101)) {
			DEBUG_('Taille du listing', $taille);
		}
	}

	//--------------------------------------------------------------------------
	// gere tous les paramètres de gestion d'un listing (sens, tri, page, etc)
	// Entree : 
	//		$colDefaut : identifiant de la colonne par défaut utilisée pour le premier tri
	//		$cols : le tableau des colonnes du listing (MODIFIE EN RETOUR)
	//		$page : la page en cours (MODIFIE EN RETOUR)
	//		$tri : le tri en cours (MODIFIE EN RETOUR)
	//		$sens : le sens encours (MODIFIE EN RETOUR)
	// Retour
	//		Les champs $cols, $page, tri et sens passés en paramètres sont modifiés
	//--------------------------------------------------------------------------
	// Modifiaction de la méthode le 07.03.2019
	// On passe maintenant l'identifiant de la colonne par défaut et non pas la liste des champs triables
	// On ajoute aussi un die()	si la colonne par défaut n'existe pas
	//--------------------------------------------------------------------------
	public static function getParams($colDefaut, &$cols, &$page, &$tri, &$sens)
	{	
		//si la colonne par défaut n'a pas été renseignée, erreur
		if (!isset($cols[$colDefaut])) die('SimpleListingHelper::getParams -> Default column does not exist');

		//construction de la liste des tris possibles
		$choixTri = array_column($cols, 'tri');

		//recuperation page encours
		(isset($_GET['page'])) ? $page = MySQLDataProtect($_GET['page']) : $page = 1;
		if (!preg_match(PAGEREGEX, $page)) $page = 1;

		//recuperation du tri souhaité
		(isset($_GET['tri'])) ? $tri = MySQLDataProtect($_GET['tri']) : $tri = $cols[$colDefaut]['tri'];
		if (!in_array($tri, $choixTri)) $tri = $cols[$colDefaut]['tri'];

		//recuperation du sens d'affichage souhaité ascendant (ASC) ou descendant (DESC)
		$choixSens = array('ASC', 'DESC');
		(isset($_GET['sens'])) ? $sens = MySQLDataProtect($_GET['sens']) : $sens = $cols[$colDefaut]['sens'];
		if (!in_array($sens, $choixSens)) $sens = 'ASC';

		//swap du sens d'affichage proposé sur la colonne de tri choisie
		foreach($cols as $indice => $colonne) {
			if ($colonne['tri'] == $tri) {
				if ($sens == 'ASC') {
					$cols[$indice]['sens'] = 'DESC';
				}
				else {
					$cols[$indice]['sens'] = 'ASC';
				}
				break;
			}
		}
	}

	//--------------------------------------------------------------------------
	// affiche le total d'éléments affichés sur la page
	// Entree : $nombreLignes
	// Retour : echo du code HTML d'affichage
	//--------------------------------------------------------------------------
	public static function drawTotal($nombreLignes)
	{
		if ($nombreLignes == 0)
			echo getLib('AUCUNE_REF_TROUVEE');
		elseif ($nombreLignes == 1)
			echo getLib('1_REF_TROUVEE');
		else echo getLib('X_REF_TROUVEE', $nombreLignes);
	}

	//--------------------------------------------------------------------------
	// dessin de l'entete du listing (ne pas mettre autre chose que echo sinon marche pas)
	// Entree : 
	//		$cols : le tableau des colonnes du listing
	//		$tri : le tri en cours
	//		$sens : le sens encours
	//		$css : code CSS pour habillage de l'entête
	// Retour
	//		echo du code HTML d'affichage
	//--------------------------------------------------------------------------
	public static function drawHead($cols, $tri, $sens, $css='')
	{
		//affichage de l'entete
		echo '<thead class="'.$css.'">';
		//on supprime les parametres tri et sens de l'url
		$leLienColonne = delUrlParameter($_SERVER['REQUEST_URI'], 'tri=');
		$leLienColonne = delUrlParameter($leLienColonne, 'sens=');
		//test si le lien possède encore au moins un paramètre
		$tabUrl = parse_url($leLienColonne);
		$possedeParams = (isset($tabUrl['query']));
		foreach($cols as $colonne) {
			$class = trim('text-'.$colonne['align'].' uw-w'.$colonne['size'].' '.$colonne['css']);
			echo '<th scope="col" class="'.$class.'">';
			if ($colonne['tri'] != '') {
				if ($colonne['title'] != '') {
					echo '<span data-toggle="tooltip" title="'.$colonne['title'].'">';
				}
				if ($colonne['tri'] == $tri) {
					if ($sens == 'ASC') echo '<span class="fas fa-caret-down">&nbsp</span>';
					if ($sens == 'DESC') echo '<span class="fas fa-caret-up">&nbsp</span>';
				}
				if ($possedeParams) {
					echo '<a href="'.$leLienColonne.'&amp;tri='.$colonne['tri'].'&amp;sens='.$colonne['sens'].'">'.$colonne['name'].'</a>';
				}
				else {
					echo '<a href="'.$leLienColonne.'?tri='.$colonne['tri'].'&amp;sens='.$colonne['sens'].'">'.$colonne['name'].'</a>';
				}
				if ($colonne['title'] != '') {
					echo '</span>';
				}
			}
			else {
				if ($colonne['title'] != '') {
					echo '<span data-toggle="tooltip" title="'.$colonne['title'].'">';
				}
				echo $colonne['name'];
				if ($colonne['title'] != '') {
					echo '</span>';
				}
			}
			echo '</th>';
		}
		echo '</thead>';
	}

	//--------------------------------------------------------------------------
	// dessin du corps du listing (ne pas mettre autre chose que echo sinon marche pas)
	// la couleur du background de la ligne est donné par le champ 'line-color' des données à afficher
	// Entree : 
	//		$cols : le tableau des colonnes du listing
	//		$listing : le tableau contenant les données à afficher
	//		$page : la page d'affichage en cours
	// Retour
	//		echo du code HTML d'affichage
	//--------------------------------------------------------------------------
	public static function drawBody($cols, $listing, $page)
	{
		//affichage du corps du tableau : donnees
		echo '<tbody>';
		foreach($listing as $ligne)	{
			(!empty($ligne['line-color'])) ? $couleur = ' class="'.$ligne['line-color'].'"' : $couleur = '';
			echo '<tr'.$couleur.'>';
			foreach($cols as $indiceCol => $colonne) {
				($colonne['header']) ? $tag = 'th scope="row"' : $tag = 'td';
				echo '<'.$tag.' class="'.trim('text-'.$colonne['align'].' '.$colonne['css']).'">';
					$fonction = 'Col_'.$indiceCol;
					if (function_exists($fonction)) {call_user_func($fonction, $ligne, $page);}
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</tbody>';
	}

}