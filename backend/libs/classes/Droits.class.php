<?php
//------------------------------------------------------------------
// Classe Droits
//------------------------------------------------------------------
// éè : pour enregistrement UTF-8
// Auteur : Fabrice Labrousse
// Date : 05 août 2014
//------------------------------------------------------------------
// Gères les droits d'accès à l'application
//------------------------------------------------------------------
// 1 - Dans la gestion des droits il y a 3 choses à gérer :
//		- les profils utilisateurs
//		- les fonctionnalités de l'application
//		- les droits associés à un couple profil/fonctionnalité
//
// 2 - Ces informations sont stockées dans cet objet sous forme de
//  trois tableaux, fidèles à 3 tables de la base de données :
//	table bd_profils ->
//		$_profils : (id_profil, libelle, code)
//	table bd_fonctionnalites ->
//		$_fonctionnalites : (id_fonctionnalite, libelle, code)
//	table bd_droits ->
//		$_droits: (id_fonctionnalite, id_profil, autorisation)
//
// 3 - Le "code" est un mnémonique qui a pour seul but de faciliter
//	l'écriture du code tout en rendant l'objet independant de
//	l'application. Ex pour le profil de base (admin) : 
//		PROFIL_ADMIN (pour profil administrateur)
//------------------------------------------------------------------
// 22.03.2018
//		- Ajout de la méthode retreiveCodeFonctionnaliteFromId() qui renvoie le code de la fonctionnalité dont d'id est passé en paramètre
//------------------------------------------------------------------

//appel simplifié à la methode accesAutorise pour l'utilisateur logué
function accesAutorise($codeFonctionnalite) {
	if (!isset($codeFonctionnalite)) return false;		//on retourne false si la fonctionnalité n'a pas été créée
	//test de la fonctionnalité
	return $_SESSION[_APP_DROITS_]->accesAutorise($codeFonctionnalite, $_SESSION[_APP_LOGIN_]->getProfil());
}

class Droits {

	private $_fonctionnalites;			//tableau des fonctionnalites de l'application
	private $_profils;					//tableau des profils utilisateur de l'application
	private $_droits;					//tableau des droits

	//=======================================
	// Constructeur / Destructeur
	//=======================================

	public function __construct() {
		$this->_chargeFonctionnalites();
		$this->_chargeProfils();
		$this->_chargeDroits();
	}

	//=======================================
	// Méthodes privées
	//=======================================	

	private function _chargeFonctionnalites()	{sqlDroits_loadFonctionnalites($this->_fonctionnalites);}	//chargement des fonctionnalites de l'application
	private function _chargeProfils()			{sqlDroits_loadProfils($this->_profils);}					//chargement des profils de l'application
	private function _chargeDroits()			{sqlDroits_loadDroits($this->_droits);}						//chargement des droits	(fonc./profil/autorisation)

	//=======================================
	// Méthodes publiques
	//=======================================	

	public function countFonctionnalite()	{return count($this->_fonctionnalites);}	//renvoie le nombre de fonctionnalités
	public function countProfils()			{return count($this->_profils);}			//renvoie le nombre de profils
	public function fonctionnalite()		{return $this->_fonctionnalites;}			//renvoie le tableau des fonctionnalités
	public function profils()				{return $this->_profils;}					//renvoie le tableau des profils utilisateurs

	//--------------------------------------------------------------------
	// Renvoie le code d'une fonctionnalité dont l'id_fonctionnalite est passé en paramètre
	// Entree : id de la fonctionnalité dont on cherche le code
	// Retour : le code de la fonctionnalité
	//--------------------------------------------------------------------
	public function retreiveCodeFonctionnaliteFromId($idFonctionnalite) {
		//on cree d'abord un tableau contenant tous les codes de fonctionnalites
		$colonnes = array_column($_SESSION[_APP_DROITS_]->getArrayFonctionnalite(), 'code');
		//on recherche l'indice de la fonctionnalité dans  le tableau 
		$key = array_search($idFonctionnalite, array_column($_SESSION[_APP_DROITS_]->getArrayFonctionnalite(), 'id_fonctionnalite')); 
		return $colonnes[$key];
	}

	public function getArrayFonctionnalite() {return $this->_fonctionnalites;}
	//--------------------------------------------------------------------
	// Renvoie le tableau des informations d'une fonctionnalité en particulier
	// Entree : $codeFonctionnalite (le code de la fonctionnalite)
	// Retour : le tableau de la fonctionnalité choisie (id_fonctionnalite, libelle, code)
	//--------------------------------------------------------------------
	public function getFonctionnalite($codeFonctionnalite) {
		return $this->_fonctionnalites[$codeFonctionnalite];
	}	

	//--------------------------------------------------------------------
	// Renvoie l'id d'une fonctionnalité
	// Entree : $codeFonctionnalite (le code de la fonctionnalite)
	// Retour : l'id de la fonctionnalité
	//--------------------------------------------------------------------
	public function getIdFonctionnalite($codeFonctionnalite) {
		return $this->_fonctionnalites[$codeFonctionnalite]['id_fonctionnalite'];
	}

	//--------------------------------------------------------------------
	// Renvoie le libellé d'une fonctionnalité
	// Entree : $codeFonctionnalite (le code de la fonctionnalite)
	// Retour : le libelle de la fonctionnalité
	//--------------------------------------------------------------------
	public function getLibelleFonctionnalite($codeFonctionnalite) {
		return $this->_fonctionnalites[$codeFonctionnalite]['libelle'];
	}

	//--------------------------------------------------------------------
	// Renvoie le tableau des informations d'un profil
	// Entree : $codeProfil (le code du profil)
	// Retour : le tableau du profil (id_profil, libelle, code)
	//--------------------------------------------------------------------
	public function getProfil($codeProfil) {
		return $this->_profils[$codeProfil];
	}

	//--------------------------------------------------------------------
	// Renvoie l'id d'un profil
	// Entree : $codeProfil (le code du profil)
	// Retour : l'id du profil
	//--------------------------------------------------------------------
	public function getIdProfil($codeProfil) {
		return $this->_profils[$codeProfil]['id_profil'];
	}

	//--------------------------------------------------------------------
	// Renvoie le libelle d'un profil
	// Entree : $codeProfil (le code du profil)
	// Retour : le libelle du profil
	//--------------------------------------------------------------------
	public function getLibelleProfil($codeProfil) {
		return $this->_profils[$codeProfil]['libelle'];
	}

	//--------------------------------------------------------------------
	// Dit si un droit existe pour un couple fonctionnalite / profil
	// Entree : $codeFonctionnalite (le code de la fonctionnalite)
	//			$profil (l'id du profil)
	// Retour : true / false
	//--------------------------------------------------------------------
	public function droitExiste($codeFonctionnalite, $profil) {
		if (!isset($this->_fonctionnalites[$codeFonctionnalite])) return false;		//on retourne false si la fonctionnalité n'a pas été créée
		return (isset($this->_droits[$this->_fonctionnalites[$codeFonctionnalite]['id_fonctionnalite'].'-'.$profil]));
	}

	//--------------------------------------------------------------------
	// Dit si un acces à une fontionnalité pour un profil est autorisé
	// Entree : $codeFonctionnalite (le code de la fonctionnalite)
	//			$profil (l'id du profil)
	// Retour : true / false
	//--------------------------------------------------------------------
	public function accesAutorise($codeFonctionalite, $profil) {
		if ($this->droitExiste($codeFonctionalite, $profil)) {
			return ($this->_droits[$this->_fonctionnalites[$codeFonctionalite]['id_fonctionnalite'].'-'.$profil] == 1);
		}
		return false;
	}


}