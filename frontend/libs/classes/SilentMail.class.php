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
// 07.04.2020 : v2.1.0 (2020-04-23)
//		- Ajout d'un numéro de version de la classe
//		- Modification et uniformisation des constantes de message d'erreur + libellés UniversalWeb
//		- Ajout de la valeur 'test' au paramètre 'trace' de la méthode send() qui permet de simuler l'envoi de mail
//		- Création de la méthode statique getMessage permettant de récupérer un message en clair selon le code d'erreur passé en paramètre
//		- Ajout des méthodes publiques getTo, getCc, getBcc et getFrom
//----------------------------------------------------------------------

class SilentMail {
    private $_to = array();					//destinataire
    private $_cc = array();					//Carbon Copy
    private $_bcc = array();				//Blind Carbon Copy
    private $_from = null;					//expediteur
    private $_subject = null;				//sujet
    private $_body = null;					//corps
	private $_erreurMessage = null;			//message d'erreur
	private $_erreurNum = null;				//numéro d'erreur
	private $_contentType = 'plain';		//'plain' (texte) par défaut ou 'html' pour message HTML
    private $_charSet = 'UTF-8';			//ex : autres possibilités parmi ISO-8859-1, Windows-1252, CP1252

	const UWSM_TEST					= 0;	//message de test : aucun envoi (sauf dans fichier email.txt)
	const UWSM_SUCCESS				= 1;	//Aucune erreur
	const UWSM_NO_SENDER			= -1;	//Pas d'expéditeur déclaré
	const UWSM_BAD_SENDER			= -2;	//Adresse email de l'expéditeur erronée
	const UWSM_NO_OBJECT			= -3;	//Aucun objet déclaré
	const UWSM_NO_RECIPIENT			= -4;	//Pas de destinataire déclaré
	const UWSM_TOO_MANY_RECIPIENTS	= -5;	//Trop de destinataires déclarés
	const UWSM_BAD_RECIPIENT		= -6;	//Adresse email destinataire erronée
	const UWSM_SEND_ERROR			= -7;	//Autre erreur

	const VERSION = 'v2.1.0 (2020-04-23)';

	//-------------------
	//CONSTRUCTEUR
	//-------------------
    public function __construct() {        
    }

	static function getMessage($code) {
		if ($code == self::UWSM_SUCCESS) return getLib('UWSM_SUCCESS');
		elseif ($code == self::UWSM_TEST) return getLib('UWSM_TEST');
		elseif ($code == self::UWSM_NO_SENDER) return getLib('UWSM_NO_SENDER');
		elseif ($code == self::UWSM_BAD_SENDER) return getLib('UWSM_BAD_SENDER');
		elseif ($code == self::UWSM_NO_OBJECT) return getLib('UWSM_NO_OBJECT');
		elseif ($code == self::UWSM_NO_RECIPIENT) return getLib('UWSM_NO_RECIPIENT');
		elseif ($code == self::UWSM_TOO_MANY_RECIPIENTS) return getLib('UWSM_TOO_MANY_RECIPIENTS');
		elseif ($code == self::UWSM_BAD_RECIPIENT) return getLib('UWSM_BAD_RECIPIENT');
		elseif ($code == self::UWSM_SEND_ERROR) return getLib('UWSM_SEND_ERROR');
		else return getLib('UWSM_ERROR_UNKNOWN');
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
	public function getTo() {return $this->_to;}
	public function getCc() {return $this->_cc;}
	public function getBcc() {return $this->_bcc;}
	public function getFrom() {return $this->_from;}

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

	//Affichage d'un libellé correspondant à un therme (constante)
	//Si jamais une fonction getLib() existe dans l'application, c'est cette fonction qui sera appelée
	//Ceci permet par exemple aux application de gérer le multi-langues
	public function getLib($mnemo, $param1='') {
		if (function_exists('getLib')) return getLib($mnemo, $param1);
		//dans le cas contraire
		$libelles = array(
			//------------------------------------------
			// Libellés des classes UniversalField (UFC)
			//------------------------------------------
			'UWSM_TEST'						=> 'Test',
			'UWSM_SUCCESS'					=> 'Message envoyé avec succès&hellip;',
			'UWSM_NO_SENDER'				=> 'Pas d\'expéditeur déclaré',
			'UWSM_BAD_SENDER'				=> 'eMail expéditeur non valide',
			'UWSM_NO_OBJECT'				=> 'Pas d\'objet déclaré',
			'UWSM_NO_RECIPIENT'				=> 'Pas de destinaitaire déclaré',
			'UWSM_TOO_MANY_RECIPIENTS'		=> 'Trop de destinataires ! 3 maximum autorisés',
			'UWSM_BAD_RECIPIENT'			=> 'eMail d\'un destinataire non valide',
			'UWSM_SEND_ERROR'				=> 'Erreur lors de l\'envoi',
			'UWSM_ERROR_UNKNOWN'			=> 'Erreur inconnue'
		);
		return vsprintf($libelles[$mnemo], $params);
	}

    public function send($trace='off') {
		if ($this->_from === null) {
			$this->_setErreur(self::UWSM_NO_SENDER, getLib('UWSM_NO_SENDER'));
			return $this->getErreurNum();
        }
		elseif (!(mb_eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$", $this->_from))) {
			$this->_setErreur(self::UWSM_BAD_SENDER, getLib('UWSM_BAD_SENDER'));
			return $this->getErreurNum();
		}
        elseif (count($this->_to) === 0) {
			$this->_setErreur(self::UWSM_NO_RECIPIENT, getLib('UWSM_NO_RECIPIENT'));
			return $this->getErreurNum();
        }
        elseif (count($this->_to) > 3) {
			$this->_setErreur(self::UWSM_TOO_MANY_RECIPIENTS, getLib('UWSM_TOO_MANY_RECIPIENTS'));
			return $this->getErreurNum();
		}
        elseif ($this->_subject === null) {
			$this->_setErreur(self::UWSM_NO_OBJECT, getLib('UWSM_NO_OBJECT'));
			return $this->getErreurNum();
        }        
        elseif ($this->_body === null) {
            //$stErros .= '<li>Entrez le texte du message.</li>';
        }
		//test si les emails destinataires sont valides
		foreach($this->_to as $dest) {
			if (!(mb_eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$", $dest))) {
			$this->_setErreur(self::UWSM_BAD_RECIPIENT, getLib('UWSM_BAD_RECIPIENT'));
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
		if (($trace == 'on') || ($trace == 'test')) file_put_contents('email.txt', $stHeaders."\r\n".$this->_body);

		//si trace vaut 'test' alors on envoi pas le mail
		if ($trace != 'test') {
	        $boSend = @mail($stTo, $this->_subject, $this->_body, $stHeaders);
		    if (!$boSend) {
			    //throw new Exception('Email fail');
				$this->_setErreur(self::UWSM_SEND_ERROR, getLib('UWSM_SEND_ERROR'));
				return $this->getErreurNum();
		    }
			return self::UWSM_SUCCESS;  //pas d'erreur
		}
		else return self::UWSM_TEST;  //pas d'erreur mais retour de test
    }
  
    public function clearAllRecipients() {
        $this->_to = array();
        $this->_cc = array();
        $this->_bcc = array();
    }

}