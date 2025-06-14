<?php
include("includes/head.php");
include("includes/db.php");

$message = '';
$type_message = '';
$utilisateur = null;

// Récupération de l'utilisateur
if (isset($_GET['id'])) {
    try {
        $stmt = $bdd->prepare("SELECT id, prenom, nom, email FROM utilisateurs WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$utilisateur) {
            header("Location: utilisateurs.php");
            exit;
        }
    } catch(PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    header("Location: utilisateurs.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $mot_de_passe_confirm = $_POST['mot_de_passe_confirm'];

    // Validation des données
    if (empty($prenom) || empty($nom) || empty($email)) {
        $message = "Les champs prénom, nom et email sont obligatoires.";
        $type_message = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'adresse email n'est pas valide.";
        $type_message = "danger";
    } else {
        try {
            // Vérifier si l'email existe déjà pour un autre utilisateur
            $stmt = $bdd->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
            $stmt->execute([$email, $utilisateur['id']]);
            $utilisateur_existant = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($utilisateur_existant) {
                $message = "Cette adresse email est déjà utilisée.";
                $type_message = "danger";
            } else {
                // Si un nouveau mot de passe est fourni
                if (!empty($mot_de_passe)) {
                    if ($mot_de_passe !== $mot_de_passe_confirm) {
                        $message = "Les mots de passe ne correspondent pas.";
                        $type_message = "danger";
                    } elseif (strlen($mot_de_passe) < 8) {
                        $message = "Le mot de passe doit contenir au moins 8 caractères.";
                        $type_message = "danger";
                    } else {
                        // Mise à jour avec nouveau mot de passe
                        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                        $stmt = $bdd->prepare("UPDATE utilisateurs SET prenom = ?, nom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
                        $stmt->execute([$prenom, $nom, $email, $mot_de_passe_hash, $utilisateur['id']]);
                    }
                } else {
                    // Mise à jour sans changer le mot de passe
                    $stmt = $bdd->prepare("UPDATE utilisateurs SET prenom = ?, nom = ?, email = ? WHERE id = ?");
                    $stmt->execute([$prenom, $nom, $email, $utilisateur['id']]);
                }

                $message = "Utilisateur modifié avec succès !";
                $type_message = "success";
                // Mise à jour des données affichées
                $utilisateur['prenom'] = $prenom;
                $utilisateur['nom'] = $nom;
                $utilisateur['email'] = $email;
            }
        } catch(PDOException $e) {
            $message = "Erreur lors de la modification de l'utilisateur : " . $e->getMessage();
            $type_message = "danger";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Modifier l'Utilisateur</h2>
    <a href="utilisateurs.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<?php if (!empty($message)) : ?>
    <div class="alert alert-<?php echo $type_message; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="" method="POST" class="needs-validation" novalidate autocomplete="off">
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($utilisateur['prenom']); ?>" required autocomplete="off">
                <div class="invalid-feedback">Veuillez entrer un prénom.</div>
            </div>

            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($utilisateur['nom']); ?>" required autocomplete="off">
                <div class="invalid-feedback">Veuillez entrer un nom.</div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($utilisateur['email']); ?>" required autocomplete="off">
                <div class="invalid-feedback">Veuillez entrer une adresse email valide.</div>
            </div>

            <div class="mb-3">
                <label for="mot_de_passe" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" minlength="8" autocomplete="new-password">
                <div class="invalid-feedback">Le mot de passe doit contenir au moins 8 caractères.</div>
            </div>

            <div class="mb-3">
                <label for="mot_de_passe_confirm" class="form-label">Confirmer le nouveau mot de passe</label>
                <input type="password" class="form-control" id="mot_de_passe_confirm" name="mot_de_passe_confirm" autocomplete="new-password">
                <div class="invalid-feedback">Les mots de passe ne correspondent pas.</div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Enregistrer les modifications
            </button>
        </form>
    </div>
</div>

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
            
            // Vérification de la correspondance des mots de passe
            var password = document.getElementById('mot_de_passe')
            var confirm = document.getElementById('mot_de_passe_confirm')
            
            if (password.value !== '') {
                if (password.value !== confirm.value) {
                    confirm.setCustomValidity('Les mots de passe ne correspondent pas')
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    confirm.setCustomValidity('')
                }
            } else {
                confirm.setCustomValidity('')
            }
            
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include("includes/footer.php"); ?> 