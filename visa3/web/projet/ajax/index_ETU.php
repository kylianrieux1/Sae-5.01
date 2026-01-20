<?php
include 'fonctions_ETU.php';
include 'formulaires_ETU.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <script src="ajax/Ajax_ETU.js" type="text/javascript"></script>
    <link href="style.css" rel="stylesheet" type="text/css" />
    <title>Module WEB2 TD8: Ajax</title>
</head>

<body>
    <header>
        <h1>Module WEB2 TD8: Ajax</h1>
        <h1>Lister les utilisateurs par ville en AJAX</h1>
    </header>

    <div id="zoneAJAX"></div>
    <?php afficheFormulaireEtudiantParVilleAJAX(); ?>


</body>

</html>