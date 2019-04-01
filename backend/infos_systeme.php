<?php
//-----------------------------------------------------------
// INFORMATIONS SYSTEME																
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8				
// 27.11.2018
//		- Ajout du pannel informations version APACHE
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

		$tabUrl = parse_url(_URL_SERVEUR_);
		$host = $tabUrl['host']; 
		//DEBUG_TAB_($tabUrl);

		echo '<div class="row">';
			echo '<div class="col">';
				echo '<p class="display-4">'.getLib('INFORMATIONS_SYSTEME').'</p>';

				//INFOS
				echo '<div class="card-columns">';
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
					echo '<div class="card">';
						$requete = "SHOW VARIABLES LIKE '%version%'";
						$res = executeQuery($requete, $nombre, _SQL_MODE_);
						//DEBUG_('$res', $res);
						echo '<div class="card-body">';
							echo '<h3 class="card-title">'.getLib('VERSION_MYSQL').'</h3>';
							echo '<p class="card-text">';
							echo getLib('VERSION').' : '.$res[3]['Value'].'<br />';
							echo getLib('VERSION_INNODB').' : '.$res[0]['Value'].'<br />';
							echo getLib('VERSION_PROTOCOLE').' : '.$res[1]['Value'].'<br />';
							echo '</p>';
						echo '</div>';
					echo '</div>';
					echo '<div class="card">';
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
							echo getLib('VERSION').' : '.$versionApache[0].' '.$os.'<br />';
							echo getLib('MODULE_AUTHENTIFICATION').' : '.$versionApache[1].'<br />';
							echo 'SSL : '.$versionApache[2].'<br />';
							//echo 'PHP : '.$versionApache[3].'<br />';
							echo '</p>';
						echo '</div>';
					echo '</div>';
				echo '</div>';

				//PHPINFO
				echo '<div class="card">';
					echo '<div class="card-body">';
						//echo '<h3 class="card-title">Infos PHP</h3>';
						echo '<style type="text/css">';
						echo '#phpinfo body, td, th, h1, h2 {font-family: sans-serif;}';
						echo '#phpinfo pre {margin: 0px; font-family: monospace;}';
						echo '#phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff;}';
						echo '#phpinfo a:hover {text-decoration: underline;}';
						echo '#phpinfo table {border-collapse: collapse; width:100%}';
						echo '#phpinfo .center {text-align: center;}';
						echo '#phpinfo .center table { margin-left: auto; margin-right: auto; text-align: left;}';
						echo '#phpinfo .center th { text-align: center !important; }';
						echo '#phpinfo td, th { border: 1px solid #000000; font-size: 100%; vertical-align: baseline;}';
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