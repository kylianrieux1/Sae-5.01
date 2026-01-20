<?php
	session_start(); // cette page utilise les sessions
	include 'fonctions.php';
	include 'formulaires.php';
	?>
<!DOCTYPE html>
<html lang="fr" >
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="style.css" type="text/css" />
		<link rel="stylesheet" href="./bootstrap-5.3.2-dist/css/bootstrap.min.css">
		<script src="Ajax.js"></script>
		<title>Sae24</title>
	</head>
	<body>
		<header>
			<h1>Sae24</h1>
		</header>
		<nav class="d-flex">
		<?php
		Menu();
		?>
		</nav>
			
		<article>
			
			<?php
				// Affichage du message accueil en fonction de la connexion
				if (!empty($_SESSION)){
					echo '<h2>Bienvenue ' . $_SESSION['login'] . ' sur la page d accueil.</h2>';	
				}
				else{ echo '<p>Vous êtes déconnectés</p>';	
					redirect("connexion.php",0);
				}
				// traitement de la zone centrale de la page en fonction des liens GET du menu s'il y a une session
				echo"<nav class='d-flex'>";
				echo "<div class='col-6' id='nav'>";
				echo '<h2>Liste des etudiants</h2>';
				$eleve=listerEtudiants();
				afficheTableau($eleve);
				echo "</div>";
				echo "<div class='col-6'>";
				echo '<h2>Liste des etudiants Ajax</h2>';
				formchgrp();
				echo "<div id='taba'></div>";
				echo "</div>";
				echo "</nav>";
			?>
		</article>
		<footer>
			<p>Pied de la page <?php echo $_SERVER['PHP_SELF']; ?></p>
			<a href="javascript:history.back()">Retour à la page précédente</a>
		</footer>
	</body>
</html>