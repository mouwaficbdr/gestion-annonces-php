<?php
include("includes/head.php");
include("includes/db.php");

$message = '';
$type_message = '';
$annonce = null;

// Récupération de l'annonce
if (isset($_GET['id'])) {
    try {
        $stmt = $bdd->prepare("SELECT * FROM annonces WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$annonce) {
            header("Location: annonces.php");
            exit;
        }
    } catch(PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    header("Location: annonces.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $image = $_FILES['image'];

    // Validation des données
    if (empty($titre) || empty($description)) {
        $message = "Le titre et la description sont obligatoires.";
        $type_message = "danger";
    } else {
        try {
            // Si une nouvelle image est uploadée
            if ($image['error'] === UPLOAD_ERR_OK) {
                $image_tmp = $image['tmp_name'];
                $image_name = time() . '_' . $image['name'];
                $image_path = 'uploads/' . $image_name;

                // Supprimer l'ancienne image si elle existe
                if (!empty($annonce['image']) && file_exists('uploads/' . $annonce['image'])) {
                    unlink('uploads/' . $annonce['image']);
                }

                // Déplacer la nouvelle image
                if (move_uploaded_file($image_tmp, $image_path)) {
                    $stmt = $bdd->prepare("UPDATE annonces SET titre = ?, description = ?, image = ? WHERE id = ?");
                    $stmt->execute([$titre, $description, $image_name, $annonce['id']]);
                } else {
                    throw new Exception("Erreur lors de l'upload de l'image.");
                }
            } else {
                // Mise à jour sans changer l'image
                $stmt = $bdd->prepare("UPDATE annonces SET titre = ?, description = ? WHERE id = ?");
                $stmt->execute([$titre, $description, $annonce['id']]);
            }

            $message = "Annonce modifiée avec succès !";
            $type_message = "success";
            // Mise à jour des données affichées
            $annonce['titre'] = $titre;
            $annonce['description'] = $description;
        } catch(Exception $e) {
            $message = "Erreur lors de la modification de l'annonce : " . $e->getMessage();
            $type_message = "danger";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Modifier l'Annonce</h2>
    <a href="annonces.php" class="btn btn-secondary">
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
        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?php echo htmlspecialchars($annonce['titre']); ?>" required>
                <div class="invalid-feedback">Veuillez entrer un titre.</div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($annonce['description']); ?></textarea>
                <div class="invalid-feedback">Veuillez entrer une description.</div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <?php if (!empty($annonce['image'])) : ?>
                    <div class="mb-2">
                        <img src="uploads/<?php echo $annonce['image']; ?>" alt="Image actuelle" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <div class="form-text">Laissez vide pour conserver l'image actuelle.</div>
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
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include("includes/footer.php"); ?> 