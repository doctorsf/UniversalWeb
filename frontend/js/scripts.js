//----------------------------------------------------------------------------------
// Bibliothèque de scripts javascript
//----------------------------------------------------------------------------------

//----------------------------------------------------------------------------------
// Récupération de la résolition de l'écran de l'utilisateur via ajax
// Pas de fonction de callback ici, ajax sert juste à appeler la fonction php qui va
// stocker la résolution dans des variables de session
//----------------------------------------------------------------------------------
function getScreenResolution() {
	if (xhr && xhr.readyState != 0) {
		xhr.abort(); // On annule la requête en cours si une déja envoyée!
	}
	var xhr = getXMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
		}
	}
	xhr.open("GET", "reponses-ajax.php?f=getScreenResolution&w=" + screen.width + "&h=" + screen.height, true);
	xhr.send(null);
}