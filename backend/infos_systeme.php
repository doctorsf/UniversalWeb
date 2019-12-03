<?php
//-----------------------------------------------------------
// INFORMATIONS SYSTEME																
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8				
// 27.11.2018
//		- Ajout du pannel informations version APACHE
// 15.04.2019
//		- Test de l'existence des classes UniverslaWeb pour affichage sans bug en cas de manque
// 22.04.2019
//		- Ajout de la remontée du fichier _PHP_FILE_ERRORS_
//		- Ajout de la remontée du fichier _FRONTEND_PHP_FILE_ERRORS_
//		- Affichage et navigation sous forme d'onglets
// 29.10.2019
//		- Affichage de toutes les informations connues de version MySQL
//-----------------------------------------------------------
require_once('libs/common.inc.php');

function MyClassExists($nomClasse) {
	if (file_exists(_CLASSES_.$nomClasse.'.class.php')) 
		return $nomClasse.' : '.$nomClasse::VERSION.'<br />'; 
	else 
		return $nomClasse.' : '.getLib('MANQUANT').'<br />';
}
function MyFrontendClassExists($nomClasse) {
	if (file_exists(_FRONTEND_CLASSES_.$nomClasse.'.class.php')) 
		return $nomClasse.' : '.$nomClasse::VERSION.'<br />'; 
	else 
		return $nomClasse.' : '.getLib('MANQUANT').'<br />';
}

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

		$tabUrl = parse_url(_URL_SERVEUR_);
		$host = $tabUrl['host']; 
		//DEBUG_TAB_($tabUrl);

		echo '<div class="row">';
			echo '<div class="col">';
				echo '<h1 class="d-none d-md-block">'.getLib('INFORMATIONS_SYSTEME').'</h1>';

				//tabs
				echo '<ul class="nav nav-tabs" id="myTab" role="tablist">';
					echo '<li class="nav-item">';
						echo '<a class="nav-link active" id="system-tab" data-toggle="tab" href="#system" role="tab" aria-controls="system" aria-selected="true">';
						echo '<span class="d-lg-none fas fa-desktop fa-2x" data-toggle="tooltip" title="'.getLib('SYSTEME').'"></span>';
						echo '<span class="d-none d-lg-inline-block ml-2">'.getLib('SYSTEME').'</span>';
						echo '</a>';
					echo '</li>';
					echo '<li class="nav-item">';
						echo '<a class="nav-link" id="php-infos-tab" data-toggle="tab" href="#php-infos" role="tab" aria-controls="php-infos" aria-selected="false">';
						echo '<span class="d-lg-none fab fa-php fa-2x" data-toggle="tooltip" title="'.getLib('INFOS_PHP').'"></span>';
						echo '<span class="d-none d-lg-inline-block ml-2">'.getLib('INFOS_PHP').'</span>';
						echo '</a>';
					echo '</li>';
					echo '<li class="nav-item">';
						echo '<a class="nav-link" id="config-tab" data-toggle="tab" href="#config" role="tab" aria-controls="config" aria-selected="false">';
						echo '<span class="d-lg-none fas fa-cogs fa-2x" data-toggle="tooltip" title="'.getLib('CONFIGURATION').'"></span>';
						echo '<span class="d-none d-lg-inline-block ml-2">'.getLib('CONFIGURATION').'</span>';
						echo '</a>';
					echo '</li>';
					echo '<li class="nav-item">';
						echo '<a class="nav-link" id="berrors-tab" data-toggle="tab" href="#berrors" role="tab" aria-controls="berrors" aria-selected="false">';
						echo '<span class="d-lg-none fas fa-bomb fa-2x" data-toggle="tooltip" title="'.getLib('ERREURS_BACKEND').'"></span>';
						echo '<span class="d-none d-lg-inline-block ml-2">'.getLib('ERREURS_BACKEND').'</span>';
						echo '</a>';
					echo '</li>';
					echo '<li class="nav-item">';
						echo '<a class="nav-link" id="ferrors-tab" data-toggle="tab" href="#ferrors" role="tab" aria-controls="ferrors" aria-selected="false">';
						echo '<span class="d-lg-none fas fa-bug fa-2x" data-toggle="tooltip" title="'.getLib('ERREURS_FRONTEND').'"></span>';
						echo '<span class="d-none d-lg-inline-block ml-2">'.getLib('ERREURS_FRONTEND').'</span>';
						echo '</a>';
					echo '</li>';
				echo '</ul>';

				//contenu des tabs
				echo '<div class="tab-content" id="myTabContent">';

					//------------------------------------------
					// INFOS SYSTEME
					//------------------------------------------
					echo '<div class="tab-pane fade show active" id="system" role="tabpanel" aria-labelledby="system-tab">';
						echo '<div class="row">';
							echo '<div class="col-12 col-lg-4 mt-3">';
								echo '<div class="card">';
									echo '<div class="card-body">';
										echo '<h3 class="card-title">'.getLib('VERSION_PHP').' : '.phpversion().'</h3>';
										echo '<p class="card-text">';
										echo getLib('MEM_REELLE_PHP').' : '.(memory_get_usage(true) / 1024 / 1024).' Mo<br />';
										echo getLib('MEM_USED_PHP').' : '.(memory_get_usage(false) / 1024 / 1024).' Mo<br />';
										echo '<a href="'.$_SERVER['REQUEST_SCHEME'].'://'.$host.'/phpmyadmin/" target="_blank">'.getLib('ACCEDER_PHPMYADMIN').'</a>';
										echo '</p>';
									echo '</div>';
								echo '</div>';
							echo '</div>';
							echo '<div class="col-12 col-lg-4 mt-3">';
								echo '<div class="card">';
									$requete = "SHOW VARIABLES LIKE '%version%'";
									$res = executeQuery($requete, $nombre, _SQL_MODE_);
									//DEBUG_('$res', $res);
									$indexColonnes = array_flip(array_column($res, 'Variable_name'));
									//DEBUG_('$test', $indexColonnes);
									echo '<div class="card-body">';
										echo '<h3 class="card-title">'.getLib('VERSION_MYSQL').'</h3>';
										echo '<p class="card-text">';
										//echo getLib('VERSION').' : '.$res[$indexColonnes['version']]['Value'].'<br />';
										//echo getLib('VERSION_INNODB').' : '.$res[$indexColonnes['innodb_version']]['Value'].'<br />';
										//echo getLib('VERSION_PROTOCOLE').' : '.$res[$indexColonnes['protocol_version']]['Value'].'<br />';
										//echo 'TLS Version'.' : '.$res[$indexColonnes['tls_version']]['Value'].'<br />';
										foreach ($indexColonnes as $key => $info) {
											echo '<b>'.$key.'</b> : '.$res[$info]['Value'].'<br />';
										}
										echo '</p>';
									echo '</div>';
								echo '</div>';
							echo '</div>';
							echo '<div class="col-12 col-lg-4 mt-3">';
								echo '<div class="card">';
									if(!function_exists('apache_get_version')){
										function apache_get_version() {
											if(!isset($_SERVER['SERVER_SOFTWARE']) || strlen($_SERVER['SERVER_SOFTWARE']) == 0){
												return '';
											}
											return $_SERVER['SERVER_SOFTWARE'];
										}
									}
									$versionApache = apache_get_version();
									//la valeur retournée est de la forme : Apache/2.4.29 (Win32) mod_authnz_sspi/0.1.0 OpenSSL/1.1.0g PHP/7.2.0
									//on isole l'os marqué entre parenthèses
									//DEBUG_('versionApache', $versionApache);
									$os = '';
									$res = getBetweenTags('(', ')', $versionApache);
									if ($res) {
										$os = '('.$res.')';
										$versionApache = str_replace($os, '', $versionApache);
									}
									//passage des informations sous forme de tableau
									$versionApache = trimUltime($versionApache);
									$versionApache = explode(' ', $versionApache);
									//DEBUG_('versionApache', $versionApache);
									//affichage
									echo '<div class="card-body">';
										echo '<h3 class="card-title">'.getLib('VERSION_APACHE').'</h3>';
										echo '<p class="card-text">';
										if (isset($versionApache[0])) echo getLib('VERSION').' : '.$versionApache[0].' '.$os.'<br />';
										if (isset($versionApache[1])) echo getLib('MODULE_AUTHENTIFICATION').' : '.$versionApache[1].'<br />';
										if (isset($versionApache[2])) echo 'SSL : '.$versionApache[2].'<br />';
										if (isset($versionApache[3])) echo 'PHP : '.$versionApache[3].'<br />';
										echo '</p>';
									echo '</div>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';

					//------------------------------------------
					// PHP INFOS
					//------------------------------------------
					echo '<div class="tab-pane fade" id="php-infos" role="tabpanel" aria-labelledby="php-infos-tab">';
						echo '<div class="card mt-3">';
							echo '<div class="card-body">';
								echo '<style type="text/css">';
								echo '#phpinfo pre {margin: 0px; font-family: monospace;}';
								echo '#phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff;}';
								echo '#phpinfo a:hover {text-decoration: underline;}';
								echo '#phpinfo table {border-collapse: collapse; width:100%}';
								echo '#phpinfo .center {text-align: center;}';
								echo '#phpinfo .center table { margin-left: auto; margin-right: auto; text-align: left;}';
								echo '#phpinfo .center th { text-align: center !important; }';
								echo '#phpinfo td {border: 1px solid #000000; font-size: 100%; vertical-align: baseline;}';
								echo '#phpinfo th {border: 1px solid #000000; font-size: 100%; vertical-align: baseline;}';
								echo '#phpinfo h1 {font-size: 150%;}';
								echo '#phpinfo h2 {font-size: 125%;}';
								echo '#phpinfo .p {text-align: left;}';
								echo '#phpinfo .e {background-color: #ccccff; font-weight: bold; color: #000000;}';
								echo '#phpinfo .h {background-color: #9999cc; font-weight: bold; color: #000000;}';
								echo '#phpinfo .v {background-color: #cccccc; color: #000000;}';
								echo '#phpinfo .vr {background-color: #cccccc; text-align: right; color: #000000;}';
								echo '#phpinfo img {float: right; border: 0px;}';
								echo '#phpinfo hr {width: 800px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}';
								echo '</style>';
								echo '<div id="phpinfo">';
									ob_start () ;
									phpinfo () ;
									$pinfo = ob_get_contents () ;
									ob_end_clean () ;
									// the name attribute "module_Zend Optimizer" of an anker-tag is not xhtml valide, so replace it with "module_Zend_Optimizer"
									echo ( str_replace ( "module_Zend Optimizer", "module_Zend_Optimizer", preg_replace ( '%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo ) ) ) ;
								echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';

					//------------------------------------------
					// CONFIGURATION APPLI
					//------------------------------------------
					echo '<div class="tab-pane fade" id="config" role="tabpanel" aria-labelledby="config-tab">';
						echo '<div class="row">';
							echo '<div class="col-12 col-lg-4 mt-3">';
								echo '<div class="card">';
									echo '<div class="card-body">';
										echo '<h3>'._APP_TITLE_.'</h3>';
										echo '_APP_SCHEMA_ : '.((_APP_SCHEMA_ == _SCHEMA_NATUREL_) ? 'Naturel' : 'Domaine').'<br />';
										echo '_APP_TITLE_ : '._APP_TITLE_.'<br />';
										echo '_APP_SLOGAN_ : '._APP_SLOGAN_.'<br />';
										echo '_AUTEUR_ : '._AUTEUR_.'<br />';
										echo '_COPYRIGHT_ : '._COPYRIGHT_.'<br />';
										echo '_EMAIL_WEBMASTER_ : '._EMAIL_WEBMASTER_.'<br />';
										echo '_IP_DEVELOPPEMENT_ : '.implode(' / ', _IP_DEVELOPPEMENT_).'<br />';
										echo '('.getLib('VOTRE_IP').' : '.$_SERVER['REMOTE_ADDR'].')<br />';
										echo '_APP_BLOWFISH_ : '._APP_BLOWFISH_.'<br />';
										echo '_RUN_MODE_ : '.((_RUN_MODE_ == _DEVELOPPEMENT_) ? 'Developpement' : 'Production').'<br />';
										echo '_ANNUAIRE_ : '._ANNUAIRE_.'<br />';
										echo '_SQL_MODE_: '._SQL_MODE_.'<br />';
										echo '_APP_VERSION_: '._APP_VERSION_.'<br />';
										echo '_APP_RELEASE_ : '._APP_RELEASE_.'<br />';
										echo '_HOST_SYSTEM_ : '._HOST_SYSTEM_.'<br />';
									echo '</div>';
								echo '</div>';
							echo '</div>';
							echo '<div class="col-12 col-lg-4 mt-3">';
								echo '<div class="card">';
									echo '<div class="card-body">';
										echo '<h3>UniversalWeb</h3>';
										echo MyClassExists('UniversalForm');
										echo MyClassExists('UniversalList');
										echo MyClassExists('UniversalTree');
										echo MyClassExists('UniversalZip');
										echo MyClassExists('UniversalCsvImport');
										echo MyClassExists('UniversalDatabase');
										echo MyClassExists('Fpdf');
										echo '<h3 class="mt-3 mb-0">UniversalWeb</h3>';
										echo '<p class="lead">(frontend)</p>';
										echo MyFrontendClassExists('UniversalForm');
										echo MyFrontendClassExists('UniversalList');
										echo MyFrontendClassExists('UniversalTree');
										echo MyFrontendClassExists('UniversalZip');
										echo MyFrontendClassExists('UniversalCsvImport');
										echo MyFrontendClassExists('UniversalDatabase');
									echo '</div>';
								echo '</div>';
							echo '</div>';
							echo '<div class="col-12 col-lg-4 mt-3">';
								echo '<div class="card">';
									echo '<div class="card-body">';
										echo '<h3>'.getLib('COMPOSANTS').'</h3>';
										echo '_JQUERY_VERSION_ : '._JQUERY_VERSION_.'<br />';
										echo '_BOOTSTRAP_VERSION_ : '._BOOTSTRAP_VERSION_.'<br />';
										echo '_FONTAWESOME_VERSION_ : '._FONTAWESOME_VERSION_.'<br />';
									echo '</div>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';

					//------------------------------------------
					// ERREURS PHP BACKEND
					//------------------------------------------
					echo '<div class="tab-pane fade" id="berrors" role="tabpanel" aria-labelledby="berrors-tab">';
						echo '<div class="card mt-3">';
							echo '<div class="card-body">';
								//lecture du fichier _PHP_FILE_ERRORS_ (format CSV avec délimiteur tabulation et sans entete)
								$lesErreurs = loadCSV(_PHP_FILE_ERRORS_, LOADCSV_INDICE, CODAGE_UTF8, LOADCSV_DELIMITER_TAB, false);
								//DEBUG_TAB_($lesErreurs);
								if (!empty($lesErreurs)) {
									function Col_bheure($ligne)		{echo $ligne[0];}
									function Col_bscript($ligne)	{echo $ligne[1];}
									function Col_bmessage($ligne)	{echo $ligne[2];}
									function Col_burl($ligne)		{echo $ligne[3];}
									function Col_bip($ligne)		{echo $ligne[4];}
									$cols_backend['bheure']		= SimpleListingHelper::createCol(array('name' => 'Date', 'size' => 5));
									$cols_backend['bscript']	= SimpleListingHelper::createCol(array('name' => 'Script : Line', 'size' => 30));
									$cols_backend['bmessage']	= SimpleListingHelper::createCol(array('name' => 'Message', 'size' => 30));
									$cols_backend['burl']		= SimpleListingHelper::createCol(array('name' => 'Url', 'size' => 30));
									$cols_backend['bip']		= SimpleListingHelper::createCol(array('name' => 'IP', 'size' => 5, 'align' => 'center'));
									SimpleListingHelper::getParams('bheure', $cols_backend, $page, $tri, $sens);
									//affichage du listing
									SimpleListingHelper::drawTotal(count($lesErreurs));
									echo '<table class="table table-hover table-responsive table-striped">';	//table-responsive table-striped table-sm 
										//affichage de l'entete
										SimpleListingHelper::drawHead($cols_backend, $tri, $sens, 'text-danger');
										//affichage du corps du tableau : donnees
										SimpleListingHelper::drawBody($cols_backend, $lesErreurs, $page);
									echo '</table>';
								}
								else {
									echo '<span class="text-success">'.getLib('ERREUR_AUCUNE').'</span>';
								}
							echo '</div>';
							if (file_exists(_PHP_FILE_ERRORS_)) {
								echo '<div class="card-footer text-center">';
									echo '<a href="'._URL_MAINTENANCE_.'?operation=reseterrors" class="btn btn-primary">'.getLib('PHP_ERROR_FILE_VIDER').'</a>';
								echo '</div>';
							}
						echo '</div>';
					echo '</div>';

					//------------------------------------------
					// ERREURS PHP FRONTEND
					//------------------------------------------
					echo '<div class="tab-pane fade" id="ferrors" role="tabpanel" aria-labelledby="ferrors-tab">';
						echo '<div class="card mt-3">';
							echo '<div class="card-body">';
								//lecture du fichier _FRONTEND_PHP_FILE_ERRORS_ (format CSV avec délimiteur tabulation et sans entete)
								$lesErreurs = loadCSV(_FRONTEND_PHP_FILE_ERRORS_, LOADCSV_INDICE, CODAGE_UTF8, LOADCSV_DELIMITER_TAB, false);
								//DEBUG_TAB_($lesErreurs);
								if (!empty($lesErreurs)) {
									function Col_fheure($ligne)		{echo $ligne[0];}
									function Col_fscript($ligne)	{echo $ligne[1];}
									function Col_fmessage($ligne)	{echo $ligne[2];}
									function Col_furl($ligne)		{echo $ligne[3];}
									function Col_fip($ligne)		{echo $ligne[4];}
									$cols_frontend['fheure']		= SimpleListingHelper::createCol(array('name' => 'Date', 'size' => 5));
									$cols_frontend['fscript']		= SimpleListingHelper::createCol(array('name' => 'Script : Line', 'size' => 30));
									$cols_frontend['fmessage']		= SimpleListingHelper::createCol(array('name' => 'Message', 'size' => 30));
									$cols_frontend['furl']			= SimpleListingHelper::createCol(array('name' => 'Url', 'size' => 30));
									$cols_frontend['fip']			= SimpleListingHelper::createCol(array('name' => 'IP', 'size' => 5, 'align' => 'center'));
									SimpleListingHelper::getParams('fheure', $cols_frontend, $page, $tri, $sens);
									//affichage du listing
									SimpleListingHelper::drawTotal(count($lesErreurs));
									echo '<table class="table table-hover table-responsive table-striped">';	//table-responsive table-striped table-sm 
										//affichage de l'entete
										SimpleListingHelper::drawHead($cols_frontend, $tri, $sens, 'text-danger');
										//affichage du corps du tableau : donnees
										SimpleListingHelper::drawBody($cols_frontend, $lesErreurs, $page);
									echo '</table>';
								}
								else {
									echo '<span class="text-success">'.getLib('ERREUR_AUCUNE').'</span>';
								}
							echo '</div>';
							if (file_exists(_FRONTEND_PHP_FILE_ERRORS_)) {
								echo '<div class="card-footer text-center">';
									echo '<a href="'._URL_MAINTENANCE_.'?operation=reseterrorsfrontend" class="btn btn-primary">'.getLib('PHP_ERROR_FILE_VIDER').'</a>';
								echo '</div>';
							}
						echo '</div>';
					echo '</div>';

				echo '</div>';  //fin contenu des tabs

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