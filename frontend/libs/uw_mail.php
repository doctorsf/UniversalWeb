<?php
//--------------------------------------------------------------------------
// routines_mail.php
// Ensemble de routines orientées email
//--------------------------------------------------------------------------

//----------------------------------------------------------------------
// Envoi d'un eMail silencieux
// entrée :	
//		$exp : email de l'expéditeur
//		$destinataires : array des destinataires (3 maxi)
//		$objet : obejt du message
//		$contenu : array() de lignes du message
//		$copie_webmestre : envoi d'une copie au webmestre si true (defaut)
// retour (entier)
//		code d'erreur (négatif) ou 1 si ok
//----------------------------------------------------------------------
function sendSilentEmail($exp, $destinataires, $objet, $contenu, $copie_webmestre=true)
{
	//recuperation destinataires et champs
	$nb_dest = count($destinataires);
	$nb_contenus = count($contenu);

	//tests
	if ($exp == '') // Pas de nom 'expéditeur déclaré
		return -1;
	if (!(mb_eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$",$exp))) // email expediteur non valide
		return -2;
	if ($objet == '') // Pas d'objet de message déclaré
		return -3;
	if ($nb_dest == 0) // Pas de destinataire déclaré
		return -4;
	if ($nb_dest > 3) // Trop de destinataires ! 3 maximum autorisés
		return -5;
	//test si les emails destinataires sont valides
	foreach($destinataires as $index => $dest) {
		if (!(mb_eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$", $dest)))
			return -6;
	}

	//mise en forme de la chaine des destinataires
	$dest = '';
	for ($i = 0; $i <= ($nb_dest - 1); $i++)
	{
		$dest .= $destinataires[$i];
		if ($i < ($nb_dest - 1))
			$dest .= ', ';
	}
	$message = '';
	for ($i = 0; $i < $nb_contenus; $i++)
		$message.= $contenu[$i]."\r\n";
	$message = stripslashes($message);
	if ($copie_webmestre) $copie_webmestre = 'Bcc: '._PARAM_EMAIL_WEBMASTER_."\r\n"; else $copie_webmestre = '';
	$headers =  'From: '.$exp."\r\n".
				$copie_webmestre.
				'Reply-To: '.$exp."\r\n".
				'Content-Type: text/plain; charset="UTF-8"'."\r\n".
				'Content-Transfer-Encoding: quoted-printable'."\r\n".
				'X-Mailer: PHP/'.phpversion();
	//echo $dest.'<br />'.$objet.'<br />'.$message.'<br />'.$headers;
//	mail($dest, utf8_decode($objet), utf8_decode($message), $headers);
	mail($dest, $objet, $message, $headers);
	return 1;
}

//----------------------------------------------------------------------
// Envoi d'un eMail silencieux
// entrée :
//		$exp : email de l'expéditeur
//		$destinataires : array des destinataires (3 maxi)
//		$objet : obejt du message
//		$contenu : array() de lignes du message
// retour (entier)
//		code d'erreur (négatif) ou 1 si ok
//----------------------------------------------------------------------
function sendSilentHTMLEmail($exp, $destinataires, $objet, $contenu)
{
	//recuperation destinataires et champs
	$nb_dest = count($destinataires);
	$nb_contenus = count($contenu);
	$br = "\r\n";

	//tests
	if ($exp == '') // Pas de nom 'expéditeur déclaré
		return -1;
	if (!(mb_eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$",$exp))) // email expediteur non valide
		return -2;
	if ($objet == '') // Pas d'objet de message déclaré
		return -3;
	if ($nb_dest == 0) // Pas de destinataire déclaré
		return -4;
	if ($nb_dest > 3) // Trop de destinataires ! 3 maximum autorisés
		return -5;
	//test si les emails destinataires sont valides
	foreach($destinataires as $index => $dest) {
		if (!(mb_eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$", $dest)))
			return -6;
	}

	//mise en forme de la chaine des destinataires
	$dest = '';
	for ($i = 0; $i <= ($nb_dest - 1); $i++) {
		$dest .= $destinataires[$i];
		if ($i < ($nb_dest - 1)) {
			$dest .= ', ';
		}
	}

/*	if(preg_match("#@(hotmail|live|msn).[a-z]{2,4}$#", $dest)) {
		$br = "\n";
	} 
	else {
		$br = "\r\n";
	}
*/
	$boundary = md5(rand());

	$message_html = '';
	for ($i = 0; $i < $nb_contenus; $i++) {
		$message_html.= $contenu[$i].$br;
	}
	$message_txt = strip_tags($message_html);

	$headers = 'From: '.$exp.$br;
	$headers.= 'Reply-to: '.$exp.$br;
	$headers.= 'MIME-Version: 1.0'.$br;
	$headers.= "X-Mailer: PHP/".phpversion().$br;
	$headers.= 'Content-Type: multipart/alternative; boundary="'.$boundary.'"'.$br.$br;

	$message = '--'.$boundary.$br;
	$message.= 'Content-Type: text/plain; charset="UTF-8"'.$br;
	$message.= 'Content-Transfer-Encoding: quoted-printable'.$br;
	$message.= $br.$message_txt.$br;

	$message.= '--'.$boundary.$br;	
	$message.= 'Content-Type: text/html; charset="UTF-8"'.$br;
	$message.= 'Content-Transfer-Encoding: quoted-printable'.$br;
	$message.= $br.$message_html.$br;

	$message.= '--'.$boundary.'--';

	if (mail($dest, $objet, $message, $headers)){
		return 1;
	}
	else {
		return -7;
	}
}