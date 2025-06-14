<?php
include("includes/head.php");
include("includes/db.php");

$message = '';
$type_message = '';
$confirmation_requise = false;

if (isset($_GET['id'])) {
    try {
        // Vérifier si l'utilisateur essaie de se supprimer lui-même (nonsense)
        if ((int)$_GET['id'] === (int)$_SESSION['utilisateur_id']) {
            $message = "Vous ne pouvez pas supprimer votre propre compte.";
            $type_message = "danger";
        } else {
            // Vérifier si l'utilisateur existe
            $stmt = $bdd->prepare("SELECT id, prenom, nom FROM utilisateurs WHERE id = ?");
            $stmt->execute([(int)$_GET['id']]);
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$utilisateur) {
                $message = "Utilisateur non trouvé.";
                $type_message = "danger";
            } else {
                // Si c'est une confirmation de suppression
                if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
                    // Commencer une transaction
                    $bdd->beginTransaction();
                    
                    try {
                        // Supprimer l'utilisateur
                        $stmt = $bdd->prepare("DELETE FROM utilisateurs WHERE id = ?");
                        $stmt->execute([(int)$_GET['id']]);
                        
                        // Valider la transaction
                        $bdd->commit();
                        
                        $message = "Utilisateur supprimé avec succès !";
                        $type_message = "success";
                    } catch(PDOException $e) {
                        // En cas d'erreur, annuler la transaction
                        $bdd->rollBack();
                        throw $e;
                    }
                } else {
                    // Demander confirmation
                    $confirmation_requise = true;
                    $message = "Êtes-vous sûr de vouloir supprimer cet utilisateur ?";
                    $type_message = "warning";
                }
            }
        }
    } catch(PDOException $e) {
        $message = "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage();
        $type_message = "danger";
    }
} else {
    $message = "ID utilisateur non spécifié.";
    $type_message = "danger";
}

// Redirection après 2 secondes seulement si la suppression a réussi
if (isset($_GET['confirm']) && $_GET['confirm'] === 'true' && $type_message === 'success') {
    header("refresh:2;url=utilisateurs.php");
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Suppression d'Utilisateur</h2>
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

<?php if ($confirmation_requise) : ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Confirmation de suppression</h5>
            <p class="card-text">
                Vous êtes sur le point de supprimer l'utilisateur <?php echo htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']); ?>.
            </p>
            <p class="card-text text-danger">
                <strong>Cette action est irréversible.</strong>
            </p>
            <div class="d-flex gap-2">
                <a href="?id=<?php echo (int)$_GET['id']; ?>&confirm=true" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Confirmer la suppression
                </a>
                <a href="utilisateurs.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Annuler
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include("includes/footer.php"); ?> 