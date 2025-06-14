<?php
include("includes/head.php");
include("includes/db.php");

// Récupération des utilisateurs
try {
    $query = $bdd->query("SELECT id, prenom, nom, email FROM utilisateurs ORDER BY nom, prenom");
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestion des Utilisateurs</h2>
    <a href="ajouter_utilisateur.php" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Nouvel Utilisateur
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($utilisateur = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($utilisateur['nom']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['email']); ?></td>
                            <td>
                                <a href="modifier_utilisateur.php?id=<?php echo $utilisateur['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="supprimer_utilisateur.php?id=<?php echo $utilisateur['id']; ?>" class="btn btn-sm btn-danger">
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