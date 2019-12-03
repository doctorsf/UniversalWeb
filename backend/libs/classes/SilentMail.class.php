<?php
//----------------------------------------------------------------------
// Auteur : Fabrice Labrousse											
// Classe SilentMail (Envoi de mail)
// Date : 26.10.2016
// éè : UTF-8															
//----------------------------------------------------------------------
// 10.08.2017 : 
//		Ajout du paramètre optionnel 'trace' à la fonction. Il permet d'enregistrer 
//		le contenu du mail dans le fichier text "email.txt"
// 17.01.2018 : 
//		Correction bug lors de l'écriture dans le fichier email.txt : enlevé les 
//		headers (qui était un tableau) et qui ne sert de toute façon à rien. Cela provoquait un 
//		Notice Array to string conversion.
// 14.05.2018 : 
//		Remis les headers avec écriture du tableau
// 31.10.2019 : 
//		Ajout de constantes pour compte rendu d'erreur
//----------------------------------------------------------------------

class SilentMail {
    private $_to = array();
    private $_cc = array();
    private $_bcc = array();
    private $_from = null; 
    private $_subject = null;
    private $_body = null;
	private $_erreurMessage = null;
	private $_erreurNum = null;
	private $_contentType = 'plain';		//'plain' (texte) par défaut ou 'html' pour message HTML
    private $_charSet = 'UTF-8';			//ex : autres possibilités parmi ISO-8859-1, Windows-1252, CP1252

	const UWSM_SUCCESS				= 1;		//Aucune erreur
	const UWSM_NO_SENDER			= -1;		//Pas d'expéditeur déclaré
	const UWSM_BAD_SENDER			= -2;		//Adresse email de l'expéditeur erronée
	const UWSM_NO_OBJECT			= -3;		//Aucun objet déclaré
	const UWSM_NO_RECIPIENT			= -4;		//Pas de destinataire déclaré
	const UWSM_TOO_MANY_RECIPIENTS	= -5;		//Trop de destinataires déclarés
	const UWSM_BAD_RECIPIENT		= -6;		//Adresse email destinataire erronée
	const UWSM_ERROR				= -7;		//Autre erreur

	//-------------------
	//CONSTRUCTEUR
	//-------------------
    public function __construct() {        
    }

	//-------------------
	//METHODES PRIVEES
	//-------------------
    private function _addAddress($email, $destType, $name = null) {
		$destType = '_'.$destType;
        if ($name !== null) {
            $this->{$destType}[] = trim($name).' <'.trim($email).'>';
        } 
		else {
            $this->{$destType}[] = $email;
        }        
	}

	//-------------------
	//GETTERS
	//-------------------
	public function getErreurNum() {return $this->_erreurNum;}
	public function getErreurMessage() {return $this->_erreurMessage;}

	//-------------------
	//SETTERS
	//-------------------
	private function _setErreur($num, $message) {
		$this->_erreurMessage = $message;
		$this->_erreurNum = $num;
	}
    
	public function setMode($contentType)			{$this->_contentType= $contentType;}
	public function setCharSet($charSet)			{$this->_charSet= $charSet;}

    public function setFrom($email, $name = null) {        
        if ($name !== null) {
            $this->_from = trim($name).' <'.trim($email).'>';
        } 
		else {
            $this->_from = $email;
        }
    }

    public function addTo($email, $name = null)		{$this->_addAddress($email, 'to', $name);}
    public function addCC($email, $name = null)		{$this->_addAddress($email, 'cc', $name);}
    public function addBCC($email, $name = null)	{$this->_addAddress($email, 'bcc', $name);}
    public function setSubject($subject)			{$this->_subject = trim($subject);}
    public function setBody($body) {
		foreach($body as $indice => $ligne) {
			$body[$indice] = stripslashes($ligne);
		}
		$this->_body = implode("\r\n", $body);
	}

	//-------------------
	//METHODES PUBLIQUES
	//-------------------
    public function send($trace='off') {
		if ($this->_from === null) {
			$this->_setErreur(self::UWSM_NO_SENDER, getLib('PAS_EXPEDITEUR'));
			return $this->getErreurNum();
        }
		elseif (!(mb_eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$", $this->_from))) {
			$this->_setErreur(self::UWSM_BAD_SENDER, getLib('EMAIL_EXPEDITEUR_KO'));
			return $this->getErreurNum();
		}
        elseif (count($this->_to) === 0) {
			$this->_setErreur(self::UWSM_NO_RECIPIENT, getLib('PAS_DESTINATAIRE'));
			return $this->getErreurNum();
        }
        elseif (count($this->_to) > 3) {
			$this->_setErreur(self::UWSM_TOO_MANY_RECIPIENTS, getLib('TROP_DESTINATAIRES'));
			return $this->getErreurNum();
		}
        elseif ($this->_subject === null) {
			$this->_setErreur(self::UWSM_NO_OBJECT, getLib('PAS_OBJET'));
			return $this->getErreurNum();
        }        
        elseif ($this->_body === null) {
            //$stErros .= '<li>Entrez le texte du message.</li>';
        }
		//test si les emails destinataires sont valides
		foreach($this->_to as $dest) {
			if (!(mb_eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$", $dest))) {
			$this->_setErreur(self::UWSM_BAD_RECIPIENT, getLib('EMAIL_DESTINATAIRE_INVALIDE'));
				return $this->getErreurNum();
			}
		}
        
        $headers = array();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/{$this->_contentType}; charset={$this->_charSet}";               
        $headers[] = "From: {$this->_from}"; 
        
        if (count($this->_cc) > 0) {
            foreach ($this->_cc as $bcc) {
                $headers[] = 'Cc: ' . $bcc;
            }         
        }
        if (count($this->_bcc) > 0) {
            foreach ($this->_bcc as $bcc) {
                $headers[] = 'Bcc: ' . $bcc;
            }         
        }
        
        $stTo = implode(", ", $this->_to);
        $stHeaders = implode("\r\n", $headers);
        
		//écrit (aussi) le mail dans un fichier si trace='on'
		if ($trace == 'on') file_put_contents('email.txt', $stHeaders."\r\n".$this->_body);

        $boSend = @mail($stTo, $this->_subject, $this->_body, $stHeaders);
        if (!$boSend) {
            //throw new Exception('Email fail');
			$this->_setErreur(self::UWSM_ERROR, getLib('EMAIL_ENVOI_ERREUR'));
			return $this->getErreurNum();
        }        
		return self::UWSM_SUCCESS;  //pas d'erreur
    }
  
    public function clearAllRecipients() {
        $this->_to = array();
        $this->_cc = array();
        $this->_bcc = array();
    }

}