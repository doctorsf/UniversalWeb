<?php
//-----------------------------------------------------------
// CREATEIMAGE				
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
//chargement des définitions
require_once('libs/defines.inc.php');

//session
session_name(_APP_BLOWFISH_);
session_start();

header('Content-type: image/jpeg');
//$chemin_image = _IMAGES_LANGUE_.'affiche_nondisponible.jpg';
if (isset($_GET['img'])) $chemin_image = _PATH_IMAGES_PRODUITS_.$_GET['img'];
$image = imagecreatefromjpeg($chemin_image);
imagejpeg($image);
imagedestroy($image);