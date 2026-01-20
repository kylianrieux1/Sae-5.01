<?php

#$pdo_db = new PDO(
 #   'mysql:host=db;port=3306;dbname=ma_bdd',
 #   'api_user',
  #  'api_password'
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('API_BASE_URL')) {
    define('API_BASE_URL', 'http://api:8000');
}

// --- FONCTION DE COMMUNICATION UNIVERSELLE ---
if (!function_exists('callAPI')) {
function callAPI($method, $endpoint, $data = false) {
    $url = API_BASE_URL . $endpoint;
    $headers = "Content-type: application/json\r\n";
    
    if (isset($_SESSION['token'])) {
        $headers .= "Authorization: Bearer " . $_SESSION['token'] . "\r\n";
    }

    $options = [
        'http' => [
            'header'  => $headers,
            'method'  => $method,
            'content' => $data ? json_encode($data) : null,
            'ignore_errors' => true // Permet de voir les erreurs 401/404
        ]
    ];
    
    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    // Analyse des headers pour détecter une expiration (Point 2)
    if (isset($http_response_header)) {
        if (strpos($http_response_header[0], '401') !== false) {
            session_destroy(); // Le jeton n'est plus valide
            return ['error' => 'session_expired'];
        }
    }

    return $result ? json_decode($result, true) : false;
}
}

if (!function_exists('listerEtudiantsParGroupe')) {
    function listerEtudiantsParGroupe($grp) {
        return callAPI('GET', '/etudiants/groupe/' . urlencode($grp));
    }
}
// --- AUTHENTIFICATION VIA API ---
if (!function_exists('authentification')) {
function authentification($mail, $pass) {
    $postData = http_build_query(['username' => $mail, 'password' => $pass]);
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $postData,
            'ignore_errors' => true
        ]
    ];
    $context  = stream_context_create($options);
    $result = @file_get_contents(API_BASE_URL . '/token', false, $context);

    if ($result) {
        $data = json_decode($result, true);
        if (isset($data['access_token'])) {
            $_SESSION['token'] = $data['access_token'];
            $_SESSION['login'] = $mail;
            // On vérifie si le statut contient 'admin' (Point 2)
            $_SESSION['admin'] = (strtolower($data['statut']) === 'admin'); 
            return true;
        }
    }
    return false;
}
}
// --- EXEMPLE : LISTER LES ETUDIANTS VIA API ---
if (!function_exists('listerEtudiants')) {
function listerEtudiants() {
    return callAPI('GET', '/etudiants');
}
}
if (!function_exists('ajouterEtudiant')) {
function ajouterEtudiant($nom, $grp) {
    // 1. On prépare les données pour l'API (format JSON)
    $donnees = [
        'nom' => $nom,
        'no_groupe' => $grp
    ];

    // 2. On appelle l'API sur l'endpoint dédié
    // On suppose que votre FastAPI a une route POST /etudiants/
    $reponse = callAPI('POST', '/etudiants/', $donnees);

    // 3. Si l'API renvoie quelque chose, c'est que l'étudiant a été ajouté
    if ($reponse && !isset($reponse['error'])) {
        return 1; // Succès
    }

    return 0; // Échec
}
}
// MODIFIER un étudiant
if (!function_exists('modifierEtudiant')) {
function modifierEtudiant($nom_actuel, $nouveau_nom, $nouveau_grp) {
    $donnees = [
        'nom' => $nouveau_nom, // Doit correspondre à la classe Pydantic Etudiant
        'no_groupe' => (int)$nouveau_grp
    ];

    $endpoint = "/etudiants/" . urlencode($nom_actuel);
    $reponse = callAPI('PUT', $endpoint, $donnees);

    return ($reponse && !isset($reponse['detail'])) ? 1 : 0;
}
}
// SUPPRIMER un étudiant
// --- EXEMPLE : LISTER LES ETUDIANTS VIA API ---
if (!function_exists('supprimerEtudiant')) {
function supprimerEtudiant($nom) {
    $endpoint = "/etudiants/" . urlencode($nom);
    $reponse = callAPI('DELETE', $endpoint);

    return ($reponse && !isset($reponse['error'])) ? 1 : 0;
}
}
if (!function_exists('redirect')) {
function redirect($url, $tps) {
    $temps = $tps * 1000;
    echo "<script type=\"text/javascript\">
        function redirect() { window.location='$url'; }
        setTimeout('redirect()', $temps);
    </script>";
}
}
if (!function_exists('afficheTableau')) {
function afficheTableau($tab) {
    // Vérification : si c'est vide ou pas un tableau
    if (empty($tab) || !is_array($tab)) {
        echo "<p style='color:orange;'>Aucune donnée disponible ou format de réponse invalide.</p>";
        return;
    }

    // Si l'API renvoie un dictionnaire d'erreur au lieu d'une liste
    if (isset($tab['detail'])) {
        echo "<p style='color:red;'>Erreur API : " . htmlspecialchars($tab['detail']) . "</p>";
        return;
    }

    echo '<table border="1" style="border-collapse: collapse; width: 100%;">';
    
    // Entêtes : on vérifie que le premier élément est bien un tableau
    if (isset($tab[0]) && is_array($tab[0])) {
        echo '<tr>';
        foreach($tab[0] as $colonne => $valeur) {
            echo "<th>" . htmlspecialchars($colonne) . "</th>";
        }
        echo '</tr>';

        // Données
        foreach($tab as $ligne) {
            echo '<tr>';
            foreach($ligne as $cellule) {
                echo "<td>" . htmlspecialchars($cellule) . "</td>";
            }
            echo '</tr>';
        }
    }
    echo '</table>';
}
	}
if (!function_exists('listerGroupes')) {
function listerGroupes() {
    return callAPI('GET', '/groupes'); 
}
}
