<?php
include_once("fonctions.php");
	//******************************************************************************
	
	function FormulaireAuthentification(){
	?>
	<form id="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<fieldset>
			<legend>Formulaire d'authentification</legend>	
			<label for="id_mail">Adresse Mail : </label><input type="email" name="mail" id="id_mail" placeholder="@mail" required size="20" /><br />
			<label for="id_pass">Mot de passe : </label><input type="password" name="pass" id="id_pass" required size="10" /><br />
			<input type="submit" name="connect" value="Connexion" />
		</fieldset>
	</form>
	<?php
	}
	
	//******************************************************************************
	
	function Menu(){		
	?>
	<div id="men" class="col-12">
	<nav class="navbar navbar-expand-md navbar-dark bg-dark d-flex flex-wrap" aria-label="Fourth navbar example" id="menu">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample04">
        <ul class="navbar-nav me-auto mb-2 mb-md-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="index.php">Index</a>
          </li>
		  <li><a class="nav-link" aria-current="page" href="connexion.php?action=logout">Se Deconnecter</a>
		  </li>
<?php
if ($_SESSION['admin']){
?>
          <li class="nav-item">
            <a class="nav-link" href="insertion.php">Insertion</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="modification.php">Modification</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="supression.php">Suppression</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  </div>
  <?php
}
	}
	

	
	function FormulaireAjoutEtudiant(){
		$bdd = new PDO('sqlite:bdd/etudiants_grp.db');
		$req = "SELECT DISTINCT NoGroupe, NomGroupe, TailleGroupe FROM Groupes";
		$res = $bdd -> query($req);
		$groupes = $res -> fetchAll(PDO::FETCH_ASSOC);
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return verifFormetu()">
		<fieldset> 
			<label for="id_nom"> Nom : </label><input type="text" name="nom" id="id_nom" size="20" /><br />
			<label for="id_groupe">Groupes :</label> 
			<select id="id_groupe" name="groupe_etu" size="1">
				<?php // on se sert de value directement pour l'insertion
					foreach($groupes as $groupe){
						echo '<option value="'.$groupe['NoGroupe'].'">'.$groupe['NomGroupe'].' </option>';
					}
				?>
			</select>
			<input type="submit" value="Insérer"/>
		</fieldset>
	</form>
	<?php
		echo "<br/>";
	}


	function FormulaireAjoutgroupe(){
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return verifFormgrp()">
		<fieldset> 
			<label for="id_nomg"> Nom du groupe : </label><input type="nom" name="nom" id="id_nomg" placeholder="nom" required size="20" /><br />
			<label for="id_no">Numero du groupe :</label><input type="number" name="no" id="id_no" placeholder="no" /><br />
			<label for="id_groupet">TailleGroupes :</label><input type="number" name="taille" id="id_groupet" placeholder="taille" required size="20" /><br />
			<input type="submit" value="Insérer"/>
		</fieldset>
	</form>
	<?php
		echo "<br/>";
	}

	function FormulaireChoixEtudiant($choix){
		$bdd = new PDO('sqlite:bdd/etudiants_grp.db');
		$sql = "SELECT * FROM Etudiants;";
		$res = $bdd -> query($sql);
		$etudiants = $res -> fetchAll(PDO::FETCH_ASSOC);
		$captchaErr = "";
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<fieldset> 
			<select id="id_nom" name="nom_etu" size="1">
				<?php
				foreach($etudiants as $nom){
					echo '<option value="'.$nom['Nom'].'">'.$nom['Nom'].'</option>';
				}
			?>
		</select>
        <label for="captcha">Captcha :</label> <input type="text" name="captcha" id='captcha'>
        <span class="error"> <?php echo $captchaErr;?></span><br>
		<img src="image.php" onclick="this.src='image.php?' + Math.random();" alt="captcha" style="cursor:pointer;">
        <input type="submit" name="submit" value="Submit">
		</fieldset>
	</form>
	<?php
	}
	function FormulaireModificationEtudiant($nom) {
		$bdd = new PDO('sqlite:bdd/etudiants_grp.db');
		$requete = "SELECT * FROM Etudiants WHERE Nom = :nom";
		$stmt = $bdd->prepare($requete);
		$stmt->bindParam(':nom', $nom);
		$stmt->execute();
		$etud = $stmt->fetch(PDO::FETCH_ASSOC);
	
		if ($etud) {
	?>
			<form id="modificationForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<fieldset>
					<input type="hidden" name="original_nom" value="<?php echo $nom; ?>" />
					<label for="id_noETU">NoEtu :</label>
					<input type="number" name="noETU" id="id_noETU" value="<?php echo $etud['NoEtu']; ?>" required size="20" /><br />
					<label for="id_nom">Nom :</label>
					<input type="text" name="nom" id="id_nom" value="<?php echo $etud['Nom']; ?>" required size="20" /><br />
					<label for="id_groupe">NoGroupe :</label>
					<input type="number" name="NoGroupe" id="id_groupe" value="<?php echo $etud['NoGroupe']; ?>" required size="20" /><br />
					<input type="submit" value="Modifier" />
				</fieldset>
			</form>
	<?php
		} else {
			echo "Étudiant non trouvé.";
		}
	}
	// fin 
	function formchgrp(){
		$bdd = new PDO('sqlite:bdd/etudiants_grp.db');
		$req = "SELECT DISTINCT NoGroupe, NomGroupe FROM Groupes;";
		$res = $bdd -> query($req);
		$groupes = $res -> fetchAll(PDO::FETCH_ASSOC);
		?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onchange="EnvoiRequete(event, this)">
		<fieldset> 
				<label for="id_grp">Groupes :</label> 
				<select id="id_grp" name="grp">
				<?php
					foreach($groupes as $key_groupe => $value){
						echo '<option value="'.$value['NoGroupe'].'">'.$value['NomGroupe'].' '.$value['NoGroupe'].'</option>';
					}
				?>
			</select>
		</fieldset>
	</form>
	<?php
	}
?>