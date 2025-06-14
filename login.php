<?php
include("includes/db.php");

$message = '';
$type_message = '';

// Récupérer le message de la session s'il existe
session_start();
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $type_message = $_SESSION['type_message'];
    // Supprimer le message de la session
    unset($_SESSION['message']);
    unset($_SESSION['type_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['password'];

    if (empty($email) || empty($mot_de_passe)) {
        $message = "Veuillez remplir tous les champs.";
        $type_message = "danger";
    } else {
        try {
            $stmt = $bdd->prepare("SELECT id, prenom, nom, email, mot_de_passe FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                // Démarrer la session
                session_start();
                $_SESSION['auth'] = true;
                $_SESSION['utilisateur_id'] = $utilisateur['id'];
                $_SESSION['utilisateur_prenom'] = $utilisateur['prenom'];
                $_SESSION['utilisateur_nom'] = $utilisateur['nom'];
                $_SESSION['utilisateur_email'] = $utilisateur['email'];

                // Redirection vers le dashboard
                header("Location: index.php");
                exit;
            } else {
                $message = "Email ou mot de passe incorrect.";
                $type_message = "danger";
            }
        } catch(PDOException $e) {
            $message = "Erreur lors de la connexion : " . $e->getMessage();
            $type_message = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion Annonces</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/icons/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Connexion</h2>
                        
                        <?php if (!empty($message)) : ?>
                            <div class="alert alert-<?php echo $type_message; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
                        <?php endif; ?>

                        <form action="" method="POST" class="needs-validation" novalidate autocomplete="off">
            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required autocomplete="off">
                                <div class="invalid-feedback">Veuillez entrer une adresse email valide.</div>
                </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                                <div class="invalid-feedback">Veuillez entrer votre mot de passe.</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-in-right"></i> Se connecter
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p class="mb-0">Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.js"></script>
    <script>
    // Validation côté client
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
</body>
</html>
