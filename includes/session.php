<?php
session_start();

function est_connecte() {
    return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
}

function rediriger_si_non_connecte() {
    if (!est_connecte()) {
        header("Location: login.php");
        exit;
    }
}

function obtenir_utilisateur_connecte() {
    if (est_connecte()) {
        return [
            'id' => $_SESSION['utilisateur_id'],
            'prenom' => $_SESSION['utilisateur_prenom'],
            'nom' => $_SESSION['utilisateur_nom']
        ];
    }
    return null;
} 