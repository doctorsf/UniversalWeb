<?php
//------------------------------------------------------------------
// Classe Listing_logs
//------------------------------------------------------------------
// éè : pour enregistrement UTF-8
// Auteur : Fabrice Labrousse
// Date : 11 janvier 2018
//------------------------------------------------------------------
// Gère le listing client
//------------------------------------------------------------------

class Listing_logs extends UniversalList {

	//-------------------------------------
	// Construction des colonnes
	//-------------------------------------
	protected function construitColonnes() {

		//colonne TYPE DE LOG
		$this->createCol('typelog', array(
			'order' => 1,
			'libelle' => 'Type',	
			'size' => 10, 
			'tri' => true, 
			'triSql' => 'libellelog', 
			'triSqlSecondaire' => 'quand',
			'triLibelle' => getLib('LOG_TYPE_DATE'),
			'align' => 'center',
			'title' => 'Type de journal',
			'titlePos' => 'left',
			'filtre' => true,
			'filtreType' => 'select',
			'filtreScope' => 'sqlLogs_fillSelectTypesTous',
			'filtreRange' => UniversalListColonne::EGAL, 
			'filtreValue' => 'TOUS',
			'filtreSqlField' => 'T1.id_log_type',
			'filtreCaption' => 'Type',
			'filtreHelp' => getLib('LOG_FILTRE_TYPE')
		));

		//colonne LIBELLE OPERATION
		$this->createCol('libelle', array(
			'order' => 2,
			'libelle' => 'Opération',
			'size' => 70, 
			'tri' => true, 
			'triSql' => 'operation', 
			'triSqlSecondaire' => 'quand',
			'triLibelle' => getLib('LOG_OPERATION_DATE'),
			'title' => 'Opération logguée',
			'filtre' => true,
			'filtreType' => 'text',
			'filtreScope' => UniversalListColonne::MENU,
			'filtreRange' => UniversalListColonne::TOUT, 
			'filtreValue' => '',
			'filtreSqlField' => 'operation',
			'filtreHelp' => getLib('LOG_FILTRE_LIBELLE')
		));

		//colonne QUI
		$this->createCol('qui', array(
			'order' => 3,
			'libelle' => 'Utilisateur', 
			'size' => 10,
			'tri' => true,
			'triSql' => 'CONCAT (nom, prenom)',
			'triSqlSecondaire' => 'quand',
			'triLibelle' => getLib('LOG_USER_DATE'),
			'filtre' => true,
			'filtreType' => 'select',
			'filtreScope' => 'sqlLogs_fillSelectUtilisateursTous',
			'filtreRange' => UniversalListColonne::EGAL,
			'filtreValue' => 'TOUS',
			'filtreSqlField' => 'T1.id_user',
			'filtreCaption' => getLib('UTILISATEUR'),
			'filtreHelp' => getLib('LOG_FILTRE_USER')
		));

		//colonne QUAND
		$this->createCol('quand', array(
			'order' => 4,
			'libelle' => 'Date', 
			'size' => 10, 
			'title' => getLib('LOG_DATE'),
			'tri' => true, 
			'triSql' => 'quand', 
			'triLibelle' => 'par date',
			'triSens' => 'DESC'
		));

		if (($this->getSize() < 100) || ($this->getSize() > 101)) {
			DEBUG_(getLib('TAILLE_LISTING'), $this->getSize());
		}

	}

	//-------------------------------------
	// Méthodes de récupération des données 
	//-------------------------------------

	//----------------------------------------------------------------------
	// Lance la requete de calcul du nombre de ligne ramenées pour le listing
	// Entree : rien
	// Retour : le nombre de lignes ramenées (ou false si erreur SQL)
	//SELECT count(id_log) nombre 
	//FROM wy_logs T1 
	//INNER JOIN wy_logs_types T2 ON T2.id_log_type = T1.id_log_type 
	//INNER JOIN wy_users T3 ON T3.id_user = T1.id_user 
	//WHERE 1
	//----------------------------------------------------------------------
	protected function getListeNombre() {
		//----- SELECT -------------
		$requete = "SELECT ";
		$requete.= "count(id_log) nombre ";

		//----- FROM -------------
		$requete.= "FROM "._PREFIXE_TABLES_."logs T1 ";																							//T1  : table logs
		//colonne SITE -> on ramène tous les sites disponibles
		if ($this->isColonneActive('typelog'))	$requete.= "INNER JOIN "._PREFIXE_TABLES_."logs_types T2 ON T2.id_log_type = T1.id_log_type ";	//T2  : table logs_types
		if ($this->isColonneActive('qui')) 		$requete.= "INNER JOIN "._PREFIXE_TABLES_."users T3 ON T3.id_user = T1.id_user ";						//T3  : table users

		//----- WHERE -------------
		$requete.= "WHERE 1 ";
		$requete.= $this->buildFiltres();

		//DEBUG_('requete', $requete);

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
	// Retourne les articles correspondant à une requete
	// Entree : rien
	// Retour : false (erreur SQL) / tableau des tuples sinon
	//	SELECT id_log, T1.id_log_type, T1.id_user, operation, quand, 
	//	libelle libellelog, 
	//	T3.nom, T3.prenom 
	//	FROM wy_logs T1 
	//	INNER JOIN wy_logs_types T2 ON T2.id_log_type = T1.id_log_type 
	//	LEFT OUTER JOIN wy_users T3 ON T3.id_user = T1.id_user 
	//	WHERE 1
	//----------------------------------------------------------------------
	protected function getListe() {
		$laListe = array();

		//----- SELECT -------------
		$requete = "SELECT id_log, T1.id_log_type, T1.id_user, operation, ";
		if ($this->isColonneActive('typelog'))	$requete.= "libelle libellelog, ";
		if ($this->isColonneActive('qui'))		$requete.= "T3.nom, T3.prenom, ";
		$requete.= "quand ";

		//----- FROM -------------
		$requete.= "FROM "._PREFIXE_TABLES_."logs T1 ";																							//T1  : table logs
		//colonne SITE -> on ramène tous les sites disponibles
		if ($this->isColonneActive('typelog'))	$requete.= "INNER JOIN "._PREFIXE_TABLES_."logs_types T2 ON T2.id_log_type = T1.id_log_type ";	//T2  : table logs_types
		if ($this->isColonneActive('qui')) 		$requete.= "LEFT OUTER JOIN "._PREFIXE_TABLES_."users T3 ON T3.id_user = T1.id_user ";						//T3  : table users

		//----- WHERE -------------
		$requete.= "WHERE 1 ";
		$requete.= $this->buildFiltres();

		//----- ORDER BY -------------
		$requete.= "ORDER BY ".$this->buildTris()." ";
		if ($this->getNbLinesParPage() != 0)
			$requete.= "LIMIT ".$this->getSqlLimitStart().", ".$this->getNbLinesParPage();

		//DEBUG_('requete', $requete);

		$laListe = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($laListe !== false) {
			return $laListe;
		}
		return false;
	}

	//-------------------------------------
	// Méthodes d'affichage du contenu des colonnes
	//-------------------------------------

	public function Col_typelog($ligne) {
		echo $ligne['libellelog'];
	}

	public function Col_libelle($ligne) {
		echo $ligne['operation'];
	}

	public function Col_qui($ligne) {
		echo '<a href="'._URL_USER_.'?operation=consulter&amp;id='.$ligne['id_user'].'">';
			echo $ligne['nom'].' '.$ligne['prenom'];
		echo '</a>';
	}

	public function Col_quand($ligne) {
		echo changeDateFormat($ligne['quand'], _FORMAT_DATE_TIME_SQL_, _FORMAT_DATE_TIME_);
	}

}