<?php
//--------------------------------------------------------------------------
// routines_geo.php
// Ensemble de routines orientées geolocalisation
//--------------------------------------------------------------------------
// 13.02.2018
//		- Correction mauvais fonctionnement de setCookie dans la fonction getGeolocalisation
//--------------------------------------------------------------------------

//==============================================================================================
//								G E O L O C A L I S A T I O N
//==============================================================================================
//Creation des tableaux de langues parlées selon code ISO-3166 (http://www.iso.org/iso/fr/country_codes.htm)
$_PAYS_PARLE_FRANCAIS = array('-', 'BE', 'BJ', 'CF', 'CG', 'CD', 'CI', 'DJ', 'DM', 'FR', 'BF', 'BI', 'CM', 'KM', 'GA', 'GP', 'GN', 'GQ', 'GF', 'LU', 'MG', 'ML', 'MA', 'MQ', 'MR', 'YT', 'MC', 'NE', 'NC', 'PG', 'PF', 'RE', 'RW', 'BL', 'LC', 'SM', 'MF', 'PM', 'SN', 'SC', 'TD', 'TG', 'TN', 'WF');
$_PAYS_LAISSE_CHOIX_LANGUE = array('-', 'DZ', 'AD', 'CA', 'GW', 'HT', 'IL', 'KE', 'MU', 'SH', 'VA', 'VC', 'CH', 'TF');
$_MONNAIE_LIVRE_STERLING_GBP = array('GB', 'IE', 'UK', 'EN');
$_MONNAIE_DOLLARS_USD = array('US');

//----------------------------------------------------------------------------------------------
// Classe de géolocalisation proposée par le site ipinfodb.com
// L'API http://ipinfodb.com/ip_location_api.php s'appuie sur la base de données lite.ip2location.com
// disponible ici : http://lite.ip2location.com/database-ip-country#ipv4
// La class ip2location_lite retourne le pays ou la ville d'une adresse ip au niveau mondial
// par interrogation de base de données : http://www.ipinfodb.com
// TEST : http://api.ipinfodb.com/v3/ip-country/?key=975074d5cc711569cfb2d19b29a23919469ede61d702d94ed267c8b5581cd793&ip=46.218.168.165&format=xml
// renvoi un tableau avec les infos suivantes :
//		statusCode : OK
//		ipAddress : 46.218.168.165
//		countryCode : FR (ISO-3166)
//		countryName : FRANCE
// Gestion des erreur, écrire : $errors = $ipLite->getError();
//----------------------------------------------------------------------------------------------
final class ip2location_lite{
	protected $errors = array();
	protected $service = 'api.ipinfodb.com';
	protected $version = 'v3';
	protected $apiKey = '';

	public function __construct(){}

	public function __destruct(){}

	public function setKey($key){
		if(!empty($key)) $this->apiKey = $key;
	}

	public function getError(){
		return implode("\n", $this->errors);
	}

	public function getCountry($host){
		return $this->getResult($host, 'ip-country');
	}

	public function getCity($host){
		return $this->getResult($host, 'ip-city');
	}

	private function getResult($host, $name){
		$ip = @gethostbyname($host);

		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
			//TEST : http://api.ipinfodb.com/v3/ip-country/?key=975074d5cc711569cfb2d19b29a23919469ede61d702d94ed267c8b5581cd793&ip=46.218.168.165&format=xml
//			$xml = @file_get_contents('http://' . $this->service . '/' . $this->version . '/' . $name . '/?key=' . $this->apiKey . '&ip=' . $ip . '&format=xml');
			$xml = chargeUrlExterne('http://' . $this->service . '/' . $this->version . '/' . $name . '/?key=' . $this->apiKey . '&ip=' . $ip . '&format=xml');

//			if (get_magic_quotes_runtime()){
				$xml = stripslashes($xml);
//			}

			try{
				$response = @new SimpleXMLElement($xml);

				foreach($response as $field=>$value){
					$result[(string)$field] = (string)$value;
				}

				return $result;
			}
			catch(Exception $e){
				$this->errors[] = $e->getMessage();
				return;
			}
		}

		$this->errors[] = '"' . $host . '" is not a valid IP address or hostname.';
		return;
	}
}

//--------------------------------------------------------------------------
// Récupere les infos de pays en fonction d'une IP V4
// Ceci se fait par interrogation de la table ip2location_db1 ajoutée à la base de données
// POUR INFOS VOIR :
// http://lite.ip2location.com/
// http://lite.ip2location.com/database-ip-country
// http://www.ip2location.com/tutorials/redirect-web-visitors-by-country-using-php-and-mysql-database
// Entree : 
//		$ip : adresse IP V4 dont il faut trouver le pays d'origine
// Retour : 
//		un tableau avec les informations suivantes
//		Array (
//		    [statusCode] => OK
//			[statusMessage] =>
//			[ipAddress] => 31.35.132.225
//			[countryCode] => FR
//			[countryName] => FRANCE
//		)
//--------------------------------------------------------------------------
function getPaysFromIp($ip)
{
	//remplissage d'infos de géolocalisation de pays par défaut
	$status = array('statusCode' => 'OK', 'statusMessage' => '', 'ipAddress' => $ip, 'countryCode' => 'FR', 'countryName' => 'FRANCE');

	if ($ip != '') {
		//transformation de l'adresse IP en nombre
		$parts = explode('.', $ip);
		$ipNombre = $parts[3] + $parts[2] * 256 + $parts[1] * 256 * 256 + $parts[0] * 256 * 256 * 256;
		//lancement de la requete SQL
		$requete = "SELECT country_code, country_name FROM ip2location_db1 WHERE ".$ipNombre." <= ip_to LIMIT 1";
		$res = executeQuery($requete, $nombre, _SQL_MODE_);
		if ($res !== false) {
			if ($nombre == 1) {
				$status = array('statusCode' => 'OK', 
								'statusMessage' => '',
								'ipAddress' => $ip, 
								'countryCode' => $res[0]['country_code'], 
								'countryName' => $res[0]['country_name']);
			}
		}
	}

	return $status;
}

//--------------------------------------------------------------------------
// Géolocalisation d'après adresse IP
// Deux possibilités
// 1 - Fait appel à l'API du site ipinfodb.com (voir class ip2location_lite)
// 2 - Recherche l'info dans la base de données construite par http://lite.ip2location.com/
//		sur laquelle s'appuie d'ailleurs ipinfodb.com. Dans ce cas il faut qu'une version de la 
//		base de données lite.ip2location soit installé dans la base de l'application
// Les deux méthjodes renvoient les mêmes informations : exemple
// Array
// (
//    [statusCode] => OK
//    [statusMessage] =>
//    [ipAddress] => 31.35.132.225
//    [countryCode] => FR
//    [countryName] => FRANCE
// )
// On tente d'abord de lire l'information depuis le cookie 'geolocation'
// Si cookie inexistant, alors on le créé et on le remplit avec l'info
//--------------------------------------------------------------------------
function getGeolocalisation()
{
	//Set geolocation cookie
	if(!isset($_COOKIE['geolocation'])) {

		/*
		//METHODE 1
		//méthode via appel à la base de données du site http://lite.ip2location.com/
		//Load the class
		$ipLite = new ip2location_lite;
		$ipLite->setKey('975074d5cc711569cfb2d19b29a23919469ede61d702d94ed267c8b5581cd793');
		//Get location
		$location = $ipLite->getCountry($_SERVER['REMOTE_ADDR']);
		*/

		//METHODE 2
		//méthode via appel à la base de données applicative
		$location = getPaysFromIp($_SERVER['REMOTE_ADDR']);

		if ($location['statusCode'] == 'OK') {
			$data = base64_encode(serialize($location));
			setcookie('geolocation', $data, strtotime('+15 day'), '/'); //positionne le cookie pour une durée de 15 jours
		}
	}
	else {
		//Read geolocation cookie
		$location = unserialize(base64_decode($_COOKIE['geolocation']));
	}
	return $location;
}