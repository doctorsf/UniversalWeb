<?php
//--------------------------------------------------------------------------
// routines_debug.php
// Ensemble de routines orientées debuggage
//--------------------------------------------------------------------------
// 22.12.2014 : 
//		- Amélioration de la fonction DEBUG_
// 13.04.2018
//		- Amélioration de la fonction tron (prise en compte d'un tableau en entrée)
//--------------------------------------------------------------------------

//----------------------------------------------------------------------
// Ecrit une trace dans le fichier log.txt
// si $raz = true alors on commence par effacer fichier log (fichier vierge)
// par défaut, $raz = false
//----------------------------------------------------------------------
// 13.04.2018 : amélioration pour prendre en compte un tableau en entrée
//----------------------------------------------------------------------
function tron($chaine, $raz=false)
{
	$CR = chr(13).chr(10);
	$fichier_log = 'log.txt';
	if ($raz == true)
		$fp = fopen($fichier_log, 'w');
	else
		$fp = fopen($fichier_log, 'a');

	if (is_array($chaine)) {
		fwrite($fp, date('Y-m-d H:i:s').$CR);	
		fwrite($fp, print_r($chaine, true));
	}
	else {
		fwrite($fp, date('Y-m-d H:i:s').' : '.$chaine.$CR);	
	}
	fclose($fp);				
}

//----------------------------------------------------------------------
// Affiche le contenu d'un tabelau quelconque passé en paramètre
//----------------------------------------------------------------------
function DEBUG_TAB_($leTab)
{
	echo '<pre>';													
	print_r($leTab);												
	echo '</pre>';	
}

//----------------------------------------------------------------------
// Ecrit des information de debuggaged d'une variable
// les informations sont écrite dans une variable de session. Celle-ci
// est rappelée (et le contenu du debuggage) et affiché dans la fonction
// DEBUG_PRINT_()
//----------------------------------------------------------------------
function DEBUG_($libelle, $variable=null)
{
	ob_start(); 
	echo '<pre>';

	if (isset($variable)) {
		//demande d'affichage d'une variable
		$leType = '';
		if (empty($variable)) $leType.= '(empty)';
		if (is_numeric($variable)) $leType.= '(numérique)';
		if (is_int($variable)) $leType.= '(entier)';
		if (is_float($variable)) $leType.= '(flottant)';
		//if (is_scalar($variable)) $leType.= '(scalaire)';
		if (is_bool($variable)) $leType.= '(booleen)';
		if (is_string($variable)) $leType.= '(chaine)';
		if (is_object($variable)) $leType.= '(objet)';
		if (is_array($variable)) $leType.= '(tableau)';
		if (is_resource($variable)) $leType.= '(ressource)';
		if (is_null($variable)) $leType.= '(null)';
		if (is_array($variable)) {
			echo '$'.$libelle.' = '.$leType.'<br />';
			print_r($variable);
		}
		else if (is_object($variable)) {
			echo '$'.$libelle.' = '.$leType.'<br />';
			var_dump($variable);
		}
		else {
			if ((is_bool($variable)) && ($variable === true)) {
				echo '$'.$libelle.' = (true)'.$leType.'<br />';
			}
			elseif ((is_bool($variable)) && ($variable === false)) {
				echo '$'.$libelle.' = (false)'.$leType.'<br />';
			}
			else echo '$'.$libelle.' = ('.$variable.')('.strlen($variable).')'.$leType.'<br />';
		}
	}
	else {
		if (func_num_args() == 2) {
			echo '$'.$libelle.' = (unset)<br />';
		}
		elseif (is_string($libelle)) {
			//demande d'affichage d'un libelle simple
			echo $libelle.'<br />';
		}
		else {
			echo 'Erreur de syntaxe => DEBUG_(libelle, variable)<br />';
		}
	}
	echo '</pre>';
	$chaine = ob_get_contents(); 
	ob_end_clean(); 
	$_SESSION[_APP_ID_.'DEBUG_PHP'][] = $chaine;
}

//----------------------------------------------------------------------
// Ecrit des information de debuggaged d'une variable
// les informations sont écrite dans une variable de session. Celle-ci
// est rappele (et le contenu du debuggage) affiché dans la fonction
// DEBUG_PRINT_()
//----------------------------------------------------------------------
function DEBUG_PRINT_()
{
	foreach($_SESSION[_APP_ID_.'DEBUG_PHP'] as $debug_line) {
		echo $debug_line;
	}
	unset($_SESSION[_APP_ID_.'DEBUG_PHP']);
}