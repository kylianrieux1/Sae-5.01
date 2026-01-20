<?php 
session_start();
include_once "fonctions.php" ;
include_once "formulaires.php" ;

?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="style.css" type="text/css" />
	<link rel="stylesheet" href="./bootstrap-5.3.2-dist/css/bootstrap.min.css">
	<script src="./Ajax.js"></script>
	<title>SAE 24</title>
</head>

<body>
	<header>
		<h1>SAE 24</h1>
	</header>
	<nav>
		<?php
		if (empty($_SESSION)) redirect("connexion.php",0);
		else Menu();
		if (
			isset($_POST) && !empty($_POST) && isset($_POST['admin'])
			&& isset($_POST['login']) && isset($_POST['pass'])
		){
				if(!empty($_POST) && isset($_POST["login"]) && isset($_POST["pass"])){
					if (authentification($_POST["login"],$_POST["pass"])){
						$_SESSION["login"] = $_POST["login"];
						$_SESSION["admin"] = isAdmin($_POST["login"]);
						redirect("index.php",0);
					}
					else{
						echo"<p>lol t'existe pas mdr</p>";
					}
				}
			}
		// Destruction de la session
		if (!empty($_GET) && isset($_GET['action']) && $_GET['action'] == 'logout') {
			$_SESSION = array();
			session_destroy();
			redirect('index.php', 0.001);
		}
		?>
	</nav>
	<article>
		<?php
		#test si l'utilisateur est connecter
		if (!isset($_SESSION['login'])) {
			$_SESSION = array();
			session_destroy();
			redirect('index.php', 0);
		}

		echo '<h2>Bienvenue ' . $_SESSION['login'] . ' sur la page de suppression.</h2>';
		#test si aucun formulaire a été remplie
		if (empty($_POST)) {
			FormulaireChoixEtudiant('supprimer');

		}

		#test si le captcha existe
		if (isset($_POST['captcha'])){
			if ($_POST['captcha'] != $_SESSION['code']) {
					echo' Captcha incorrect';
					redirect("supression.php",0);
				} else {
					$_SESSION["cap"]=True;
					#test si un nom a été renseigner
					if (isset($_POST['nom_etu'])) {
						#test si la supression
						if (supprimerEtudiant($_POST['nom_etu'])){
							echo 'la suppression de ' . $_POST['nom_etu'] . ' a réussi.';
							echo '<br>';
							$tab=listerEtudiants();
							afficheTableau($tab);
							redirect("supression.php",5);
						}
					} 
				
					else {
						echo '<p>'.$_SESSION['cap'].'';
						echo '<p> erreur de la suppression de ' . $_POST['nom_etu'] . '';
						redirect("supression.php",5);
					}
				}
			}

	
		?>
	</article>
	<footer>
		<p>Pied de la page <?php echo $_SERVER['PHP_SELF']; ?> </p>
		<a href="javascript:history.back()">Retour à la page précédente</a>
	</footer>
</body>

</html>