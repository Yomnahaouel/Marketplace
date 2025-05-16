<?php
// 1. Sécurité et connexion DB
 // Vérifie le rôle Admin
require_once 'db.php';

// 2. Traitement des actions (AJOUT/SUPPRESSION)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    // AJOUT d'utilisateur
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $type = $_POST['type'];
    $mdp = password_hash('temp123', PASSWORD_DEFAULT); // Mot de passe temporaire
    
    $stmt = $conn->prepare("INSERT INTO utilisateur (nom, email, mot_de_passe, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nom, $email, $mdp, $type);
    $stmt->execute();
    $message = "Utilisateur ajouté !";
}

if (isset($_GET['supprimer'])) {
    // SUPPRESSION (uniquement non-Admins)
    $id = intval($_GET['supprimer']);
    $conn->query("DELETE FROM utilisateur WHERE id = $id AND type != 'Admin'");
    $message = "Utilisateur supprimé !";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <!-- 3. Affichage des messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <!-- 4. Formulaire AJOUT -->
        <form method="POST" class="mb-5 p-3 border rounded">
            <h4>Ajouter un utilisateur</h4>
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="nom" class="form-control" placeholder="Nom" required>
                </div>
                <div class="col-md-4">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select" required>
                        <option value="Client">Client</option>
                        <option value="Vendeur">Vendeur</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="ajouter" class="btn btn-success w-100">+ Ajouter</button>
                </div>
            </div>
        </form>

        <!-- 5. Liste des utilisateurs -->
        <h4 class="mb-3">Liste des utilisateurs</h4>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // 6. Récupération des utilisateurs
                $result = $conn->query("SELECT * FROM utilisateur ORDER BY id DESC");
                while ($user = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['type'] ?></td>
                    <td>
                        <?php if ($user['type'] !== 'Admin'): ?>
                            <a href="?supprimer=<?= $user['id'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Confirmer la suppression ?')">
                               Supprimer
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>