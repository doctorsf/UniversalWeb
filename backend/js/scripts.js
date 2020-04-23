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

//----------------------------------------------------------------------------------
// Appel Ajax de la méthode uw_paramSort qui intervertit la position (ordre 
// d'affichage) de deux paramètres. En cas de succès, le javascript recharge la page
//----------------------------------------------------------------------------------
function uw_paramSort(source, cible) {
	if (xhr && xhr.readyState != 0) {
		xhr.abort(); // On annule la requête en cours si une déja envoyée!
	}
	var xhr = getXMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			//en cas de succes on reactualise la page
			// Recharge la page actuelle, sans utiliser le cache
			document.location.reload(true);
		}
	}
	xhr.open("GET", "reponses-ajax.php?f=uw_paramSort&source=" + source + "&cible=" + cible, true);
	xhr.send(null);
}

//----------------------------------------------------------------------------------
// Appel Ajax de la méthode uw_moveGroupe qui positionne une fonctionnalité dans un
// groupe de fonctionnalités. En cas de succès, le javascript recharge la page
//----------------------------------------------------------------------------------
function uw_MoveFoncToGroupe(id_fonc, id_groupe) {
	if (xhr && xhr.readyState != 0) {
		xhr.abort(); // On annule la requête en cours si une déja envoyée!
	}
	var xhr = getXMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			//en cas de succes on reactualise la page
			// Recharge la page actuelle, sans utiliser le cache
			document.location.reload(true);
		}
	}
	xhr.open("GET", "reponses-ajax.php?f=uw_MoveFoncToGroupe&fonc=" + id_fonc + "&groupe=" + id_groupe, true);
	xhr.send(null);
}

//----------------------------------------------------------------------------------
// Appel Ajax de la méthode uw_moveGroupe qui positionne une fonctionnalité dans un
// groupe de fonctionnalités. En cas de succès, le javascript recharge la page
//----------------------------------------------------------------------------------
function uw_MoveGroupe(id_source, id_after) {
	if (xhr && xhr.readyState != 0) {
		xhr.abort(); // On annule la requête en cours si une déja envoyée!
	}
	var xhr = getXMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			//en cas de succes on reactualise la page
			// Recharge la page actuelle, sans utiliser le cache
			document.location.reload(true);
		}
	}
	xhr.open("GET", "reponses-ajax.php?f=uw_MoveGroupe&source=" + id_source + "&after=" + id_after, true);
	xhr.send(null);
}