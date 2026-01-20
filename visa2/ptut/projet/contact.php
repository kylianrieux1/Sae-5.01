<?php 
session_start();

// Fonction pour enregistrer les tentatives dans les fichiers de log
function log_attempt($filename, $status) {
    $file = fopen($filename, 'a+'); 
    $message = date('Y-m-d H:i:s') . " - CAPTCHA: " . $_POST['captcha'] . " - IP: " . $_SERVER['REMOTE_ADDR'] . " - Status: " . $status . "\n";
    fputs($file, $message);
    fclose($file);
}

function formater_saisie($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$nomErr = $emailErr = $genreErr = $sitewebErr = $commentErr = $captchaErr = "";
$nom = $email = $genre = $comment = $siteweb = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

    // Validation du captcha
    if (empty($_POST['captcha']) || $_POST['captcha'] != $_SESSION['code']) {
        $captchaErr = "Captcha incorrect";
        $valid = false;
        log_attempt('access_failure.log', 'Failure');
    } else {
        log_attempt('access_success.log', 'Success');
    }

    // Validation des autres champs avec htmlentities
    $nom = formater_saisie($_POST["nom"]);
    $email = formater_saisie($_POST["email"]);
    $genre = formater_saisie($_POST["genre"]);
    $comment = formater_saisie($_POST["comment"]);
    $siteweb = formater_saisie($_POST["siteweb"]);

    if (empty($nom)) {
        $nomErr = "Il faut saisir un nom";
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $nom)) {
        $nomErr = "Seules les lettres et les espaces sont autorisées.";
        $valid = false;
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "L'email n'est pas conforme";
        $valid = false;
    }

    if (empty($siteweb) || !filter_var($siteweb, FILTER_VALIDATE_URL)) {
        $sitewebErr = "L'URL n'est pas conforme";
        $valid = false;
    }

    if (empty($comment)) {
        $commentErr = "Le commentaire ne peut pas être vide";
        $valid = false;
    }

    if (empty($genre)) {
        $genreErr = "Le choix du genre est obligatoire";
        $valid = false;
    } elseif (!in_array($genre, ["femme", "homme"])) {
        $genreErr = "Choix de genre non valide";
        $valid = false;
    }

    if ($valid) {
        $_SESSION['nom'] = $nom;
        $_SESSION['email'] = $email;
        $_SESSION['siteweb'] = $siteweb;
        $_SESSION['comment'] = $comment;
        $_SESSION['genre'] = $genre;
        header("Location: test.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Formulaire de contact avec CAPTCHA</title>
    <style>.error {color: #FF0000;}</style>
</head>
<body>
    <h1>Formulaire de contact avec CAPTCHA</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        Nom: <input type="text" name="nom" value="<?php echo $nom; ?>">
        <span class="error">* <?php echo $nomErr;?></span><br><br>
        E-mail: <input type="email" name="email" value="<?php echo $email; ?>">
        <span class="error">* <?php echo $emailErr;?></span><br><br>
        Site Web: <input type="text" name="siteweb" value="<?php echo $siteweb; ?>">
        <span class="error"><?php echo $sitewebErr;?></span><br><br>
        Commentaire: <textarea name="comment" rows="5" cols="40"><?php echo $comment; ?></textarea>
        <span class="error"><?php echo $commentErr;?></span><br><br>
        Genre: <input type="radio" name="genre" value="femme" <?php if ($genre == "femme") echo "checked"; ?>> Féminin
               <input type="radio" name="genre" value="homme" <?php if ($genre == "homme") echo "checked"; ?>> Masculin
        <span class="error">* <?php echo $genreErr;?></span><br><br>
        <img src="image.php" onclick="this.src='image.php?' + Math.random();" alt="CAPTCHA" style="cursor:pointer;">
        <br>
        Captcha: <input type="text" name="captcha">
        <span class="error">* <?php echo $captchaErr;?></span><br><br>
        <input type="submit" name="submit" value="Submit">
    </form>
</body>
</html>
