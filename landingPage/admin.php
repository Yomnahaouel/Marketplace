<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch statistics from the database
require_once '../db.php';
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM utilisateur")->fetch_assoc()['count'];
$totalSellers = $conn->query("SELECT COUNT(*) AS count FROM utilisateur WHERE type = 'Vendeur'")->fetch_assoc()['count'];
$totalProducts = $conn->query("SELECT COUNT(*) AS count FROM produit")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) AS count FROM commande")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(montant_total) AS total FROM commande WHERE statut IN ('Confirmée', 'Livrée')")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Administrateur</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="header">
        <h1>Espace Administrateur</h1>
        <p>Connecté en tant que: <?= htmlspecialchars($_SESSION['user']['nom']) ?> (Admin)</p>
    </div>
    <div class="menu">
        <a href="./admin.php">Tableau de bord</a>
        <a href="../functionalities/manageUsers.php">Gestion des utilisateurs</a>
        <a href="../functionalities/products.php">Gestion des produits</a>
        <a href="../compte/logout.php">Déconnexion</a>
    </div>
    <div class="content centered">
        <h2>Statistiques</h2>
        <p>Bienvenue dans votre espace d'administration. Voici un aperçu des statistiques de la plateforme :</p>
        
        <div class="stats-container">
            <div class="stat-box" style="background: #e3f2fd;">
                <h3>Utilisateurs</h3>
                <p class="stat-value"><?= $totalUsers ?></p>
            </div>
            <div class="stat-box" style="background: #e8f5e9;">
                <h3>Vendeurs</h3>
                <p class="stat-value"><?= $totalSellers ?></p>
            </div>
            <div class="stat-box" style="background: #fff3e0;">
                <h3>Produits</h3>
                <p class="stat-value"><?= $totalProducts ?></p>
            </div>
            <div class="stat-box" style="background: #ede7f6;">
                <h3>Commandes</h3>
                <p class="stat-value"><?= $totalOrders ?></p>
            </div>
            <div class="stat-box" style="background: #ffebee;">
                <h3>Revenu Total</h3>
                <p class="stat-value"><?= number_format($totalRevenue, 2) ?> TND</p>
            </div>
        </div>
    </div>
</body>
</html>