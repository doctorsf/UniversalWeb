<?php
//----------------------------------------------------------------------
// Modele UniversalCsvImport
// Classe spécialisé dans l'import de fichiers CSV (séparateur ;)
//----------------------------------------------------------------------
// le modele est un tableau de colonnes contenant les informations suivantes :
//		-> numéro de la colonne dans le fichier csv
//		-> libelle én clair de la colonne
//		-> champ sql qui correspond à la colonne à importer
//		-> tableau de tests à effectuer sur la colonne pour définir sa validité
//		-> booleen (active) qui détermine si la colonne doit être utilisée
//	exemple
//    [civilite] => Array
//        (
//            [colonne] => 1
//            [libelle] => Civilite
//            [sql] => 
//            [match] => Array()
//			  [active] => 1
//        )
//----------------------------------------------------------------------

defined('CHECK_INTEGER')			|| define('CHECK_INTEGER',			'#^[-+]?[0-9]{1,}$#');			//1..n chiffres signé (pas de signe -+ obligatoire)
defined('CHECK_DOLLARS')			|| define('CHECK_DOLLARS',			'#^[$]?[0-9 ]{1,}$#');			//$99 999 999)
defined('CHECK_MONNAIE')			|| define('CHECK_MONNAIE',			'#^(€|\$|£)?[0-9 ]{1,}$#');		//$ ou £ ou € et 99 999 999)
defined('CHECK_INTEGER_SPACED')		|| define('CHECK_INTEGER_SPACED',	'#^[0-9 ]{1,}$#');				//99 999 999)
defined('CHECK_UNSIGNED_INTEGER')	|| define('CHECK_UNSIGNED_INTEGER',	'#^[0-9]{1,}$#');				//1..n chiffres	(signe -+ interdit)
defined('CHECK_SIGNED_INTEGER')		|| define('CHECK_SIGNED_INTEGER',	'#^[-+][0-9]{1,}$#');			//1..n chiffres (signe -+ obligatoire)
defined('CHECK_INTEGER_1OU2')		|| define('CHECK_INTEGER_1OU2',		'#^[0-9]{1,2}$#');				//1 ou 2 chiffre obligatoire
defined('CHECK_FLOAT')				|| define('CHECK_FLOAT',			'#^[0-9]+(\.[0-9]{1,})?$#');	//nombre à virgule avec décimales optionnelles
defined('CHECK_FLOAT_2DEC')			|| define('CHECK_FLOAT_2DEC',		'#^[0-9]+(\.[0-9]{1,2})?$#');	//nombre à virgule avec 2 décimales optionnelles (ex : prix)
defined('CHECK_INTEGER_4')			|| define('CHECK_INTEGER_4',		'#^[0-9]{4}$#');				//4 chiffres obligatoires (ex : annee)
defined('CHECK_INTEGER_8')			|| define('CHECK_INTEGER_8',		'#^[0-9]{8}$#');				//8 chiffres obligatoires (ex : reference)
defined('CHECK_BOOLEAN')			|| define('CHECK_BOOLEAN',			'#^[01]{1}$#');					//0 ou 1
defined('CHECK_DATETIME')			|| define('CHECK_DATETIME',			'#^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})?$#'); //datetime : 0000-00-00 00:00 (les secondes sont optionnelles)
defined('CHECK_EMAIL')				|| define('CHECK_EMAIL',			'#^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$#');	//eMail
defined('CHECK_EMAIL_APOSTROPHE')	|| define('CHECK_EMAIL_APOSTROPHE',	'#^[\'_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$#');	//eMail
defined('CHECK_ALPHA_SIMPLE')		|| define('CHECK_ALPHA_SIMPLE',		'#^[_\.0-9A-Za-z- ]+$#');		//alphanumérique simple (chifre + minuscules + _ . - [espace]
defined('CHECK_ALPHA_NOMS')			|| define('CHECK_ALPHA_NOMS',		'/^[[:alpha:]|àéèêëïôöûüç\\\\\' -]+$/');	//majuscules/minuscules/accents/espace/apostrophe/tiret
defined('CHECK_FILE_NAME')			|| define('CHECK_FILE_NAME',		'#^[_\.0-9A-Za-z-/]+$#');		//compatible nommage des fichiers
defined('CHECK_URL')				|| define('CHECK_URL',				'@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS');	//une url (http://mathiasbynens.be/demo/url-regex)
defined('CHECK_IPV4')				|| define('CHECK_IPV4',				'/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/');	//Adresse ip V4
defined('CHECK_MAC')				|| define('CHECK_MAC',				'/^(([0-9a-f]){2}:){5}([0-9a-f]){2}$/');	//Adresse mac
defined('CHECK_SHA1')				|| define('CHECK_SHA1',				'/^([0-9a-f]){40}$/');			//40 hexa

class UniversalCsvImport {

	private $_filename = '';			//chemin + nom du fichier csv à importer
	private $_lines = array();			//tableau contenant toutes les lignes (brutes) de données du fichier à importer et encodées utf-8
	private $_modele = array();			//modele (structure) du fichier d'import
	private $_entete = array();			//tableau contenant l'entete (correspondance colonne modele / colonne CSV)

	const VERSION = 'v1.0.0 (2019-02-05)';

	//--------------------------------------
	// Méthodes privées
	//--------------------------------------

	private function _keyExists($colonne) {
		if (!array_key_exists($colonne, $this->_modele)) {
			die ('Colonne "'.$colonne.'" inexistante dans le modèle créé');
		}
	}

	//--------------------------------------
	// Chargement du fichier CSV
	// Codage (CODAGE_ANSI / CODAGE_UTF8)
	// La methode applique un Trim (enleve les espaces devant et derrière la valeur) sur chaque valeur chargée
	//--------------------------------------
	private function _loadCSV($codage = CODAGE_ANSI) {
		$tabRes = array();
		$fp = fopen($this->_filename, 'r');
		//lecture de la première ligne contenant intitulé des colonnes : séparateur ';'
		$dummy = fgetcsv($fp, 3000, ';');
		//lecture des lignes de données
		while ($ligneData = fgetcsv($fp, 3000, ';')) {		
			$enreg = array();
			foreach($this->_modele as $key => $colonne) {
				if ($colonne['active']) {
					if ($codage == CODAGE_UTF8) {
						$enreg[$key] = convert_utf8(trim($ligneData[$colonne['colonne']]));
					}
					else $enreg[$key] = trim($ligneData[$colonne['colonne']]);
				}
			}
			//ajout d'un champ 'erreur' pour chaque ligne
			$enreg['erreur'] = false;
			//insertion de la ligne chargée dans le tableau final
			$tabRes[] = $enreg;
		}
		return $tabRes;
	}

	//--------------------------------------
	// GETTERS
	//--------------------------------------

	public function getModele() {
		return $this->_modele;
	}

	public function getNbColonnes() {
		return count($this->_modele);
	}

	public function getEntete() {
		return $this->_entete;
	}

	public function getNumCol($id) {
		return $this->_modele[$id]['colonne'];
	}

	public function getLibelle($id) {
		return $this->_modele[$id]['libelle'];
	}

	//--------------------------------------
	// SETTERS
	//--------------------------------------

	// défini le champ sql qui correspond à la colonne du fichier csv
	public function setSqlField($colonne, $sql) {
		$this->_keyExists($colonne);
		$this->_modele[$colonne]['sql'] = $sql;
	}

	// ajoute un test match à la colonne
	public function setMatch($colonne, $match) {
		$this->_keyExists($colonne);
		if (empty($match)) {
			$this->_modele[$colonne]['match'] = array();
		}
		else {
			$this->_modele[$colonne]['match'] = array_unique(array_merge($this->_modele[$colonne]['match'], $match));
		}
	}

	// création d'une colonne dans le modèle d'import
	public function createColonne($id, $num, $libelle, $sql, $match=array()) {
		if (isset($this->_modele[$id])) {
			die('La colonne "'.$id.'" existe déjà dans le modèle');
		}
		$this->_modele[$id] = array('colonne' => $num, 
									'libelle' => $libelle, 
									'sql' => $sql, 
									'match' => (array)$match, 
									'active' => true);
		$this->_entete = array();
		foreach($this->_modele as $key => $valeur) {
			$this->_entete[$valeur['colonne']] = $key;
		}

	}

	//--------------------------------------
	// Méthodes publiques
	//--------------------------------------

	//Affichage d'un libellé correspondant à un therme (constante)
	//Si jamais une fonction getLib() existe dans l'application, c'est cette fonction qui sera appelée
	//Ceci permet par exemple aux application de gérer le multi-langues
	public function getLib($mnemo, $param1='') {
		if (function_exists('getLib')) return getLib($mnemo, $param1);
		//dans le cas contraire
		$libelles = array(
			//------------------------------------------
			// Libellés des classes UniversalField (UFC)
			//------------------------------------------
			'UFC_ERREURS_A_CORRIGER'		=> 'Erreurs à corriger avant importation',
			'UFC_CHAMP_REQUIS'				=> 'Ce champ ne doit pas être vide',
			'UFC_CHAMP_NUMERIQUE'			=> 'Ce champ doit être numérique',
			'UFC_CHOIX_INVALIDE'			=> 'Ce choix n\'est pas valide',
			'UFC_INTEGER_1OU2'				=> 'Entier attendu (2 chiffres max)',
			'UFC_FLOAT'						=> 'Nombre à virgule attendu (décimales optionnelles)',
			'UFC_FLOAT_2DEC'				=> 'Nombre à virgule attendu (2 décimales optionnelles)',
			'UFC_INTEGER_X'					=> '%d chiffres attendus',
			'UFC_INTEGER'					=> 'Entier attendu',
			'UFC_DOLLARS'					=> 'Dollars attendus',
			'UFC_MONNAIE'					=> 'Monnaie attendue',
			'UFC_INTEGER_SPACED'			=> 'Entier attendu (espaces autorisés)',
			'UFC_UNSIGNED_INTEGER'			=> 'Entier non signé attendu',
			'UFC_SIGNED_INTEGER'			=> 'Entier signé attendu',
			'UFC_BOOLEAN'					=> '1 ou 0 attendu',
			'UFC_DATETIME'					=> 'Format date heure attendu : yyyy-mm-jj hh:mm:ss',
			'UFC_EMAIL'						=> 'eMail attendue',
			'UFC_EMAIL_APOSTROPHE'			=> 'eMail attendue (apostrophe autorisée)',
			'UFC_ALPHA_SIMPLE'				=> 'Caractères alphanumériques attendus',
			'UFC_ALPHA_NOMS'				=> 'Caractères alphanumériques accentués attendus',
			'UFC_FILE_NAME'					=> 'Caractères compatibles avec nomage de fichiers attendus',
			'UFC_URL'						=> 'url attendue',
			'UFC_IPV4'						=> 'adresse IP V4 attendue',
			'UFC_MAC'						=> 'adresse Mac attendue',
			'UFC_SHA1'						=> '40 caractères hexadécimaux attendus',
			'UFC_MAJ_VERROUILLEES'			=> 'Attention, les majuscules sont verrouilllées !',
			'UFC_FICHIER_INEXISTANT'		=> 'Fichier inexistant&hellip;',
			'UFC_TYPE_FICHIER_NON_AUTORISE'	=> 'Ce type de fichier n\'est pas autorisé. Sont autorisés : ',
			'UFC_NOM_FICHIER_NON_VALIDE'	=> 'Nom de fichier non valide.',
			'UFC_POIDS_FICHIER_MOINS_DE'	=> 'Votre fichier doit faire moins de ',
			'UFC_UPLOAD_MAX_FILE_SIZE_INI'	=> 'Le poids du fichier dépasse celui précisé par la directive upload_max_filesize du fichier php.ini',
			'UFC_UPLOAD_MAX_FILE_SIZE_FORM'	=> 'Le poids du fichier dépasse celui précisé par la directive MAX_FILE_SIZE du formulaire',
			'UFC_IMAGE_PARTIEL_TELECHARGEE'	=> 'L\'image n\'a été que partiellement téléchargée',
			'UFC_AUCUN_FICHIER_TELECHARGE'	=> 'Aucun fichier n\'a été téléchargé',
			'UFC_DOSSIER_TEMP_MANQUANT'		=> 'Un dossier temporaire est manquant',
			'UFC_ECHEC_ECRITURE_DISQUE'		=> 'Échec de l\'écriture du fichier sur le disque',
			'UFC_ECHEC'						=> 'Échec&hellip;',
			'UFC_MATCH_INVALIDE'			=> 'Paramètre de propriété testMatches inconnu!'
		);
		return sprintf($libelles[$mnemo], $param1);
	}

	// active une colonne pour qu'elle soit pruise en compte dans la lecture
	// par défaut, toutes les colonnes sont actives à leur création
	public function active($colonne) {
		$this->_modele[$colonne]['active'] = true;
	}

	// desactive une colonne pour qu'elle ne soit pas prise en compte dans la lecture
	// par défaut, toutes les colonnes sont actives à leur création
	public function desactive($colonne) {
		$this->_modele[$colonne]['active'] = false;
	}

	// compte le nombre de colonne dans le modele
	public function modeleColCount() {
		return count($this->_modele);
	}

	// charge les données du fichier CVS
	public function charge($file) {
		$this->_filename = $file;
		return $this->_loadCSV(CODAGE_UTF8);
	}

	//--------------------------------------
	// test le contenu d'un enregistrement selon les matches de colonnes définis
	// $enregistrement contient une ligne du fichier d'import. Exemple : 
	//	[0] => Array
	//	(
	//		[civilite] => Mme
	//		[prenom] => Francoise
	//		[nom_usuel] => REBOURS
	//		[nom_famille] => MARIE
	//		[nom_marital] => REBOURS
	//		[sexe] => Féminin
	//		[email] => francoise.marie@intradef.gouv.fr
	//		[erreur] => 
	//	)
	//--------------------------------------
	// Entrée : l'enregistrement à tester (modifié si erreur(s) rencontrée(s))
	// Sortie : true (au loins une erreur rencontrée) / false (pas d'erreur)
	//--------------------------------------
	public function testColonnes(&$enregistrement) {
		// pour chaque colonne du modele
		foreach($this->_modele as $keyModele => $colonneModele) {
			//pour chaque match du modele
			foreach($colonneModele['match'] as $test) {
	
				//test si le champ est vide
				if ($test == 'REQUIRED') {
					if ($enregistrement[$keyModele] == '') {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_CHAMP_REQUIS').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}	
				elseif ($test == 'NUMERIC') {
					if (($enregistrement[$keyModele] != '') && (!is_numeric($enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_CHAMP_NUMERIQUE').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'NOT_ZERO') {
					if (($enregistrement[$keyModele] != '') && ($enregistrement[$keyModele] == 0)) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_CHOIX_INVALIDE').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_INTEGER') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_INTEGER, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_INTEGER').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_INTEGER_1OU2') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_INTEGER_1OU2, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_INTEGER_1OU2').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_UNSIGNED_INTEGER') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_UNSIGNED_INTEGER, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_UNSIGNED_INTEGER').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_SIGNED_INTEGER') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_SIGNED_INTEGER, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_SIGNED_INTEGER').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_INTEGER_4') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_INTEGER_4, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_INTEGER_X', 4).'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_INTEGER_8') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_INTEGER_8, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_INTEGER_X', 8).'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_FLOAT') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_FLOAT, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_FLOAT').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_FLOAT_2DEC') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_FLOAT_2DEC, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_FLOAT_2DEC').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_BOOLEAN') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_BOOLEAN, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_BOOLEAN').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_DATETIME') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_DATETIME, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_DATETIME').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_EMAIL') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_EMAIL, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_EMAIL').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_EMAIL_APOSTROPHE') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_EMAIL_APOSTROPHE, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_EMAIL_APOSTROPHE').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_ALPHA_SIMPLE') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_ALPHA_SIMPLE, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_ALPHA_SIMPLE').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_ALPHA_NOMS') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_ALPHA_NOMS, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_ALPHA_NOMS').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_FILE_NAME') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_FILE_NAME, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_FILE_NAME').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_URL') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_URL, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_URL').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_IPV4') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_IPV4, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_IPV4').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_MAC') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_MAC, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_MAC').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif ($test == 'CHECK_SHA1') {
					if (($enregistrement[$keyModele] != '') && (!preg_match(CHECK_SHA1, $enregistrement[$keyModele]))) {
						$enregistrement[$keyModele] = '<span class="text-danger">'.getLib('UFC_SHA1').'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				elseif (($res = utf8_left($test, 5)) == 'PARMI') {
					//pour tous les PARMI (autres)
					if (($enregistrement[$keyModele] != '') && (!in_array($enregistrement[$keyModele], $this->$test))) {
						$enregistrement[$keyModele] = '<span class="text-danger"><del>'.$enregistrement[$keyModele].'</del> : Saisie attendue parmi : '.implode(', ', $this->$test).'</span>';
						$enregistrement['erreur'] = true;
						break;
					}
				}
				else {
					//test inconnu
				}

			}
		}
		return ($enregistrement['erreur'] == true);
	} 

	//-----------------------------------------------------------
	// Propose code HTML / BOOTSTRAP pour affichage des 
	// lignes en erreur dans un tableau classique (1 colonne par champs)
	//-----------------------------------------------------------
	public function displayErrorsTab($data, $nbErreur) {
		$chaine = '';
			$chaine.= '<div class="row">';
				$chaine.= '<div class="col-12">';
					$chaine.= '<h1 class="display-4">'.getLib('UFC_ERREURS_A_CORRIGER').'</h1>';
					if ($nbErreur == 1)
						$chaine.= $nbErreur.' référence trouvée';
					else 
						$chaine.= $nbErreur.' références trouvées';
					$chaine.= '<table class="table table-hover table-striped">';
						$chaine.= '<thead>';
							$chaine.= '<tr>';
								$chaine.= '<th class="text-center" width="5%">ligne</th>';
								foreach ($this->_modele as $key => $colonne) {
									$chaine.= '<th class="text-left" width="'.div(95, $this->getNbColonnes()).'%">'.$key.'</th>';
								}
							$chaine.= '</tr>';
						$chaine.= '</thead>';
						$chaine.= '<tbody class="small">';
							foreach ($data as $numLine => $enreg) {
								if ($data[$numLine]['erreur'] == true) {
									$chaine.= '<tr>';
										$chaine.= '<td width="5%" align="center">'.($numLine + 2).'</td>';
										foreach ($this->_modele as $key => $colonne) {
											$chaine.= '<td align="left" width="'.div(95, $this->getNbColonnes()).'%" class="small">'.$enreg[$key].'</td>';
										}
									$chaine.= '</tr>';
								}
							}
						$chaine.= '</tbody>';
					$chaine.= '</table>';
				$chaine.= '</div>';
			$chaine.= '</div>';
		return $chaine;
	}

	//-----------------------------------------------------------
	// Propose code HTML / BOOTSTRAP pour affichage des lignes 
	// en erreur dans un tableau regroupé (tous les champs dans la colonne).
	// plus lisible
	//-----------------------------------------------------------
	public function displayErrors($data, $nbErreur) {
		$chaine = '';
			$chaine.= '<div class="row">';
				$chaine.= '<div class="col-12">';
					$chaine.= '<div class="d-flex flex-row align-items-center">';
						$chaine.= '<h1 class="display-4">Erreurs à corriger avant importation</h1>';
					$chaine.= '</div>';
					if ($nbErreur == 1)
						$chaine.= $nbErreur.' référence trouvée';
					else 
						$chaine.= $nbErreur.' références trouvées';
					$chaine.= '<table class="table table-hover table-striped">';
						$chaine.= '<thead>';
							$chaine.= '<tr>';
								$chaine.= '<th class="text-center" width="5%">ligne</th>';
								$chaine.= '<th class="text-left" width="95%">contenu</th>';
							$chaine.= '</tr>';
						$chaine.= '</thead>';
						$chaine.= '<tbody>';
							foreach ($data as $numLine => $enreg) {
								if ($data[$numLine]['erreur'] == true) {
									$chaine.= '<tr>';
										$chaine.= '<td width="5%" align="center">'.($numLine + 2).'</td>';
										$chaine.= '<td align="left" width="95%">';
										foreach ($enreg as $key => $colonne) {
											$chaine.= '<mark>'.$key.'</mark> : '.$enreg[$key].'<br />';
										}
										$chaine.= '</td>';
									$chaine.= '</tr>';
								}
							}
						$chaine.= '</tbody>';
					$chaine.= '</table>';
				$chaine.= '</div>';
			$chaine.= '</div>';
		return $chaine;
	}

	//-----------------------------------------------------------
	// Propose code HTML simple pour affichage des lignes en erreur
	//-----------------------------------------------------------
	public function displayRawErrors($data, $nbErreur) {
		$chaine = '<h1>Erreurs à corriger avant importation</h1>';
		$chaine.= '<p>';
		if ($nbErreur == 1)
			$chaine.= $nbErreur.' référence trouvée';
		else 
			$chaine.= $nbErreur.' références trouvées';
		$chaine.= '</p>';
		foreach ($data as $numLine => $enreg) {
			if ($data[$numLine]['erreur'] == true) {
				$chaine.= '<p>ligne n°'.($numLine + 2).'</p>';
				$chaine.= '<pre>';
				foreach($enreg as $key => $value) {
					$chaine.= '['.$key.'] => '.$value.'<br />';
				}
				$chaine.= '</pre>';
				$chaine.= '<hr />';
			}
		}
		return $chaine;
	}

}