<?php
	session_start(); // cette page utilise les sessions
	include 'fonctions.php';
	include 'formulaires.php';

if (isset($_POST['connect'])) {
    if (authentification($_POST["login"], $_POST["pass"])) {
        // Le jeton est déjà mis en session par la fonction authentification()
        header('Location: index.php');
        exit();
    } else {
        $error_message = "Identifiant ou mot de passe incorrect (via API).";
    }
}
	?>

<!DOCTYPE html>
<html lang="fr" >
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="style.css" type="text/css" />
		<title>Sae24</title>
	</head>
	<body>
		<header>
			<h1>Sae24</h1>
		</header>
		<nav>
			<?php
				// affichage du formulaire de connexion ou le menu avec le nom de la personne
				if (empty($_SESSION)) {// Affichage du formulaire
					FormulaireAuthentification(); 
				}
				else {// Affichage du menu				
					Menu();
				}				
				// test de la connexion: on traite le formulaire
				if(!empty($_POST) && isset($_POST["mail"]) && isset($_POST["pass"])  ){	
					if(authentification($_POST["mail"],$_POST["pass"] )){
					//Création de deux variables de sessions
						$_SESSION["login"]=$_POST["mail"];
						$_SESSION["admin"]=isAdmin($_POST["mail"]);		
						redirect("index.php",1);
					}
					else {
					
					echo '<p>Echec Authentification de '.$_POST["mail"].'</p>';
					
					}
				}				
				// Destruction de la session
				if(!empty($_GET) && isset($_GET["action"]) && $_GET["action"]=='logout'){
					session_destroy();
					$_SESSION=array();	
					redirect("index.php",1);
				}
			?>
		</nav>
		
		<footer>
			<p>Pied de la page <?php echo $_SERVER['PHP_SELF']; ?></p>
			<a href="javascript:history.back()">Retour à la page précédente</a>
		</footer>
	</body>
</html>
