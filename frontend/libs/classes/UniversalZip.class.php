<?php
//==============================================================
// Classe UniversalZip
//--------------------------------------------------------------
// Permet de créer et de lire des fichiers archive ZIP
// Version 1.0.0 du 18.12.2018
//==============================================================

class UniversalZip extends ZipArchive {

	const VERSION = 'v1.0.0 (2018-12-18)';

	//-----------------------------------------------------------
	// Ajout d'un dossier et de tout ses sous-dossiers dans le fichier zip
	// Entree : 
	//		$sourceDir : répertoire source à copier
	//		$cibleDir : structure de répertoire à retranscrire dans le fichier zip
	// Sortie : rien
	//-----------------------------------------------------------
	private function _folderToZip($sourceDir, $cibleDir) { 
		$handle = opendir($sourceDir); 
		while (false !== ($leFichier = readdir($handle))) { 
			if (($leFichier != '.') && ($leFichier != '..')) { 
				$archive = $sourceDir.'/'.$leFichier; 
				if (is_file($archive)) { 
					if ($cibleDir != '') {
						$this->addFile($archive, $cibleDir.'/'.$leFichier); 
					}
					else {
						$this->addFile($archive, $leFichier); 
					}
				} 
				elseif (is_dir($archive)) { 
					// Add sub-directory. 
					if ($cibleDir != '') {
						self::addEmptyDir($cibleDir.'/'.$leFichier); 
						self::_folderToZip($archive, $cibleDir.'/'.$leFichier); 
					}
					else {
						self::addEmptyDir($leFichier); 
						self::_folderToZip($archive, $leFichier); 
					}
				} 
			} 
		} 
		closedir($handle); 
	} 

	//-----------------------------------------------------------
	// Création d'un fichier zip
	// Si le fichier existe déjà il est écrasé
	// Entrée : 
	//		$filename : le nom du fichier
	// Retour : 
	//		true : fichier créé
	//		false : erreur création de fichier (par exemple, le fichier existe 
	//			et est ouvert par quelqu'un.
	//-----------------------------------------------------------
	public function create($filename) {
		//if (file_exists($filename)) unlink($filename);
		return $this->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
	}

	//-----------------------------------------------------------
	// Ajoute un dossier complet et ses sous-dossiers de manière récursive
	// Si on veut écrire à la racine du fichier zip, alors $ciblePath doit être vide ''
	//-----------------------------------------------------------
	public function addDir($sourcePath, $ciblePath='idem') { 
		if ($ciblePath == 'idem') $ciblePath = $sourcePath;
		if (!empty($ciblePath)) {
			$this->addEmptyDir($ciblePath);
		}
		$this->_folderToZip($sourcePath, $ciblePath); 
	} 

	//-----------------------------------------------------------
	// Renvoie un message en clair
	// Entree : 
	//		$code : code du message
	// Retour
	//		la chaine de caractère du message en clair
	//-----------------------------------------------------------
	public function message($code) {
		switch ($code) 	{
			case 0:
			return 'No error';
		
			case 1:
			return 'Multi-disk zip archives not supported';
			
			case 2:
			return 'Renaming temporary file failed';
			
			case 3:
			return 'Closing zip archive failed';
			
			case 4:
			return 'Seek error';
			
			case 5:
			return 'Read error';
			
			case 6:
			return 'Write error';
			
			case 7:
			return 'CRC error';
			
			case 8:
			return 'Containing zip archive was closed';
			
			case 9:
			return 'No such file';
			
			case 10:
			return 'File already exists';
			
			case 11:
			return 'Can\'t open file';
			
			case 12:
			return 'Failure to create temporary file';
			
			case 13:
			return 'Zlib error';
			
			case 14:
			return 'Malloc failure';
			
			case 15:
			return 'Entry has been changed';
			
			case 16:
			return 'Compression method not supported';
			
			case 17:
			return 'Premature EOF';
			
			case 18:
			return 'Invalid argument';
			
			case 19:
			return 'Not a zip archive';
			
			case 20:
			return 'Internal error';
			
			case 21:
			return 'Zip archive inconsistent';
			
			case 22:
			return 'Can\'t remove file';
			
			case 23:
			return 'Entry has been deleted';
			
			default:
			return 'An unknown error has occurred('.intval($code).')';
		}                
	}
}