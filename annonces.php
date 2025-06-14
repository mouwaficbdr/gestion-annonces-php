<?php
include("includes/head.php");
include("includes/db.php");

// Récupération des annonces
try {
    $query = $bdd->query("SELECT * FROM annonces ORDER BY date_creation DESC");
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Gestion des messages
$message = '';
$type_message = '';

if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'suppression_reussie':
            $message = "L'annonce a été supprimée avec succès.";
            $type_message = "success";
            break;
        case 'erreur_suppression':
            $message = "Une erreur est survenue lors de la suppression de l'annonce.";
            $type_message = "danger";
            break;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestion des Annonces</h2>
    <a href="ajouter_annonce.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nouvelle Annonce
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
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($annonce = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                        <tr>
                            <td><?php echo $annonce['id']; ?></td>
                            <td>
                                <?php if (!empty($annonce['image'])) : ?>
                                    <img src="uploads/<?php echo $annonce['image']; ?>" alt="Image annonce" class="img-thumbnail" style="max-width: 100px;">
                                <?php else : ?>
                                    <span class="text-muted">Pas d'image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($annonce['titre']); ?></td>
                            <td><?php echo htmlspecialchars(substr($annonce['description'], 0, 100)) . '...'; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($annonce['date_creation'])); ?></td>
                            <td>
                                <a href="modifier_annonce.php?id=<?php echo $annonce['id']; ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="supprimer_annonce.php?id=<?php echo $annonce['id']; ?>" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?> 