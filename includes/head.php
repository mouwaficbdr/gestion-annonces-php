<?php
include("includes/db.php");
include("includes/session.php");
rediriger_si_non_connecte();
$utilisateur = obtenir_utilisateur_connecte();

// Obtenir le titre de la page
function obtenir_titre_page() {
    $currentPage = basename($_SERVER['PHP_SELF']);
    switch ($currentPage) {
        case 'index.php':
            return 'Dashboard';
        case 'utilisateurs.php':
            return 'Gestion des Utilisateurs';
        case 'ajouter_utilisateur.php':
            return 'Nouvel Utilisateur';
        case 'modifier_utilisateur.php':
            return 'Modifier l\'Utilisateur';
        case 'supprimer_utilisateur.php':
            return 'Suppression d\'Utilisateur';
        case 'annonces.php':
            return 'Gestion des Annonces';
        case 'ajouter_annonce.php':
            return 'Nouvelle Annonce';
        case 'modifier_annonce.php':
            return 'Modifier l\'Annonce';
        case 'supprimer_annonce.php':
            return 'Suppression d\'Annonce';
        default:
            return 'Dashboard';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo obtenir_titre_page(); ?> - Gestion Annonces</title>
    <!-- Bootstrap 5 -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="assets/icons/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column position-fixed" id="sidebar">
        <div class="sidebar-header d-flex align-items-center">
            <i class="bi bi-lightning-charge-fill"></i>
            <span>Gestion Annonces</span>
            <button class="sidebar-toggler d-none d-lg-inline" id="sidebarCollapse" title="Réduire le menu">
                <i class="bi bi-chevron-double-left"></i>
            </button>
        </div>

        <ul class="nav flex-column px-2 mt-3">
            <?php
            $currentPage = basename($_SERVER['PHP_SELF']);
            ?>
            <li class="nav-item">
                <a href="index.php" class="nav-link <?php echo ($currentPage === 'index.php') ? 'active' : ''; ?>">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="utilisateurs.php" class="nav-link <?php echo ($currentPage === 'utilisateurs.php') ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i>
                    <span>Utilisateurs</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="annonces.php" class="nav-link <?php echo ($currentPage === 'annonces.php') ? 'active' : ''; ?>">
                    <i class="bi bi-megaphone"></i>
                    <span>Annonces</span>
                </a>
            </li>
        </ul>

    </nav>
    <div class="overlay" id="sidebarOverlay"></div>
    <!-- Main content -->
    <div class="main-content min-vh-100">
        <!-- Header -->
        <header class="dashboard-header">
            <button class="btn-burger d-lg-none" id="sidebarMobileBtn" title="Menu">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="h5 mb-0"><?php echo obtenir_titre_page(); ?></h1>
            <div class="user-info">
                <img src="assets/images/avatar.jpeg" alt="Avatar">
                <span><?php echo htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']); ?></span>
                <a href="deconnexion.php" class="btn btn-outline-primary btn-sm">Déconnexion</a>
            </div>
        </header>
        <main class="container py-4" id="dashboard-content">

            <div class="row g-4">