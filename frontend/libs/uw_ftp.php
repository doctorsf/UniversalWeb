<?php
//--------------------------------------------------------------------------
// routines_ftp.php
// Ensemble de routines orientées FTP
//--------------------------------------------------------------------------

//----------------------------------------------------------------------
// Cette fonction envoi un fichier via le protocole FTP
//----------------------------------------------------------------------
function FTP_SendFile($ftp_server, $ftp_port, $ftp_user_name, $ftp_user_pass, $chemin_source, $chemin_cible, $fichier)
{
	$source_file = $chemin_source.$fichier;
	$destination_file = $chemin_cible.$fichier;

	// v?rification de l'existence d'un fichier.
	if (!(file_exists($source_file))) {
		//echo "Le fichier source est inexistant!";
		return(-1);
	}

	// Création d'une connexion FTP
	$conn_id = ftp_connect($ftp_server, $ftp_port);
	if (!$conn_id) {
		//echo "Tentative de connexion ? ".$ftp_server."<br />";
		//echo "La connexion FTP a ?chou?!";
		ftp_quit($conn_id);
		return(-2);
	}

	// Authentification avec nom de compte et mot de passe
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	//ftp_pasv($conn_id, true);

	// Vérification de la connexion FTP
	if (!$login_result) {
		//echo "Tentative de connexion avec ".$ftp_user_name."<br />";
		//echo "La connexion FTP a ?chou?!";
		ftp_quit($conn_id);
		return(-3);
	}

	// Téléchargement d'un fichier.
	$upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);
	if (!$upload) {
		//echo "Le téléchargement Ftp de ".$source_file." a échoué!";
		ftp_quit($conn_id);
		return(-4);
	}

	// Fermeture de la connexion FTP.
	ftp_quit($conn_id);
	return(0);
}

//----------------------------------------------------------------------
// Cette fonction supprime un fichier via le protocole FTP
//----------------------------------------------------------------------
function FTP_DeleteFile($ftp_server, $ftp_port, $ftp_user_name, $ftp_user_pass, $chemin_cible, $fichier)
{
	$destination_file = $chemin_cible.$fichier;

	//Création d'une connexion FTP
	$conn_id = ftp_connect($ftp_server, $ftp_port);
	if (!$conn_id) {
		//echo "Tentative de connexion ? ".$ftp_server."<br />";
		//echo "La connexion FTP a ?chou?!";
		ftp_quit($conn_id);
		return(-2);
	}

	//Authentification avec nom de compte et mot de passe
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	//ftp_pasv($conn_id, true);

	//Vérification de la connexion FTP
	if (!$login_result) {
		//echo "Tentative de connexion avec ".$ftp_user_name."<br />";
		//echo "La connexion FTP a ?chou?!";
		ftp_quit($conn_id);
		return(-3);
	}

	// Suppression du fichier
	if (!ftp_delete($conn_id, $destination_file)) {
		//echo "La suppression du fichier ".$destination_file." a ?chou?!";
		ftp_quit($conn_id);
		return(-4);
	}

	// Fermeture de la connexion FTP.
	ftp_quit($conn_id);
	return(0);
}