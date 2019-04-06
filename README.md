UniversalWeb - Squelette d'application Web
UniversalWeb est un squelette d�application Web permettant de construire rapidement votre site Internet multilingue. A base de PHP et s�appuyant sur une base de donn�es MySQL, Il propose en standard un frontend (site visible par l�internaute final) et un backend (site d�administration du frontend). UniversalWeb fournit �galement une aide non n�gligeable � la programmation en PHP et au d�veloppement de ces deux modules par la mise en place de nombreuses classes utilitaires. Ce n�est pas � proprement parler un framework mais plut�t une collection d�outils PHP mis en �uvre dans un squelette d�applications et les int�gre aupr�s d�outils tiers tels que Bootstrap, JQuery ou encore Font-Awesome.

-----------------
V 1.0.0
-----------------
Version de base

-----------------
V 1.0.1 (20.10.2014)
-----------------
1- Correction de bug sur les classes "UniversalFieldCancel" et "UniversalFieldButton"
Ces �l�ments de type html button ne renvoyant aucun POST, la propri�t� 'value' �tait r�initialis�e � '0' par l'objet parent UniversalField. Cons�quence directe : le libell� du champ disparaisait ! Il a donc suffit d'interdire l'appel � la m�thode relever() d'UniversalField en la surchargeant pour les classes "UniversalFieldCancel" et "UniversalFieldButton"

2- Ajout et prise en compte des param�tres 'group', lineType' et 'lineClass' pour les classes de boutons "UniversalFieldSubmit", "UniversalFieldReset", "UniversalFieldCancel" et "UniversalFieldButton"

-----------------
V 1.0.2 (18.12.2014)
-----------------
1 � Am�lioration classe UniversalField.
Modification de la m�thode test() pour que les tests soit fait si le champ n'est pas vide. Si le champ vide ne doit pas �tre accept� alors il faut maintenant jouter le param�tre "REQUIRED"

-----------------
V 1.0.3 (21.01.2015)
-----------------
1 - Ajout dans "defines.inc.php" du param�trage de l'IP du d�veloppeur pour affichage de la fen�tre de debug.

2 � Corrections mineures sur "listing_users.inc.php"

3 � Correction bug dans "routines.inc.php", fonctions g�n�ralistes goPageBack() et lance()
	Il fallait ajouter un "die()" une fois la redirection faite afin de s'assurer que le reste du code n'est pas ex�cut�. 

4 � For�age de la case des noms, pr�noms des utilisateurs (premi�res lettres en majuscules seulement)

5 � Am�lioration de la requ�te "sqlUsers_getListing" de listing des utilisateur pour permettre un tri sur plusieurs champs concat�n�s (ex : "nom,prenom")

6 � Sur le listing des utilisateurs, suppression de l'ic�ne poubelle pour son propre compte

7 � ajout du test CHECK_ALPHA_NOMS sur les champs de formulaires

-----------------
V 1.0.4
-----------------
1 � correction "listing_users.inc.php" et  "user.php" pour que le retour vers listing (apr�s ajout/modif/suppr) prennent en compte la page d'origine affich�e et se positionne dessus.

-----------------
V 1.0.5
-----------------
1 - UniversalForm -> Ajout de la classe getFieldObjectByName()

2 � UniversalFormXxxx -> Modifications mineures

-----------------
V 2.0 (06.02.2015)
-----------------
1 - Utilise les nouvelles classes de UniversalFORM

-----------------
V2.10 (20.03.2015)
-----------------
Modification UniversalForm.class.php
1 - Ajout des fonctions 'mySqlDataProtect' et 'rnTo13' hors classe
2 - Correction disfonctionnement -> Ajout d'un test d'existence des champs dans getData()

-----------------
V2.11 (08.07.2015)
-----------------
1 - Modification du constructeur pour UniversalFieldRadio. Pour �viter une erreur PHP si le param�tre � value � n�est pas sp�cifi�, un test est r�alis� avant et un message d�erreur est mont�.
2 - Ajout d'une propri�t� fieldType pour chaque objet qui renseigne sur le type d'objet (text / radio / area / div / etc.)
3 - Ajout (prise en compte) du param�tre 'maxlength' et 'invisible' pour champ type � area �
4 - Button : Ajout prise en compte param�tre 'invisible'
5 - Cancel : Ajout prise en compte param�tre 'invisible'
6 - Checkbox : Prise en compte du param�tre 'invisible' (uniquement sur le premier bouton) (un champ 'invisible' renvoie quand m�me ses donn�es au POST mais plus aucun TEST n'est r�alis� sur le champ)
7 - Radio : Correction bug readonly
8 - Radio : Prise en compte du param�tre 'invisible' (uniquement sur le premier bouton) (un champ 'invisible' renvoie quand m�me ses donn�es au POST mais plus aucun TEST n'est r�alis� sur le champ)
9 - Reset : Ajout prise en compte param�tre 'invisible'
10 - Select : Ajout de la m�thode priv�e getBetween
11 - Select : Ajout de la propri�t� 'invisible'
12 - Separateur : Ajout prise en compte param�tre 'invisible'
13 - Submit : Ajout prise en compte param�tre 'invisible'
14 - Correction getData suite � bug readonly pour les boutons radio

-----------------
V2.12 (12.10.2015)
-----------------
- UniversalForm :
	1. Il est maintenant possible de mettre Plusieurs formulaires dans le m�me script. Pour ce faire il faut donner un num�ro unique au formulaire dans le script (2�me param�tre du new).
	2. testMatches() est maintenant une m�thode publique, ce qui permet de la surcharger si besoin.
	3. Am�lioration du CSS pour que le curseur devienne un point d'interrogation au survol d'un champ qui poss�de un 'title' (c'est-�-dire pour lequel le param�tre 'labelHelp') est positionn�).
	4. Ajout de 2 nouveaux param�tres pour l'objet SELECT : 'SIZE' permet de d�finir une liste non d�roulante mais de X lignes d'�paisseur et 'MULTIPLE' permet la s�lection multiple de lignes.
- Routines.inc.php : 
	- Am�lioration de la fonction rnTo13($valeur) pour prendre en compte les tableaux (array)(n�cessaire suite � ajout des 2 param�tres 'Size' et 'Multiple' pour les SELECT).
- db.connexion.pbo.php : 
	- Am�lioration de la fonction mySqlDataProtect pour prendre en compte les tableaux (n�cessaire suite � ajout des 2 param�tres 'Size' et 'Multiple' pour les SELECT).

-----------------
V2.13 (15.01.2016)
-----------------
- UniversalForm : passage en version 2.17
	-  Ajout d'une fonction javascript setValue(champ, valeur)
	-  Ajout de l'objet 'Comment' (commentaire)
- UniversalForm : passage en version 2.18
	- Modification de la classe UniversalFieldText pour prendre en charge les champs input de type 'file'.
- UniversalForm : passage en version 2.19
	- Modification de la classe UniversalFieldSubmit. On peut maintenant utiliser plusieurs submit dans un m�me formulaire. On peut m�me les grouper par nom (param�tre groupName) pour avoir en retour l'action valid�e dans un unique champ dbfield.
- UniversalForm : passage en version 2.20
	- Utilise la version 15.01.2015 de routine.inc.php

-----------------
V2.21 (09.02.2016)
-----------------
- UniversalForm : passage en version 2.21
Corrig� bug methode _transform() de transformation de la saisie
Ajout� TRIM � la methode _transform() qui supprime les espaces avant et apr�s la saisie
- Correction d'un bug sur classe Droits.class.php
- Ajout d'icones possibles
- Am�lioration de la gestion des droits contre la suppression du droit admin

-----------------
V2.23 (16.02.2016)
-----------------
- UniversalForm : passage en version 2.23

-----------------
V2.24 (21.02.2016)
-----------------
- UniversalForm : passage en version 2.24

-----------------
V2.30 (02.03.2016)
-----------------
- UniversalForm : passage en version 2.30

-----------------
V2.32 (05.03.2016)
-----------------
- UniversalForm : passage en version 2.32
- Routines.inc.php

-----------------
V2.33 (15.03.2016)
-----------------
- UniversalForm : passage en version 2.33

-----------------
V2.35 (18.03.2016)
-----------------
- UniversalForm : passage en version 2.35
- Routines.inc.php du 18.03.2016

-----------------
V2.36 (06.04.2016)
-----------------
- UniversalForm : passage en version 2.36
- Routines.inc.php du 02.04.2016

-----------------
V2.37 (23.08.2016)
-----------------
- UniversalForm : passage en version 2.37

-----------------
V2.38 (22.09.2016)
-----------------
- Possibilit� d�administration user � partir annuaire LDAP
- Modification de la proc�dure de cr�ation de premi�re mise en oeuvre
- Ajout d�un code de base pour Frontend bas� sur HTML 5, Bootstrap 4 et jQuery 3.1.0

-----------------
V3.0.0 (25.10.2016)
-----------------
- UniversalForm : passage en version v3.0.0 (2016-10-17) - Bootstrap v4.0.0-alpha.4
- Modification des pages pour Bootstrap v4.0.0-alpha.4

-----------------
V3.1.0 (28.10.2016)
-----------------
- Cr�ation de la classe unique UniversalFieldBouton qui remplace UniversalFieldSubmit, UniversalFieldButton et UniversalFieldReset
- Am�lioration UniversalFormText pour affichage si label vide

-----------------
V3.2.0 (02.11.2016)
-----------------
- UniversalForm : passage en version 3.2.0 (Cr�ation de la classe UniversalFormSearch)
- Am�lioration des exemples du Frontend

-----------------
V3.3.0 (09.11.2016)
-----------------
- UniversalForm : passage en version 3.3.0

-----------------
V3.3.1 (23.11.2016)
-----------------
- UniversalForm : passage en version 3.3.1
- Mise � jour Tether 1.3.3 (corrige bug de popover Bootstrap)

-----------------
V3.3.2 (12.01.2017)
-----------------
- UniversalForm : passage en version 3.3.2
- R�organisation de l�arborescence de dossiers

-----------------
V3.4.0 (23.01.2017)
-----------------
- UniversalForm : passage en version 3.4.0
- Utilise Bootstrap v4.0.0-alpha.6
	- implique la disparition de col-xs-xx au profit de col ou col-xx
	- implique la disparition des blocs de champs sur tous les objets au positionnement 'inline'
	- implique la disparition des zones de titres sur certains objets (bouton et search)

-----------------
V3.5.0 (23.02.2017)
-----------------
- UniversalForm : passage en version 3.5.0
	- Ajout des type de champ � filtretext � et � filtreselect �
	- Ajout de la propri�t� � showErreur �

-----------------
V3.5.1 (20.04.2017)
-----------------
- UniversalForm : passage en version 3.5.1
	- Ajout des � tooltip � de bootstrap pour les champs titre
	- Am�lioration de l�ordre de chargement des librairies javascript
- routines.inc.php => ajout de la fonction getReferer() et modification des scripts pour utiliser cette fonction � la place de header('Location: '.$_SERVER['HTTP_REFERER']); 

-----------------
V3.5.2 (25.04.2017)
-----------------
- Ajout de la classe Listing.class.php et cr�ation de la table � listings � associ�e
- UniversalForm : passage en version 3.5.2
	- Ajout de la propri�t� talign : alignement du titre (left, right, center ou justify) pour les "checkbox"
	- Ajout de la propri�t� talign : alignement du titre (left, right, center ou justify) pour les "radio"
	- Ajout de la propri�t� talign : alignement du titre (left, right, center ou justify) pour les "fitretext"
	- Correction affichage "tooltip" sur les "fitretext"
	- Ajout de la propri�t� talign : alignement du titre (left, right, center ou justify) pour les "search"
	- Correction affichage "tooltip" sur le titre "search"
	- Correction bug : idTravail �tait initialis� avec 0, il est maintenant initialis� avec 'null' (car il pouvait y avoir un id de travail � 0 existant)

-----------------
V3.5.3 (05.05.2017)
-----------------
- UniversalForm : passage en version 3.5.1
- Ajout de la propri�t� labelHelpPos : alignement du libelle d'aide (left, top (d�faut), right, bottom) pour les tous les champs sauf 'div', 'divfin' et 'hidden'

-----------------
V3.5.4 (16.05.2017)
-----------------
- UniversalForm : passage en version 3.5.4
- Routines en version 16.05.2017

-----------------
V3.5.5 (14.06.2017)
-----------------
- UniversalForm : passage en version 3.5.5
- Routines en version 14.06.2017
- Utilise font-awesome-4.7.0

-----------------
V3.5.5.1 (26.07.2017)
-----------------
Nouvelles version de 
- input.php et Form_input.class.php
- Listing.class.php
- SimpleListingHelper.class.php

-----------------
V3.5.6.1 (27.07.2017)
-----------------
- UniversalForm : passage en version 3.5.6
- Ajout de la m�thode statique swapBool() � sqlSimple.class.php

-----------------
V3.5.6.2 (01.08.2017)
-----------------
- fonctions.inc.php en version 01.08.2017

-----------------
V3.5.7.2 (08.08.2017)
-----------------
- UniversalForm : passage en version 3.5.7

-----------------
V3.5.7.3 (10.08.2017)
-----------------
- Classe SilentMail en version du 10.08.2017 : Ajout du param�tre optionnel 'trace' � la fonction. Il permet d'enregistrer le contenu du mail dans le fichier texte "email.txt"

-----------------
V3.5.8.3 (04.09.2017)
-----------------
- UniversalForm : passage en version 3.5.8 (Suppression de la m�thode usesJavascriptFunctions() au profil d'une int�gration du fichier universalform.min.js contenant le code des fonctions javascript).

-----------------
V3.6.1.0 (25.10.2017)
-----------------
1 - universalForm en version 3.6.1 (Utilise Bootstrap v4.0.0-beta.2) et les fonctions javascripts remarchent
2 - routines.inc.php (version 17.10.2017)
	- Cr�ation de la fonction "utf8_strpos"
	- renommage de la fonction mb_ucwords() en utf8_ucwords()
	- renommage de la fonction mb_right() en utf8_right()
	- renommage de la fonction mb_left() en utf8_left()
	- Cr�ation de la fonction futureDate() qui calcule une date future
	- Suppression de la fonction pageNavigator au profit de l'objet PageNavigator
	- Ajout de la fonction buildImage qui permet d'obtenir une image formatt� de taille fix� (x, y) pr�fix� par une chaine de caract�re.
	- Ajout de la fonction getRefererScriptName() qui renvoie le script de la page referer (appelante)
3 - frontend
	- Mise � jour
4 - classe � Listing � (version 25.10.2017)
	- Cr�ation de la m�thode getDisplayedSize()

-----------------
V3.6.2.0 (en cours)
-----------------
1 - UniversalForm en version 3.6.2 (tr�s l�g�re correction)
2 � Les classes Listing.class et Colonne.class sont remplac�es par la classe UniversalList
3 � sqlSimple.class : Version 29.11.2017 - modification des param�tres de getListe (enlev� le param�tre de langue)
4 � Ajout de la classe Fpdf.class dans sa version ultime 1.81
5 � PageNavigator en version  22.11.2017 (Modification du constructeur. Il fait maintenant appel � des setters puis � la m�thode priv�e _build())
6 � Ajout� 3 exemples d�utilisations des listes (exemple_liste_simple.php, exemple_liste_simple_pages.php et exemple_liste_complexe.php)
7 � Routines : 
	- ajout array_delkey_with_reset_keys(),
	- ajout noDoubleSpace()
	- ajout trimUltime()
	- d�placement du fichier d'erreurs dans le r�pertoire LIBS
	- am�lioration de transposePourUrl()
	- am�lioration de la fonction convert_utf8()

-----------------
V3.7.0.0 (17.03.2018)
-----------------
- routines.inc.php
	- fonction rnTo13 : la fonction est d�clar�e si elle ne l'est pas d�j� par ailleurs car exite la classe UniversalForm la d�clare aussi (11.01.2018)
	- Am�lioration fonction transposePourUrl() (17.01.2018)
	- Correction mauvais fonctionnement de setCookie dans la fonction getGeolocalisation (13.02.2018)
	- Modification de la fonction convert_utf8()
	- Ajout de la fonction convert_ansi()
	- Ajout de la fonction encode()
	- Modification de la fonction saveCSV() pour meilleur encodage
- db.connexion.pdo.php
	mySqlDataProtect d�clar� si pas encore d�clar�
- Ldap.class.php 12.01.2018 :
	- ajout du port dans le constructeur avec port par d�faut � 389
	- ajout de l'erreur -1 (Can't contact LDAP server)
- listing_droits.php 06.02.2018 : 
	- Petite correction bug
- PageNavigator.class.php 10.01.2018 : 
	- correction _drawStandard() pour compatibilit� PHP 7.2.0 fonction count()
- UniversalList 12.01.2018 : VERSION 2.2.0
	- Renomm� parametre de colonne sens en triSens
	- Renomm� m�thode getSens() en getTriSens()
	- Renomm� m�thode setSens() en setTriSens()
	- Renomm� propri�t� priv�e _sensEncours en _triSensEncours
	- Renomm� propri�t� priv�e _sensDefault en _triSensDefault
	- Renomm� m�thode getSensEncoursLibelle() en getTriSensEncoursLibelle()
	- Ajout du param�tre de colonne titlePos qui positionne le title (top/right/bottom/left)
- Ajout de la classe UniversalTree (+ doc)
- fonctions.inc.php 15.01.2018 : 
	- Ajout de la fonction newUser()
- Form_login.class.php 15.01.2018 : 
	- Ajout de la prise en compte ERREUR_ANNUAIRE
- Form_user.class.php 15.01.2018 : 
	- correction petit bug (ajout addslashes � code javascript)
- sql_droits.inc.php 06.02.2018 :
	- Modification sqlDroits_addProfil() et sqlDroits_addFonctionnalite() : 
	- Maintenant � chaque cr�ation de profil ou de fonctionnalit�, les droits sont automatiquement ajout� dans la table "droits" avec interdiction (0)
- JQuery
	- Utilise Version JQuery 3.3.1
- Font-awesome
	- Utilise version 5.0.8 gratuite
- Bootstrap
	- Utilise version Bootstrap 4.0.0
- UniversalForm
	- Utilise version 3.7.1

-----------------
V3.7.1.0 (20.03.2018)
-----------------
- Icons Font-Awesome 5.0.8 fonctionnels

-----------------
V3.8.0.0 (non test�e)
-----------------
- Renommage ExtraWeb en UniversalWeb
- Suppression du fichier version.doc et remplacement par le fichier versions.txt (ce fichier)
- Ajout de tables de logs Extraweb (+listing sur l'interface d'admin)
- Classe UniversalList
	16.04.2018 : VERSION 2.4.0
	- Ajout	de la m�thode publique setTriEncours() (modification du tri en cours)
	- Ajout de la m�thode publique setTriSensEncours() (modification du sens du tri en cours)
	- Ajout de la m�thode publique getTriEncours() qui renvoie le champ tri� en cours
	- Ajout de la m�thode publique getTriSensEncours() qui renvoie le champ tri� en cours
	- Documentation mise � jour
	14.05.2018 : VERSION 2.4.1
	- Modifi� pour prendre en compte version UniversalForm 3.8.0
- Routines
	- am�lioration de la fonction tron
	03.05.2018
	- Simplification de la fonction etatDuRepertoire()
	- Cr�ation de la fonction litRepertoire()
	07.05.2018
	- getThumb() marche maintenant pour jpeg et png
	11.05.2018
	- Ajout de la fonction array_flip_key()
	17.05.2018
	- Modification fonctions restoreDatabase() et saveDatabase() pour obliger � renseigner les binaires "mysql" et "mysqldump"
	28.05.2018
	- Am�lioration de la fonction oteAccents() pour que cela marche dans tous les codages (comme UTF-8)
	29.05.2018
	- Ajout de la fonction writeLog() qui �crit une ligne de log standardis�e dans un fichier log
	01.06.2018
	- Modification saveDataBase() et restoreDatabase() : les noms de fichier de sauvegardes prennent maintenant en compte un param�tre de version de l'application en cours (ex : v.1.2) pour renseigner sur la version sauvegard�e
	15.06.2018
	- Modification saveDataBase() : supprime le fichier de la sauvegarde si celle-ci ne s'est pas bien pass� (on avait des fichiers de sauvegarde vide !)
- classe Droits.class.php (22.03.2018)
	- Ajout de la m�thode retreiveCodeFonctionnaliteFromId() qui renvoie le code de la fonctionnalit� dont d'id est pass� en param�tre
- backend\libs\sql\sqlSqueletteReferences.inc.php (28.03.2018)
	- Renomm� en backend\libs\sql\sql_squelette_references.inc.php et modifications suivantes
	- Correction de l'appel aux m�thodes pour compatiblit� PHP 7 (le nombre de param�tre doit $etre identique entre la m�thode et sa surcharge -> rajout param�tre $debug )
	- Changement du nom du script en sql_squelette_references.inc.php
	- Ajout de la fonction sqlSqueletteReference_fillSelectTous()
- Form_squelette.class.php
	Am�lioration des boutons pour �tre adaptive (28.03.2018)
- Classe SimpleListingHelper (05.04.2018)
	- Rend les classe statiques car version PHP 7.1.8 est stricte et �vite ainsi ce genre de message : Deprecated: Non-static method SimpleListingHelper::getParams() should not be called statically 
- Classes UniversalForm version 3.9.1
	Version 3.8.0 du 14.05.2018
	- Obligation ajouter la classe "uf" au tag "<form>" qui doit utiliser les classes UniversalForm
	- Ajout des propri�tes labelPlus, labelPlusHelp et labelPlusHelpPos sur les champ de type UniversalFieldText (permet de scinder le label en 2 parties, par exemple un texte et une icone)
	- Modification apport�e � tout UniversalWeb
	Version 3.9.0 du 17.05.2018
	- Ajout de la propri�t� flexLine : application des utilitaires flexbox sur les lignes d'objets (permet des cadrages, positionnement et plein d'autre choses sur les objets de la ligne)
	Version 3.9.1 du 12.06.2018
	- Correction bug UniversalFieldSelect : le textMatches 'REQUIRED_SELECTION' ne fonctionnait pas car la m�thode relever() rendait disable la liste et le match ne se faisait plus
- Classe SilentMail
	14.05.2018 : 
	- Remis les headers avec �criture du tableau dans le fichier de sortie texte
- Int�gration des fonctions de sauvegarde et de restauration de la base de donn�es
- actions_droits.php (backend) 
	- version : 01.06.2018 : am�lioration testMatch sur Input pour saisie num�rique des ids de profis et fonctionnalit�s
- maintenance.php (backend)
	01.06.2018
	- modification des entr�es 'savedb', 'loaddb' et 'gorestoredb' pour int�grer la notion de version d'application dans le nom de la sauvegarde
	- ajout de l'entr�e 'deldb' de suppression de la sauvegarde
	- pr�sentation de la liste des sauvegardes disponibles sous forme de table
- Classes Login_*
	16.07.2018
	- Correction bug getLib() - Les getLib des ces classes �taient �crit sans ''
- authentification.php
	16.07.2018
	- ajout d'un else dans la cas ou la m�thode login() renvoie un code != true
- Remaniement de l'initialisation de l'application
	- cr�ation d'un config.inc.php qui devient le SEUL fichier � configurer pour l'application recevant le param�trage propre � l'appli + la base de donn�es + mode d'ex�cution (dev, prod, etc)
	- suppression de db.inc.php (plus n�cessaire)
	- remaniement de defines.inc.php
	- all�gement de init.inc.php


-----------------
V3.8.0.1 (non test�e)
-----------------
- Ldap.class.php
	23.07.2018
	- am�lioration de la m�thode search 
	voir https://forum.phpfrance.com/php-avance/suivre-referral-ldap-t35236.html
	ou https://www.developpez.net/forums/d123654/php/bibliotheques-frameworks/ldap-ldap_search/
	le referral est une d�finition dans OpenLDAP qui permet de dire au CLIENT d'aller voir ailleurs s'il ne trouve pas la r�ponse dans l'OpenLDAP directement.

-----------------
V3.8.0.2 (en cours ... � tester)
-----------------
- UniversalField
	Version 3.9.2 du 12.11.2018
	- Ajout du validateur CHECK_SHA1
- Droits de l'appication sortis du fichier fonctions.inc.php vers le fichier sp�cifique droits.inc.php
- Remplacement des constantes d'ip de d�veloppement par par une seule qui contient un tableau

-----------------
V3.8.1.0
-----------------
- routines
	26.11.2018
	- Renomm� la fonction writeLog() en writeStdLog()	
- init.inc.php
	27.11.2018
	- Mode d'execution des requetes fonction du mode d'execution (dev ou prod)
- Utilise Bootstrap V4.1.3
- Utilise version UniversalForm V3.10.0 pour compatibilit� avec Bootstrap V4.1.3
- Utilise Font Awesome 5.0.10
- Ajout version Apache dans panel information syst�me extranet
- Ajout du r�pertoire 'armoire' destin� aux imports/exports d'informations
- Coloration syntaxique de la liste des sauvegardes de bases de donn�es disponibes en fonction des version (avec conseils)
- Fichiers de langue enrichis et corrig�s
- Am�lioration du header backend
- Am�lioration de la pr�sentation du formulaire de saisie users

-----------------
V3.8.2.0 (en cours ... � tester)
-----------------
- Utilise Font Awesome V5.5.0
- Utilise UniversalWeb V3.10.1
- routines
	30.11.2018
	- fonction transposePourUrl() -> Correction Bug suite � passage en PHP 7.2 -> $str[0] = '' ne marche plus !
		il faut remplacer par $str = substr($str, 1)
- defines.inc.php
	01.12.2018
	- ajout version FontAwesome, cr�ation _FONT_AWSOME_ et modification _FONT_AWSOME_CSS_
- fonctions.inc.php
	01.12.2018
	- correction writeHtmlHeader() suite � modif _FONT_AWSOME_CSS_ du defines.inc.php
- ajoute la version de Font Awesome sur les footer
- routine.inc.php a �t� scind� en 9 fichiers uw_[fonction].php et ne fait plus que l'agr�gat de ces fichiers

-----------------
V3.9.0.0
-----------------
- Remplacement de la variable _URL_BACK_END_ par _URL_BASE_SITE_ le plus possible afin de permettre � un backend de devenir facilement un frontrend
- Utilise UniversalWeb V3.11.3
	- Renommage du Match 'FIRST-LETTER' en 'FIRST-LETTER-ONLY' (il passait toute le texte en minuscule avant de mettre la premi�re lettre en majuscule, donc plus appropri�)
	- Cr�ation du Match 'FIRST-LETTER' (mise en majuscule de la premi�re lettre sans passage en minuscule pr�alable � tout le texte)
	- En plus d'une valeur vide, le testMatch REQUIRED_SELECTION (pour les select) teste maintenant aussi sur la "chaine de caract�re" 'NULL' (et non pas NULL)
	- Ajout du test matches CHECK_EMAIL_APOSTROPHE (idem CHECK_EMAIL mais autorise l'apostrpohe en plus)
- Utilise UniversalList V2.5.1
	- Ajout dela m�thode publique UniversalListColonne::setFiltreValueDefault() (elle �tait jusque l� seulement priv�e)
	- Ajout de la m�thode publique UniversalList::colExist($id) qui renseigne si la colonne identifi�e par $id existe dans la liste (renvoie true ou false)
	- Correction createTable() pour corriger la structure de la table Listing. tinyint(3) � la place de tinyint(3)
- Corrig� classe sqlSimple en SqlSimple
- Utilise Font-Awesome V5.6.3
- Ajout de la classe UniversalZip
- Ajout du module de signature du code (avec signature finale)
- Cr�ation de la classe UniversalDatabase (v1.0.0) (mais non utilis�e par UniversalWeb)
- Sorti _VERSION_APP_, _AUTEUR_ et _COPYRIGHT_ de "config.inc.php" vers "define.inc.php"
- Ajout d'une page de versionning dans le menu administration
- Ajout d'un .htaccess sous le repertoire "DB_backup" pour interdire l'acc�s direct
- Fonction de purge log op�rationnelle
- Ajout fonction de suppression des logs
- Traduction "maintenance.php"
- Traduction "actions_droits.php"
- Traduction "infos_systeme.php"
- Traduction "listing_droits.php"
- Traduction "listing_logs.php"
- Traduction "brique_erreur.php"
- Traduction "Form_input.class.php"
- Traduction "Form_login.class.php"
- Traduction "Form_simple_text.class.php"
- Traduction "Form_squelette.class.php"
- Traduction "Listing_logs.class.php"
- Traduction "SilentMail.class.php"
- Traduction "SimpleListingHelper.class.php"
- Ajout et test de la classe UniversalCsvImport.class.php (aide � l'import de fichiers CSV)
- Scind� le fichier config.inc.php (mis tout ce qui concerne la connexion � la base de donn�es dans config_db.inc.php)
- Cr�ation et test de la m�thode SqlSimple::importMany() (import en plusieurs requetes)
- Cr�ation de la documentation UniversalCsvImport()(v1.0.0)
- Ajout des versions pour les classes UniversalZip (v1.0.0) et UniversalTree (v1.0.0)
- Correction affichage journaux lors d'erreur de login (affichait le mot de passe et pas le login de l'utilisateur)

-----------------
V3.10.0.0
-----------------
- Utilise Bootstrap 4.3.1
- Utilise Font Awesome 5.7.2
- SimpleListingHelper
	07.03.2019
		Changement de param�tres pour la m�thode getParams()
		On passe maintenant l'identifiant de la colonne par d�faut et non pas la liste des champs triables
		On ajoute aussi un die() si la colonne par d�faut n'existe pas
- Ajout et utilisation de la police Montserrat � la place de Roboto
- Passage en UniversalList V2.6.0 (14.03.2019)
	- Ajout des m�thodes publiques UniversalList::setHeadClass() et UniversalList::getHeadClass() pour modifier la classe CSS de l'ent�te du tableau
	- Ajout des m�thodes publiques UniversalList::setFiltresClass() et UniversalList::getFiltresClass() pour modifier la classe CSS du bandeau de filtres du tableau (valeur d�faut "thead-light")
	- Ajout du param�tre "header" dans la construction de la colonne de la table. (objet UniversalListColonne) : true (la colonne est une entetre pour la ligne), false sinon (valeur par d�faut)
	ce param�tre indique si la donn�e de la colonne doit servir d'ent�te pour la ligne.
	- Ajout des m�thodes UniversalListColonne::setHeader() et UniversalListColonne:: getHeader() pour prendre en compte le nouveau param�tre "header"
	- Affichage du listing : supprim� la taille de la colonne pour permettre � du code javascript de la modifier en drag & drop 
	(voir code https://www.brainbell.com/javascript/making-resizable-table-js.html)
- Classe SimpleListingHelper
	14.03.2019
		Ajout du param�tre "header" dans la construction de la colonne de la table. true (la colonne est une entete pour la ligne), false sinon (valeur par d�faut). Ce param�tre indique si la donn�e de la colonne doit servir d'ent�te pour la ligne.
		Ajout du param�tre $css � la methode drawHead (personnalisation de l'ent�te)
- backend : ajout affichage de la release en cours... (affichage du contenu du fichier release.txt)
- backend/libs/fonctions.inc.php
	19.03.2019
		Ajout de la fonction getRelease() qui vient lire le contenur du fichier release.txt
- Ajout de la fonction "Cookie Disclamer" en standard sur le Frontend.
- Ajout de la signature du code frontend.