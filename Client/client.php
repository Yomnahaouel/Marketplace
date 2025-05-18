<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Client') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="client.css">
    <style>

    </style>
</head>
<body>
    <div class="header">
        <h1>Mon Compte Client</h1>
        <p>Connecté en tant que: <?= $_SESSION['user']['nom'] ?> (Client)</p>
    </div>
    
    <div class="menu">
        <a href="./client.php">Mon compte</a>
        <a href="products.php">Produits</a>
        <a href="wishlist.php">Ma liste de souhaits</a>
        <a href="cart.php">Mon panier</a>
        <a href="order.php">Mes commandes</a>
        <a href="../compte/logout.php">Déconnexion</a>
    </div>
    
    <div class="content">
        <h2>Mes informations</h2>
        
        <div style="background: white; padding: 20px; border-radius: 5px;">
            <p><strong>Nom:</strong> <?= $_SESSION['user']['nom'] ?></p>
            <p><strong>Email:</strong> <?= $_SESSION['user']['email'] ?></p>
            <p><strong>Date d'inscription:</strong> 15/06/2023</p>
        </div>
        
        <h2 style="margin-top: 30px;">Dernières commandes</h2>
        
        <div class="order">
            <?php
            require_once '../db.php';
            $user_id = $_SESSION['user']['id'];
            $query = "SELECT id, date_commande AS date, statut AS status FROM commande WHERE id_client = $user_id ORDER BY date_commande DESC LIMIT 5";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($order = $result->fetch_assoc()) {
                    echo "<p><strong>Commande #" . $order['id'] . "</strong> - " . $order['date'] . "</p>";
                    echo "<p>Statut: " . $order['status'] . "</p>";
                }
            } else {
                echo "<p>Aucune commande récente.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>