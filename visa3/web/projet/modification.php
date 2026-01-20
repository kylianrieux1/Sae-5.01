<?php 
session_start();
include_once "fonctions.php";
include_once "formulaires.php";

// POINT 2 : Vérification de la session (Visa 3)
if (!isset($_SESSION['login']) || $_SESSION['admin'] !== true) {
    header("Location: connexion.php"); 
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./style.css" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="./Ajax.js"></script>
    <title>SAE 24 - Modification</title>
</head>
<body>
    <header>
        <h1>SAE 24 - Administration</h1>
    </header>
    <nav>
        <div id="men" class="col-12">
            <?php 
            // Affiche le menu
            Menu(); 

            // Gestion de la déconnexion
            if (isset($_GET['action']) && $_GET['action'] == 'logout') {
                $_SESSION = array();
                session_destroy();
                // Utilisation de la redirection native pour éviter les erreurs
                header("Location: index.php");
                exit();
            }
            ?>
        </div>
    </nav>
    <article class="container mt-4">
        <?php
        echo '<h2>Bienvenue ' . htmlspecialchars($_SESSION['login']) . '</h2>';
        echo '<h3>Modifier un étudiant</h3>';

        // Étape 1 : Si le formulaire n'a pas encore été envoyé
        if (empty($_POST)) {
            FormulaireChoixEtudiant('modifier');
        } 
        // Étape 2 : Traitement du formulaire de modification
        else if (isset($_POST['captcha'])) {
            
            // Vérification du Captcha
            if ($_POST['captcha'] != $_SESSION['code']) {
                echo '<div class="alert alert-danger">Captcha incorrect !</div>';
                // Utilisation de votre fonction redirect
                redirect("modification.php", 2);
            } 
            else {
                // Si on a le nom de l'étudiant choisi dans la liste mais pas encore les nouvelles valeurs
                if (isset($_POST['nom_etu']) && !isset($_POST['nouveau_nom'])) {
                    // Affiche le formulaire avec les champs Nom et NoGroupe pré-remplis
                    FormulaireModificationEtudiant($_POST['nom_etu']); 
                } 
                // Si on a reçu les nouvelles infos finales (nom_actuel, nouveau_nom, no_groupe)
                else if (isset($_POST['nom_actuel']) && isset($_POST['nouveau_nom'])) {
                    
                    // Appel de la fonction de fonctions.php
                    if (modifierEtudiant($_POST['nom_actuel'], $_POST['nouveau_nom'], $_POST['no_groupe'])) {
                        echo '<div class="alert alert-success">L\'étudiant ' . htmlspecialchars($_POST['nouveau_nom']) . ' a été mis à jour.</div>';
                        afficheTableau(listerEtudiants());
                        redirect("modification.php", 5);
                    } else {
                        echo '<div class="alert alert-danger">Erreur lors de la mise à jour via l\'API.</div>';
                        redirect("modification.php", 3);
                    }
                }
            }
        }
        ?>
        <div id="result"></div>
    </article>
    <footer>
        <p>Pied de la page /modification.php </p>
        <a href="javascript:history.back()">Retour à la page précédente</a>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
