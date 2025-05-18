<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Client') {
    header("Location: login.php");
    exit();
}
require_once '../db.php';

// Handle filters
$whereClauses = [];
if (!empty($_GET['name'])) {
    $name = $conn->real_escape_string($_GET['name']);
    $whereClauses[] = "name LIKE '%$name%'";
}
if (!empty($_GET['price_min'])) {
    $price_min = floatval($_GET['price_min']);
    $whereClauses[] = "price >= $price_min";
}
if (!empty($_GET['price_max'])) {
    $price_max = floatval($_GET['price_max']);
    $whereClauses[] = "price <= $price_max";
}
if (!empty($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $whereClauses[] = "category = '$category'";
}
if (isset($_GET['in_stock']) && $_GET['in_stock'] === '1') {
    $whereClauses[] = "stock > 0";
}
$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$query = "SELECT id, nom AS name, description, prix AS price, quantite_stock AS stock, category, image FROM produit $whereSql ORDER BY id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits</title>
    <link rel="stylesheet" href="client.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <h1>Produits</h1>
    </div>
    <div class="menu">
        <a href="./client.php">Mon compte</a>
        <a href="wishlist.php">Ma liste de souhaits</a>
        <a href="cart.php">Mon panier</a>
        <a href="order.php">Mes commandes</a>
        <a href="../compte/logout.php">Déconnexion</a>
    </div>
    <div class="container mt-4">
        <h4 class="mb-3">Filtres</h4>
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Nom du produit" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="number" name="price_min" class="form-control" placeholder="Prix min" value="<?= htmlspecialchars($_GET['price_min'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="number" name="price_max" class="form-control" placeholder="Prix max" value="<?= htmlspecialchars($_GET['price_max'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Toutes les catégories</option>
                    <option value="Electronics">Électronique</option>
                    <option value="Clothing">Vêtements</option>
                    <option value="Home">Maison</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="checkbox" name="in_stock" value="1" <?= isset($_GET['in_stock']) ? 'checked' : '' ?>> En stock uniquement
            </div>
            <div class="col-md-12 text-end">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="products.php" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </form>

        <h4 class="mb-3">Liste des produits</h4>
        <div class="row">
            <?php while ($product = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="<?= htmlspecialchars($product['image'] ?? 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name'] ?? 'Produit') ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name'] ?? 'Nom non disponible') ?></h5>
                        <p class="card-text">Prix: <?= isset($product['price']) ? number_format($product['price'], 2) : '0.00' ?> €</p>
                        <p class="card-text">Catégorie: <?= htmlspecialchars($product['category'] ?? 'Non spécifiée') ?></p>
                        <p class="card-text">Stock: <?= isset($product['stock']) && $product['stock'] > 0 ? 'En stock' : 'Rupture de stock' ?></p>
                        <a href="wishlist.php?add=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary">Ajouter à la liste de souhaits</a>
                        <a href="cart.php?add=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Ajouter au panier</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
