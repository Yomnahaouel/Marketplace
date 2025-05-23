<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Admin') {
    header("Location: ../compte/login.php");
    exit();
}
require_once '../db.php';

// Handle delete action
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $conn->query("DELETE FROM publicite WHERE id = $id");
    header("Location: publicite.php");
    exit();
}

// Handle status change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_statut'])) {
    $id = intval($_POST['id']);
    $statut = $_POST['statut'];

    $stmt = $conn->prepare("UPDATE publicite SET statut = ? WHERE id = ?");
    $stmt->bind_param("si", $statut, $id);
    $stmt->execute();
    header("Location: publicite.php");
    exit();
}

// Handle filters
$whereClauses = [];
if (!empty($_GET['produit'])) {
    $produit = $conn->real_escape_string($_GET['produit']);
    $whereClauses[] = "pr.nom LIKE '%$produit%'";
}

if (!empty($_GET['date_debut'])) {
    $date_debut = $conn->real_escape_string($_GET['date_debut']);
    $whereClauses[] = "p.date_debut >= '$date_debut'";
}
if (!empty($_GET['date_fin'])) {
    $date_fin = $conn->real_escape_string($_GET['date_fin']);
    $whereClauses[] = "p.date_fin <= '$date_fin'";
}
$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$query = "SELECT p.id, p.id_produit, p.date_debut, p.date_fin, p.statut, p.budget, pr.nom AS produit_nom 
          FROM publicite p 
          JOIN produit pr ON p.id_produit = pr.id 
          $whereSql 
          ORDER BY p.id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Publicités</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="header">
        <h1>Espace Administrateur</h1>
    </div>
    <div class="menu">
        <a href="./admin.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : '' ?>">Tableau de bord</a>
        <a href="./manageUsers.php" class="<?= basename($_SERVER['PHP_SELF']) === 'manageUsers.php' ? 'active' : '' ?>">Gestion des utilisateurs</a>
        <a href="./products.php" class="<?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '' ?>">Gestion des produits</a>
        <a href="./publicite.php" class="<?= basename($_SERVER['PHP_SELF']) === 'publicite.php' ? 'active' : '' ?>">Gestion des publicités</a>
        <a href="../compte/logout.php">Déconnexion</a>
    </div>
    <div class="container mt-4">
        <h4 class="mb-3">Filtres</h4>
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="produit" class="form-control" placeholder="Nom du produit" value="<?= htmlspecialchars($_GET['produit'] ?? '') ?>">
            </div>
            
            <div class="col-md-3">
                <input type="date" name="date_debut" class="form-control" placeholder="Date début" value="<?= htmlspecialchars($_GET['date_debut'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_fin" class="form-control" placeholder="Date fin" value="<?= htmlspecialchars($_GET['date_fin'] ?? '') ?>">
            </div>
            <div class="col-md-12 text-end">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="publicite.php" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </form>

        <h4 class="mb-3">Liste des publicités</h4>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Produit</th>
                    <th scope="col">Date Début</th>
                    <th scope="col">Date Fin</th>
                    <th scope="col">Budget</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($publicite = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $publicite['id'] ?></td>
                    <td><?= htmlspecialchars($publicite['produit_nom']) ?></td>
                    <td><?= $publicite['date_debut'] ?></td>
                    <td><?= $publicite['date_fin'] ?></td>
                    <td><?= number_format($publicite['budget'], 2) ?> €</td>
                    <td>
                        <a href="?supprimer=<?= $publicite['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>