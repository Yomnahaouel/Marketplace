<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Admin') {
    header("Location: ../landingPage/login.php");
    exit();
}
require_once '../db.php';

// Handle delete action
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $conn->query("DELETE FROM produit WHERE id = $id");
    header("Location: products.php");
    exit();
}

// Handle modify action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $id = intval($_POST['id']);
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $quantite_stock = intval($_POST['quantite_stock']);

    $stmt = $conn->prepare("UPDATE produit SET nom = ?, description = ?, prix = ?, quantite_stock = ? WHERE id = ?");
    $stmt->bind_param("ssdii", $nom, $description, $prix, $quantite_stock, $id);
    $stmt->execute();
    header("Location: products.php");
    exit();
}

// Handle filters
$whereClauses = [];
if (!empty($_GET['nom'])) {
    $nom = $conn->real_escape_string($_GET['nom']);
    $whereClauses[] = "p.nom LIKE '%$nom%'";
}
if (!empty($_GET['prix_min'])) {
    $prix_min = floatval($_GET['prix_min']);
    $whereClauses[] = "p.prix >= $prix_min";
}
if (!empty($_GET['prix_max'])) {
    $prix_max = floatval($_GET['prix_max']);
    $whereClauses[] = "p.prix <= $prix_max";
}
if (!empty($_GET['quantite_min'])) {
    $quantite_min = intval($_GET['quantite_min']);
    $whereClauses[] = "p.quantite_stock >= $quantite_min";
}
if (!empty($_GET['vendeur'])) {
    $vendeur = $conn->real_escape_string($_GET['vendeur']);
    $whereClauses[] = "u.nom LIKE '%$vendeur%'";
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
$query = "SELECT p.id, p.nom, p.description, p.prix, p.quantite_stock, u.nom AS vendeur 
          FROM produit p 
          JOIN utilisateur u ON p.id_vendeur = u.id 
          $whereSql 
          ORDER BY p.id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="products.css">
</head>
<body>
    <div class="header">
        <h1>Espace Administrateur</h1>
        <p>Connecté en tant que: <?= htmlspecialchars($_SESSION['user']['nom']) ?> (Admin)</p>
    </div>
    <div class="menu">
        <a href="./admin.php">Tableau de bord</a>
        <a href="./manageUsers.php">Gestion des utilisateurs</a>
        <a href="./products.php" class="active">Gestion des produits</a>
        <a href="../compte/logout.php">Déconnexion</a>
    </div>
    <div class="container mt-4">
        <h4 class="mb-3">Filtres</h4>
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="nom" class="form-control" placeholder="Nom du produit" value="<?= htmlspecialchars($_GET['nom'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="prix_min" class="form-control" placeholder="Prix min (€)" value="<?= htmlspecialchars($_GET['prix_min'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="prix_max" class="form-control" placeholder="Prix max (€)" value="<?= htmlspecialchars($_GET['prix_max'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <input type="number" name="quantite_min" class="form-control" placeholder="Quantité min" value="<?= htmlspecialchars($_GET['quantite_min'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="vendeur" class="form-control" placeholder="Nom du vendeur" value="<?= htmlspecialchars($_GET['vendeur'] ?? '') ?>">
            </div>
            <div class="col-md-12 text-end">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="products.php" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </form>

        <h4 class="mb-3">Liste des produits</h4>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Quantité en stock</th>
                    <th>Vendeur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['nom']) ?></td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td><?= number_format($product['prix'], 2) ?> €</td>
                    <td><?= $product['quantite_stock'] ?></td>
                    <td><?= htmlspecialchars($product['vendeur']) ?></td>
                    <td>
                        <a href="?supprimer=<?= $product['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                        <button class="btn btn-sm btn-primary" onclick="openModifyModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['nom']) ?>', '<?= htmlspecialchars($product['description']) ?>', <?= $product['prix'] ?>, <?= $product['quantite_stock'] ?>)">Modifier</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for modifying a product -->
    <div class="modal" id="modifyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le produit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="productId">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="productName" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="productDescription" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Prix</label>
                            <input type="number" step="0.01" class="form-control" id="productPrice" name="prix" required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Quantité en stock</label>
                            <input type="number" class="form-control" id="productStock" name="quantite_stock" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="modifier" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModifyModal(id, name, description, price, stock) {
            document.getElementById('productId').value = id;
            document.getElementById('productName').value = name;
            document.getElementById('productDescription').value = description;
            document.getElementById('productPrice').value = price;
            document.getElementById('productStock').value = stock;
            new bootstrap.Modal(document.getElementById('modifyModal')).show();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
