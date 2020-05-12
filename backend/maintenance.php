<?php
//-----------------------------------------------------------
// OPERATIONS DE MAINTENANCE SUR LA BASE DE DONNEES
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8
// 01.06.2018
//		- modification des entrées 'savedb', 'loaddb' et 'gorestoredb' pour intégrer la notion de version 
//			d'application dans le nom de la sauvegarde
//		- ajout de l'entrée 'deldb' de suppression de la sauvegarde
//		- présentation de la liste des sauvegardes disponibles sous forme de table
// 22.04.2019
//		- Ajout entrées "reseterrors" et "reseterrorsfrontend"
//-----------------------------------------------------------
require_once('libs/common.inc.php');

//gère l'accès au script
$operation = grantAcces() or die();

$titrePage = _APP_TITLE_;
$scriptSup = '';
$fJquery = '';
echo writeHTMLHeader($titrePage, '', '');

echo '<body>';
	echo '<div class="container-fluid">';

	//--------------------------------------
	// HEADER
	//--------------------------------------
	include_once(_BRIQUE_HEADER_);

	//--------------------------------------
	// CORPS
	//--------------------------------------
	echo '<section>';
	echo '<article>';

		echo '<div class="row">';
			echo '<div class="col-12">';

				//------------------------------------------
				// ACTION A MENER
				//------------------------------------------
				switch($operation)
				{
					//--------------------------------------
					// Supprime le fichier d'erreurs _FRONTEND_PHP_FILE_ERRORS_
					//--------------------------------------
					case 'reseterrorsfrontend' : 
					{
						$res = unlink(_FRONTEND_PHP_FILE_ERRORS_);
						if ($res) {
							riseMessage(getLib('PHP_ERROR_FILE_DELETED'));
						}
						else {
							riseErrorMessage(getLib('PHP_ERROR_FILE_ERROR'));
						}
						//retour
						goReferer();
						break;
					}

					//--------------------------------------
					// Supprime le fichier d'erreurs _PHP_FILE_ERRORS_
					//--------------------------------------
					case 'reseterrors' : 
					{
						$res = unlink(_PHP_FILE_ERRORS_);
						if ($res) {
							riseMessage(getLib('PHP_ERROR_FILE_DELETED'));
						}
						else {
							riseErrorMessage(getLib('PHP_ERROR_FILE_ERROR'));
						}
						//retour
						goReferer();
						break;
					}

					//--------------------------------------
					// Purger les log > 3 mois
					//--------------------------------------
					case 'epurelog' : 
					{
						$log = new sqlLogs();
						$nombre = $log->epure();
						if ($nombre === false) {
							riseErrorMessage(getLib('ERREUR_SQL'));
						}
						elseif ($nombre === 0) {
							riseWarningMessage(getLib('LOG_RETIRE_AUCUN'));
						}
						else {
							riseMessage(getLib('LOG_RETIRE_X', $nombre));
						}
						//retour
						goReferer();
						break;
					}

					//--------------------------------------
					// Purger tous les log
					//--------------------------------------
					case 'purgelog' : 
					{
						$log = new sqlLogs();
						$nombre = $log->purge();
						if ($nombre === false) {
							riseErrorMessage(getLib('ERREUR_SQL'));
						}
						else {
							riseMessage(getLib('LOG_PURGE'));
						}
						//retour
						goReferer();
						break;
					}

					//--------------------------------------
					// sauvegarde base de donnees
					// le nom des fichiers de sauvegarde de la base de données est ainsi construit
					// appli_version_date_heure.sql (ex : wyniss_v1.0.0_2018-05-31_17-29-46.sql)
					//--------------------------------------
					case 'savedb':
					{
						$res = saveDatabase(_APP_BLOWFISH_, _APP_VERSION_, _SAUVEGARDE_BASE_);
						if ($res) {
							riseMessage(getLib('SAUVEGARDE_OK')); 
						}
						else {
							riseErrorMessage(getLib('SAUVEGARDE_KO')); 
						}
						goReferer();
						break;
					}

					//--------------------------------------
					// restauration base de donnees
					// le nom des fichiers de sauvegarde de la base de données est ainsi construit
					// appli_version_date_heure.sql (ex : wyniss_v1.0.0_2018-05-31_17-29-46.sql)
					// Sur l'exemple ci-dessus en explosant le nom du fichier on récupère donc les informations suivantes :
					//Array (
					//	[0] => _APP_BLOWFISH_
					//	[1] => v1.0.0
					//	[2] => 2018-05-31
					//	[3] => 17-29-46
					//)
					//--------------------------------------
					case 'loaddb':
					{
						function Col_libelle($ligne) {
							$parts = explode('_', $ligne);
							if (count($parts) == 4) { 
								//vérification de la cohérence de version de base de données
								if ($parts[1] === _APP_VERSION_) {
									$colorLink = 'success';
									$conseil = '';
								}
								else {
									$colorLink = 'danger';
									$conseil = ' ('.getLib('NON_CONSEILLE').')';
								}
								$javascript = 'onclick="return confirm(\''.addslashes(getLib('RESTAURATION_CERTAIN')).'\');"';
								echo '<a class="text-'.$colorLink.'" href="'._URL_MAINTENANCE_.'?operation=gorestoredb&amp;fichier='.$ligne.'"'.$javascript.'>';
								echo getLib('SAUVEGARDE_VERSION', mySqlToDateClair($parts[2], _LG_), $parts[3], $parts[1]).$conseil;
								echo '</a>';
							}
							else {
								echo '<span class="text-danger">'.getLib('FORMAT_IGNORE').'</span>';
							}
						}
						function Col_delete($ligne) {
							$javascript = 'onclick="return confirm(\''.addslashes(getLib('SAUVEGARDE_SUPPRIMER_CERTAIN')).'\');"';
							echo '<a href="'._URL_MAINTENANCE_.'?operation=deldb&amp;fichier='.$ligne.'" '.$javascript.'>';
								echo '<span class="fas fa-trash" data-toggle="tooltip" title="'.getLib('SUPPRIMER_LA_SAUVEGARDE').'"></span>';
							echo '</a>';
						}
						echo '<div class="container-lg px-0">';
							echo '<h1>'.getLib('RESTORATION_BD').'</h1>';
							echo '<p class="lead">'.getLib('SAUVEGARDES_LISTE').'</p>';
							$dirname = _SAUVEGARDE_BASE_; 
							$dir = opendir($dirname); 
							$cols['libelle'] = SimpleListingHelper::createCol(array('name' => getLib('LIBELLE'),'size' => 95));
							$cols['delete'] = SimpleListingHelper::createCol(array('name' => getLib('SUPPR'),'size' => 5,'align' => 'center'));
							SimpleListingHelper::getParams('libelle', $cols, $page, $tri, $sens);
							$listing = array();
							while($file = readdir($dir)) { 
								$elements = pathinfo($file);
								if (($file != '.') && ($file != '..') && (!is_dir($dirname.$file)) && ($elements['extension'] == 'sql')) { 
									if (preg_match(SAVEDBREGEX, $elements['filename'])) {
										$listing[] = $elements['filename'];
									}
								}
							}
							closedir($dir);
							SimpleListingHelper::drawTotal(count($listing));
							echo '<table class="table table-hover table-responsive table-striped">';	//table-responsive table-striped table-sm 
								//affichage de l'entete
								SimpleListingHelper::drawHead($cols, $tri, $sens);
								//affichage du corps du tableau : donnees
								SimpleListingHelper::drawBody($cols, $listing, $page);
							echo'</table>';
						echo '</div>';
						break;
					}

					//--------------------------------------
					// restauration effective d'une base de données
					//--------------------------------------
					case 'gorestoredb':
					{
						echo '<h1>'.getLib('RESTORATION_BD').'</h1>';
						//recuperation du fichier. sql à restorer
						(isset($_GET['fichier'])) ? $fichier = MySQLDataProtect($_GET['fichier']) : $fichier = 'aucun';
						if ($fichier != 'aucun') {
							//petite sauvegarde systeme (true) avant... quand même
							$res1 = saveDatabase('systemsecuritybackup', _APP_VERSION_, _SAUVEGARDE_BASE_, null, _SAVE_DB_SYSTEM_);
							if ($res1) {
								$res2 = restoreDatabase(_SAUVEGARDE_BASE_, $fichier);
								if ($res2) {
									riseMessage(getLib('RESTAURATION_OK')); 
									header('Location: '._URL_LOGOUT_);
									die();
								}
								else {
									riseErrorMessage(getLib('RESTAURATION_KO')); 
								}
							}
							else {
								echo riseErrorMessage(getLib('SAUVEGARDE_SECURITE_KO'));
							}
						}
						goReferer();
						break;
					}

					//--------------------------------------
					// supprime une sauvegarde de base de données
					//--------------------------------------
					case 'deldb':
					{
						(isset($_GET['fichier'])) ? $fichier = MySQLDataProtect($_GET['fichier']) : $fichier = 'aucun';
						$savedFile = _SAUVEGARDE_BASE_.$fichier.'.sql';
						if(file_exists($savedFile)) {
							if (unlink($savedFile)) {
								riseMessage(getLib('SAUVEGARDE_SUPPRIMEE_OK')); 
							}
							else {
								riseErrorMessage(getLib('SAUVEGARDE_SUPPRIMEE_KO')); 
							}
						}
						else {
							echo riseErrorMessage(getLib('SAUVEGARDE_INEXISTANTE'));
						}
						goReferer();
						break;
					}

					//--------------------------------------
					// affiche la signature de la structure de la base de données (pas les données)
					//--------------------------------------
					case 'dbsign':
					{
						//recherche de la signature de la base
						$signature = signatureDatabase($lesTables);

						echo '<div class="container-lg px-0">';
							echo '<div class="row">';
								echo '<div class="col">';
									echo '<div class="d-flex flex-row align-items-center">';
										echo '<h1>'.getLib('SIGNATURE_BASE').' (sha1)</h1>';
									echo '</div>';
									echo '<table class="table table-hover table-striped table-lg-responsive">';
										echo '<thead>';
										echo '<tr>';
											echo '<th class="uw-w60 text-left">'.getLib('TABLE').'</th>';
											echo '<th class="uw-w40 text-left">SHA1</th>';
										echo '</tr>';
										echo '</thead>';
										echo '<tbody>';
											foreach($lesTables as $table) {
												echo '<tr>';
													echo '<td class="uw-w60 text-left">'.$table['name'].'</td>';
													echo '<td class="uw-w40 text-left text-monospace">'.$table['sha1'].'</td>';
												echo '</tr>';
											}
										echo '</tbody>';
									echo '<tfoot>';
										echo '<tr class="table-success">';
										  echo '<td class="lead">'.getLib('SIGNATURE_FINALE').'</td>';
										  echo '<td class="text-monospace lead">'.$signature.'</td>';
										echo '</tr>';
									echo '</tfoot>';
								echo '</table>';
							echo '</div>';
						echo '</div>';

						break;
					}

					//--------------------------------------
					// affiche le hash du code
					//--------------------------------------
					case 'hash':
					{
						function drawlines(&$hashs) {
							$fichiers = array(
								_URL_ACTIONS_DIVERS_, 
								_URL_ACTIONS_DROITS_, 
								_URL_AUTHENTIFICATION_, 
								_URL_INFOS_SYSTEME_, 
								'input.php', 
								_URL_LANGUE_, 
								_URL_LISTING_DROITS_, 
								_URL_LISTING_LOGS_, 
								_URL_LISTING_PARAMS_, 
								_URL_LISTING_USERS_, 
								_URL_LOGOUT_, 
								_URL_MAINTENANCE_, 
								_URL_MEDIA_, 
								_URL_PARAM_, 
								'reponses-ajax.php',
								_URL_USER_, 
								_LIBS_.'common.inc.php', 
								_LIBS_.'db.connexion.pdo.oracle.php', 
								_LIBS_.'db.connexion.pdo.php', 
								_LIBS_.'defines.inc.php', 
								_LIBS_.'droits.inc.php', 
								_LIBS_.'fonctions.inc.php', 
								_LIBS_.'init.inc.php', 
								_LIBS_.'routines.inc.php', 
								_LIBS_.'uw_chaines.php', 
								_LIBS_.'uw_dates.php', 
								_LIBS_.'uw_debug.php', 
								_LIBS_.'uw_file.php', 
								_LIBS_.'uw_flux.php', 
								_LIBS_.'uw_ftp.php', 
								_LIBS_.'uw_geo.php', 
								_LIBS_.'uw_img.php', 
								_LIBS_.'uw_mail.php', 
								_LIBS_.'uw_nav.php', 
								_BRIQUES_.'brique_debug.inc.php', 
								_BRIQUES_.'brique_erreur.inc.php', 
								_BRIQUES_.'brique_footer.inc.php', 
								_BRIQUES_.'brique_header.inc.php', 
								_BRIQUES_.'brique_message.inc.php', 
								_CLASSES_.'Droits.class.php', 
								_CLASSES_.'Form_import_csv.class.php', 
								_CLASSES_.'Form_input.class.php', 
								_CLASSES_.'Form_login.class.php', 
								_CLASSES_.'Form_media.class.php', 
								_CLASSES_.'Form_param.class.php', 
								_CLASSES_.'Form_recherche_addon.class.php', 
								_CLASSES_.'Form_recherche_simple.class.php', 
								_CLASSES_.'Form_user.class.php', 
								_CLASSES_.'Fpdf.class.php', 
								_CLASSES_.'Ldap.class.php', 
								_CLASSES_.'Listing_logs.class.php', 
								_CLASSES_.'Login.class.php', 
								_CLASSES_.'PageNavigator.class.php', 
								_CLASSES_.'Params.class.php', 
								_CLASSES_.'SilentMail.class.php', 
								_CLASSES_.'SimpleListingHelper.class.php', 
								_CLASSES_.'SqlSimple.class.php', 
								_CLASSES_.'UniversalCsvImport.class.php', 
								_CLASSES_.'UniversalDatabase.class.php', 
								_CLASSES_.'UniversalField.class.php', 
								_CLASSES_.'UniversalFieldArea.class.php', 
								_CLASSES_.'UniversalFieldBouton.class.php', 
								_CLASSES_.'UniversalFieldCheckbox.class.php', 
								_CLASSES_.'UniversalFieldDiv.class.php', 
								_CLASSES_.'UniversalFieldFiltreselect.class.php', 
								_CLASSES_.'UniversalFieldFiltretext.class.php', 
								_CLASSES_.'UniversalFieldHidden.class.php', 
								_CLASSES_.'UniversalFieldImage.class.php', 
								_CLASSES_.'UniversalFieldRadio.class.php', 
								_CLASSES_.'UniversalFieldSearch.class.php', 
								_CLASSES_.'UniversalFieldSelect.class.php', 
								_CLASSES_.'UniversalFieldSeparateur.class.php', 
								_CLASSES_.'UniversalFieldSwitch.class.php', 
								_CLASSES_.'UniversalFieldText.class.php', 
								_CLASSES_.'UniversalForm.class.php', 
								_CLASSES_.'UniversalList.class.php', 
								_CLASSES_.'UniversalTree.class.php', 
								_CLASSES_.'UniversalZip.class.php', 
								_CLASSES_.'User.class.php', 
								_LANGUES_.'langue_fr.inc.php', 
								_LANGUES_.'langue_us.inc.php', 
								_SQL_.'sql_divers.inc.php', 
								_SQL_.'sql_droits.inc.php', 
								_SQL_.'sql_logs.inc.php', 
								_SQL_.'sql_params.inc.php',
								_SQL_.'sql_users.inc.php', 
								_CSS_.'styles.css', 
								_JAVASCRIPT_.'oXHR.js', 
								_JAVASCRIPT_.'php.js', 
								_JAVASCRIPT_.'resizable.min.js', 
								_JAVASCRIPT_.'scripts.js', 
								_JAVASCRIPT_.'universalform.js', 
								_JAVASCRIPT_.'universalform.min.js');
							$hashs = '';
							$chaine = '';
							foreach($fichiers as $fichier) {
								$hash = sha1_file($fichier);
								$hashs.= $hash;
								$chaine.= '<tr>';
									$chaine.= '<td class="uw-w60 text-left">'.$fichier.'</td>';
									if (file_exists($fichier)) {
										$chaine.= '<td class="uw-w40 text-left text-monospace">'.$hash.'</td>';
									}
									else {
										$chaine.= '<td class="uw-w40 text-left"><span class="text-danger">'.getLib('ERREUR_FICHIER_INEXISTANT').'</span></td>';
									}
								$chaine.= '</tr>';
							}
							$hashs = sha1($hashs);
							return $chaine;
						}

						echo '<div class="container-lg px-0">';
							echo '<div class="row">';
								echo '<div class="col">';
									echo '<div class="d-flex flex-row align-items-center">';
										echo '<h1>'.getLib('SIGNATURE_CODE').' (sha1)</h1>';
									echo '</div>';
									echo '<table class="table table-hover table-striped table-responsive">';
										echo '<thead>';
										echo '<tr>';
											echo '<th class="uw-w60 text-left">'.getLib('FICHIER').'</th>';
											echo '<th class="uw-w40 text-left">SHA1</th>';
										echo '</tr>';
										echo '</thead>';
										echo '<tbody>';
										echo drawlines($hashFinal);
									echo '</tbody>';
									echo '<tfoot>';
										echo '<tr class="table-success">';
										  echo '<td class="lead">'.getLib('SIGNATURE_FINALE').'</td>';
										  echo '<td class="text-monospace lead">'.$hashFinal.'</td>';
										echo '</tr>';
									echo '</tfoot>';
								echo '</table>';
							echo '</div>';
						echo '</div>';

						break;
					}

					//--------------------------------------
					// affiche le hash du code
					//--------------------------------------
					case 'hashfrontend':
					{
						function drawlines(&$hashs) {
							$fichiers = array(
								'../frontend/index.php', 
								'../frontend/input.php', 
								'../frontend/logout.php', 
								'../frontend/reponses-ajax.php', 
								'../frontend/css/cookieDisclaimer.css', 
								'../frontend/css/cookieDisclaimer.min.css', 
								'../frontend/css/styles.css', 
								'../frontend/js/jquery.cookieDisclaimer.min.js', 
								'../frontend/js/oXHR.js', 
								'../frontend/js/php.js', 
								'../frontend/js/resizable.min.js', 
								'../frontend/js/scripts.js', 
								'../frontend/js/universalform.js', 
								'../frontend/js/universalform.min.js',
								'../frontend/libs/briques/brique_coldroite.inc.php',
								'../frontend/libs/briques/brique_colgauche.inc.php',
								'../frontend/libs/briques/brique_debug.inc.php',
								'../frontend/libs/briques/brique_footer.inc.php',
								'../frontend/libs/briques/brique_message.inc.php',
								'../frontend/libs/briques/brique_panelappli.inc.php',
								'../frontend/libs/classes/Droits.class.php',
								'../frontend/libs/classes/Form_input.class.php',
								'../frontend/libs/classes/Form_login.class.php',
								'../frontend/libs/classes/Form_user.class.php',
								'../frontend/libs/classes/Fpdf.class.php',
								'../frontend/libs/classes/Ldap.class.php',
								'../frontend/libs/classes/Login.class.php',
								'../frontend/libs/classes/PageNavigator.class.php',
								'../frontend/libs/classes/Parametres.class.php',
								'../frontend/libs/classes/SilentMail.class.php',
								'../frontend/libs/classes/SimpleListingHelper.class.php',
								'../frontend/libs/classes/SqlSimple.class.php',
								'../frontend/libs/classes/UniversalCsvImport.class.php',
								'../frontend/libs/classes/UniversalDatabase.class.php',
								'../frontend/libs/classes/UniversalField.class.php',
								'../frontend/libs/classes/UniversalFieldArea.class.php',
								'../frontend/libs/classes/UniversalFieldBouton.class.php',
								'../frontend/libs/classes/UniversalFieldCheckbox.class.php',
								'../frontend/libs/classes/UniversalFieldComment.class.php',
								'../frontend/libs/classes/UniversalFieldDiv.class.php',
								'../frontend/libs/classes/UniversalFieldFiltreselect.class.php',
								'../frontend/libs/classes/UniversalFieldFiltretext.class.php',
								'../frontend/libs/classes/UniversalFieldHidden.class.php',
								'../frontend/libs/classes/UniversalFieldImage.class.php',
								'../frontend/libs/classes/UniversalFieldRadio.class.php',
								'../frontend/libs/classes/UniversalFieldSearch.class.php',
								'../frontend/libs/classes/UniversalFieldSelect.class.php',
								'../frontend/libs/classes/UniversalFieldSeparateur.class.php',
								'../frontend/libs/classes/UniversalFieldSwitch.class.php', 
								'../frontend/libs/classes/UniversalFieldText.class.php',
								'../frontend/libs/classes/UniversalForm.class.php',
								'../frontend/libs/classes/UniversalList.class.php',
								'../frontend/libs/classes/UniversalTree.class.php',
								'../frontend/libs/classes/UniversalZip.class.php',
								'../frontend/libs/classes/User.class.php',
								'../frontend/libs/langues/langue_fr.inc.php',
								'../frontend/libs/langues/langue_us.inc.php',
								'../frontend/libs/sql/sql_divers.inc.php',
								'../frontend/libs/sql/sql_droits.inc.php',
								'../frontend/libs/sql/sql_logs.inc.php',
								'../frontend/libs/sql/sql_users.inc.php',
								'../frontend/libs/common.inc.php',
								'../frontend/libs/db.connexion.pdo.oracle.php', 
								'../frontend/libs/db.connexion.pdo.php', 
								'../frontend/libs/defines.inc.php', 
								'../frontend/libs/droits.inc.php', 
								'../frontend/libs/fonctions.inc.php', 
								'../frontend/libs/init.inc.php', 
								'../frontend/libs/routines.inc.php', 
								'../frontend/libs/uw_chaines.php', 
								'../frontend/libs/uw_dates.php', 
								'../frontend/libs/uw_debug.php', 
								'../frontend/libs/uw_file.php', 
								'../frontend/libs/uw_flux.php', 
								'../frontend/libs/uw_ftp.php', 
								'../frontend/libs/uw_geo.php', 
								'../frontend/libs/uw_img.php', 
								'../frontend/libs/uw_mail.php', 
								'../frontend/libs/uw_nav.php');
							$hashs = '';
							$chaine = '';
							foreach($fichiers as $fichier) {
								$chaine.= '<tr>';
								if (file_exists($fichier)) {
									$hash = sha1_file($fichier);
									$hashs.= $hash;
									$chaine.= '<td class="uw-w60 text-left">'.$fichier.'</td>';
									$chaine.= '<td class="uw-w40 text-left text-monospace">'.$hash.'</td>';
								}
								else {
									$chaine.= '<td class="uw-w40 text-left"><span class="text-danger">'.getLib('ERREUR_FICHIER_INEXISTANT').'</span></td>';
									$chaine.= '<td></td>';
								}
								$chaine.= '</tr>';
							}
							$hashs = sha1($hashs);
							return $chaine;
						}

						echo '<div class="container-lg px-0">';
							echo '<div class="row">';
								echo '<div class="col">';
									echo '<div class="d-flex flex-row align-items-center">';
										echo '<h1>'.getLib('SIGNATURE_CODE').' (frontend)(sha1)</h1>';
									echo '</div>';
									echo '<table class="table table-hover table-striped table-responsive">';
										echo '<thead>';
										echo '<tr>';
											echo '<th class="uw-w60 text-left">'.getLib('FICHIER').'</th>';
											echo '<th class="uw-w40 text-left">SHA1</th>';
										echo '</tr>';
										echo '</thead>';
										echo '<tbody>';
										echo drawlines($hashFinal);
									echo '</tbody>';
									echo '<tfoot>';
										echo '<tr class="table-success">';
										  echo '<td class="lead">'.getLib('SIGNATURE_FINALE').'</td>';
										  echo '<td class="text-monospace lead">'.$hashFinal.'</td>';
										echo '</tr>';
									echo '</tfoot>';
								echo '</table>';
							echo '</div>';
						echo '</div>';

						break;
					}

					//--------------------------------------
					// COMMANDES NON RECONNUES
					//--------------------------------------
					default:
					{	
						riseErrorMessage(getLib('ERREUR_COMMANDE'));
						goReferer();
						break;
					}
				}

			echo '</div>';
		echo '</div>';

	echo '</article>';
	echo '</section>';

	//--------------------------------------
	// FOOTER
	//--------------------------------------
	include_once(_BRIQUE_FOOTER_);

	echo '</div>';		//container
echo '</body>';
echo '</html>';