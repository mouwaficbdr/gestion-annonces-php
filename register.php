<?php
session_start();
include("includes/db.php");

// Récupération des messages de la session
$message = '';
$type_message = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $type_message = $_SESSION['type_message'];
    // Nettoyage des messages de la session
    unset($_SESSION['message']);
    unset($_SESSION['type_message']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion Annonces</title>
    <!-- Bootstrap 5 -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="assets/icons/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4f8cff 0%, #3358d1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            background: #fff;
            border-radius: 1.2rem;
            box-shadow: 0 4px 32px rgba(80,120,200,0.13);
            padding: 2.5rem 2rem;
            max-width: 600px;
            width: 100%;
        }
        .register-card .form-control:focus {
            border-color: #4f8cff;
            box-shadow: 0 0 0 0.2rem rgba(79,140,255,.15);
        }
        .register-card .logo {
            width: 60px;
            height: 60px;
            background: #eaf1ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            font-size: 2rem;
            color: #4f8cff;
        }
        .register-card .btn-primary {
            background: linear-gradient(90deg, #4f8cff 0%, #3358d1 100%);
            border: none;
        }
        .register-card .btn-primary:hover {
            background: linear-gradient(90deg, #3358d1 0%, #4f8cff 100%);
        }
        @media (max-width: 575.98px) {
            .register-card {
                padding: 2rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="register-card shadow">
                    <div class="logo mb-3">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h2 class="text-center mb-4">Créer un compte</h2>
                    
                    <?php if (!empty($message)) : ?>
                        <div class="alert alert-<?php echo $type_message; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="register_action.php" class="needs-validation" novalidate autocomplete="off">
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Prénom</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Votre prénom" required autocomplete="off">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Nom</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Votre nom" required autocomplete="off">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Votre e-mail" required autocomplete="off">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirmez le mot de passe" required autocomplete="new-password">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">S'inscrire</button>
                    </form>
                    <div class="text-center mt-3">
                        <span class="text-muted small">Déjà un compte ?</span>
                        <a href="login.php" class="small text-primary">Se connecter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
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

                // Validation du prénom (pas de chiffres)
                var prenom = document.getElementById('firstname')
                if (/\d/.test(prenom.value)) {
                    prenom.setCustomValidity('Le prénom ne doit pas contenir de chiffres')
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    prenom.setCustomValidity('')
                }

                // Validation du nom (pas de chiffres)
                var nom = document.getElementById('lastname')
                if (/\d/.test(nom.value)) {
                    nom.setCustomValidity('Le nom ne doit pas contenir de chiffres')
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    nom.setCustomValidity('')
                }

                // Validation de l'email avec regex
                var email = document.getElementById('email')
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                    email.setCustomValidity('Veuillez entrer une adresse email valide')
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    email.setCustomValidity('')
                }

                // Validation du mot de passe (8 caractères minimum)
                var password = document.getElementById('password')
                if (password.value.length < 8) {
                    password.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères')
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    password.setCustomValidity('')
                }

                // Vérification de la correspondance des mots de passe
                var confirm = document.getElementById('password_confirm')
                if (password.value !== confirm.value) {
                    confirm.setCustomValidity('Les mots de passe ne correspondent pas')
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    confirm.setCustomValidity('')
                }
                
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
</body>
</html>
