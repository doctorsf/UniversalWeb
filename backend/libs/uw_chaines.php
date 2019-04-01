<?php
//--------------------------------------------------------------------------
// routines_chaines.php
// Ensemble de routines orientées chaines de caracteres et encodage
// Comprend aussi les routines spécialisées dans la manipulation des tableaux
// Comprend aussi les routines spécialisées dans le calcul
//--------------------------------------------------------------------------
// 11.10.2015 :
//		- Amélioration de la fonction rnTo13($valeur) pour prendre en compte les tableaux (array)
// 15.10.2015 : 
//		- Ajout de la fonction removeSlashes() qui supprimes les slashes (de textes -> ex : l\'hotel) y compris dans les tableaux
// 18.10.2015
//		- Correction bug fonction removeSlashes()
//		- Création de la fonction stringToArray()
// 13.11.2015
//		- déclaration des constantes CODAGE_ANSI et CODAGE_UTF8
// 25.02.2016
//		- Correction bug transposePourUrl (envoyait un notice si entrée $str était vide -> maintenant renvoie $str)
// 18.03.2016
//		- Création de la fonction convert_utf8($string) qui convertit une chaine de caractère codée 'Windows-1252' en UTF-8
//		- Création de la fonction utf8_file($leFichier) qui convertit le contenu d'un fichier éventuellement 
//			codé 'Windows-1252' en UTF-8
// 12.12.2016
//		- Amélioration "oteArticlesDebut"
// 21.07.2017
//		- La fonction truncateText utilise maintenant la fonction native PHP mb_strimwidth
// 29.09.2017
//		- Correction bug "oteArticlesDebut"
// 11.10.2017
//		- Création de la fonction "utf8_strpos"
//		- renommage de la fonction mb_ucwords() en utf8_ucwords()
//		- renommage de la fonction mb_right() en utf8_right()
//		- renommage de la fonction mb_left() en utf8_left()
// 28.11.2017
//		- Ajout de la fonction array_delkey_with_reset_keys qui supprime une clé d'un tableau et reset les clés
// 03.01.2018
//		- Petite amélioration de transposePourUrl
//		- Ajout des fonctions noDoubleSpace() et trimUltime()
//		- Amélioration de la fonction convert_utf8()
// 11.01.2018
//		- fonction rnTo13 : la fonction est déclarée si elle ne l'est pas déjà par ailleurs car exite la classe UniversalForm la déclare aussi
// 17.01.2018
//		- Amélioration fonction transposePourUrl()
// 13.02.2018
//		- Modification de la fonction convert_utf8()
//		- Ajout de la fonction convert_ansi()
//		- Ajout de la fonction encode()
// 11.05.2018
//		- Ajout de la fonction array_flip_key()
// 28.05.2018
//		- Amélioration de la fonction oteAccents() pour que cela marche dans tous les codagezsq (comme UTF-8)
// 30.11.2018
//		- Fonction transposePourUrl() -> Correction Bug suite à passage en PHP 7.2 -> $str[0] = '' ne marche plus !
//			il faut remplacer par $str = substr($str, 1)
//--------------------------------------------------------------------------

defined('CODAGE_ANSI')				|| define('CODAGE_ANSI', 0);
defined('CODAGE_UTF8')				|| define('CODAGE_UTF8', 1);

//=========================================================
// TRAITEMENT CHAINES DE CARACTERES
//=========================================================

//--------------------------------------------------------------------------
// Supprimer tous les espaces d'une chaine lorsqu'il y en a plus de 2 consécutifs
// Entrée : 
//		$chaine : la chaine à analyser
// Retour : 
//		la chaine modifiée
//--------------------------------------------------------------------------
function noDoubleSpace($chaine) {
	return preg_replace('/\s{2,}/', ' ', $chaine);
}

//--------------------------------------------------------------------------
// Supprimer tous les espaces d'une chaine lorsqu'il y en a plus de 2 consécutifs
// plus les espaces insécables et les espaces de début et de fin de la chaine
// Entrée : 
//		$chaine : la chaine à analyser
// Retour : 
//		la chaine modifiée
//--------------------------------------------------------------------------
function trimUltime($chaine) {
	//supprime les espaces insécables
	$chaine = str_replace('&nbsp;', ' ', $chaine);
	//supprime les doublons d'espaces (deux espace deviennent 1 etc.)
	$chaine = preg_replace('/\s{2,}/', ' ', $chaine);
	//supprime les espaces avant et après la chaine
	$chaine = trim($chaine);
	return $chaine;
}

//--------------------------------------------------------------------------
// Converti une chaine de caractère encodée 'Windows-1252' en 'utf-8' si nécessaire
// voir https://fr.wikipedia.org/wiki/Windows-1252
// Entree : 
//		$string : la chaine à encoder
// Retour
//		La chaine encodée UTF-8
//--------------------------------------------------------------------------
function convert_utf8($string) {
	$encodageActuel = mb_detect_encoding($string, 'UTF-8, ISO-8859-1, Windows-1252, CP1252', true);
	//echo $encodageActuel ;
    if ($encodageActuel === false) { 
		//encodage actuel ne peut pas être détecté
		return $string;
	}
    elseif ($encodageActuel === 'UTF-8') { 
		//déjà encodé utf-8
		return $string;
	}
	else {
		//$string n'est pas en UTF-8 -> on encode
		return iconv($encodageActuel, 'UTF-8//TRANSLIT', $string);
	}
}

//--------------------------------------------------------------------------
// Converti une chaine de caractère encodée 'utf-8' en 'Windows-1252' si nécessaire
// voir https://fr.wikipedia.org/wiki/Windows-1252
// Entree : 
//		$string : la chaine à encoder
// Retour
//		La chaine encodée ANSI
//--------------------------------------------------------------------------
function convert_ansi($string) {
	$encodageActuel = mb_detect_encoding($string, 'UTF-8, ISO-8859-1, Windows-1252, CP1252', true);
	//echo $encodageActuel;
    if ($encodageActuel === false) { 
		//encodage actuel ne peut pas être détecté
		return $string;
	}
    elseif ($encodageActuel === 'UTF-8') { 
		//on encode en ANSI (ISO-8859-1, Windows-1252, CP1252)
		//voir https://fr.wikipedia.org/wiki/Windows-1252
		return iconv($encodageActuel, 'ISO-8859-1//TRANSLIT', $string);
	}
	else {
		//$string est déjà en AINSI
		return $string;
	}
}

//--------------------------------------------------------------------------
// Encode une chaine de caractère UTF-8 ou ANSI si nécessaire
// voir https://fr.wikipedia.org/wiki/Windows-1252
// Entree : 
//		$string : la chaine à encoder
//		$codage : encodage choisi CODAGE_UTF8 ou CODAGE_ANSI (défaut)
// Retour
//		La chaine encodée
//--------------------------------------------------------------------------
function encode($string, $codage=CODAGE_ANSI) {
	//on commence par détecter l'encodage actuel de la chaine
	$encodageActuel = mb_detect_encoding($string, 'UTF-8, ISO-8859-1, Windows-1252, CP1252', true);
	if ($codage == CODAGE_ANSI) {
		if ($encodageActuel == 'UTF-8') {
			//encode UTF-8 en ANSI
			return iconv($encodageActuel, 'ISO-8859-1//TRANSLIT', $string);
		}
		else {
			//sinon (codageActuel non trouvé ou autre que UTF-8) pas de changement
			return $string;
		}
	}
	elseif ($codage == CODAGE_UTF8) {
		if ($encodageActuel == 'UTF-8') {
			//pas d'encodage, on est déjà en UTF-8
			return $string;
		}
		else {
			//encode ANSI en UTF-8
			return iconv($codageActuel, 'UTF-8//TRANSLIT', $string);
		}
	}
}

//--------------------------------------------------------------------------
// Charge un fichier et renvoi son contenu en utf-8 si nécessaire
// Entree : 
//		$leFichier : le fichier à charger
// Retour : 
//		Tableau de lignes représentant le contenu du fichier
//--------------------------------------------------------------------------
function utf8_file($leFichier) {
	$lines = file($leFichier, FILE_IGNORE_NEW_LINES);
	foreach($lines as $indice => $line) {
		$lines[$indice] = convert_utf8($line);
	}
	return $lines;
}

//----------------------------------------------------------------------
// Transforme une chaine en majuscule compatibles en UTF-8
//----------------------------------------------------------------------
function utf8_strtoupper($chaine) {
	return mb_strtoupper($chaine, 'UTF-8');
}

//----------------------------------------------------------------------
// Transforme une chaine en minuscules compatibles en UTF-8
//----------------------------------------------------------------------
function utf8_strtolower($chaine) {
	return mb_strtolower($chaine, 'UTF-8');
}

//----------------------------------------------------------------------
// Met en majuscule la première lettre d'une chaine. Compatibles en UTF-8
//----------------------------------------------------------------------
function utf8_ucfirst($chaine) {
	mb_internal_encoding('UTF-8');
	return mb_strtoupper(mb_substr($chaine, 0, 1)).mb_substr($chaine, 1);
}

//----------------------------------------------------------------------
// Renvoie les $longueur premiers caractères d'une chaine en tenant
// Codage UTF-8 (anciennement mb_left)
// Entree :
//		$texte : le texte concerné
//		$longueur : longueur max
// Retour : le texte formatté
//----------------------------------------------------------------------
function utf8_left($texte, $longueur) {
	return mb_substr($texte, 0, $longueur, 'UTF-8');
}

//----------------------------------------------------------------------
// Renvoie les caractères d'une chaine à droite de $position
// Codage UTF-8 (anciennement mb_right)
// Entree :
//		$texte : le texte concerné
//		$position : position ou commencer à couper
// Retour : le texte formatté
//----------------------------------------------------------------------
function utf8_right($texte, $position) {
	return mb_substr($texte, $position, mb_strlen($texte), 'UTF-8');
}

//----------------------------------------------------------------------
// Met en majuscule la première lettre de tous les mots (utf-8)
// Permet de mettre les accentués majuscules (anciennement mb_ucwords)
//----------------------------------------------------------------------
function utf8_ucwords($str) { 
    $str = mb_convert_case($str, MB_CASE_TITLE, 'UTF-8'); 
    return $str; 
}

//----------------------------------------------------------------------
// Retourne la taille d'une chaîne (utf-8)
//----------------------------------------------------------------------
function utf8_strlen($valeur) {
	return mb_strlen($valeur, 'UTF-8');
}

//----------------------------------------------------------------------
// Lit une sous-chaîne (utf-8)
//----------------------------------------------------------------------
function utf8_substr($string, $start, $length) {
	return mb_substr($string, $start, $length, 'UTF-8');
}

//----------------------------------------------------------------------
// Repère la première occurrence d'un caractère dans une chaîne (utf-8)
//----------------------------------------------------------------------
function utf8_strpos($haystack, $needle, $offset=0) {
	return mb_strpos($haystack, $needle, $offset, 'UTF-8');
}

//----------------------------------------------------------------------
// Renvoie un texte tronqué à la longueur max + ... si la longueur du
// texte est supérieure à la taille demandée. Sinon renvoie le texte
// en entier
// Entree :
//		$texte : le texte concerné
//		$longueur : longueur max
// Retour : renvoie le texte formatté
//----------------------------------------------------------------------
function truncateText($texte, $longueur) {
	return mb_strimwidth($texte, 0, $longueur, '&hellip;', 'UTF-8');
}

//----------------------------------------------------------------------
// Vérifie la cohérence de la construction d'une email
// RETOUR : renvoie TRUE si syntaxe correcte, FALSE sinon
//----------------------------------------------------------------------
function isValidEmail($email) {
	$res = mb_eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$", $email);
	if($res == false) return false;
	else return true;
}

//--------------------------------------------------------------------------
// Test d'une expression rationnelle standard REGEX
// Entree : 
//		$regex : l'expression rationnelle
//		$valeur  : la valeur à tester
// Retour : 
//		true : la valeur correspond bien à l'expression rationnelle $regex
//		false : la valeur ne correspond pas à l'expression rationnelle $regex
//--------------------------------------------------------------------------
function regexTest($regex, $valeur) {
	return preg_match($regex, $valeur);
}

//----------------------------------------------------------------------
// OBTENIR UNE CHAINE COMPRISE ENTRE DEUX TAGS
// exemple : permet d'obtenir la chaine après le tag [ra]
// [f]un roman[a]a novel[rf]The Sentinel[ra]The Sentinel
//----------------------------------------------------------------------
function getBetween($tagdebut, $tagfin, $temp)
{
  if (($tagdebut == '') && ($tagfin == ''))
    return $temp;
  if (($tagdebut == '') && ($tagfin <> ''))
      return substr($temp, 0, strpos($temp, $tagfin));
  if (($tagdebut <> '') && ($tagfin == ''))
     return substr($temp, strpos($temp, $tagdebut) + strlen($tagdebut), strlen($temp) - strpos($temp, $tagdebut) - strlen($tagdebut));
  if (($tagdebut <> '') && ($tagfin <> ''))
      return substr($temp, strpos($temp, $tagdebut) + strlen($tagdebut), strpos($temp, $tagfin) - strpos($temp, $tagdebut) - strlen($tagdebut));
}

//----------------------------------------------------------------------
// renvoie une sous-chaine comprise entre 2 tags.
// fonction sensible à la casse
// entree :
//		$tagdebut : le tag de début (peut être composé de plusieurs caractères)
//		$tagfin : le tag de fin (peut être composé de plusieurs caractères)
//		$temp : la chaine à matcher
// retour :
//		la sous-chaine si elle existe
//		false si l'un des tags n'existe pas
//----------------------------------------------------------------------
function getBetweenTags($tagdebut, $tagfin, $temp)
{
	if (($posStart = strpos($temp, $tagdebut)) === false) {return false;}				//pas trouvé le tag de début
	$posStart += strlen($tagdebut);														//calcul de l'indice de départ
	if (($posEnd   = strpos($temp, $tagfin, $posStart)) === false) {return false;}		//pas trouve le tag de fin
	$long = $posEnd - $posStart;														//calcul de la longueur
	return substr($temp, $posStart, $long);
}

//----------------------------------------------------------------------
// Renvoie le debut d'une chaine jusqu'à la rencontre d'un tag (non renvoyé)
// entree :
//		$chaine : la chaine
//		$tag : le tag à parti duquel couper
// retour :
//		la chaine en retour
//----------------------------------------------------------------------
function deleteFromTagToEnd($chaine, $tag)
{
	return mb_substr($chaine, 0, mb_strpos($chaine, $tag));
}

//----------------------------------------------------------------------
// Cette methode renvoie les codes asci d'une chaine de caractere
//----------------------------------------------------------------------
function getAsciiTranslation($chaine)
{
	$resultat = '';
	for ($i = 0; $i < mb_strlen($chaine); $i++) 
		$resultat.= ord($chaine[$i]).' '; 
	return $resultat;
}

//----------------------------------------------------------------------
// Cette methode supprime les : . , ; ' ? ! & d'une chaine de caractères
//----------------------------------------------------------------------
function otePonctuation(&$valeur)
{
  $valeur = str_replace(".", "", $valeur);
  $valeur = str_replace(":", "", $valeur);
  $valeur = str_replace("\"", "", $valeur);
  $valeur = str_replace("'", "", $valeur);
  $valeur = str_replace(";", "", $valeur);
  $valeur = str_replace(",", "", $valeur);
  $valeur = str_replace("?", "", $valeur);
  $valeur = str_replace("!", "", $valeur);
  $valeur = str_replace("-", "", $valeur);
  $valeur = str_replace("&", "", $valeur);
}

//----------------------------------------------------------------------
// Cette fonction supprime les accents d'une chaine de caractères 
// 28.05.2018 : Amélioration de la fonction pour que cela marche dans tous les codagezsq (comme UTF-8)
//----------------------------------------------------------------------
function oteAccents($valeur)
{
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
    );
    return strtr($valeur, $table);
}

//----------------------------------------------------------------------
// Cette methode supprime TOUS les articles et pas seulement ceux du début
//----------------------------------------------------------------------
function oteArticles(&$valeur)
{
	$avirer = array('le ', 'les ', 'la ', 'the ', 'l\'');
	return str_replace($avirer, '', $valeur);
}

//----------------------------------------------------------------------
// Cette methode supprime SEULEMENT les articles du début de chaine
//----------------------------------------------------------------------
function oteArticlesDebut($valeur)
{
	return preg_replace('/^(la )|^(le )|^(les )|^(the )|^(l\')|(l\\\\\' )/i', '', $valeur, 1);
}

//----------------------------------------------------------------------
// Cette fonction renvoie un titre avec l'article à la fin
//----------------------------------------------------------------------
function setArticleFin($valeur)
{
  	//cas d'une chaine vide
	if($valeur == '') return '';

	//recherche du l'
	if (strpos(utf8_strtolower($valeur), 'l\'') === 0) {  
		return utf8_ucfirst(utf8_substr($valeur, 2, utf8_strlen($valeur) - 2)).' (l\')';
	}
	if (strpos(utf8_strtolower($valeur), 'l\\\'') === 0) {  
		return utf8_ucfirst(utf8_substr($valeur, 3, utf8_strlen($valeur) - 3)).' (l\\\')';
	}

    //pour tout le reste
	$pieces = explode(' ', $valeur);
	$pieces[0] = utf8_strtolower($pieces[0]);
	if (($pieces[0] == 'les') || ($pieces[0] == 'le') || ($pieces[0] == 'la') || ($pieces[0] == 'the')) {
		$article = $pieces[0]; 
		unset($pieces[0]);
		return utf8_ucfirst(implode(' ', $pieces)).' ('.$article.')';
	}
	//sinon on renvoie la valeur : pas d'article
	return utf8_ucfirst($valeur);
}

//----------------------------------------------------------------------
// Problematique :
// MySQL code les retours chariot avec chr(13)
// Notepad code les retours chariot avec chr(13).chr(10)
// les <textarea> des <form> code avec des \r\n
//----------------------------------------------------------------------
// rnTo13 : Transforme les \r\n (textearea) en chr(13).chr(10)
// (equivalent "\r\n")
// donc utile pour une entree provenant de textarea
//----------------------------------------------------------------------
// 11.01.2018 : la fonction est déclarée si elle ne l'es pas déjà par ailleurs
// car exite la classe UniversalForm la déclare aussi
//----------------------------------------------------------------------
if (!function_exists('rnTo13')) {
	function rnTo13($valeur)
	{
		if (is_array($valeur)) {
			foreach($valeur as $index => $dummy) {
				$valeur[$index] = str_replace('\r\n', "\r\n", $valeur[$index]);
			}
			return $valeur;
		}
		return str_replace('\r\n', "\r\n", $valeur);
	}
}

//----------------------------------------------------------------------
// c13ToBr : Transforme les chr(13)chr(10) et chr(13) en code <br />
// donc utile pour une entree de MySQL vers HTML
// INFO : le comportement de cette fonction est différent de la fonction
// php nl2br qui ne remplace que "\r" (code 13)
//----------------------------------------------------------------------
function c13ToBr($valeur)
{
	$order = array("\r\n", "\r");
	// ou : $order = array(chr(13).chr(10), chr(13));
	return str_replace($order, '<br />', $valeur);
}

//----------------------------------------------------------------------
// mySql2Notepad : Transforme les chr(13) en sequence chr(13).chr(10) et ne
// touche pas aux sequence chr(13).chr(10)
//----------------------------------------------------------------------
function mySql2Notepad($valeur)
{
	$retour = str_replace(chr(13).chr(10), chr(13), $valeur);
	$retour = str_replace(chr(13), chr(13).chr(10), $retour);
	return $retour;
}

//----------------------------------------------------------------------
// nl2rn : Transforme les chr(13).chr(10) en \r\n
//----------------------------------------------------------------------
function nl2rn($valeur)
{
	return str_replace(chr(13).chr(10), '\r\n', $valeur);
}

//----------------------------------------------------------------------
// br2n : Transforme les <br /> en "\n"
//----------------------------------------------------------------------
function br2n($valeur)
{
	$order = array('<br>', '<br />');
	return str_replace($order, "\n", $valeur);
}

//----------------------------------------------------------------------
// Supprimer le dernier retour chariot inutile d'une chaine de caratère
// provenant d'un "textarea". (si exite, la chaine se termine par '\r\n'.
// La fonction supprime ce caractère final
//----------------------------------------------------------------------
function oteLastRn($valeur)
{
	$pos = mb_strrpos($valeur, '\r\n');	//recherche dernière occurence de du code '\r\n'
	if (($pos !== false) && ($pos == (mb_strlen($valeur) - mb_strlen('\r\n')))) {
		return mb_left($valeur, $pos);
	}
	return $valeur;
}

//----------------------------------------------------------------------
// Création d'un mot de passe aléatoire
// entree : 
//		$nombre : nombre de caractère
//----------------------------------------------------------------------
function getRandomPassword($nombre)
{
	$code = '';
	for($i=1; $i<=$nombre; $i++)
	{
		$plage = rand(1,3);
		if ($plage == 1) $lettre = chr(rand(48, 57));   //chiffres
		if ($plage == 2) $lettre = chr(rand(65, 90));   //majuscules
		if ($plage == 3) $lettre = chr(rand(97, 122));  //minuscules
		$code.= $lettre;
	}
	return $code;
}

//----------------------------------------------------------------------
// Supprime les slashes d'une valeur, même les tableaux
// Entree : 
//		$data : la donnée à laquelle supprimer les slashes
// Retour : 
//		La données sans les slashes
//----------------------------------------------------------------------
function removeSlashes($data)
{
	if (is_array($data)) {
		foreach($data as $index => $dummy) {
			$data[$index] = stripslashes($data[$index]);
		}
	}
	else {
		$data = stripslashes($data);
	}
	return $data;
}

//----------------------------------------------------------------------
// Cette methode transpose en chaine de caractere valable pour une url une
// chaine passée en paramètre. Concrètement cela sert à générer des url pour
// pour faire de l'url rewriting
//----------------------------------------------------------------------
// 30.11.2018
//		Correction Bug suite à passage en PHP 7.2 -> $str[0] = '' ne marche plus !
//			il faut remplacer par $str = substr($str, 1)
//----------------------------------------------------------------------
function transposePourUrl($str)
{
	$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
	if (empty($str)) return $str;
	$replace = array('.', '"', ':', ';', ',', '%', '?', '!', '+', '&', '*', '(', ')', '[', ']', '/', '\\');
	$str = str_replace($replace, '', $str);
	$str = str_replace('²', '2', $str);
	$str = str_replace('³', '3', $str);
	$str = str_replace('€', 'e', $str);
	$replace = array('\'', '#x27', ' - ', ' : ', '  ', ' -- ', ' ');
	$str = str_replace($replace, '-', $str);
    $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
	$str = str_replace('-amp-', '-', $str);
	$str = str_replace('--', '-', $str);
	if ($str[0] == '-') $str = substr($str, 1);    //supprimer le premier '-' s'il en reste un
	$str = strtolower($str); 
    return $str;
}

//----------------------------------------------------------------------
// Extrait le nom d'un fichier (sans extention) d'une adresse complete
// entrée :
//		$valeur : adresse à étudier
// retour (chaine)
//		le nom du fichier sans extension ou $valeur si pas possible
//----------------------------------------------------------------------
function getFileNameFromUrl($valeur)
{
	$dummy = explode('/', $valeur);
	$dummy2 = explode('.', $dummy[count($dummy)-1]);
	return $dummy2[0];
}




//=========================================================
// FONCTIONS SPECIALISEES CALCULS
//=========================================================



//----------------------------------------------------------------------
// Tronque un réel à 2 décimales sans l'arrondir
//----------------------------------------------------------------------
function trunc($val) {
	return floor($val * 100) / 100;
}

//----------------------------------------------------------------------
// Fonction division entière
// Entrée : 
//		$a : dividende (valeur à diviser)
//		$b : diviseur
// Sortie :
//		Le résultat de la division entière
//----------------------------------------------------------------------
function div($a, $b) {
	return ($a - ($a % $b)) / $b;
}

//----------------------------------------------------------------------
// Calcul de modulo
// Entrée : 
//		$a : dividende (valeur à diviser)
//		$b : diviseur
// Sortie :
//		Le module (reste de la division entière)
//----------------------------------------------------------------------
function mod($a, $b) {
	return ($a % $b);
}




//=========================================================
// FONCTIONS SPECIALISEES SUR LES TABLEAUX
//=========================================================




//----------------------------------------------------------------------
// Transforme le contenu d'une tableau de lignes de texte en une seule
// et unique chaine de caractere avec un chr(13) comme retour chariot
// paramètres d'entrée : le tableau de chaines
// sortie			   : la chaine de caractères résultante
//----------------------------------------------------------------------
function arrayToString($tableau)
{
	if (empty($tableau)) return '';
	return implode(chr(13), $tableau);
}

//----------------------------------------------------------------------
// Transforme une chaine de caractere en un tableau de lignes, chaque ligne
// étant caracterisée par un chr(13) comme code retour chariot
// Si la chaine est vide, un tableau vide est renvoyé.
// Entree : 
//		$chaine : chaine de caractère à convertir
//		&$tableau : le tableau de résultat
//		$deleteEmptyLines : booleen -> dit si on souhaite supprimer les lignes vides du tableau
// Retour : 
//		Aucun
//----------------------------------------------------------------------
function stringToArray($chaine, $deleteEmptyLines=false)
{
	$retour = array();
	if (empty($chaine)) return $retour;
	$leTexte = explode(chr(13), $chaine);
	foreach($leTexte as $ligne) {
		$ligne = Trim($ligne);
		if (!(($deleteEmptyLines) && ($ligne == ''))) { 
			$retour[] = Trim($ligne);
		}
	}
	return $retour;
}

//---------------------------------------------------------
// fonction qui crée un tableau à partir du tableau $tableau en 
// changeant la clé par la valeur $cle du tableau d'entrée
// ex soit le tableau en entrée : 
//    [0] => Array (
//            [id] => 4
//            [libelle] => voiture)
//    [1] => Array (
//            [id] => 5
//            [libelle] => moto)
//    [2] => Array (
//            [id] => 9
//            [libelle] => vélo)
// avec l'appel à array_flip_on_key($tableau, 'id')
// renvoie le tableau suivant
//    [4] => Array (
//            [id] => 4
//            [libelle] => voiture)
//    [5] => Array (
//            [id] => 5
//            [libelle] => moto)
//    [9] => Array (
//            [id] => 9
//            [libelle] => vélo)
// ATTENTION : ne fonctionne pas si la $cle n'est pas unique
//---------------------------------------------------------
// Entrée : 
//		$tableau : tableau en entrée
//		$cle : clé de $tableau qui servira de clé au tableau en sortie
// Retour : 
//		le tableau de sortie
//---------------------------------------------------------
function array_flip_key($tableau, $cle) {
	$tEntree = array_flip(array_column($tableau, $cle));
	foreach ($tEntree as $key => $value) {
		$tSortie[$key] = $tableau[$value];
	}
	return $tSortie;
}

//----------------------------------------------------------------------
// Renvoi un tableau dédoublonné et reset les clé
// ex : [0] => Yellow [1] => Green [2] => Yellow [3] => Blue [4] => Yellow
// renvoi : [0] => Yellow [1] => Green [2] => Blue
//----------------------------------------------------------------------
function array_unique_with_reset_keys($tableau)
{
	return array_keys(array_flip($tableau));
}

//----------------------------------------------------------------------
// Supprime une clé d'un tableau et reset les clés
// ex : tableau en entrée : [0] => Yellow [1] => Green [2] => Yellow [3] => Blue [4] => Yellow
//		clé à supprimée '1'
// renvoie : [0] => Yellow [1] => Yellow [2] => Blue [3] => Yellow
//----------------------------------------------------------------------
function array_delkey_with_reset_keys($tableau, $keyToDelete)
{
	unset($tableau[$keyToDelete]);
	return array_keys(array_flip($tableau));
}

//----------------------------------------------------------------------
// Renvoi un tableau sans les cellules contenant la valeur choisie
// ex : [0] => Yellow [1] => Green [2] => Yellow [3] => Blue [4] => Yellow
// array_delete_cell($tableau, 'Yellow')
// renvoi : [1] => Green [3] => Blue
//----------------------------------------------------------------------
function array_delete_cell($tableau, $valeur)
{
	$tabPositions = array_keys($tableau, $valeur);
	foreach($tabPositions as $key) {
		unset($tableau[$key]);
	} 
	return $tableau;
}

//----------------------------------------------------------------------
// Tri d'un tableau de tableaux selon un champ du tableau en conservant les clés
// pris dans http://php.net/manual/fr/function.sort.php
//----------------------------------------------------------------------
function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}