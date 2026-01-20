<?php 
session_start();
include_once("./fonctions.php");
include_once("./formulaires.php");


?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="./style.css" type="text/css" />
	<link rel="stylesheet" href="./bootstrap-5.3.2-dist/css/bootstrap.min.css">
	<script src="./Ajax.js"></script>
	<title>SAE 24</title>
	<script src="form.js"></script>
</head>

<body>
	<header>
		<h1>SAE 24</h1>
	</header>
	<nav>
		<?php
		// affichage du formulaire de connexion ou le menu avec le nom de la personne
		if (empty($_SESSION)) redirect("./connexion.php",0);
		else Menu();


		// test de la connexion
		if (
			isset($_POST) && !empty($_POST) && isset($_POST['connect'])
			&& isset($_POST['login']) && isset($_POST['pass'])
		) {
			if (authentification($_POST['login'], $_POST["pass"])) {
				$_SESSION['login'] = $_POST['login'];
				if (isAdmin($_SESSION['login'])) $_SESSION["statut"] = 'admin';
				else{ $_SESSION["statut"] = 'user';
				redirect('index.php', 0.001);}
			} else {
				echo "l'utilisateur n'existe pas !";
			}
		}

		// Destruction de la session
		if (!empty($_GET) && isset($_GET['action']) && $_GET['action'] == 'logout') {
			$_SESSION = array();
			session_destroy();
			redirect('./index.php', 0.001);
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
		#test des permissions
		if(!$_SESSION['admin']){
			echo "Vous n'avez pas les droit !";
			redirect('index.php', 0);
		}
		
		#affichage du formulaires et du tableau
		echo '<h2>Bienvenue ' . $_SESSION['login'] . ' sur la page d administration.</h2>';
		if (empty($_POST)) {
			echo "<nav id='42' class='d-flex'>";
			echo "<div class='col-6'>";
			FormulaireAjoutEtudiant();
			$eleve=listerEtudiants();
			afficheTableau($eleve);
			echo "</div>";
			echo "</nav>";

		}
		#test si l'étudiant est reneigné
		if (isset($_POST['nom']) && isset($_POST['groupe_etu'])) {
			#test si l'ajout fonctionne
			if (ajouterEtudiant($_POST['nom'], $_POST['groupe_etu']) == 1) {
				echo 'Insertion reussit de ' . $_POST['nom'] . '';
				redirect("insertion.php",0);
				echo '<br>';
			} else {
				echo '<p> erreur de l insertion de ' . $_POST['nom'] . '';
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