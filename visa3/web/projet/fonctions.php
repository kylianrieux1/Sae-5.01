<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('API_BASE_URL')) {
    define('API_BASE_URL', 'http://api:8000');
}

// --- FONCTION DE COMMUNICATION SIMPLIFIÉE (SANS JWT) ---
if (!function_exists('callAPI')) {
function callAPI($method, $endpoint, $data = false) {
    $url = API_BASE_URL . $endpoint;
    
    // On ne garde que le Content-type JSON
    $headers = "Content-type: application/json\r\n";

    $options = [
        'http' => [
            'header'  => $headers,
            'method'  => $method,
            'content' => $data ? json_encode($data) : null,
            'ignore_errors' => true
        ]
    ];
    
    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    return $result ? json_decode($result, true) : false;
}
}

// --- LISTER LES GROUPES ---
if (!function_exists('listerGroupes')) {
function listerGroupes() {
    return callAPI('GET', '/groupes'); 
}
}

// --- LISTER LES ETUDIANTS ---
if (!function_exists('listerEtudiants')) {
function listerEtudiants() {
    return callAPI('GET', '/etudiants');
}
}

if (!function_exists('listerEtudiantsParGroupe')) {
    function listerEtudiantsParGroupe($grp) {
        return callAPI('GET', '/etudiants/groupe/' . urlencode($grp));
    }
}

// --- AJOUTER UN ETUDIANT ---
if (!function_exists('ajouterEtudiant')) {
function ajouterEtudiant($nom, $grp) {
    $donnees = [
        'nom' => $nom,
        'no_groupe' => (int)$grp // Cast en int pour correspondre au Pydantic
    ];

    $reponse = callAPI('POST', '/etudiants', $donnees);

    if ($reponse && !isset($reponse['detail'])) {
        return 1;
    }
    return 0;
}
}

// --- MODIFIER UN ETUDIANT ---
if (!function_exists('modifierEtudiant')) {
function modifierEtudiant($nom_actuel, $nouveau_nom, $nouveau_grp) {
    $donnees = [
        'nom' => $nouveau_nom,
        'no_groupe' => (int)$nouveau_grp
    ];

    $endpoint = "/etudiants/" . urlencode($nom_actuel);
    $reponse = callAPI('PUT', $endpoint, $donnees);

    return ($reponse && !isset($reponse['detail'])) ? 1 : 0;
}
}

// --- SUPPRIMER UN ETUDIANT ---
if (!function_exists('supprimerEtudiant')) {
function supprimerEtudiant($nom) {
    $endpoint = "/etudiants/" . urlencode($nom);
    $reponse = callAPI('DELETE', $endpoint);

    return ($reponse && !isset($reponse['detail'])) ? 1 : 0;
}
}

// --- UTILITAIRES D'AFFICHAGE ---
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
    if (empty($tab) || !is_array($tab)) {
        echo "<p style='color:orange;'>Aucune donnée disponible.</p>";
        return;
    }

    if (isset($tab['detail'])) {
        echo "<p style='color:red;'>Erreur : " . htmlspecialchars($tab['detail']) . "</p>";
        return;
    }

    echo '<table class="table table-striped table-bordered">';
    if (isset($tab[0]) && is_array($tab[0])) {
        echo '<thead><tr>';
        foreach($tab[0] as $colonne => $valeur) {
            echo "<th>" . htmlspecialchars($colonne) . "</th>";
        }
        echo '</tr></thead><tbody>';

        foreach($tab as $ligne) {
            echo '<tr>';
            foreach($ligne as $cellule) {
                echo "<td>" . htmlspecialchars($cellule) . "</td>";
            }
            echo '</tr>';
        }
        echo '</tbody>';
    }
    echo '</table>';
}
}
function authentification($mail, $pass) {
    $donnees = ['mail' => $mail, 'pass' => $pass];
    $reponse = callAPI('POST', '/auth', $donnees);

    if ($reponse && isset($reponse['success']) && $reponse['success'] === true) {
        $_SESSION['login'] = $reponse['login'];
        $_SESSION['admin'] = ($reponse['statut'] === 'admin');
        return true;
    }
    return false;
}
