<?php
//-----------------------------------------------------------
// Téléchargement d'un fichier
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
function protectData($data) {
	$data = addslashes(strip_tags($data));
	$data = str_replace(chr(13).chr(10), '\r\n', $data);
	return $data;
}

if (isset($_GET['file'])) $file = protectData($_GET['file']); else exit;
$parts = explode('/', $file);
$leFichier = array_pop($parts);
$leChemin = implode('/', $parts);
header('Content-Type: application/octet-stream');
header('Content-Length: '.filesize($file));
header('Content-disposition: attachment; filename='.$leFichier);
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
readfile($leChemin.'/'.$leFichier);