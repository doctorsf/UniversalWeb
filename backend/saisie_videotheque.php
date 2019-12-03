<?php
require_once('libs/common.inc.php');

//création du formulaire
$monFormulaire = new Form_videotheque(UniversalForm::AJOUTER, 1);

//création de la page HTML
echo '<!doctype html>';
echo '<html lang="fr">';
echo '<head>';
  echo '<meta charset="utf-8" />';
  //prise en compte pour l’affichage responsive
  echo '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
  echo '<meta http-equiv="x-ua-compatible" content="ie=edge">';
  echo '<link rel="stylesheet" href="bootstrap-4.3.1-dist/css/bootstrap.min.css">'; //CSS Bootstrap
  echo '<title>Vidéothèque</title>';
echo '</head>';

echo '<body>';
  echo '<div class="container-fluid">';
    echo '<div class="row mt-3">';
      echo '<div class="col-6 ml-auto mr-auto">';

        //panel de l'application
        echo '<div class="row p-3">';
          echo '<div class="col-12 bg-light ">';
            echo '<p class="h1">Saisie vidéothèque</p>';
            echo '<p class="lead">Version UniversalForm : '.UniversalForm::VERSION.'</p>';
          echo '</div>';
        echo '</div>';

        //code propre à la page
          echo '<div class="row mt-3">';
            echo '<div class="col-12">';

				//création du formulaire
				$monFormulaire = new Form_videotheque(UniversalForm::AJOUTER, 1);
				$action = $monFormulaire->getAction();

				if ($action == 'ajouter') {
					$monFormulaire->init();
					echo $monFormulaire->afficher();
				}
				elseif ($action == 'valid_ajouter') {
					if (!$monFormulaire->tester()) {
						echo $monFormulaire->afficher();
					}
					else {
						$lesDonnees = $monFormulaire->getData();
						echo '<pre>';					
						print_r($lesDonnees);										
						echo '</pre>';
					}
				}

            echo '</div>';
          echo '</div>';

      echo '</div>';  //fin colonne centrale
    echo '</div>';
  echo '</div>';

  //Chargement du code Javascript nécessaire au
  //fonctionnement de boostrap
  echo '<script src="js/jquery-3.3.1.min.js"></script>';
  echo '<script>';
  echo '$(document).ready(function () {';
  echo '$("[data-toggle=\'tooltip\']").tooltip();';
	//autres fonctions jQuery à charger …
  echo '});';
  echo '</script>';

  echo '<script src="bootstrap-4.3.1-dist/js/bootstrap.bundle.min.js"></script>';
  echo '<script src="js/php.js"></script>';
  echo '<script src="js/universalform.min.js"></script>';

echo '</body>';
echo '</html>';