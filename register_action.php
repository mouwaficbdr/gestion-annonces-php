<?php
session_start();
include("includes/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['firstname']);
    $nom = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['password'];
    $mot_de_passe_confirm = $_POST['password_confirm'];

    $message = '';
    $type_message = '';

    // Validation des données
    if (empty($prenom) || empty($nom) || empty($email) || empty($mot_de_passe)) {
        $message = "Tous les champs sont obligatoires.";
        $type_message = "danger";
    } elseif ($mot_de_passe !== $mot_de_passe_confirm) {
        $message = "Les mots de passe ne correspondent pas.";
        $type_message = "danger";
    } elseif (strlen($mot_de_passe) < 8) {
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
        $type_message = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'adresse email n'est pas valide.";
        $type_message = "danger";
    } else {
        try {
            // Vérifier si l'email existe déjà
            $stmt = $bdd->prepare("SELECT id FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $utilisateur_existant = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($utilisateur_existant) {
                $message = "Cette adresse email est déjà utilisée.";
                $type_message = "danger";
            } else {
                // Hashage du mot de passe
                $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

                // Insertion dans la base de données
                $stmt = $bdd->prepare("INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
                $stmt->execute([$prenom, $nom, $email, $mot_de_passe_hash]);

                // Démarrer la session pour stocker le message
                $_SESSION['message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                $_SESSION['type_message'] = "success";
                
                // Redirection immédiate vers la page de connexion
                header("Location: login.php");
                exit;
            }
        } catch(PDOException $e) {
            $message = "Erreur lors de l'inscription : " . $e->getMessage();
            $type_message = "danger";
        }
    }

    // Si on arrive ici, c'est qu'il y a eu une erreur
    $_SESSION['message'] = $message;
    $_SESSION['type_message'] = $type_message;
    header("Location: register.php");
    exit;
} else {
    header("Location: register.php");
    exit;
} 