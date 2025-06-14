<?php
include("includes/head.php");
include("includes/db.php");

$message = '';
$type_message = '';
$confirmation_requise = false;

if (isset($_GET['id'])) {
    try {
        // Vérifier si l'annonce existe
        $stmt = $bdd->prepare("SELECT id, titre, image FROM annonces WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $annonce = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$annonce) {
            $message = "Annonce non trouvée.";
            $type_message = "danger";
        } else {
            // Si c'est une confirmation de suppression
            if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
                // Commencer une transaction
                $bdd->beginTransaction();
                
                try {
                    // Supprimer l'image si elle existe
                    if (!empty($annonce['image']) && file_exists('uploads/' . $annonce['image'])) {
                        unlink('uploads/' . $annonce['image']);
                    }
                    
                    // Supprimer l'annonce
                    $stmt = $bdd->prepare("DELETE FROM annonces WHERE id = ?");
                    $stmt->execute([(int)$_GET['id']]);
                    
                    // Valider la transaction
                    $bdd->commit();
                    
                    $message = "Annonce supprimée avec succès !";
                    $type_message = "success";
                } catch(PDOException $e) {
                    // En cas d'erreur, annuler la transaction
                    $bdd->rollBack();
                    throw $e;
                }
            } else {
                // Demander confirmation
                $confirmation_requise = true;
                $message = "Êtes-vous sûr de vouloir supprimer cette annonce ?";
                $type_message = "warning";
            }
        }
    } catch(PDOException $e) {
        $message = "Erreur lors de la suppression de l'annonce : " . $e->getMessage();
        $type_message = "danger";
    }
} else {
    $message = "ID annonce non spécifié.";
    $type_message = "danger";
}

// Redirection après 2 secondes seulement si la suppression a réussi
if (isset($_GET['confirm']) && $_GET['confirm'] === 'true' && $type_message === 'success') {
    header("refresh:2;url=annonces.php");
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Suppression d'Annonce</h2>
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

<?php if ($confirmation_requise) : ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Confirmation de suppression</h5>
            <p class="card-text">
                Vous êtes sur le point de supprimer l'annonce "<?php echo htmlspecialchars($annonce['titre']); ?>".
            </p>
            <p class="card-text text-danger">
                <strong>Cette action est irréversible.</strong>
            </p>
            <div class="d-flex gap-2">
                <a href="?id=<?php echo (int)$_GET['id']; ?>&confirm=true" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Confirmer la suppression
                </a>
                <a href="annonces.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Annuler
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include("includes/footer.php"); ?> 