<?php 
session_start();
include_once "fonctions.php";
include_once "formulaires.php";

// POINT 2 : Vérification stricte de la session et du rôle Admin
if (!isset($_SESSION['token']) || $_SESSION['admin'] !== true) {
    header("Location: index.php");
    exit(); 
}
?>
<!DOCTYPE html>
<<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./style.css" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        // Affiche le menu (qui contient déjà le lien déconnexion)
        Menu(); 

        // Gestion de la déconnexion
        if (isset($_GET['action']) && $_GET['action'] == 'logout') {
            $_SESSION = array();
            session_destroy();
            redirect('index.php', 0);
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
            
            // Vérification du Captcha (Point 4 - Librairie GD)
            if ($_POST['captcha'] != $_SESSION['code']) {
                echo '<div class="alert alert-danger">Captcha incorrect !</div>';
                redirect("modification.php", 2);
            } 
            else {
                // Si on a le nom de l'étudiant à modifier mais pas encore les nouvelles infos
                if (isset($_POST['nom_etu']) && !isset($_POST['nouveau_nom'])) {
                    // Ici on affiche le formulaire de saisie des nouvelles valeurs
                    FormulaireModificationEtudiant($_POST['nom_etu']); 
                } 
                // Si on a reçu les nouvelles infos (nom_actuel, nouveau_nom, nouveau_grp)
                else if (isset($_POST['nom_actuel']) && isset($_POST['nouveau_nom'])) {
                    
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

    <!-- Ajout du script JavaScript pour gérer l'envoi AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</body>

</html>
