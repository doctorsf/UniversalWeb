<?php
//--------------------------------------------------------------------------
// routines_nav.php
// Ensemble de routines orientées vers la navigation web et les traitements URL
//--------------------------------------------------------------------------
// 12.01.2015 :
//		- Modification de la fonction setPageBack() : on ne prend plus en compte une éventuelle page de retour mémorisée
//		- Ajout de la fonction hasPageBack() : dit si une page de retour est mémorisée
// 04.10.2015 :
//		- Amélioration de la fonction delUrlParameter. Elle marche maintenant dans tous les cas de figure
//		- Ajout de la fonction hasUrlParameter($url, $paramChoisi)
// 22.02.2017
//		- Ajout de la fonction getScriptName() qui renvoie le nom (et uniquement le nom) du script en cours
// 20.04.2017
//		- Ajout de la fonction goReferer() qui renvoie à la page appelante
// 14.06.2017
//		- Ajout de la fonction getUrlWithoutFirstSlash($url) qui supprimer le slash devant une url
// 28.06.2017
//		- Les fonctions setPageBack et goPageBack ont été modifiées pour fonctionner le la sorte : setPageBack doit être placé dans 
//			la page d'appel et non dans la page cible.
// 19.07.2017
//		- Ajout de la fonction usingSecureMode() qui dit si script http ou https
//		- Ajout de la fonction forceHttps() qui force l'appel des scipts en https
// 17.10.2017
//		- Ajout de la fonction getRefererScriptName() qui renvoie le script de la page referer (appelante)
// 04.11.2017
//		- Ajout de la fonction getVersionNavigateur() qui envoie la version, du navigateur utilisé par le lcient
// 03.01.2018
//		- Déplacement du fichier d'erreurs dans le répertoire LIBS : fonction userErrorHandler()
// 22.04.2019
//		- Modification userErrorHandler pour prise en compte du fichier d'erreur standardisé _PHP_FILE_ERRORS_
// 08.04.2020
//		- Correction bug de la fonction getUrlWithoutFirstSlash()
//--------------------------------------------------------------------------

//----------------------------------------------------------------------
// Afin de comprendre une erreur PHP dans un script, il est possible de
// gérer un fichier d'erreur à l'exécution de ce dernier.
// La mise en place du reporting d'erreurs se fait en positionnant les deux
// lignes suivantes dans le script
//		error_reporting(0);
//		$old_error_handler = set_error_handler("userErrorHandler");
//----------------------------------------------------------------------
function userErrorHandler ($errno, $errmsg, $filename, $linenum, $vars) { 
	$time=date("d M Y H:i:s"); 
    // Get the error type from the error number 
    $errortype = array (1    => "Error",
                        2    => "Warning",
                        4    => "Parsing Error",
                        8    => "Notice",
                        16   => "Core Error",
                        32   => "Core Warning",
                        64   => "Compile Error",
                        128  => "Compile Warning",
                        256  => "User Error",
                        512  => "User Warning",
                        1024 => "User Notice"); 

	$errlevel=$errortype[$errno]; 

	//Write error to log file (CSV format) 
	$errfile = fopen(_PHP_FILE_ERRORS_, 'a');
	fputs($errfile, $time."\t".$filename.' : '.$linenum."\t".($errlevel).' '.$errmsg."\t".$_SERVER['REQUEST_URI']."\t".$_SERVER['REMOTE_ADDR']."\r\n"); 
	fclose($errfile);

	if (($errno != 2) && ($errno != 8)) {
		//Terminate script if fatal errror
		die("Erreur fatale. Execution du script stoppée..."); 
	} 
}

//mise en place du reporting d'erreurs
//error_reporting(0); 
//$old_error_handler = set_error_handler("userErrorHandler");

//--------------------------------------------------------------------------
// Dit si le script utilise un mode sécurisé (https) ou normal (http)
// Entree : rien
// Sortie : true (mode sécurisé https) / false sinon
//--------------------------------------------------------------------------
function usingSecureMode()
{
   if (isset($_SERVER['HTTPS']))
	  return ($_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) == 'on');
   // $_SERVER['SSL'] exists only in some specific configuration
   if (isset($_SERVER['SSL']))
	  return ($_SERVER['SSL'] == 1 || strtolower($_SERVER['SSL']) == 'on');
   // $_SERVER['REDIRECT_HTTPS'] exists only in some specific configuration
   if (isset($_SERVER['REDIRECT_HTTPS']))
	  return ($_SERVER['REDIRECT_HTTPS'] == 1 || strtolower($_SERVER['REDIRECT_HTTPS']) == 'on');

   return false;
}

//--------------------------------------------------------------------------
// Force les urls passer en https
// Entree : rien
// Sortie : rien
//--------------------------------------------------------------------------
function forceHttps(){
	//création de l'url sécurisée
	$httpsUrl = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	//if (count($_POST) > 0)
	//	die('Page should be accessed with HTTPS, but a POST Submission has been sent here. Adjust the form to point to '.$httpsUrl);
	if (!usingSecureMode()) { 
		if (!headers_sent()) {
			header('Status: 301 Moved Permanently', false, 301);
			header('location: '.$httpsUrl);
			exit();
		}
		else {
		  die('<script>document.location.href="'.$httpsUrl.'";</script>');
		}
	}
}

//--------------------------------------------------------------------------
// Recupere la version de Internet Explorer
// Renvoie false si pas internet explorer ou version inconnue
// Renvoie la version sinon ('safari' ou 'firefox' ou 'chrome', ou 'ie ie8', 'ie ie9', 'ie ie10', 'ie ie11')
// Attention vérifier pour chaque navigateur car ne fonctionne pas à tous les coups
//--------------------------------------------------------------------------
function getVersionNavigateur()
{
    $browser = '';
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (preg_match('~(?:msie ?|trident.+?; ?rv: ?)(\d+)~', $ua, $matches)) $browser = 'ie ie'.$matches[1];
    elseif (preg_match('~(safari|chrome|firefox)~', $ua, $matches)) $browser = $matches[1];
	//$browser reçoit ici : 'safari' ou 'firefox' ou 'chrome', ou 'ie ie8', 'ie ie9', 'ie ie10', 'ie ie11'
	//on ne veux que : 'safari' ou 'firefox' ou 'chrome', ou 'ie7', 'ie8', 'ie9', 'ie10', 'ie11'
    return str_replace('ie ', '', $browser);
}

//--------------------------------------------------------------------------
// Cette fonction dit si le client est un moteur de recherche, un crawler
// Pour se faire on utilise le fait que les moteurs de recherche et crawlers
// NE RENSEIGNENT PAS la plupart du temps la variable serveur "HTTP_ACCEPT_LANGUAGE"
// Dans le cas ou cette valeur est quand même renseignée on recherche quelques 
// termes reconnaissable dans son "HTTP_USER_AGENT". Fiable à 95%
// Entree : Rien
// Sortie : true => c'est un bot ;  false => ce n'en est pas un
//--------------------------------------------------------------------------
function isAbot()
{	
	if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		return ((!empty($_SERVER['HTTP_USER_AGENT'])) && 
					preg_match('/bot|facebook|spider|coccoc|yahoo|twitterbot|grabber|search|crawler|^$/i', $_SERVER['HTTP_USER_AGENT']));
	}
	return true;
}

//--------------------------------------------------------------------------
// Récupere la langue de l'utilisateur par défaut et son pays
// Se base sur l'analyse la variable serveur "HTTP_ACCEPT_LANGUAGE" quand 
// elle existe. Dans le cas contraire défaut = français
// Entree / Sortie : 
//		$langue : retourne la langue de l'utilisateur (ISO 3166)
//		$pays : retourne le pays de l'utilisateur (ISO 3166)
//--------------------------------------------------------------------------
function getDefaultLanguage(&$langue, &$pays)
{
	$langue = 'fr';
	$pays = 'fr';

	if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) return;

	$http_accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	if (isset($http_accept) && strlen($http_accept) > 1) {
		//On peut exploser dans un tableau sur la virgule (ex : "fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3")
		$x = explode(',', $http_accept);
		foreach ($x as $val) {
			//recherche de valeurs q et création d'un tableau associatif. Pas de valeur q signifie un poids de 1
			//$matches[0] contiendra le texte qui satisfait le masque complet, $matches[1] contiendra le texte qui satisfait la première parenthèse capturante, etc.
			if (preg_match("/(.*);q=([0-1]{0,1}.d{0,4})/i", $val, $matches))
				$lang[$matches[1]] = (float)$matches[2];
			else
				$lang[$val] = 1.0;
		}
		//la langue est celle qui a la plus grande valeur de q
		$qval = 0.0;
		foreach ($lang as $key => $value) {
			if ($value > $qval) {
				$qval = (float)$value;
				$parts = explode('-', strtolower($key));
				$langue = $parts[0];
				$pays = $parts[0];
				if (isset($parts[1])) {
					$pays = $parts[1];
				}
			}
			if ($value == 1.0) break;		//on a un 1, on teste pas le reste (le premier 1 trouvé est considéré comme le bon)
		}
		//petites corrections : 
		//- si pas de pays on choisi les USA comme défaut
		//- transforme 'gb' en 'uk'
		if ($pays == 'en') $pays = 'us';
		elseif ($pays == 'gb') $pays = 'uk';
	}
}

//--------------------------------------------------------------------------
// Renvoi le nom (et extention) du script actuel (sans chemin)
//--------------------------------------------------------------------------
function getScriptName()
{
	$site = explode('/', $_SERVER['PHP_SELF']);
	return $site[count($site) - 1];
}

//--------------------------------------------------------------------------
// Renvoi le nom (et extention) du script référant (appelant)(sans chemin)
//--------------------------------------------------------------------------
function getRefererScriptName()
{
	if (isset($_SERVER['HTTP_REFERER'])) {
		$parts = parse_url($_SERVER['HTTP_REFERER']);
		$site = explode('/', $parts['path']);
		return $site[count($site) - 1];
	}
	return '';
}

//--------------------------------------------------------------------------
// Renvoi une url sans le slash de début
//--------------------------------------------------------------------------
function getUrlWithoutFirstSlash($url)
{
	return substr_replace($url, '', 0, 1);
}

//--------------------------------------------------------------------------
// Cette fonction évite le renvoi répétitif d'un formulaire en
// rafraichissant la page avec F5. On utilise pour cela une redirection.
// Tout est expliqué ici :
// http://fr.openclassrooms.com/informatique/cours/eviter-le-renvoi-repetitif-d-un-formulaire-en-rafraichissant
// Fonction à revoir. Ne fonctionne pas pour CINE-SONGES
//--------------------------------------------------------------------------
function cancelF5()
{
	if (!empty($_POST) OR !empty($_FILES)) {
		$_SESSION[_APP_ID_.'sauvegardePOST'] = $_POST ;
	    $_SESSION[_APP_ID_.'sauvegardeFILES'] = $_FILES ;
	    $fichierActuel = $_SERVER['PHP_SELF'] ;
		if(!empty($_SERVER['QUERY_STRING'])) {
	        $fichierActuel .= '?' . $_SERVER['QUERY_STRING'] ;
		}
		header('Location: ' . $fichierActuel);
		exit;
	}
	if (isset($_SESSION[_APP_ID_.'sauvegardePOST'])) {
	    $_POST = $_SESSION[_APP_ID_.'sauvegardePOST'] ;
		$_FILES = $_SESSION[_APP_ID_.'sauvegardeFILES'] ;
	    unset($_SESSION[_APP_ID_.'sauvegardePOST'], $_SESSION[_APP_ID_.'sauvegardeFILES']);
	}
}

//----------------------------------------------------------------------
// Relance la page d'appel au script
// Entree : rien
//----------------------------------------------------------------------
function goReferer()
{
	if (isset($_SERVER['HTTP_REFERER'])) {
		header('Location: '.$_SERVER['HTTP_REFERER']);
		die();
	}
	else {
		header('Location: '._URL_BASE_SITE_);
		die();
	}
}

//----------------------------------------------------------------------
// Sauvegarde de la page pour un éventuel retour
// Entree : 
//		$url (facultatif) : si vide, enregistre le script en cours, sinon l'url choisie
// Retour : Rien
//----------------------------------------------------------------------
function setPageBack($url='')
{
	if ($url != '') {
		$_SESSION[_APP_ID_.'PageRetour'] = $url;
	}
	else {
		$_SESSION[_APP_ID_.'PageRetour'] = $_SERVER['REQUEST_URI'];
	}
}

//----------------------------------------------------------------------
// Dit si une page de retour est actuellement sauvegardée
// Entree :	Aucune
// Retour : true (une page est sauvegardée) / false (non)
//----------------------------------------------------------------------
function hasPageBack()
{
	return (isset($_SESSION[_APP_ID_.'PageRetour']));
}

//----------------------------------------------------------------------
// Renvoie pour information l'éventuelle page de retour enregistrée.
// Si aucune page de retour n'est programmée, c'est la racine du site 
// qui est renvoyée
// Entree :	Aucune
// Retour : Le nom de la page
//----------------------------------------------------------------------
function echoPageBack()
{
	if (isset($_SESSION[_APP_ID_.'PageRetour']))
		return $_SESSION[_APP_ID_.'PageRetour'];
	else
		return _URL_BASE_SITE_;
}

//----------------------------------------------------------------------
// Renvoie pour information l'éventuelle nom du script de la page de retour 
// enregistrée. Si aucune page de retour n'est programmée, c'est le script de
// la racine du site qui est renvoyée.
// Entree :	Aucune
// Retour : Le nom du script
//----------------------------------------------------------------------
function echoScriptPageBack()
{
	if (isset($_SESSION[_APP_ID_.'PageRetour']))
		$pb = $_SESSION[_APP_ID_.'PageRetour'];
	else
		$pb = _URL_BASE_SITE_;
	$scriptName = explode('/', $pb);
	$scriptName = end($scriptName);
	return $scriptName;
}

//----------------------------------------------------------------------
// Efface la mémorisation d'une éventuelle page de retour
// Entree :	Aucune
// Retour : Rien
//----------------------------------------------------------------------
function clearPageBack()
{
	unset($_SESSION[_APP_ID_.'PageRetour']);
}

//----------------------------------------------------------------------
// Recharge la page de retour si elle existe, la page racine du site sinon.
// Opère une redirection vers la page stockées dans la variable de
// session $_SESSION[_APP_ID_.'PageRetour']. Si celle-ci est vide on redirige
// vers l'index
// Entree :
//		$delai (facultatif) : delai en ms avant de lancer le redirection
//----------------------------------------------------------------------
function goPageBack($delai=0)
{
	if(!empty($_SESSION[_APP_ID_.'PageRetour'])) {
		$url = $_SESSION[_APP_ID_.'PageRetour'];
		unset($_SESSION[_APP_ID_.'PageRetour']);
	}
	else {
		//on renvoie sur la page d'index
		$url = _URL_BASE_SITE_;
	}
	if ($delai != 0) {
		lance($url, $delai);		//lancement via javacript pour prendre en compte le delai
		die();
	}
	else {
		header('Location: '.$url);	//lancement via php
		die();
	}
}

//----------------------------------------------------------------------
// Lancement d'une page (utilise javascript)
// Entree : 
//		$url (url de la redirection)
//		$delai (facultatif) : délai en ms
//----------------------------------------------------------------------
function lance($url, $delai=0)
{
	echo '<script>';
	if ($delai == 0) 
		echo 'location.href="'.$url.'";';
	else 
		echo 'setTimeout("location.href=\"'.$url.'\"", '.$delai.');';
	echo '</script>';
	die();
}

//----------------------------------------------------------------------
// Supprime un parametre (choisi) des parametres d'une QUERY_STRING (url)
// ex : chaine = delUrlParameter($_SERVER['QUERY_STRING'], 'tri=')
// retour la chaine de caractères modifié
// Améliorée le 04/10/2015
//----------------------------------------------------------------------
function delUrlParameter($url, $paramChoisi)
{
	$tabUrl = parse_url($url);
	//s'il n'y a pas de paramètre on retourne le chemin (cela permet aussi de na pas remvoyer le ? si jamais il y en avait un)
	if (!isset($tabUrl['query'])) return $tabUrl['path'];
	//sinon on continue l'analyse
	$query = explode('&', $tabUrl['query']);
	//on supprime le parametre choisi de la query
	foreach($query as $index => $part) 
		if (strpos($part, $paramChoisi) === 0) unset($query[$index]);
	//debut de reconstruction
	$new_query = $tabUrl['path'];
	if (!empty($query)) {
		//ajout du ?
		$new_query.= '?';
		//on recolle les paramètres restants
		foreach($query as $index => $part) {
			$new_query.= $part;
			if ($index < (count($query) - 1)) $new_query.= '&';
		}
	}
	return $new_query;
}

//----------------------------------------------------------------------
// Dit si une url possède un paramètre $paramChoisi. Renvoie true ou false
//----------------------------------------------------------------------
function hasUrlParameter($url, $paramChoisi)
{
	return (strpos($url, $paramChoisi) !== false);
}

//----------------------------------------------------------------------
// Affichage de texte dans une boite de dialogue (javascript)
//----------------------------------------------------------------------
function dialogue($texte)
{
	echo '<script>';
	echo 'alert(\''.$texte.'\');';
	echo '</script>';
}

//----------------------------------------------------------------------
// Curseur en sablier
//----------------------------------------------------------------------
function curseur_wait() 
{
	echo '<script>';
	echo 'document.body.style.cursor=\'wait\';';
	echo '</script>';
}

//----------------------------------------------------------------------
// Curseur en flèche
//----------------------------------------------------------------------
function curseur_pointer() 
{
	echo '<script>';
	echo 'document.body.style.cursor=\'pointer\';';
	echo '</script>';
}

//----------------------------------------------------------------------
// Position du focus sur un champ de formulaire (utilise javascript)
//----------------------------------------------------------------------
function focus($leChamp)
{
	echo '<script type="text/javascript">';
	echo $leChamp.'.focus();';
	echo '</script>';
}