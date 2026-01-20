<?php
include 'fonctions_ETU.php';
include 'formulaires_ETU.php';

if (!empty($_POST) && isset($_POST["choix"])) {
    // appel de la fonction qui retourne seulement les étudiants de la ville choisie
    $tab = listeEtudiantParVille($_POST['ville']);
    if ($tab) {
        afficheTableau($tab);
    } else echo 'KO';
}
