<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Client') {
    header("Location: login.php");
    exit();
}
require_once '../db.php';

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $user_id = $_SESSION['user']['id'];

    // Fetch cart items
    $query = "SELECT p.*, c.quantite AS quantity FROM produit p JOIN panier c ON p.id = c.id_produit WHERE c.id_client = $user_id";
    $result = $conn->query($query);

    // Add error handling for placing an order with an empty cart
    if ($result->num_rows === 0) {
        $_SESSION['error'] = 'Votre panier est vide. Ajoutez des articles avant de passer une commande.';
        header("Location: cart.php");
        exit();
    }

    $total = 0;
    $order_items = [];
    while ($item = $result->fetch_assoc()) {
        $total += $item['prix'] * $item['quantity'];
        $order_items[] = $item;
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO commande (id_client, montant_total, statut) VALUES (?, ?, 'En attente')");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    $stmt = $conn->prepare("INSERT INTO commande_items (commande_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($order_items as $item) {
        $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['prix']);
        $stmt->execute();
    }

    // Clear cart
    $stmt = $conn->prepare("DELETE FROM panier WHERE id_client = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    header("Location: order.php?success=1");
    exit();
}

// Fetch cart items for review
$user_id = $_SESSION['user']['id'];
$query = "SELECT p.id, p.nom AS name, p.prix AS prix, c.quantite AS quantity 
          FROM produit p 
          JOIN panier c ON p.id = c.id_produit 
          WHERE c.id_client = $user_id";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Passer la Commande</title>
    <link rel="stylesheet" href="client.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <h1>Passer la Commande</h1>
    </div>
    <div class="menu">
        <a href="./client.php">Mon compte</a>
        <a href="products.php">Produits</a>
        <a href="wishlist.php">Ma liste de souhaits</a>
        <a href="cart.php">Mon panier</a>
        <a href="../compte/logout.php">Déconnexion</a>
    </div>
    <div class="container mt-4">
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Votre commande a été passée avec succès !</div>
        <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php else: ?>
        <h4 class="mb-3">Détails de la commande</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; while ($item = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['prix'], 2) ?> €</td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['prix'] * $item['quantity'], 2) ?> €</td>
                </tr>
                <?php $total += $item['prix'] * $item['quantity']; endwhile; ?>
            </tbody>
        </table>
        <h4>Total: <?= number_format($total, 2) ?> TND</h4>
        <form method="POST">
            <button type="submit" name="place_order" class="btn btn-success">Confirmer la commande</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
