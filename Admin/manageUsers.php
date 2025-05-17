<?php
// 1. Sécurité et connexion DB
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Admin') {
    header("Location: ../landingPage/login.php");
    exit();
}
require_once '../db.php';

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

// Handle filters
$whereClauses = [];
if (!empty($_GET['nom'])) {
    $nom = $conn->real_escape_string($_GET['nom']);
    $whereClauses[] = "nom LIKE '%$nom%'";
}
if (!empty($_GET['email'])) {
    $email = $conn->real_escape_string($_GET['email']);
    $whereClauses[] = "email LIKE '%$email%'";
}
if (!empty($_GET['type'])) {
    $type = $conn->real_escape_string($_GET['type']);
    $whereClauses[] = "type = '$type'";
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
$query = "SELECT * FROM utilisateur $whereSql ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="manageUsers.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
    <div class="header">
        <h1>Espace Administrateur</h1>
        <p>Connecté en tant que: <?= htmlspecialchars($_SESSION['user']['nom']) ?> (Admin)</p>
    </div>
    <div class="menu">
        <a href="./admin.php">Tableau de bord</a>
        <a href="./manageUsers.php" class="active">Gestion des utilisateurs</a>
        <a href="./products.php">Gestion des produits</a>
        <a href="../compte/logout.php">Déconnexion</a>
    </div>
    <div class="container mt-4">
        <!-- 3. Affichage des messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <!-- Filters -->
        <h4 class="mb-3">Filtres</h4>
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="nom" class="form-control" placeholder="Nom" value="<?= htmlspecialchars($_GET['nom'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <select name="type" class="form-select">
                    <option value="">Tous les types</option>
                    <option value="Admin" <?= (isset($_GET['type']) && $_GET['type'] === 'Admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="Vendeur" <?= (isset($_GET['type']) && $_GET['type'] === 'Vendeur') ? 'selected' : '' ?>>Vendeur</option>
                    <option value="Client" <?= (isset($_GET['type']) && $_GET['type'] === 'Client') ? 'selected' : '' ?>>Client</option>
                </select>
            </div>
            <div class="col-md-12 text-end">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="manageUsers.php" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </form>

        <!-- 5. Liste des utilisateurs -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Liste des utilisateurs</h4>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal" title="Ajouter un utilisateur">
                <i class="bi bi-person-plus"></i>
            </button>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" name="nom" id="nom" class="form-control" placeholder="Nom" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="Client">Client</option>
                                    <option value="Vendeur">Vendeur</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="ajouter" class="btn btn-success">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
                <?php while ($user = $result->fetch_assoc()): ?>
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