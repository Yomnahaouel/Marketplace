<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Client') {
    header("Location: login.php");
    exit();
}
require_once '../db.php';

// Handle adding to cart
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);
    $user_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO panier (id_client, id_produit, quantite) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE quantite = quantite + 1");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Handle removing from cart
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    $user_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("DELETE FROM panier WHERE id_client = ? AND id_produit = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Handle updating quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("UPDATE panier SET quantite = ? WHERE id_client = ? AND id_produit = ?");
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Fetch cart items
$user_id = $_SESSION['user']['id'];
// Update query to include `quantite` column from `panier`
$query = "SELECT p.id, p.nom, p.prix, c.quantite FROM produit p JOIN panier c ON p.id = c.id_produit WHERE c.id_client = $user_id";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="client.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <h1>Mon Panier</h1>
    </div>
    <div class="menu">
        <a href="./client.php">Mon compte</a>
        <a href="products.php">Produits</a>
        <a href="wishlist.php">Ma liste de souhaits</a>
        <a href="order.php">Mes commandes</a>
        <a href="../compte/logout.php">Déconnexion</a>
    </div>
    <div class="container mt-4">
        <h4 class="mb-3">Produits dans mon panier</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; while ($item = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nom']) ?></td>
                    <td><?= number_format($item['prix'], 2) ?> €</td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <input type="number" name="quantity" value="<?= $item['quantite'] ?>" min="1" class="form-control d-inline w-auto">
                            <button type="submit" name="update" class="btn btn-sm btn-primary">Mettre à jour</button>
                        </form>
                    </td>
                    <td><?= number_format($item['prix'] * $item['quantite'], 2) ?> €</td>
                    <td>
                        <a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-sm btn-danger">Retirer</a>
                    </td>
                </tr>
                <?php $total += $item['prix'] * $item['quantite']; endwhile; ?>
            </tbody>
        </table>
        <h4>Total: <?= number_format($total, 2) ?> €</h4>
        <a href="order.php" class="btn btn-success">Passer la commande</a>
    </div>
</body>
</html>
