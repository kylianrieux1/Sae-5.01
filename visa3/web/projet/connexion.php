<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'fonctions.php';
include_once 'formulaires.php';

// 1. TRAITEMENT DU FORMULAIRE (Avant tout affichage HTML)
if (!empty($_POST) && isset($_POST["mail"], $_POST["pass"])) {
    if (authentification($_POST["mail"], $_POST["pass"])) {
        header('Location: index.php');
        exit();
    } else {
        $error_message = "Échec d'authentification pour " . htmlspecialchars($_POST["mail"]);
    }
}

// 2. DÉCONNEXION
if (isset($_GET["action"]) && $_GET["action"] == 'logout') {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css" type="text/css" />
    <title>Sae24</title>
</head>
<body>
    <header><h1>Sae24</h1></header>
    <nav>
        <?php
        if (empty($_SESSION)) {
            if (isset($error_message)) echo "<p style='color:red'>$error_message</p>";
            FormulaireAuthentification(); 
        } else {
            Menu();
        }
        ?>
    </nav>
		
		<footer>
			<p>Pied de la page <?php echo $_SERVER['PHP_SELF']; ?></p>
			<a href="javascript:history.back()">Retour à la page précédente</a>
		</footer>
	</body>
</html>

