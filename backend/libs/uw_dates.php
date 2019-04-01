<?php
//--------------------------------------------------------------------------
// routines_dates.php
// Ensemble de routines orientées dates
//--------------------------------------------------------------------------
// 22.12.2014 : 
//		- Réécriture de la fonction dateToMySQL
//		- Réécriture de la fonction mySqlToDate
//		- Réécriture de la fonction mySqlDateTimeToDate
// 07.09.2015 :
//		- modification mySqlToDateClair : remplacement paramètre langue (remplace entier contre code ISO 3166-1-alpha-2)
// 05.01.2016
//		- ajout de ma fonction isDateValide($chaine, $formatEntree) qui dit si une date est valide quel que soit le format d'entrée
//		- ajout de la fonction changeIncompleteDateFormat($laDate, $formatEntree, $formatSortie)
// 22.02.2016
//		- Ajout de la fonction changeDateFormat
// 25.10.2016
//		- Correction bug isDateValide
// 02.10.2017
//		- Amélioration "mySqlToDateClair" : renvoie une chaine vide si l'entrée est vide (évite les plantages)
// 11.10.2017
//		- Création de la fonction futureDate() qui calcule une date future
//--------------------------------------------------------------------------

defined('_FORMAT_DATE_SQL_')		|| define('_FORMAT_DATE_SQL_', 'Y-m-d');
defined('_FORMAT_DATE_TIME_SQL_')	|| define('_FORMAT_DATE_TIME_SQL_', 'Y-m-d H:i:s');

//----------------------------------------------------------------------
// Formatte une date française au format MySQL : aaaa-mm-jj
// Entrée : 
//		$chaine : chaine représentant la date. Son format peut être l'un des suivants
//			'j/m/a' ou 'jj/mm/aa' ou 'jj/mm/aaa' ou 'jj/mm/aaaa'
//			'j.m.a' ou 'jj.mm.aa' ou 'jj.mm.aaa' ou 'jj.mm.aaaa'
//		$lg : langue choisie pour le format d'entrée. Par défaut 'fr' (france)
//			aux USA, la date est de la forme m/d/Y
//			en europe la date est de la forme d/m/Y
//----------------------------------------------------------------------
function dateToMySQL($chaine, $lg='fr')
{
	//On remplace les '.' par des '/'
	$chaine = strtr($chaine, '.', '/');

	//test de la validité du format d'entree
	if (!preg_match('#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{1,4}$#', $chaine)) {
		return '0000-00-00';
	}

	//explode de la date
	$dummy = explode('/', $chaine);
	if ($lg == 'fr') {
		$leJour = $dummy[0];
		$leMois = $dummy[1];
	}
	else {
		$leJour = $dummy[1];
		$leMois = $dummy[0];
	}
	$leAnnee = $dummy[2];

	//test si la date est valide dans le calendrier Grégorien
	if (checkdate($leMois, $leJour, $leAnnee) === false) {
		return '0000-00-00';
	}

	//recherche du siècle adéquate
	if (($leAnnee < 100) and ($leAnnee > (int)date('y'))) {
		$leAnnee += 1900;
	}
	elseif ($leAnnee <= (int)date('y')) {
		$leAnnee += 2000;
	}

	//formattage de la date
	return sprintf('%04d-%02d-%02d', $leAnnee, $leMois, $leJour);
}

//----------------------------------------------------------------------
// Formatte une date MySQL selon un autre format
// Entrée :
//		chaine : contient la date au format MySQL (yyyy-mm-jj)
//		format : format de sortie. Par défaut le format français d/m/Y (03/04/1965)
// OBSOLETE : appeler dateTimeFormat à la place
//----------------------------------------------------------------------
function mySqlToDate($chaine, $format="d/m/Y")
{
	$date = new DateTime($chaine);
	return date_format($date, $format);
}

//----------------------------------------------------------------------
// Formatte une date ou datetime dans le format souhaité
// pour les formats valide se reporter à http://php.net/manual/en/function.date.php
// Entrée :
//		chaine : contient la date à formater.
//		formatEntree : format de la date en entree. Ex : d/m/Y H:i:s
//		formatSortie : format de la date en sortie. Ex : Y-m-d H:i
// Retour
//		La date dans le nouveau format ou false si erreur
//----------------------------------------------------------------------
function changeDateTimeFormat($chaine, $formatEntree, $formatSortie)
{
	$date = DateTime::createFromFormat($formatEntree, $chaine);
	if ($date !== false) {
		return $date->format($formatSortie);
	}
	else {
		return false;
	}
}

function changeDateFormat($chaine, $formatEntree, $formatSortie)
{
	return changeDateTimeFormat($chaine, $formatEntree, $formatSortie);
}

//----------------------------------------------------------------------
// Formatte une date (attention, pas de datetime) au format souhaité
// A la différence de changeDateTimeFormat, cette fonction permet de formatter des
// date incomplètes ou incorrectes comme 00/00/2016.
// Pour tester la validité d'une date, utiliser isDateValide
// Entrée : 
//		laDate : contient la date à formater.
//		formatEntree : format de la date en entree. Ex : d/m/Y
//		formatSortie : format de la date en sortie. Ex : Y-m-d
// Retour
//		La date dans le nouveau format ou false si erreur
//----------------------------------------------------------------------
function changeIncompleteDateFormat($laDate, $formatEntree, $formatSortie)
{
	if (empty($laDate)) return null;

	$separateurs = array('.', '-', '/', ' ');

	//On transforme la date en tableau
	$laDate = str_replace($separateurs, '/', $laDate);
	$laDate = explode('/', $laDate);

	//déterminons la structure du format d'entrée (où se trouve le jour (d)?, le mois (m)? et l'année (Y)?)
	$formatEntree = str_replace($separateurs, '/', $formatEntree);
	$structureEntree = explode('/', $formatEntree);
	$structureEntree = array_flip($structureEntree);

	//déterminons la structure du format de sortie (où se trouve le jour (d)?, le mois (m)? et l'année (Y)?)
	//recherche du séparateur (-./ ) seulement
	$trouve = preg_match('#[-./ ]#', $formatSortie, $matches);
	if ($trouve) $separateur = $matches[0]; else return false;
	$structureSortie = explode($separateur, $formatSortie);

	//construction et retour de la date formattée
	$retour = '';
	foreach($structureSortie as $indice => $part) {
		$retour.= $laDate[$structureEntree[$part]];
		if ($indice < count($structureSortie) - 1) $retour.= $separateur;
	}
	return $retour;
}

//----------------------------------------------------------------------
// détermine si une date est valide et ce quel que soit le format choisi.
// pour se faire on cree un premier objet date avec la date à comparer puis
// on transforme son contenu dans le format d'entrée. Si la date obtenue et
// la date d'entrée sont différente, c'est que la date n'est pas valide.
// Entrée :
//		chaine : contient la date à tester.
//		formatEntree : format de la date en entree. Ex : d/m/Y H:i:s
// Retour
//		la date est valide (true) / la date n'est pas valide (false)
//----------------------------------------------------------------------
function isDateValide($chaine, $formatEntree)
{
	$date = DateTime::createFromFormat($formatEntree, $chaine);
	return (($date) && ($date->format($formatEntree) == $chaine));
}

//----------------------------------------------------------------------
// Compare deux dates au format texte (correctement formatées)
// Entree : 
//		$date1 : premiere date (texte)
//		$date2 : deuxième date (texte)
//		$formatDate : format des dates présentées (ex : 'Y-m-d H:i:s')
//				le format des deux dates doit être identique
// Retour : un tableau
//		indice 'comparaison' : contient -1 (date1>date2), 0 (date1=date2) ou 1 (date1<date2)
//		indice 'interval' : contient l'interval entre les deux dates (objet DateInterval)
//		exemple de retour pour date1 = 12/02/2015 15:20 et date2 = 09/02/2015 15:10
//Array
//(
//    [comparaison] => -1						-> date1 > date2
//    [interval] => DateInterval Object
//        (
//            [y] => 0
//            [m] => 0
//            [d] => 3							-> 3 jours de différence
//            [h] => 0
//            [i] => 10							-> et 10 secondes de différence
//            [s] => 0
//            [weekday] => 0
//            [weekday_behavior] => 0
//            [first_last_day_of] => 0
//            [invert] => 1
//            [days] => 3						-> 3 jours de différence
//            [special_type] => 0
//            [special_amount] => 0
//            [have_weekday_relative] => 0
//            [have_special_relative] => 0
//        )
//)
//----------------------------------------------------------------------
function compareDateTime($date1, $date2, $formatDate)
{
	$arrayRetour = array();
	$datetime1 = DateTime::createFromFormat($formatDate, $date1);
	$datetime2 = DateTime::createFromFormat($formatDate, $date2);
	if (($datetime1 !== false) && ($datetime2 !== false))
	{
		if ($datetime1 > $datetime2) $arrayRetour['comparaison'] = -1;			//datetime 1 > datetime2
		elseif ($datetime1 < $datetime2) $arrayRetour['comparaison'] = 1;		//datetime 1 < datetime2
		else $arrayRetour['comparaison'] = 0;									//les deux dates sont équivalentes
		$arrayRetour['interval'] = date_diff($datetime1, $datetime2);
		return $arrayRetour;
	}
	else return false;
}

//----------------------------------------------------------------------
// Formatte une datetime MySQL en une date formattée
// Entrée :
//		chaine : contient un datetime au format MySQL (yyyy-mm-jj H:i:s)
//		format : format de sortie. Par défaut le format français d/m/Y (03/04/1965)
//----------------------------------------------------------------------
function mySqlDateTimeToDate($chaine, $format='d/m/Y')
{
	$dummy = explode(' ', $chaine);
	return mySqlToDate($dummy[0], $format);
}

//----------------------------------------------------------------------
// Renvoie le TimeStamp d'une date au format MySQL
// Entrée :
//		chaine : contient la date au format MySQL (yyyy-mm-jj)
//----------------------------------------------------------------------
function mySqlDateToTimeStamp($chaine)
{
	//explode de la date
	$dummy = explode('-', $chaine);
	return mktime(0, 0, 0, $dummy[1], $dummy[2], $dummy[0]);
}

//----------------------------------------------------------------------
// Formatte une date MySQL en date anglais ou français
// Entrée :
//		$chaine : date au format MySQL (yyyy-mm-jj) ou (yyyy-mm-jj H:m:s)
//		$langue : Code ISO 3166-1-alpha-2 de la langue. Par défaut 'fr', sinon anglais
//----------------------------------------------------------------------
function mySqlToDateClair($chaine, $langue='fr', $court=false)
{
	if (empty($chaine)) return '';
	//on vire l'éventuelle information H:m:s
	$dummy = explode(' ', $chaine);
	$chaine = $dummy[0];
	if ($langue == 'fr') {
		if ($court == true)
			$moisenclair = array('', 'janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.');
		else
			$moisenclair = array('', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'); 
		//explode de la date
		$dummy = explode('-', $chaine);
		if (($dummy[2] == '00') && ($dummy[1] == '00') && ($dummy[0] == '0000')) return 'inconnue';
		elseif (($dummy[2] == '00') && ($dummy[1] == '00')) return ($dummy[0]);
		elseif ($dummy[2] == '00') return (ucfirst($moisenclair[intval($dummy[1])]).' '.$dummy[0]);
		else return ($dummy[2].' '.$moisenclair[intval($dummy[1])].' '.$dummy[0]);
	}
	else {
		if ($court == true)
			$moisenclair = array('', 'jan.', 'feb.', 'mar.', 'apr.', 'may', 'june', 'july', 'aug.', 'sept.', 'oct.', 'nov.', 'dec.'); 
		else
			$moisenclair = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'); 
		//explode de la date
		$dummy = explode('-', $chaine);
		if (($dummy[2] == '00') && ($dummy[1] == '00') && ($dummy[0] == '0000')) return 'unknown';
		elseif (($dummy[2] == '00') && ($dummy[1] == '00')) return ($dummy[0]);
		elseif ($dummy[2] == '00') return (ucfirst($moisenclair[intval($dummy[1])]).' '.$dummy[0]);
		else return ($moisenclair[intval($dummy[1])].' '.$dummy[2].', '.$dummy[0]);
	}
}

//----------------------------------------------------------------------
// Renvoie le mois abrégé en clair d'un mois numérique passé en parametre
// Entrée :
//		$long : true (renvoie le mois en entier), false (renvoie version courte)
//		$mois : mois numérique ( 0 < $mois < 13)
//		langue : 0 (francais) sinon anglais
// Retour : le mois en clair
//----------------------------------------------------------------------
function getMonthClair($long, $mois, $langue)
{
	$mois = (int)$mois;
	$retour = '';
	if ($langue == 'fr') {
		//français
		if ($long)
			$moisenclair = array('', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
		else
			$moisenclair = array('', 'janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.');
	}
	else {
		if ($long)
			$moisenclair = array('', 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'); 
		else
			$moisenclair = array('', 'jan.', 'feb.', 'mar.', 'apr.', 'may', 'june', 'july', 'aug.', 'sept.', 'oct.', 'nov.', 'dec.'); 
	}
	if (($mois > 0) && ($mois < 13)) $retour = $moisenclair[$mois];
	return $retour;
}

//----------------------------------------------------------------------
// Valide une date au format Mysql yyyy-mm-jj
// les 0 de remplissage doivent être renseigné pour que la fonction renvoie
// true (ex 2013-03-07 ok) (2013-3-7 false)
// pour remplir avec des 0 utiliser la fonction sprintf('%04d', $valeur)
//----------------------------------------------------------------------
function isValidDateTime($dateTime) 
{ 
	if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $dateTime, $matches)) { 
		if (checkdate($matches[2], $matches[3], $matches[1])) { 
			return true; 
		} 
    } 
    return false; 
} 

//----------------------------------------------------------------------
// Ajoute ou retranche un certain nombre de mois (défaut = 1) à une date
// Entree : Date de réference au format TimeStamp
// Sortie : Nouvelle date augmentée ou diminuée au format TimeStamp
//----------------------------------------------------------------------
function addMonthToDate($timeStamp, $totalMonths=1)
{
	// You can add as many months as you want. mktime will accumulate to the next year.
	$thePHPDate = getdate($timeStamp); // Covert to Array    
	$thePHPDate['mon'] = $thePHPDate['mon']+$totalMonths; // Add to Month    
	$timeStamp = mktime($thePHPDate['hours'], $thePHPDate['minutes'], $thePHPDate['seconds'], $thePHPDate['mon'], $thePHPDate['mday'], $thePHPDate['year']); // Convert back to timestamp
	return $timeStamp;
}

//----------------------------------------------------------------------
// Calcule d'une date par ajout
// Entree :
//		$laDate : date au format MySQL (yyyy-aa-jj)
//		$modele : jours(D) / mois(M) / annees(Y)
//		$nombre : nombre à ajouter
// Sortie : Nouvelle date
//----------------------------------------------------------------------
function myDateAdd($laDate, $modele, $nombre)
{
	$objDate = new DateTime($laDate);
	$futur = $objDate->add(new DateInterval('P'.$nombre.$modele));
	return $futur->format('Y-m-d');
}

//----------------------------------------------------------------------
// calcule une date future par rapport à une date entrée
// Entrée : 
//		$chaine : chaine représentant la date d'origine
//		$interval : interval (au sens PHP) de temps à ajouter
//		$formatEntree : format de la chaine passée en entrée (si non fourni : 'Y-m-d H:i:s')
//		$formatSortie : format de la chaine en sortie(si non fourni : 'Y-m-d H:i:s')
// Retour : la date future (sous forme de chaine au format $formatSortie
//----------------------------------------------------------------------
// A PROPOS DES INTERVALS
// voir http://php.net/manual/fr/dateinterval.construct.php
//$i = new DateInterval('P1D');
//$i = DateInterval::createFromDateString('1 day');
//
//$i = new DateInterval('P2W');
//$i = DateInterval::createFromDateString('2 weeks');
//
//$i = new DateInterval('P3M');
//$i = DateInterval::createFromDateString('3 months');
//
//$i = new DateInterval('P4Y');
//$i = DateInterval::createFromDateString('4 years');
//
//$i = new DateInterval('P1Y1D');
//$i = DateInterval::createFromDateString('1 year + 1 day');
//
//$i = new DateInterval('P1DT12H');
//$i = DateInterval::createFromDateString('1 day + 12 hours');
//
//$i = new DateInterval('PT3600S');
//$i = DateInterval::createFromDateString('3600 seconds');
//----------------------------------------------------------------------
function futureDate($chaine, $interval, $formatEntree=null, $formatSortie=null)
{
	if ($formatEntree == null) $formatEntree = 'Y-m-d H:i:s';
	if ($formatSortie == null) $formatSortie = 'Y-m-d H:i:s';
	$objDate = DateTime::createFromFormat($formatEntree, $chaine);
	$objFutur = $objDate->add(new DateInterval($interval));
	return $objFutur->format($formatSortie);
}