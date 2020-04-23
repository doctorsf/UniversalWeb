# UniversalWeb
## Squelette d'application Web

UniversalWeb est un squelette d�application Web permettant de construire rapidement votre site Internet multilingue. A base de PHP et s�appuyant sur une base de donn�es MySQL, Il propose en standard un frontend (site visible par l�internaute final) et un backend (site d�administration du frontend). UniversalWeb fournit �galement une aide non n�gligeable � la programmation en PHP et au d�veloppement de ces deux modules par la mise en place de nombreuses classes utilitaires. Ce n�est pas � proprement parler un framework mais plut�t une collection d�outils PHP mis en �uvre dans un squelette d�applications et les int�gre aupr�s d�outils tiers tels que Bootstrap, JQuery ou encore Font-Awesome.

-----------------
V 1.0.0
-----------------
1. Version de base (ExtraWeb)

-----------------
V 1.0.1 (20.10.2014)
-----------------
1. Correction de bug sur les classes "UniversalFieldCancel" et "UniversalFieldButton" : Ces �l�ments de type html button ne renvoyant aucun POST, la propri�t� 'value' �tait r�initialis�e � '0' par l'objet parent UniversalField. Cons�quence directe : le libell� du champ disparaisait ! Il a donc suffit d'interdire l'appel � la m�thode relever() d'UniversalField en la surchargeant pour les classes "UniversalFieldCancel" et "UniversalFieldButton".
2. Ajout et prise en compte des param�tres 'group', lineType' et 'lineClass' pour les classes de boutons "UniversalFieldSubmit", "UniversalFieldReset", "UniversalFieldCancel" et "UniversalFieldButton"

-----------------
V 1.0.2 (18.12.2014)
-----------------
1. Am�lioration classe UniversalField : Modification de la m�thode test() pour que les tests soit fait si le champ n'est pas vide. Si le champ vide ne doit pas �tre accept� alors il faut maintenant jouter le param�tre "REQUIRED"

-----------------
V 1.0.3 (21.01.2015)
-----------------
1. Ajout dans "defines.inc.php" du param�trage de l'IP du d�veloppeur pour affichage de la fen�tre de debug.
2. Corrections mineures sur "listing_users.inc.php"
3. Correction bug dans "routines.inc.php", fonctions g�n�ralistes goPageBack() et lance() : Il fallait ajouter un "die()" une fois la redirection faite afin de s'assurer que le reste du code n'est pas ex�cut�. 
4. For�age de la case des noms, pr�noms des utilisateurs (premi�res lettres en majuscules seulement)
5. Am�lioration de la requ�te "sqlUsers_getListing" de listing des utilisateur pour permettre un tri sur plusieurs champs concat�n�s (ex : "nom,prenom")
6. Sur le listing des utilisateurs, suppression de l'ic�ne poubelle pour son propre compte
7. Ajout du test CHECK_ALPHA_NOMS sur les champs de formulaires

-----------------
V 1.0.4
-----------------
1. Correction "listing_users.inc.php" et "user.php" pour que le retour vers listing (apr�s ajout/modif/suppr) prennent en compte la page d'origine affich�e et se positionne dessus.

-----------------
V 1.0.5
-----------------
1. UniversalForm -> Ajout de la classe getFieldObjectByName()
2. UniversalFormXxxx -> Modifications mineures

-----------------
V 2.0 (06.02.2015)
-----------------
1. Utilise les nouvelles classes de UniversalFORM

-----------------
V2.10 (20.03.2015)
-----------------
1. Modification UniversalForm.class.php
	- Ajout des fonctions 'mySqlDataProtect' et 'rnTo13' hors classe
	- Correction disfonctionnement -> Ajout d'un test d'existence des champs dans getData()

-----------------
V2.11 (08.07.2015)
-----------------
1. UniversalForm
	- Modification du constructeur pour UniversalFieldRadio. Pour �viter une erreur PHP si le param�tre � value � n�est pas sp�cifi�, un test est r�alis� avant et un message d�erreur est mont�.
	- Ajout d'une propri�t� fieldType pour chaque objet qui renseigne sur le type d'objet (text / radio / area / div / etc.)
	- Ajout (prise en compte) du param�tre 'maxlength' et 'invisible' pour champ type � area �
	- Button : Ajout prise en compte param�tre 'invisible'
	- Cancel : Ajout prise en compte param�tre 'invisible'
	- Checkbox : Prise en compte du param�tre 'invisible' (uniquement sur le premier bouton) (un champ 'invisible' renvoie quand m�me ses donn�es au POST mais plus aucun TEST n'est r�alis� sur le champ)
	- Radio : Correction bug readonly
	- Radio : Prise en compte du param�tre 'invisible' (uniquement sur le premier bouton) (un champ 'invisible' renvoie quand m�me ses donn�es au POST mais plus aucun TEST n'est r�alis� sur le champ)
	- Reset : Ajout prise en compte param�tre 'invisible'
	- Select : Ajout de la m�thode priv�e getBetween
	- Select : Ajout de la propri�t� 'invisible'
	- Separateur : Ajout prise en compte param�tre 'invisible'
	- Submit : Ajout prise en compte param�tre 'invisible'
	- Correction getData suite � bug readonly pour les boutons radio

-----------------
V2.12 (12.10.2015)
-----------------
1. UniversalForm
	- Il est maintenant possible de mettre Plusieurs formulaires dans le m�me script. Pour ce faire il faut donner un num�ro unique au formulaire dans le script (2�me param�tre du new).
	- testMatches() est maintenant une m�thode publique, ce qui permet de la surcharger si besoin.
	- Am�lioration du CSS pour que le curseur devienne un point d'interrogation au survol d'un champ qui poss�de un 'title' (c'est-�-dire pour lequel le param�tre 'labelHelp') est positionn�).
	- Ajout de 2 nouveaux param�tres pour l'objet SELECT : 'SIZE' permet de d�finir une liste non d�roulante mais de X lignes d'�paisseur et 'MULTIPLE' permet la s�lection multiple de lignes.
2. routines.inc.php : 
	- Am�lioration de la fonction rnTo13($valeur) pour prendre en compte les tableaux (array)(n�cessaire suite � ajout des 2 param�tres 'Size' et 'Multiple' pour les SELECT).
3. db.connexion.pbo.php : 
	- Am�lioration de la fonction mySqlDataProtect pour prendre en compte les tableaux (n�cessaire suite � ajout des 2 param�tres 'Size' et 'Multiple' pour les SELECT).

-----------------
V2.13 (15.01.2016)
-----------------
1. UniversalForm : passage en version 2.17
	- Ajout d'une fonction javascript setValue(champ, valeur)
	- Ajout de l'objet 'Comment' (commentaire)
2. UniversalForm : passage en version 2.18
	- Modification de la classe UniversalFieldText pour prendre en charge les champs input de type 'file'.
3. UniversalForm : passage en version 2.19
	- Modification de la classe UniversalFieldSubmit. On peut maintenant utiliser plusieurs submit dans un m�me formulaire. On peut m�me les grouper par nom (param�tre groupName) pour avoir en retour l'action valid�e dans un unique champ dbfield.
4. UniversalForm : passage en version 2.20
	- Utilise la version 15.01.2015 de routine.inc.php

-----------------
V2.21 (09.02.2016)
-----------------
1. UniversalForm : passage en version 2.21
	- Corrig� bug methode _transform() de transformation de la saisie
	- Ajout� TRIM � la methode _transform() qui supprime les espaces avant et apr�s la saisie
2. Correction d'un bug sur classe Droits.class.php
3. Ajout d'icones possibles
4. Am�lioration de la gestion des droits contre la suppression du droit admin

-----------------
V2.23 (16.02.2016)
-----------------
1. UniversalForm : passage en version 2.23

-----------------
V2.24 (21.02.2016)
-----------------
1. UniversalForm : passage en version 2.24

-----------------
V2.30 (02.03.2016)
-----------------
1. UniversalForm : passage en version 2.30

-----------------
V2.32 (05.03.2016)
-----------------
1. UniversalForm : passage en version 2.32
2. Routines.inc.php

-----------------
V2.33 (15.03.2016)
-----------------
1. UniversalForm : passage en version 2.33

-----------------
V2.35 (18.03.2016)
-----------------
1. UniversalForm : passage en version 2.35
2. Routines.inc.php du 18.03.2016

-----------------
V2.36 (06.04.2016)
-----------------
1. UniversalForm : passage en version 2.36
2. Routines.inc.php du 02.04.2016

-----------------
V2.37 (23.08.2016)
-----------------
1. UniversalForm : passage en version 2.37

-----------------
V2.38 (22.09.2016)
-----------------
1. Possibilit� d�administration user � partir annuaire LDAP
2. Modification de la proc�dure de cr�ation de premi�re mise en oeuvre
3. Ajout d�un code de base pour Frontend bas� sur HTML 5, Bootstrap 4 et jQuery 3.1.0

-----------------
V3.0.0 (25.10.2016)
-----------------
1. UniversalForm : passage en version v3.0.0 (2016-10-17) - Bootstrap v4.0.0-alpha.4
2. Modification des pages pour Bootstrap v4.0.0-alpha.4

-----------------
V3.1.0 (28.10.2016)
-----------------
1. Cr�ation de la classe unique UniversalFieldBouton qui remplace UniversalFieldSubmit, UniversalFieldButton et UniversalFieldReset
2. Am�lioration UniversalFormText pour affichage si label vide

-----------------
V3.2.0 (02.11.2016)
-----------------
1. UniversalForm : passage en version 3.2.0 (Cr�ation de la classe UniversalFormSearch)
2. Am�lioration des exemples du Frontend

-----------------
V3.3.0 (09.11.2016)
-----------------
1. UniversalForm : passage en version 3.3.0

-----------------
V3.3.1 (23.11.2016)
-----------------
1. UniversalForm : passage en version 3.3.1
2. Mise � jour Tether 1.3.3 (corrige bug de popover Bootstrap)

-----------------
V3.3.2 (12.01.2017)
-----------------
1. UniversalForm : passage en version 3.3.2
2. R�organisation de l�arborescence de dossiers

-----------------
V3.4.0 (23.01.2017)
-----------------
1. UniversalForm : passage en version 3.4.0
2. Utilise Bootstrap v4.0.0-alpha.6
	- Implique la disparition de col-xs-xx au profit de col ou col-xx
	- Implique la disparition des blocs de champs sur tous les objets au positionnement 'inline'
	- Implique la disparition des zones de titres sur certains objets (bouton et search)

-----------------
V3.5.0 (23.02.2017)
-----------------
1. UniversalForm : passage en version 3.5.0
	- Ajout des type de champ � filtretext � et � filtreselect �
	- Ajout de la propri�t� � showErreur �

-----------------
V3.5.1 (20.04.2017)
-----------------
1. UniversalForm : passage en version 3.5.1
	- Ajout des � tooltip � de bootstrap pour les champs titre
	- Am�lioration de l�ordre de chargement des librairies javascript
2. routines.inc.php => ajout de la fonction getReferer() et modification des scripts pour utiliser cette fonction � la place de header('Location: '.$_SERVER['HTTP_REFERER']); 

-----------------
V3.5.2 (25.04.2017)
-----------------
1. Ajout de la classe Listing.class.php et cr�ation de la table � listings � associ�e
2. UniversalForm : passage en version 3.5.2
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
1. UniversalForm : passage en version 3.5.1
	- Ajout de la propri�t� labelHelpPos : alignement du libelle d'aide (left, top (d�faut), right, bottom) pour les tous les champs sauf 'div', 'divfin' et 'hidden'

-----------------
V3.5.4 (16.05.2017)
-----------------
1. UniversalForm : passage en version 3.5.4
2. Routines en version 16.05.2017

-----------------
V3.5.5 (14.06.2017)
-----------------
1. UniversalForm : passage en version 3.5.5
2. Routines en version 14.06.2017
3. Utilise font-awesome-4.7.0

-----------------
V3.5.5.1 (26.07.2017)
-----------------
1. Nouvelles version de 
	- input.php et Form_input.class.php
	- Listing.class.php
	- SimpleListingHelper.class.php

-----------------
V3.5.6.1 (27.07.2017)
-----------------
1. UniversalForm : passage en version 3.5.6
2. Ajout de la m�thode statique swapBool() � sqlSimple.class.php

-----------------
V3.5.6.2 (01.08.2017)
-----------------
1. fonctions.inc.php en version 01.08.2017

-----------------
V3.5.7.2 (08.08.2017)
-----------------
1. UniversalForm : passage en version 3.5.7

-----------------
V3.5.7.3 (10.08.2017)
-----------------
1. Classe SilentMail en version du 10.08.2017 : Ajout du param�tre optionnel 'trace' � la fonction. Il permet d'enregistrer le contenu du mail dans le fichier texte "email.txt"

-----------------
V3.5.8.3 (04.09.2017)
-----------------
1. UniversalForm : passage en version 3.5.8 (Suppression de la m�thode usesJavascriptFunctions() au profil d'une int�gration du fichier universalform.min.js contenant le code des fonctions javascript).

-----------------
V3.6.1.0 (25.10.2017)
-----------------
1. UniversalForm en version 3.6.1 (Utilise Bootstrap v4.0.0-beta.2) et les fonctions javascripts remarchent
2. routines.inc.php (version 17.10.2017)
	- Cr�ation de la fonction "utf8_strpos"
	- Renommage de la fonction mb_ucwords() en utf8_ucwords()
	- Renommage de la fonction mb_right() en utf8_right()
	- Renommage de la fonction mb_left() en utf8_left()
	- Cr�ation de la fonction futureDate() qui calcule une date future
	- Suppression de la fonction pageNavigator au profit de l'objet PageNavigator
	- Ajout de la fonction buildImage qui permet d'obtenir une image formatt� de taille fix� (x, y) pr�fix� par une chaine de caract�re.
	- Ajout de la fonction getRefererScriptName() qui renvoie le script de la page referer (appelante)
3. frontend
	- Mise � jour
4. classe � Listing � (version 25.10.2017)
	- Cr�ation de la m�thode getDisplayedSize()

-----------------
V3.6.2.0 (en cours)
-----------------
1. UniversalForm en version 3.6.2 (tr�s l�g�re correction)
2. Les classes Listing.class et Colonne.class sont remplac�es par la classe UniversalList
3. sqlSimple.class : Version 29.11.2017 - modification des param�tres de getListe (enlev� le param�tre de langue)
4. Ajout de la classe Fpdf.class dans sa version ultime 1.81
5. PageNavigator en version  22.11.2017 (Modification du constructeur. Il fait maintenant appel � des setters puis � la m�thode priv�e _build())
6. Ajout� 3 exemples d�utilisations des listes (exemple_liste_simple.php, exemple_liste_simple_pages.php et exemple_liste_complexe.php)
7. Routines : 
	- Ajout array_delkey_with_reset_keys(),
	- Ajout noDoubleSpace()
	- Ajout trimUltime()
	- D�placement du fichier d'erreurs dans le r�pertoire LIBS
	- Am�lioration de transposePourUrl()
	- Am�lioration de la fonction convert_utf8()

-----------------
V3.7.0.0 (17.03.2018)
-----------------
1. routines.inc.php
	- fonction rnTo13 : la fonction est d�clar�e si elle ne l'est pas d�j� par ailleurs car exite la classe UniversalForm la d�clare aussi (11.01.2018)
	- Am�lioration fonction transposePourUrl() (17.01.2018)
	- Correction mauvais fonctionnement de setCookie dans la fonction getGeolocalisation (13.02.2018)
	- Modification de la fonction convert_utf8()
	- Ajout de la fonction convert_ansi()
	- Ajout de la fonction encode()
	- Modification de la fonction saveCSV() pour meilleur encodage
2. db.connexion.pdo.php
	mySqlDataProtect d�clar� si pas encore d�clar�
3. Ldap.class.php 12.01.2018 :
	- ajout du port dans le constructeur avec port par d�faut � 389
	- ajout de l'erreur -1 (Can't contact LDAP server)
4. listing_droits.php 06.02.2018 : 
	- Petite correction bug
5. PageNavigator.class.php 10.01.2018 : 
	- correction _drawStandard() pour compatibilit� PHP 7.2.0 fonction count()
6. UniversalList 12.01.2018 : VERSION 2.2.0
	- Renomm� parametre de colonne sens en triSens
	- Renomm� m�thode getSens() en getTriSens()
	- Renomm� m�thode setSens() en setTriSens()
	- Renomm� propri�t� priv�e _sensEncours en _triSensEncours
	- Renomm� propri�t� priv�e _sensDefault en _triSensDefault
	- Renomm� m�thode getSensEncoursLibelle() en getTriSensEncoursLibelle()
	- Ajout du param�tre de colonne titlePos qui positionne le title (top/right/bottom/left)
7. Ajout de la classe UniversalTree (+ doc)
8. fonctions.inc.php 15.01.2018 : 
	- Ajout de la fonction newUser()
9. Form_login.class.php 15.01.2018 : 
	- Ajout de la prise en compte ERREUR_ANNUAIRE
10. Form_user.class.php 15.01.2018 : 
	- correction petit bug (ajout addslashes � code javascript)
11. sql_droits.inc.php 06.02.2018 :
	- Modification sqlDroits_addProfil() et sqlDroits_addFonctionnalite() : 
	- Maintenant � chaque cr�ation de profil ou de fonctionnalit�, les droits sont automatiquement ajout� dans la table "droits" avec interdiction (0)
12. JQuery
	- Utilise Version JQuery 3.3.1
13. Font-awesome
	- Utilise version 5.0.8 gratuite
14. Bootstrap
	- Utilise version Bootstrap 4.0.0
15. UniversalForm
	- Utilise version 3.7.1

-----------------
V3.7.1.0 (20.03.2018)
-----------------
1. Icons Font-Awesome 5.0.8 fonctionnels

-----------------
V3.8.0.0 (non test�e)
-----------------
1. Renommage ExtraWeb en UniversalWeb
2. Suppression du fichier version.doc et remplacement par le fichier versions.txt (ce fichier)
3. Ajout de tables de logs Extraweb (+listing sur l'interface d'admin)
4. Classe UniversalList : 16.04.2018 : VERSION 2.4.0
	- Ajout	de la m�thode publique setTriEncours() (modification du tri en cours)
	- Ajout de la m�thode publique setTriSensEncours() (modification du sens du tri en cours)
	- Ajout de la m�thode publique getTriEncours() qui renvoie le champ tri� en cours
	- Ajout de la m�thode publique getTriSensEncours() qui renvoie le champ tri� en cours
	- Documentation mise � jour
5. Classe UniversalList : 14.05.2018 : VERSION 2.4.1
	- Modifi� pour prendre en compte version UniversalForm 3.8.0
6. Routines
	- Am�lioration de la fonction tron
	- Simplification de la fonction etatDuRepertoire() (03.05.2018)
	- Cr�ation de la fonction litRepertoire() (03.05.2018)
	- getThumb() marche maintenant pour jpeg et png (07.05.2018)
	- Ajout de la fonction array_flip_key()(11.05.2018)
	- Modification fonctions restoreDatabase() et saveDatabase() pour obliger � renseigner les binaires "mysql" et "mysqldump" (17.05.2018)
	- Am�lioration de la fonction oteAccents() pour que cela marche dans tous les codages (comme UTF-8) (28.05.2018)
	- Ajout de la fonction writeLog() qui �crit une ligne de log standardis�e dans un fichier log (29.05.2018)
	- Modification saveDataBase() et restoreDatabase() : les noms de fichier de sauvegardes prennent maintenant en compte un param�tre de version de l'application en cours (ex : v.1.2) pour renseigner sur la version sauvegard�e (01.06.2018)
	- Modification saveDataBase() : supprime le fichier de la sauvegarde si celle-ci ne s'est pas bien pass� (on avait des fichiers de sauvegarde vide !) (15.06.2018)
7. Classe Droits.class.php (22.03.2018)
	- Ajout de la m�thode retreiveCodeFonctionnaliteFromId() qui renvoie le code de la fonctionnalit� dont d'id est pass� en param�tre
8. backend\libs\sql\sqlSqueletteReferences.inc.php (28.03.2018)
	- Renomm� en backend\libs\sql\sql_squelette_references.inc.php et modifications suivantes
	- Correction de l'appel aux m�thodes pour compatiblit� PHP 7 (le nombre de param�tre doit $etre identique entre la m�thode et sa surcharge -> rajout param�tre $debug )
	- Changement du nom du script en sql_squelette_references.inc.php
	- Ajout de la fonction sqlSqueletteReference_fillSelectTous()
9. Form_squelette.class.php
	Am�lioration des boutons pour �tre adaptive (28.03.2018)
10. Classe SimpleListingHelper (05.04.2018)
	- Rend les classe statiques car version PHP 7.1.8 est stricte et �vite ainsi ce genre de message : Deprecated: Non-static method SimpleListingHelper::getParams() should not be called statically 
11. Classes UniversalForm	Version 3.8.0 du 14.05.2018
	- Obligation ajouter la classe "uf" au tag "<form>" qui doit utiliser les classes UniversalForm
	- Ajout des propri�tes labelPlus, labelPlusHelp et labelPlusHelpPos sur les champ de type UniversalFieldText (permet de scinder le label en 2 parties, par exemple un texte et une icone)
	- Modification apport�e � tout UniversalWeb
12. Classes UniversalForm	Version 3.9.0 du 17.05.2018
	- Ajout de la propri�t� flexLine : application des utilitaires flexbox sur les lignes d'objets (permet des cadrages, positionnement et plein d'autre choses sur les objets de la ligne)
13. Classes UniversalForm	Version 3.9.1 du 12.06.2018
	- Correction bug UniversalFieldSelect : le textMatches 'REQUIRED_SELECTION' ne fonctionnait pas car la m�thode relever() rendait disable la liste et le match ne se faisait plus
14. Classe SilentMail (14.05.2018)
	- Remis les headers avec �criture du tableau dans le fichier de sortie texte
15. Int�gration des fonctions de sauvegarde et de restauration de la base de donn�es
16. actions_droits.php (backend) 
	- Version : 01.06.2018 : am�lioration testMatch sur Input pour saisie num�rique des ids de profis et fonctionnalit�s
17. maintenance.php (backend) 01.06.2018
	- Modification des entr�es 'savedb', 'loaddb' et 'gorestoredb' pour int�grer la notion de version d'application dans le nom de la sauvegarde
	- Ajout de l'entr�e 'deldb' de suppression de la sauvegarde
	- Pr�sentation de la liste des sauvegardes disponibles sous forme de table
18. Classes Login 16.07.2018
	- Correction bug getLib() - Les getLib des ces classes �taient �crit sans ''
19. authentification.php 16.07.2018
	- Ajout d'un else dans la cas ou la m�thode login() renvoie un code != true
20. Remaniement de l'initialisation de l'application
	- Cr�ation d'un config.inc.php qui devient le SEUL fichier � configurer pour l'application recevant le param�trage propre � l'appli + la base de donn�es + mode d'ex�cution (dev, prod, etc)
	- Suppression de db.inc.php (plus n�cessaire)
	- Remaniement de defines.inc.php
	- All�gement de init.inc.php

-----------------
V3.8.0.1 (non test�e)
-----------------
1. Ldap.class.php 23.07.2018
	- Am�lioration de la m�thode search 
	voir https://forum.phpfrance.com/php-avance/suivre-referral-ldap-t35236.html
	ou https://www.developpez.net/forums/d123654/php/bibliotheques-frameworks/ldap-ldap_search/
	le referral est une d�finition dans OpenLDAP qui permet de dire au CLIENT d'aller voir ailleurs s'il ne trouve pas la r�ponse dans l'OpenLDAP directement.

-----------------
V3.8.0.2 (en cours ... � tester)
-----------------
1. UniversalField Version 3.9.2 du 12.11.2018
	- Ajout du validateur CHECK_SHA1
2. Droits de l'appication sortis du fichier fonctions.inc.php vers le fichier sp�cifique droits.inc.php
3. Remplacement des constantes d'ip de d�veloppement par par une seule qui contient un tableau

-----------------
V3.8.1.0
-----------------
1. routines 26.11.2018
	- Renomm� la fonction writeLog() en writeStdLog()	
2. init.inc.php 27.11.2018
	- Mode d'execution des requetes fonction du mode d'execution (dev ou prod)
3. Utilise Bootstrap V4.1.3
4. Utilise version UniversalForm V3.10.0 pour compatibilit� avec Bootstrap V4.1.3
5. Utilise Font Awesome 5.0.10
6. Ajout version Apache dans panel information syst�me extranet
7. Ajout du r�pertoire 'armoire' destin� aux imports/exports d'informations
8. Coloration syntaxique de la liste des sauvegardes de bases de donn�es disponibes en fonction des version (avec conseils)
9. Fichiers de langue enrichis et corrig�s
10. Am�lioration du header backend
11. Am�lioration de la pr�sentation du formulaire de saisie users

-----------------
V3.8.2.0 (en cours ... � tester)
-----------------
1. Utilise Font Awesome V5.5.0
2. Utilise UniversalWeb V3.10.1
3. routines 30.11.2018
	- fonction transposePourUrl() -> Correction Bug suite � passage en PHP 7.2 -> $str[0] = '' ne marche plus ! il faut remplacer par $str = substr($str, 1)
4. defines.inc.php 01.12.2018
	- ajout version FontAwesome, cr�ation _FONT_AWSOME_ et modification _FONT_AWSOME_CSS_
5. fonctions.inc.php 01.12.2018
	- correction writeHtmlHeader() suite � modif _FONT_AWSOME_CSS_ du defines.inc.php
6. Ajoute la version de Font Awesome sur les footer
7. routine.inc.php a �t� scind� en 9 fichiers uw_[fonction].php et ne fait plus que l'agr�gat de ces fichiers

-----------------
V3.9.0.0
-----------------
1. Remplacement de la variable _URL_BACK_END_ par _URL_BASE_SITE_ le plus possible afin de permettre � un backend de devenir facilement un frontrend
2. Utilise UniversalWeb V3.11.3
	- Renommage du Match 'FIRST-LETTER' en 'FIRST-LETTER-ONLY' (il passait toute le texte en minuscule avant de mettre la premi�re lettre en majuscule, donc plus appropri�)
	- Cr�ation du Match 'FIRST-LETTER' (mise en majuscule de la premi�re lettre sans passage en minuscule pr�alable � tout le texte)
	- En plus d'une valeur vide, le testMatch REQUIRED_SELECTION (pour les select) teste maintenant aussi sur la "chaine de caract�re" 'NULL' (et non pas NULL)
	- Ajout du test matches CHECK_EMAIL_APOSTROPHE (idem CHECK_EMAIL mais autorise l'apostrpohe en plus)
3. UniversalList V2.5.1
	- Ajout dela m�thode publique UniversalListColonne::setFiltreValueDefault() (elle �tait jusque l� seulement priv�e)
	- Ajout de la m�thode publique UniversalList::colExist($id) qui renseigne si la colonne identifi�e par $id existe dans la liste (renvoie true ou false)
	- Correction createTable() pour corriger la structure de la table Listing. tinyint(3) � la place de tinyint(3)
4. Corrig� classe sqlSimple en SqlSimple
5. Utilise Font-Awesome V5.6.3
6. Ajout de la classe UniversalZip
7. Ajout du module de signature du code (avec signature finale)
8. Cr�ation de la classe UniversalDatabase (v1.0.0) (mais non utilis�e par UniversalWeb)
9. Sorti _VERSION_APP_, _AUTEUR_ et _COPYRIGHT_ de "config.inc.php" vers "define.inc.php"
10. Ajout d'une page de versionning dans le menu administration
11. Ajout d'un .htaccess sous le repertoire "DB_backup" pour interdire l'acc�s direct
12. Fonction de purge log op�rationnelle
13. Ajout fonction de suppression des logs
14. Traductions :
	- "maintenance.php"
	- "actions_droits.php"
	- "infos_systeme.php"
	- "listing_droits.php"
	- "listing_logs.php"
	- "brique_erreur.php"
	- "Form_input.class.php"
	- "Form_login.class.php"
	- "Form_simple_text.class.php"
	- "Form_squelette.class.php"
	- "Listing_logs.class.php"
	- "SilentMail.class.php"
	- "SimpleListingHelper.class.php"
15. Ajout et test de la classe UniversalCsvImport.class.php (aide � l'import de fichiers CSV)
16. Scind� le fichier config.inc.php (mis tout ce qui concerne la connexion � la base de donn�es dans config_db.inc.php)
17. Cr�ation et test de la m�thode SqlSimple::importMany() (import en plusieurs requetes)
18. Cr�ation de la documentation UniversalCsvImport()(v1.0.0)
19. Ajout des versions pour les classes UniversalZip (v1.0.0) et UniversalTree (v1.0.0)
20. Correction affichage journaux lors d'erreur de login (affichait le mot de passe et pas le login de l'utilisateur)

-----------------
V3.10.0.0
-----------------
1. Utilise Bootstrap 4.3.1
2. Utilise Font Awesome 5.7.2
3. SimpleListingHelper 07.03.2019
	- Changement de param�tres pour la m�thode getParams()
	- On passe maintenant l'identifiant de la colonne par d�faut et non pas la liste des champs triables
	- On ajoute aussi un die() si la colonne par d�faut n'existe pas
4. Ajout et utilisation de la police Montserrat � la place de Roboto
5. UniversalList V2.6.0 (14.03.2019)
	- Ajout des m�thodes publiques UniversalList::setHeadClass() et UniversalList::getHeadClass() pour modifier la classe CSS de l'ent�te du tableau
	- Ajout des m�thodes publiques UniversalList::setFiltresClass() et UniversalList::getFiltresClass() pour modifier la classe CSS du bandeau de filtres du tableau (valeur d�faut "thead-light")
	- Ajout du param�tre "header" dans la construction de la colonne de la table. (objet UniversalListColonne) : true (la colonne est une entetre pour la ligne), false sinon (valeur par d�faut) ce param�tre indique si la donn�e de la colonne doit servir d'ent�te pour la ligne.
	- Ajout des m�thodes UniversalListColonne::setHeader() et UniversalListColonne:: getHeader() pour prendre en compte le nouveau param�tre "header"
	- Affichage du listing : supprim� la taille de la colonne pour permettre � du code javascript de la modifier en drag & drop (voir code https://www.brainbell.com/javascript/making-resizable-table-js.html)
6. Classe SimpleListingHelper 14.03.2019
	- Ajout du param�tre "header" dans la construction de la colonne de la table. true (la colonne est une entete pour la ligne), false sinon (valeur par d�faut). Ce param�tre indique si la donn�e de la colonne doit servir d'ent�te pour la ligne.
	- Ajout du param�tre $css � la methode drawHead (personnalisation de l'ent�te)
7. backend : ajout affichage de la release en cours... (affichage du contenu du fichier release.txt)
8. backend/libs/fonctions.inc.php 19.03.2019
	- Ajout de la fonction getRelease() qui vient lire le contenu du fichier release.txt
9. Ajout de la fonction "Cookie Disclamer" en standard sur le Frontend.
10. Ajout de la signature du code frontend.

-----------------
V3.11.0.0 (11.04.2019)
-----------------

1. Classe UniversalCsvImport en v2.0.0 + documentation � jour + exemple d'import � jour
2. Correction Bugs dans les journaux
3. Mise en conformit� de tous les num�ros de version des classes UniversalWeb
4. Ajout des informations de configuration dans la page infos_systeme.php
5. Ajout des informations de version UniversalWeb dans la page infos_systeme.php

-----------------
V3.11.1.0 (12.04.2019)
-----------------
1. Correction bug infos_systeme.php (certains serveurs ne connaissent pas la fonction apache_get_version())

-----------------
V3.11.2.0 (15.04.2019)
-----------------
1. infos_systeme.php : Test de l'existence des classes UniverslaWeb pour affichage sans bug en cas de non pr�sence de la classe. la fonction php class_exists() ne convient pas !

-----------------
V3.11.3.0 (16.04.2019)
-----------------
1. UniversalList en Version V2.7.0
	- Modification de la table xx_listings : ajout du champ last_update qui donne le timestamp de la cr�ation de la liste et de sa derni�re modification (public function createTable())

-----------------
V3.11.4.0 (17.04.2019)
-----------------
1. UniversalForm en Version V3.12.0
	- Les positionnement des tooltips sont par d�faut 'auto'
2. Changement variable _VERSION_APP_ en _APP_VERSION_
3. sql_logs.inc.php
	- Correction m�thode purge() : contrairement � DELETE FROM, TRUNCATE TABLE ne retourne pas le nombre de lignes supprim�es l'ancienne m�thode renvoyait toujours 0
4. maintenance.php
	- Correction bug
	- correction purgelog()
5. infos_systeme.php
	- ajout info _APP_VERSION_

-----------------
V3.12.0.0 (22.04.2019)
-----------------
1. D�pose par d�faut du fichier d'erreurs errors.txt dans dossier "armoire" prot�g� par .htaccess
2. Pr�sentation des informations syst�me dans une s�rie d'onglets
3. Am�lioration du contenu des informations syst�me : 
	- Ajout de la configuration UniversalWeb pour le Frontend
	- Listage et �puration des erreurs PHP du backend (�vite de g�rer le fichier errors.txt manuellement)
	- Listage et �puration des erreurs PHP du frontend (�vite de g�rer le fichier errors.txt manuellement)

-----------------
V3.12.1.0 (non test�e)
-----------------
1. uw_flux.php -> fonction saveDatabase -> ajout de l'option -R � la commande Mysqldump qui permet de sauver AUSSI les proc�dures et fonctions stock�es

-----------------
V3.13.0.0 (28.05.2019)
-----------------
1. UniversalForm en Version V3.13.0
2. Ajout de la notion de groupes de fonctionnalit�s dans la gestion des droits (drag & drop, modification des droit via Ajax etc.)

-----------------
V3.13.1.0 (non test�e)
-----------------
1. uw_file.php -> ajout fonction downloadUrl() qui t�l�charge une url distante (hors site) vers un fichier cible
2. sqlUsers_updateUser() et sqlUsers_updateUserGlobal() am�lior�es pouir eviter un notice si toutes les donn�es ne sont pas envoy�es
3. Correction maintenance.php 'hashfrontend' n'affiche plus de warning si le fichier n'est pas trouv�

-----------------
V3.14.0.0 (en cours)
-----------------
1. UniversalForm en Version V3.15.1
	- Ajout de la propri�t� "multiple" pour l'objet UniversalFieldText. Seulement pris en compte pour les champs de type "file". Gr�ce � cette propri�t� on peut maintenant selectionner plusieurs fichiers en une seule fois. De ce fait la structure envoy�e par getData() (pour les champs text de type file seulement) a chang�.
	- Ajout de zoneTitre et zoneChamp pour les objets DIV 
	- Ajout du param�tre 'accept' pour les champs texte de type file (selecteur de fichier ne propose que les extentions dans accept)
	- Correction bug : le passage de la souris affichait le ? m�me sur les champs qui n'avaient pas de texte d'aide
2. Correction des void() en void(0) (erreur Javascript)
3. Ajout d'un exemple d'upload de fichiers multiples avec possibilit� de gliss� / pos�
4. Correction bug "exemple_import_csv.php". Sous Firefox, la recopie du fichier choisi marquait "undefined" (modifi� javascript dans fonction getvalue())
5. UniversalCsvImport en version V2.1.0 (ajout des tests 'MIN_LENGTH_X' et 'MAX_LENGTH_X')
6. Ajout de la fonction checkDateTimeFormat() � la librairie uw_dates.php qui test la validit� de saisie d'une date au regard d'un format (10.09.2019)

-----------------
V3.15.0.0 (en cours)
-----------------
4. Informations syst�me : Affichage de toutes les informations connues de la version MySQL sans restriction (29.10.2019)
5. Classe SilentMail -> Ajout de constantes pour compte rendu d'erreur (31.10.2019)

-----------------
V3.16.0.0 (en cours)
-----------------
1. UniversalForm en Version V3.16.0 
	- Ajout composant switch customisable
	- Ajout des propi�t�s : min, max, step, pattern, autocomplete et autofocus pour les <input> de type text (UniversalFieldText)
2. Temporisation de l'affichage des tooltips par d�faut (800ms a l'affichage et 100 � la disparition) (fonctions.inc.php)

-----------------
V3.17.0.0 (en cours)
-----------------
1. Passage en JQuery 3.4.1
2. Passage en Bootstrap 4.4.1
3. Passage en Font-Awesome 5.11.2
4. Correction bug script de d�monstration exemple_import_csv.php ligne 170 => remplac� continue par break pour compatibilt� avec PHP 7.3 (sinon plante)
5. Am�lioration de la page info_systeme.php pour utilisation portables
6. Backend -> Ajouts icones sur menu administration

-----------------
V3.18.0.0 (en cours)
-----------------
1. Cr�ation et intr�gration du fichier universalweb.css (+fonctions.inc.php)
2. SimpleListingHelper
	- Appliqu� le CSS aux tags <th> et <td> (drawBody) (04.12.2019)
	- Vir� les propri�t�s HTML "width" et "align" depr�ci�s en HTML 5 et remplac� par du CSS (04.12.2019)
3. Ajout d'icones suppl�mentaires sur les message d'alerte founis par riseErrorMessage, riseWarningMessage et riseInfoMessage (fonctions.inc.php)
4. Ajout de fonctions dans les fichiers de langue
	- existeLib() test l'existence d'un libell� de langue
 	- getLLib() transforme tous les param�tres en minuscules avant de traduire
	- getULib() transforme tous les param�tres en majuscules avant de traduire
5. Ajout fonction javascript uf_justUncheckOnCheck() (fichiers universalform.js et universalform.min.js)
6. Am�lioration de la responsivit� des exemples de formulaires et listings (valable seulement depuis Bootstrap 4.4.1)
7. Classe SqlSimple : ajout du param�tre debug � la classe existValeur et existValeurAilleurs (13.12.2019)
8. UniversalForm en version 3.17.0 (13.12.2019)
	- Ajout de la propri�t� "custom" pour le composant "switch" (switch customis� en check et radio)
	- Positionnement syst�matique du focus sur le premier champ en erreur (le focus par d�faut est m�moris� puis reposition� lorsqu'il n'y a plus d'erreurs)
9. Classe PageNavigator
	- correction bug _drawStandard() en cas de path inexistant (15.12.2019) 
	- donn� num�ro de version V2.2.2
10. Classe Fpdf
	- M�thode MultiCell am�lior�e le (06.11.2017). Elle renvoie maintenant le nombre de lignes �crites dans la cellule Par ajout de la variable $nbLines. Avant la m�thode ne renvoyait rien.
	- Ajout constantes Fpdf::VERSION et Fpdf::COPYRIGHT
11. Ajout infos des classe Fpdf et PageNavigator dans backend/info_systeme.php
12. Am�lioration Input

-----------------
V3.18.1.0 (en cours)
-----------------
function getLibLower($indice, $param1='', $param2='', $param3='', $param4='', $param5='') {
	return '<span class="text-lowercase">'.getLib($indice, $param1, $param2, $param3, $param4, $param5).'</span>';
}

-----------------
V3.19.0.0 (13.01.2020)
-----------------
1. Ajout fonction getLibLower() aux fichiers de langues
2. SqlSimple 
	- Ajout des m�thodes statiques getMin, getMax, getGap (23.12.2019)
	- Ajout de la m�thode catalog (13.01.2020)
3. UniversalForm en Version 3.18.0 du 07.01.2020
	- Ajout de la propri�t� "cheight" pour les composants Area, Select, Comment, filtreSelect, FiltreText et Text qui permet de modifier la taille des zones de texte (lg ou sm)
4. Classe PageNavigator - Version 2.2.3 (13.01.2020) 
	- Correction bug usage setLangue() qui ne fonctionnait pas
5. Correction backend/info_systeme.php (ne peut pas afficher les versions de classe UniversalWeb)
6. UniversalList en Version VERSION 3.0.0 (09.01.2020)
	- Changement de tous les mots cl�s de test (constantes de la classe UniversalListColonne)en version anglaise pour une meilleurs compr�hension du fonctionnement
		TOUT => CMP_ALL	(valeur : ALL)
		EGAL => CMP_EQUAL (valeur : EQL)
		DIFFERENT => CMP_DIFFERENT (valeur : DIF)
		COMMENCE => CMP_BEGINS_BY (valeur : BEG)
		CONTIENT => CMP_CONTENDS (valeur : CON)
		CONTIENTPAS => CMP_DO_NOT_CONTENDS (valeur : DNC)
		FINIT => CMP_ENDS_BY (valeur : END)
		IGNORE => CMP_IGNORE (valeur : IGN)
		COMMENCENUM => CMP_BEGINS_BY_NUMBER (valeur : BBN)
		SUPERIEURA => CMP_GREATER_THAN (valeur : GRT)
		SUPERIEUROUEGALA => CMP_GREATER_OR_EQUAL_TO (valeur : GET)
		INFERIEURA => CMP_LOWER_THAN (valeur : LOT)
		INFERIEUROUEGALA => CMP_LOWER_OR_EQUAL_TO (valeur : LET)
		EGALA => EQUAL_TO (valeur : ETO)
	- Remplacement des mots cl�s 'TOUT' et 'TOUTES' et par la constante UniversalListColonne::CMP_ALL pour d�signer la s�lection de toutes les valeurs d'un filtre select (m�thode _buildFiltreSelect)
7. UniversalList en Version VERSION 3.0.1 (13.01.2020)
	- Correction bugs avec usage IGN qui ne fonctionnait plus suites � modifs v3.0.0

-----------------
V3.20.0.0 (en cours)
-----------------
1. SqlSimple
	- Ajout de la constante VERSION et prise en compte dans le script infos_syst�me.php (24.01.2020)	
2. uw_chaines.php
	- Am�lioration de la fonction truncateText() -> Ajout du param�tre $wordwarp (29.01.2020)
	- Ajout de la fonction array_addslashes() (03.02.2020)
	- Ajout de la fonction array_stripslashes() (03.02.2020)
3. UniversalList en version 3.0.2 (31.01.2020)
	- Correction m�thode drawBody : remplac� tag html 'align=' par class='text-'
4. UniversalList en version 3.1.0 (31.01.2020)
	- Les filtres externes de type 'search' (donc les plus simples) peuvent maintenant effectuer leur recherche sur plusieurs champs. Pour ce faire, il suffit de saisir dans le param�tre 'filtreScope' du 
	filtre externe la liste des champs � interroger s�par�s par le caract�re pipe (|) ex : 'filtreScope' => 'titre|resume'
5. UniversalTree en version 1.0.1 (03.02.2020)
	- Correction bug m�thodes addNode et addLeave. L'�chappement des chaines est maintenant pris en compte automatiquement (utiliose la nouvelle fonctions array_addslashes() de uw_chaines.php)
6. UniversalList en version 4.0.0 (10.02.2020)
	- Extension d'utilisation des filtres externes de type 'none' (cad non graphique) pour envoyer directement des bribes complexes de SQL => nouveau mot-cl� CMP_SQL (valeur 'SQL')
	- Correction du constructeur UniversalFiltreExterne => pas de cr�ation d'objet UniversalForm pour les filtres externes de type 'none' puisque ils sont non graphique (gain m�moire)
	- Modification du filtre externe checkbox (n'affiche plus une checkbox mais un switch customisable via le nouveau param�tre filtreCustom uniquement valable pour ce type de filtre externe)
7. Correction bug dans backend/libs/sql_logs et frontend/libs/sql_logs (06.03.2020)
8. Am�lioration graphique script backend/infos_systeme.php (05.03.2020)

-----------------
V3.21.0.0 (en cours)
-----------------
1. UniversalForm en Version 3.19.0 (19.03.2020)
2. UniversalList en Version 4.2.0 (19.03.2020)
3. backend/sql_divers.inc.php 
	- Ajout fonction sqlDivers_updateTableAutoIncrement() (18.03.2020)
4. Ajout de la signature de la base de donn�es dans les outils administrateur (23.03.2020)
	- uw_flux en version du 23.03.2020
5. UniversalForm en Version 3.20.0 (26.03.2020)
	- ajout du param�tre 'complement' pour UniversalFieldImage ce qui permet via l'appel callback d'appeler des images ext�rieures au site
6. Ajout du module d'affichage et de gestion de param�tres pour l'application dans le menu syst�me
7. briques_messages
	- Transformation du message en tableau de messages indic� selon (tri dans l'ordre choisi avant affichage) (01.01.2020)
	- En cons�quence, ajout d'un 2�me param�tre non obligatoire aux fonctions riseMessage(), riseErrorMessage(), riseWarningMessage(), riseInfoMessage() qui permet de choisir dans quel ordre les messages
	envoy�s seront affich�s (lorsqu'il y a bien entendu plus de 2 message de cr��s avant changement de script) 	
8. SqlSimple en Version 1.1.0 
	- Ajout de la m�thode transaction qui execute plusieurs requetes fournies dans le tableau $requetes tout en  op�rant une transaction afin de garantir l'int�grit� de la base (30.03.2020)
9. Ajout d'un gestionnaire de media au backend (03.04.2020)
10. PageNavigator en Version 2.3.0 (07.04.2020)
	- Ajout de la m�thode setAncre qui permet de positionner une ancre navigateur � la fin de l'url (uniquement en sch�ma standard)
11. SilentMail en version 2.0.0 (07.04.2020)
	- Ajout d'un num�ro de version de la classe
	- Modification et uniformisation des constantes de message d'erreur + libell�s UniversalWeb
	- Ajout de la valeur 'test' au param�tre 'trace' de la m�thode send() qui permet de simuler l'envoi de mail
	- Cr�ation de la m�thode statique getMessage permettant de r�cup�rer un message en clair selon le code d'erreur pass� en param�tre
	- Ajout des m�thodes publiques getTo, getCc, getBcc et getFrom
12. uw_nav
	- Correction bug de la fonction getUrlWithoutFirstSlash() (08.04.2020)
13. SqlSimple en Version 1.2.0 (10.04.2020)
	- Ajout de la m�thode getSome qui ram�ne tous les tuples de la table pour laquelle le $champ a la valeur $valeur
	- La m�thode updateChamp est maintenant ex�cut�e en UPDATE IGNORE � la place du simple UPDATE
14. SimpleListingHelper en Version 1.1.0 (14.04.2020)
	- Ajout du num�ro de version plus mise � jour dans info_systeme.php
	- Ajout de param�tres possibles pour permettre du drag & drop sur les lignes
		si on ajoute aux donn�es du listing :
		- 'ligne-draggable' : on permet � la ligne <tr> d'�tre draggable
		- 'ligne-droppable' : on permet � la ligne <tr> de recevoir un drop
		- 'ligne-info' : on ajoute un attribut 'info' � la ligne <tr> (pour y mettre ce que l'on veux)
15. Ajout de la gestion des param�tres pour les administrateur
16. Ajout de la gestion des r�glages pour les webmasters
17. Ajout droit "G�rer les utilisateurs" (FONC_ADM_GERER_USERS) et modification code en cons�qsuence (permet de facilement donner les droits de gestion � un autre profil qu'ADM)
18. Correction bug affichage date de cr�ation du compte dans formulaire user
19. Ajout de la fonction getLibIfExists() dans les fichiers de langue