<?php
include_once "fonctions.php";
	//******************************************************************************
	if (!function_exists('FormulaireAuthentification')) {
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
	}
	//******************************************************************************
	if (!function_exists('Menu')) {
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
if (isset($_SESSION['admin']) && $_SESSION['admin'] == true) {
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
	}

	if (!function_exists('FormulaireAjoutEtudiant')) {
function FormulaireAjoutEtudiant(){
    // On appelle l'API pour avoir la liste des groupes dans le menu déroulant
    $groupes = listerGroupes(); 
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return verifFormetu()">
    <fieldset> 
        <label for="id_nom"> Nom : </label><input type="text" name="nom" id="id_nom" size="20" /><br />
        <label for="id_groupe">Groupes :</label> 
        <select id="id_groupe" name="groupe_etu" size="1">
            <?php
                if ($groupes && is_array($groupes)) {
                    foreach($groupes as $groupe){
                        echo '<option value="'.$groupe['NoGroupe'].'">'.$groupe['NomGroupe'].' </option>';
                    }
                }
            ?>
        </select>
        <input type="submit" value="Insérer"/>
    </fieldset>
</form>
<?php
    echo "<br/>";
}
}
	if (!function_exists('FormulaireAjoutgroupe')) {
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
	}
	if (!function_exists('FormulaireChoixEtudiant')) {
function FormulaireChoixEtudiant($choix){
    // On appelle la fonction de fonctions.php qui utilise callAPI
    $etudiants = listerEtudiants();
    $captchaErr = "";
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <fieldset> 
        <select id="id_nom" name="nom_etu" size="1">
            <?php
            if ($etudiants && is_array($etudiants)) {
                foreach($etudiants as $etu){
                    echo '<option value="'.$etu['Nom'].'">'.$etu['Nom'].'</option>';
                }
            }
        ?>
    </select>
    <label for="captcha">Captcha :</label> <input type="text" name="captcha" id='captcha'>
    <img src="image.php" onclick="this.src='image.php?' + Math.random();" alt="captcha" style="cursor:pointer;">
    <input type="submit" name="submit" value="Submit">
    </fieldset>
</form>
<?php
}
}
if (!function_exists('FormulaireModificationEtudiant')) {
    function FormulaireModificationEtudiant($nom) {
        $etud = callAPI('GET', '/etudiants/' . urlencode($nom));

        if ($etud && !isset($etud['detail'])) {
?>
            <form id="modificationForm" action="modification.php" method="post">
                <fieldset>
                    <input type="hidden" name="captcha" value="<?php echo $_SESSION['code']; ?>" />
                    
                    <input type="hidden" name="nom_actuel" value="<?php echo $nom; ?>" />
                    
                    <label for="id_nom">Nouveau nom :</label>
                    <input type="text" name="nouveau_nom" id="id_nom" value="<?php echo $etud['Nom']; ?>" required /><br />
                    
                    <label for="id_groupe">Nouveau groupe :</label>
                    <input type="number" name="no_groupe" id="id_groupe" value="<?php echo $etud['NoGroupe']; ?>" required /><br />
                    
                    <input type="submit" value="Modifier l'étudiant" />
                </fieldset>
            </form>
<?php
        } else {
            echo "Étudiant non trouvé via l'API.";
        }
    }
}
if (!function_exists('formchgrp')) {
function formchgrp() {
        // On récupère les données via l'API
        $groupes = listerGroupes(); 

        ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onchange="EnvoiRequete(event, this)">
            <fieldset> 
                <label for="id_grp">Groupes :</label> 
                <select id="id_grp" name="grp">
                    <?php
                    if ($groupes && is_array($groupes)) {
                        foreach($groupes as $value) {
                            // Vérifiez bien si votre API renvoie 'NoGroupe' ou 'no_groupe'
                            echo '<option value="'.$value['NoGroupe'].'">'.$value['NomGroupe'].'</option>';
                        }
                    } else {
                        echo '<option>Erreur : API injoignable ou vide</option>';
                    }
                    ?>
                </select>
            </fieldset>
        </form>
        <?php
    }
}
?>
