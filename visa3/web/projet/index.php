<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'fonctions.php';
include_once 'formulaires.php';

if (isset($_GET["action"]) && $_GET["action"] == 'logout') {
    session_destroy();
    $_SESSION = array();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Sae24</title>
    <link rel="stylesheet" href="style.css" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="Ajax.js"></script>
</head>
<body>
    <header>
        <h1>Sae24</h1>
    </header>

    <nav class="d-flex">
        <?php
        if (empty($_SESSION['login'])) {
            FormulaireAuthentification();
        } else {
            Menu(); 
        }
        ?>
    </nav>

    <article>
        <?php
        // Affichage du message d'accueil
        if (!empty($_SESSION['login'])) {
            echo '<h2>Bienvenue ' . htmlspecialchars($_SESSION['login']) . ' sur la page d accueil.</h2>';	
            
            // Zone centrale : Affichage des données uniquement si connecté
            echo "<nav class='d-flex'>";
                
                // Colonne de gauche : Liste globale
                echo "<div class='col-6' id='nav'>";
                    echo '<h2>Liste des etudiants</h2>';
                    $eleve = listerEtudiants();
                    afficheTableau($eleve);
                echo "</div>";

                // Colonne de droite : Filtrage Ajax / Groupe
                echo "<div class='col-6'>";
                    echo '<h2>Liste des etudiants Ajax</h2>';
                    formchgrp();
                    
                    if (isset($_GET['grp'])) {
                        $etudiants = listerEtudiantsParGroupe($_GET['grp']);
                        afficheTableau($etudiants);
                    }
                    
                    // Zone cible pour le JavaScript (Ajax.js)
                    echo "<div id='taba'></div>";
                echo "</div>";

            echo "</nav>";
        } else {
            echo '<p>Vous êtes déconnectés</p>';	
            redirect("connexion.php", 0);
        }
        ?>
    </article>

    <footer>
        <p>Pied de la page <?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?></p>
        <a href="javascript:history.back()">Retour à la page précédente</a>
    </footer>
</body>
</html>
