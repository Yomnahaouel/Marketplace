<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Client') {
    header("Location: login.php");
    exit();
}
require_once '../db.php';

// Handle adding to wishlist
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);
    $user_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE product_id = product_id");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();

    // Add error handling for duplicate items in the wishlist
    if ($stmt->errno === 1062) {
        $_SESSION['error'] = 'Cet article est déjà dans votre liste de souhaits.';
        header("Location: wishlist.php");
        exit();
    }

    header("Location: wishlist.php");
    exit();
}

// Handle removing from wishlist
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    $user_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    header("Location: wishlist.php");
    exit();
}

// Fetch wishlist items
$user_id = $_SESSION['user']['id'];
// Update query to match the new database schema
$query = "SELECT p.id AS product_id, p.nom AS name, p.description, p.prix AS price, p.quantite_stock AS stock, p.category, p.image FROM produit p JOIN wishlist w ON p.id = w.product_id WHERE w.user_id = $user_id";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ma Liste de Souhaits</title>
    <link rel="stylesheet" href="client.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <h1>Ma Liste de Souhaits</h1>
    </div>
    <div class="menu">
        <a href="./client.php">Mon compte</a>
        <a href="products.php">Produits</a>
        <a href="cart.php">Mon panier</a>
        <a href="order.php">Mes commandes</a>
        <a href="../compte/logout.php">Déconnexion</a>
    </div>
    <div class="container mt-4">
        <h4 class="mb-3">Produits dans ma liste de souhaits</h4>
        <div class="row">
            <?php while ($product = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
              <?php $basePath = 'http://localhost/Marketplace/'; ?>

<div class="card">
    <img src="<?= $basePath . htmlspecialchars($product['image'] ?? 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
    <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
        <p class="card-text">Prix: <?= number_format($product['price'], 2) ?> €</p>
        <p class="card-text">Catégorie: <?= htmlspecialchars($product['category']) ?></p>
        <a href="cart.php?add=<?= $product['product_id'] ?>" class="btn btn-sm btn-primary">Ajouter au panier</a>
        <a href="wishlist.php?remove=<?= $product['product_id'] ?>" class="btn btn-sm btn-danger">Retirer</a>
    </div>
</div>

            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
