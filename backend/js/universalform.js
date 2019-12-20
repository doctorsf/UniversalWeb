//---------------------------------------------------------
// Fonction javascript universalForm
// Validées pour universalForm 3.6.1 et supérieur
// 26.10.2017
//---------------------------------------------------------
		
//---------------------------------------------------------
// Rend un champ invisible
// Entrée : 
//		field : id UniversalForm du champ
//---------------------------------------------------------
function uf_setInvisible(field) {
	var idElem = 'ufztitre_' + field;
	var classe = document.getElementById(idElem).className;
	classe = str_replace('invisible', '', classe);
	classe = classe.trim() + ' invisible';
	document.getElementById(idElem).className = classe.trim();
	var idElem = 'ufzchamp_' + field;
	var classe = document.getElementById(idElem).className;
	classe = str_replace('invisible', '', classe);
	classe = classe.trim() + ' invisible';
	document.getElementById(idElem).className = classe.trim();
}

//---------------------------------------------------------
// Rend un champ visible
// Entrée : 
//		field : id UniversalForm du champ
//---------------------------------------------------------
function uf_setVisible(field) {
	var idElem = 'ufztitre_' + field;
	var classe = document.getElementById(idElem).className;
	classe = str_replace('invisible', '', classe);
	document.getElementById(idElem).className = classe.trim();
	var idElem = 'ufzchamp_' + field;
	var classe = document.getElementById(idElem).className;
	classe = str_replace('invisible', '', classe);
	document.getElementById(idElem).className = classe.trim();
}

//---------------------------------------------------------
// Prévient si la touche majuscule du clavier est vérouillée
// à l'utilisation d'un champ de saisie HTML
// Entrée : 
//		e : evènements captés par Javascript (clavier entre autres)
//		id : id de l'élément HTML sur lequel faire le test
//		lg : langue d'affichage de l'information : fr / en
//---------------------------------------------------------
function capLock(e, id, lg) {
	if(lg == 'fr') {
		texte = 'Attention, les majuscules sont verrouilllées !';
	}
	else {
		texte = 'Caps Lock is on';
	}
    kc = e.keyCode?e.keyCode:e.which;
    sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
	if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk)) {
		document.getElementById(id).nextSibling.innerHTML = texte;
		document.getElementById(id).nextSibling.setAttribute('style', 'color:black');
	}
	else {
		document.getElementById(id).nextSibling.innerHTML = '';
		document.getElementById(id).nextSibling.removeAttribute('style');
	}
}

//---------------------------------------------------------
// Rend enable le champ "field"
// Entrée : 
//		field : id UniversalForm du champ ciblé
//---------------------------------------------------------
function uf_enable(field) {
	var idElem = 'id' + ucfirst(field);
	document.getElementById(idElem).disabled = false;
}

//---------------------------------------------------------
// Rend disable le champ "field"
// Entrée : 
//		field : id UniversalForm du champ ciblé
//---------------------------------------------------------
function uf_disable(field) {
	var idElem = 'id' + ucfirst(field);
	document.getElementById(idElem).disabled = true;
}

//---------------------------------------------------------
// Rend un champ "field" disable si la case à cocher "déclencheur" est cochée
// Rend un champ "field" enable si la case à cocher "déclencheur" est décochée
// Entrée : 
//		field : id UniversalForm du champ ciblé
//		declencheur : id UniversalForm de la checkbox déclencheuse
//---------------------------------------------------------
function uf_disableOnChecked(field, declencheur) {
	var declencheur = 'id' + ucfirst(declencheur);
	if (document.getElementById(declencheur).checked == true) {
		uf_disable(field);
	}
	else {
		uf_enable(field);
	}
}

//---------------------------------------------------------
// Rend un champ "field" enable si la case à cocher "déclencheur" est cochée
// Rend un champ "field" disable si la case à cocher "déclencheur" est décochée
// Entrée : 
//		field : id UniversalForm du champ ciblé
//		declencheur : id UniversalForm de la checkbox déclencheuse
//---------------------------------------------------------
function uf_enableOnChecked(field, declencheur) {
	var declencheur = 'id' + ucfirst(declencheur);
	if (document.getElementById(declencheur).checked == true) {
		uf_enable(field);
	}
	else {
		uf_disable(field);
	}
}

//---------------------------------------------------------
// Rend un champ "field" visible si la case à cocher "déclencheur" est cochée
// Rend un champ "field" invisible si la case à cocher "déclencheur" est décochée
// Entrée : 
//		field : id UniversalForm du champ ciblé
//		declencheur : id UniversalForm de la checkbox déclencheuse
//---------------------------------------------------------
function uf_showOnChecked(field, declencheur) {
	var declencheur = 'id' + ucfirst(declencheur);
	if (document.getElementById(declencheur).checked == true) {
		uf_setVisible(field)
	}
	else {
		uf_setInvisible(field)
	}
}

//---------------------------------------------------------
// Rend un champ "field" enable si la case à cocher "déclencheur" est décochée
// Entrée : 
//		field : id UniversalForm du champ ciblé
//		declencheur : id UniversalForm de la checkbox déclencheuse
//---------------------------------------------------------
function uf_justEnableOnUnchecked(field, declencheur) {
	var declencheur = 'id' + ucfirst(declencheur);
	if (document.getElementById(declencheur).checked == false) {
		uf_enable(field);
	}
}

//---------------------------------------------------------
// Coche la case à cocher "field" si la case à cocher "déclencheur" est décochée
// Entrée : 
//		field : id UniversalForm de la case à cocher cibléé
//		declencheur : id UniversalForm de la checkbox déclencheuse
//---------------------------------------------------------
function uf_justCheckOnUnchecked(field, declencheur) {
	var declencheur = 'id' + ucfirst(declencheur);
	if (document.getElementById(declencheur).checked == false) {
		uf_check(field);
	}
}

//---------------------------------------------------------
// Décoche la case à cocher "field" si la case à cocher "déclencheur" est cochée
// Entrée : 
//		field : id UniversalForm de la case à cocher ciblé
//		declencheur : id UniversalForm de la checkbox déclencheuse
//---------------------------------------------------------
function uf_justUncheckOnCheck(field, declencheur) {
	var declencheur = 'id' + ucfirst(declencheur);
	if (document.getElementById(declencheur).checked == true) {
		uf_uncheck(field);
	}
}

//---------------------------------------------------------
// Coche la case à cocher "field"
// Entrée : 
//		field : id UniversalForm de la case à cocher cibléé
//---------------------------------------------------------
function uf_check(field) {
	var id = 'id' + ucfirst(field);
	document.getElementById(id).checked = true;
}

//---------------------------------------------------------
// Décoche la case à cocher "field"
// Entrée : 
//		field : id UniversalForm de la case à cocher cibléé
//---------------------------------------------------------
function uf_uncheck(field) {
	var id = 'id' + ucfirst(field);
	document.getElementById(id).checked = false;
}

//---------------------------------------------------------
// Donne la "valeur" à la propriété value du champ "field"
// Entrée : 
//		field : id UniversalForm du champ ciblé
//		valeur : valeur à afficher
//---------------------------------------------------------
function uf_setValue(field, valeur) {
	var idElem = 'id' + ucfirst(field);
	document.getElementById(idElem).value = valeur;
}

//---------------------------------------------------------
// Affecte "classe" à la classe du champ "field"
// Entrée : 
//		field : id UniversalForm du champ ciblé
//		classe : classe CSS à donner au champ field
//---------------------------------------------------------
function uf_changeClass(field, classe) {
	var idElem = 'id' + ucfirst(field);
	document.getElementById(idElem).className = classe;
}