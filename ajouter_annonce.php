<?php
include("includes/head.php");
include("includes/db.php");

$message = '';
$type_message = '';

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
            $image_name = null;
            
            // Gestion de l'upload d'image
            if ($image['error'] === UPLOAD_ERR_OK) {
                $image_tmp = $image['tmp_name'];
                $image_name = time() . '_' . $image['name'];
                $image_path = 'uploads/' . $image_name;

                if (!move_uploaded_file($image_tmp, $image_path)) {
                    throw new Exception("Erreur lors de l'upload de l'image.");
                }
            }

            // Insertion dans la base de données
            $stmt = $bdd->prepare("INSERT INTO annonces (titre, description, image, date_creation) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$titre, $description, $image_name]);

            $message = "Annonce créée avec succès !";
            $type_message = "success";
            // Redirection après 2 secondes
            header("refresh:2;url=annonces.php");
        } catch(Exception $e) {
            $message = "Erreur lors de la création de l'annonce : " . $e->getMessage();
            $type_message = "danger";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nouvelle Annonce</h2>
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
                <input type="text" class="form-control" id="titre" name="titre" required>
                <div class="invalid-feedback">Veuillez entrer un titre.</div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                <div class="invalid-feedback">Veuillez entrer une description.</div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <div class="form-text">Formats acceptés : JPG, JPEG, PNG, GIF.</div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Enregistrer
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