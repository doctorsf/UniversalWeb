<?php
//--------------------------------------------------------------------------
// routines_img.php
// Ensemble de routines orientées images
//--------------------------------------------------------------------------
// 07.09.2015 :
//		- Modification de commentaire (setThumbReplacement à la place de getThumbReplacement)
// 10.09.2017
//		- Ajout fonction getThumbReplacement() qui renvoie l'image de remplacement lorsque image inexistente
// 16.10.2017
//		- Ajout de la fonction buildImage qui permet d'obtenir une image formatté de taille fixé (x, y) préfixé par une chaine de caractère.
// 07.05.2018
//		- getThumb() marche maintenant pour jpeg et png
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
// Obtenir la miniature d'une photo / affiche
// Si miniature existe pas, la fonction en crée une avec un rapport hauteur
// largeur de 1.4 (ex : 140x100 ou 100x140)
// ENTREE : $image : l'url de la miniature à obtenir
//			$cote : taille mini de la largeur (ou hauteur) de miniature à
//			créer si celle-ci n'existe pas. La fonction déduit le nom du
//			fichier image source par rapport à l'url de la miniature fournie
//			Concrétement, on enlève son prefixe.
//			Le préfixe est l'ensemble des lettre rencontrées jusqu'au 1er
//			underscore "_" (ex : small_)
//			$ratioHauteurLargeur : ratio hauteur/largeur à donner à la
//			qui sera éventuellement générée
// RETOUR : la vignette ou false si l'image sournce n'est pas jpg / jpeg ou png
//--------------------------------------------------------------------------
// La fonction setThumbReplacement permet de renseigner l'application (et
// donc plus particulièrement la fonction getThumb) avec l'image à renvoyer
// dans le cas ou l'image source de la vignette n'existe pas. Il est donc
// impératif de l'appeler en début d'application sans quoi getThumb ne
// fontionnera pas (enfin renvera false).
// Entree : le nom de l'image de remplacement avec son chemin complet
// exemple : getThumbReplacement(_IMAGES_LANGUE_.'small_nondispo.jpg)
// NB : les sessions doivent êtres activées
//--------------------------------------------------------------------------
function setThumbReplacement($image)
{
	$_SESSION[_APP_ID_.'thumbReplacement'] = $image;
}

function getThumbReplacement()
{
	return $_SESSION[_APP_ID_.'thumbReplacement'];
}

function getThumb($image, $cote, $ratioHauteurLargeur=1.4)
{

	if (file_exists($image)) {
		return $image;
	}

	if (empty($_SESSION[_APP_ID_.'thumbReplacement'])) return false;

	//la vignette n'existe pas. Recherche et test existence de l'image source
	//recuperation du prefixe de la vignette (par convention le préfixe se termine au premier catactère "_" trouvé)
	$parts = explode('/', $image);		//pour ne pas travailler sur le chemin
	$imageSansChemin = end($parts);
	$posUnderscore = strpos($imageSansChemin, '_');
	if ($posUnderscore === false) return $_SESSION[_APP_ID_.'thumbReplacement'];		//pas d'underscore, pas de vignette
	$prefixeVignette = substr($imageSansChemin, 0, $posUnderscore + 1);
	//determination du nom de l'image source
	$imageSource = str_replace($prefixeVignette, '', $image);

	if ((!is_file($imageSource)) || (!file_exists($imageSource))) {
		//on utilise l'image 'inexistente'
		return $_SESSION[_APP_ID_.'thumbReplacement'];
	}

	if ($ratioHauteurLargeur != null) {
		// Cacul des nouvelles dimensions
		list($width_orig, $height_orig) = getimagesize($imageSource);
		if($height_orig > $width_orig) {
			//image 100x150 : portrait
			$type_recadrage = (($height_orig / $width_orig) <= $ratioHauteurLargeur);
			if ($type_recadrage) {
				$x_orig = div(($width_orig - ($height_orig / $ratioHauteurLargeur)), 2);
				$y_orig = 0;
				$width_orig = round($height_orig / $ratioHauteurLargeur);
				$width_dest = $cote;
				$height_dest = $cote * $ratioHauteurLargeur;
			}
			else {
				$x_orig = 0;
				$y_orig = div(($height_orig - ($width_orig * $ratioHauteurLargeur)), 2);
				$height_orig = round($width_orig * $ratioHauteurLargeur);
				$width_dest = $cote;
				$height_dest = $cote * $ratioHauteurLargeur;
			}
		}
		else {
			//image 150x100 : paysage
			$type_recadrage = (($width_orig / $height_orig) <= $ratioHauteurLargeur);
			if ($type_recadrage) {
				$x_orig = 0;
				$y_orig = div(($height_orig - ($width_orig / $ratioHauteurLargeur)), 2);
				$height_orig = round($width_orig / $ratioHauteurLargeur);
				$width_dest = $cote * $ratioHauteurLargeur;
				$height_dest = $cote;
			}
			else {
				$x_orig = div(($width_orig - ($height_orig * $ratioHauteurLargeur)), 2);
				$y_orig = 0;
				$width_orig = round($height_orig * $ratioHauteurLargeur);
				$width_dest = $cote * $ratioHauteurLargeur;
				$height_dest = $cote;
			}
		}
	}
	else {
		//pas de mise en ratio, juste un retaillage
		list($width_orig, $height_orig) = getimagesize($imageSource);
		if($height_orig > $width_orig) {
			//image 100x150 : portrait
			$ratioHauteurLargeur = $height_orig / $width_orig;
			$x_orig = 0;
			$y_orig = 0;
			$width_dest = $cote;
			$height_dest = $cote * $ratioHauteurLargeur;
		}
		else {
			//image 150x100 : paysage
			$ratioHauteurLargeur = $width_orig / $height_orig;
			$x_orig = 0;
			$y_orig = 0;
			$height_dest = $cote;
			$width_dest = $cote * $ratioHauteurLargeur;
		}
	}

	// Redimensionnement
	$resImgVignette = imageCreateTruecolor($width_dest, $height_dest);
	$extention = getExtension($imageSource);
	if (($extention == 'jpg') || ($extention == 'jpeg')) {
		$resImgSource = imageCreateFromJpeg($imageSource);
	}
	elseif ($extention == 'png') {
		$resImgSource = imageCreateFromPng($imageSource);
	}
	else {
		return false;
	}
	imageCopyResampled($resImgVignette, $resImgSource, 0, 0, $x_orig, $y_orig, $width_dest, $height_dest, $width_orig, $height_orig);

	//creation eventuelle du répertoire de la vignette si existe pas
	//$infosPath = explode('/', $image);
	//$dummy = array_pop($infosPath);
	//$imagePath = implode('/', $infosPath);
	//mkdir_recursive($imagePath, '0644');

	//sauvegarde de la vignette : jpeg 70% compression
	if (($extention == 'jpg') || ($extention == 'jpeg')) {
		imagejpeg($resImgVignette, $image, 70);
	}
	elseif ($extention == 'png') {
		imagepng($resImgVignette, $image, 7);
	}
	imageDestroy($resImgVignette);

	//l'image est crée, on renvoie son url
	return $image; 
}

//--------------------------------------------------------------------------
// Obtenir une image formatté de taille fixé (x, y) préfixé par une chaine
// de caractère.
// Si l'image existe, la fonction renvoie juste le nom de l'image.
// Si l'image existe pas, la fonction la crée à partir de la source. La
// source est le nom de l'image dont on a oté le préfixe.
// L'image résultante est une découpe du centre de l'image source.
// Si l'image source possède un coté plus grand que la taille demandée, elle
// est zoomee
// Si l'image source est plus petite que la taille demandée, elle est zoomee
// pour être adpatée au mieux à la taille de l'image cible
// ENTREE : $image : l'url de l'image cible à obtenir (préfixée)
//          $prefixe : prefixe de l'image (ex : slider_ / small_)
//			$largeur : largeur de l'image à obtenir
//			$hauteur : hauteur de l'image à obtenir
// RETOUR : le fichier image (éventuellement créé)
//--------------------------------------------------------------------------
function buildImage($image, $prefixe, $largeur, $hauteur)
{

	if (file_exists($image)) {
		return $image;
	}

	//La cible existe pas. Recherche et test existence de l'image source
	$imageSource = str_replace($prefixe, '', $image);

	if (!file_exists($imageSource)) {
		//on utilise l'image 'inexistente'
		$imageSource = $_SESSION[_APP_ID_.'thumbReplacement'];
	}

	//Récupération des dimensions de l'image source
	list($width_orig, $height_orig) = getimagesize($imageSource);

	//l'image source est plus petite que la cible
	if (($width_orig < $largeur) && ($height_orig < $hauteur)) {
		$src_x = 0;
		$src_y = 0;
		$src_w = $width_orig;
		$src_h = $height_orig;
		if ($width_orig <= $height_orig) {
			$dest_w = $width_orig * $hauteur / $height_orig;		//adaptation en conséquence de la largeur
			$dest_h = $hauteur;
			$dest_x = div(($largeur - $dest_w), 2);
			$dest_y = 0;
		}
		if ($width_orig > $height_orig) {
			$dest_w = $largeur;
			$dest_h = $height_orig * $largeur / $width_orig;		//adaptation en conséquence de la hauteur
			$dest_x = 0;
			$dest_y = div(($hauteur - $dest_h), 2);
		}
	}
	//l'image soucre possède au moins 1 coté plus grand que la cible
	else { 
		$src_x = 0;
		$src_y = 0;
		$src_w = $largeur;
		$src_h = $hauteur;
		if ($width_orig < $largeur) {
			$src_w = $width_orig;
			$ratio = $largeur / $width_orig;		//calcul du ratio entre largeur voulue et largeur d'origine
			$src_h = (int)($hauteur / $ratio);		//on rapporte ce ratio à la hauteur de l'image source
		}
		else {
			$src_x = div(($width_orig - $largeur), 2);
		}
		if ($height_orig < $hauteur) {
			$ratio = $hauteur / $height_orig;		//calcul du ratio entre hauteur voulue et hauteur d'origine
			$src_w = (int)($largeur / $ratio);		//on rapporte ce ratio à la largeur de l'image source
			$src_h = $height_orig;
		}
		else {
			$src_y = div(($height_orig - $hauteur), 2);
		}
		$dest_x = 0;
		$dest_y = 0;
		$dest_w = $largeur;
		$dest_h = $hauteur;
	}
	$resImgCible = imageCreateTruecolor($largeur, $hauteur);
	$resImgSource = imageCreateFromJpeg($imageSource);
	imageCopyResampled($resImgCible, $resImgSource, $dest_x, $dest_y, $src_x, $src_y, $dest_w, $dest_h, $src_w, $src_h);

	//creation eventuelle du répertoire de l'image cible si il n'existe pas
	$infosPath = explode('/', $image);
	$dummy = array_pop($infosPath);
	$imagePath = implode('/', $infosPath);
	mkdir_recursive($imagePath, '0644');

	//sauvegarde de l'image cible : jpeg 80% compression
	imagejpeg($resImgCible, $image, 80);
	imageDestroy($resImgCible);

	//l'image est crée, on renvoie son url
	return $image; 
}