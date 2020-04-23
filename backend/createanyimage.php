<?php
//-----------------------------------------------------------
// Create any image (jpg, jpeg, gif, png)
// Création d'une image quelconque à partir d'une source hors du site
//-----------------------------------------------------------
// ééàç : pour sauvegarde du fichier en utf-8								
//-----------------------------------------------------------
//chargement des définitions
require_once('libs/defines.inc.php');

//session
session_name(_APP_BLOWFISH_);
session_start();

$image = $_GET['img'];
$extension = pathinfo($image, PATHINFO_EXTENSION);
switch($extension) {
	case 'jpg':
	case 'jpeg': {
		header('Content-type: image/jpeg');
		$obj = imagecreatefromjpeg($image);
		imagejpeg($obj);
		break;
	}
	case 'gif': {
		header('Content-type: image/gif');
		$obj = imagecreatefromgif($image);
		imagegif($obj);
		break;
		break;
	}
	case 'png': {
		header('Content-type: image/png');
		$obj = imagecreatefrompng($image);
		imagepng($obj);
		break;
	}
	default: {
		break;
	}
}
imagedestroy($obj);