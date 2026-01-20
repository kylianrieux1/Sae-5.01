<?php
include_once "fonctions.php";

// ******************************************************************************
// FORMULAIRE D'AUTHENTIFICATION
// ******************************************************************************
if (!function_exists('FormulaireAuthentification')) {
    function FormulaireAuthentification(){
    ?>
    <form id="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <fieldset>
            <legend>Connexion</legend>	
            <label for="id_mail">Adresse Mail : </label>
            <input type="email" name="mail" id="id_mail" placeholder="@mail" required size="20" /><br />
            <label for="id_pass">Mot de passe : </label>
            <input type="password" name="pass" id="id_pass" required size="10" /><br />
            <input type="submit" name="connect" value="Connexion" class="btn btn-primary mt-2" />
        </fieldset>
    </form>
    <?php
    }
}

// ******************************************************************************
// MENU DE NAVIGATION
// ******************************************************************************
if (!function_exists('Menu')) {
    function Menu(){		
    ?>
    <div id="men" class="col-12">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark d-flex flex-wrap" id="menu">
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="navbarsExample04">
                    <ul class="navbar-nav me-auto mb-2 mb-md-0">
                        <li class="nav-item"><a class="nav-link" href="index.php">Index</a></li>
                        <li class="nav-item"><a class="nav-link" href="connexion.php?action=logout text-danger">Se Déconnecter</a></li>
                        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == true): ?>
                            <li class="nav-item"><a class="nav-link" href="insertion.php">Insertion</a></li>
                            <li class="nav-item"><a class="nav-link" href="modification.php">Modification</a></li>
                            <li class="nav-item"><a class="nav-link" href="supression.php">Suppression</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <?php
    }
}

// ******************************************************************************
// FORMULAIRE AJOUT ÉTUDIANT
// ******************************************************************************
if (!function_exists('FormulaireAjoutEtudiant')) {
    function FormulaireAjoutEtudiant(){
        $groupes = listerGroupes(); 
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return verifFormetu()">
        <fieldset> 
            <label for="id_nom"> Nom : </label><input type="text" name="nom" id="id_nom" size="20" required /><br />
            <label for="id_groupe">Groupes :</label> 
            <select id="id_groupe" name="no_groupe">
                <?php
                if ($groupes && is_array($groupes)) {
                    foreach($groupes as $groupe){
                        echo '<option value="'.$groupe['NoGroupe'].'">'.$groupe['NomGroupe'].'</option>';
                    }
                }
                ?>
            </select>
            <input type="submit" value="Insérer" class="btn btn-success btn-sm"/>
        </fieldset>
    </form>
    <?php
    }
}

// ******************************************************************************
// FORMULAIRE DE CHOIX ÉTUDIANT (AVEC CAPTCHA)
// ******************************************************************************
if (!function_exists('FormulaireChoixEtudiant')) {
    function FormulaireChoixEtudiant($choix){
        $etudiants = listerEtudiants();
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <fieldset> 
            <label for="id_nom_choix">Choisir un étudiant :</label>
            <select id="id_nom_choix" name="nom_etu">
                <?php
                if ($etudiants && is_array($etudiants)) {
                    foreach($etudiants as $etu){
                        echo '<option value="'.htmlspecialchars($etu['Nom']).'">'.htmlspecialchars($etu['Nom']).'</option>';
                    }
                }
                ?>
            </select><br />
            <label for="captcha">Captcha :</label> 
            <input type="text" name="captcha" id='captcha' required>
            <img src="image.php" onclick="this.src='image.php?' + Math.random();" alt="captcha" style="cursor:pointer; vertical-align: middle;">
            <br />
            <input type="submit" name="submit" value="Valider">
        </fieldset>
    </form>
    <?php
    }
}

// ******************************************************************************
// FORMULAIRE DE MODIFICATION (Étape finale)
// ******************************************************************************
if (!function_exists('FormulaireModificationEtudiant')) {
    function FormulaireModificationEtudiant($nom) {
        $etud = callAPI('GET', '/etudiants/' . urlencode($nom));

        if ($etud && !isset($etud['detail'])) {
        ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div style="margin-top: 20px;">
                    <input type="hidden" name="nom_actuel" value="<?php echo htmlspecialchars($nom); ?>" />
                    <input type="hidden" name="captcha" value="<?php echo $_SESSION['code']; ?>" />

                    <label for="id_new_nom">Nom : </label>
                    <input type="text" name="nouveau_nom" id="id_new_nom" value="<?php echo htmlspecialchars($etud['Nom']); ?>" required />
                    <br />
                    
                    <label for="id_new_groupe">NoGroupe : </label>
                    <input type="number" name="no_groupe" id="id_new_groupe" value="<?php echo $etud['NoGroupe']; ?>" required />
                    <br />
                    
                    <input type="submit" value="Modifier" style="margin-top: 10px;" />
                </div>
            </form>
        <?php
        } else {
            echo "<p>Étudiant non trouvé.</p>";
        }
    }
}

// ******************************************************************************
// FILTRE PAR GROUPE (AJAX)
// ******************************************************************************
if (!function_exists('formchgrp')) {
    function formchgrp() {
        $groupes = listerGroupes(); 
        ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onchange="EnvoiRequete(event, this)">
            <fieldset> 
                <label for="id_grp">Filtrer par Groupe :</label> 
                <select id="id_grp" name="grp">
                    <option value="">-- Choisir --</option>
                    <?php
                    if ($groupes && is_array($groupes)) {
                        foreach($groupes as $value) {
                            echo '<option value="'.$value['NoGroupe'].'">'.$value['NomGroupe'].'</option>';
                        }
                    }
                    ?>
                </select>
            </fieldset>
        </form>
        <?php
    }
}
?>
