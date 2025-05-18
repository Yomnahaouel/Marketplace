<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Vendeur') {
    header("Location: ../compte/login.php");
    exit();
}

// Fetch statistics for the seller
require_once '../db.php';
$id_vendeur = $_SESSION['user']['id'];
$totalProducts = $conn->query("SELECT COUNT(*) AS count FROM produit WHERE id_vendeur = $id_vendeur")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(DISTINCT c.id) AS count FROM commande c JOIN produit p ON c.id = p.id WHERE p.id_vendeur = $id_vendeur")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(c.montant_total) AS total FROM commande c JOIN produit p ON c.id = p.id WHERE p.id_vendeur = $id_vendeur AND c.statut IN ('Confirmée', 'Livrée')")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Vendeur</title>
    <link rel="stylesheet" href="../Admin/admin.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #1976d2;
            color: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .menu {
            background-color: #333;
            overflow: hidden;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .menu a {
            float: left;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }
        .menu a:hover {
            background-color: #ddd;
            color: black;
        }
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: white;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            justify-content: center;
        }
        .stat-box {
            flex: 1;
            min-width: 200px;
            max-width: 250px;
            background: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .stat-box h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #1976d2;
        }
        .add-product-btn {
            background: #1976d2;
            color: white;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 24px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .add-product-btn:hover {
            background: #1251a3;
        }
        .modern-product-form {
            display: none;
            max-width: 520px;
            margin: 0 auto 32px auto;
            background: #fff;
            padding: 32px 32px 24px 32px;
            border-radius: 16px;
            box-shadow: 0 6px 24px rgba(25,118,210,0.13);
            animation: fadeIn 0.3s;
        }
        .modern-product-form h3 {
            color: #1976d2;
            text-align: center;
            margin-bottom: 22px;
            font-size: 1.5rem;
        }
        .form-row {
            display: flex;
            gap: 18px;
            margin-bottom: 18px;
        }
        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }
        .form-group input,
        .form-group textarea {
            padding: 10px 12px;
            border: 1px solid #bdbdbd;
            border-radius: 7px;
            font-size: 16px;
            background: #f8fafc;
            transition: border 0.2s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border: 1.5px solid #1976d2;
            outline: none;
        }
        .form-group textarea {
            min-height: 70px;
            resize: vertical;
        }
        .submit-btn {
            background: #1976d2;
            color: white;
            padding: 13px 0;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 7px;
            margin-top: 10px;
            transition: background 0.2s;
            cursor: pointer;
        }
        .submit-btn:hover {
            background: #1251a3;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Espace Vendeur</h1>
        <p>Connecté en tant que: <?= htmlspecialchars($_SESSION['user']['nom']) ?> (Vendeur)</p>
    </div>
    <div class="menu">
        <a href="./seller.php">Tableau de bord</a>
        <a href="./products.php">Mes produits</a>
        <a href="./orders.php">Mes commandes</a>
        <a href="ads.php" style="color:#fff;text-decoration:none;font-size:1.1rem;padding:18px 22px;display:inline-block;font-weight:600;transition:background 0.2s;border-radius:8px 8px 0 0;" onmouseover="this.style.background='#1251a3'" onmouseout="this.style.background='none'">Mes publicités</a>
        <a href="../compte/logout.php">Déconnexion</a>
    </div>
    <div class="content centered">
        <h2>Statistiques</h2>
        <p>Bienvenue dans votre espace vendeur. Voici un aperçu de vos statistiques :</p>
        
        <div class="stats-container">
            <div class="stat-box" style="background: #e3f2fd;">
                <h3>Produits</h3>
                <p class="stat-value"><?= $totalProducts ?></p>
            </div>
            <div class="stat-box" style="background: #fff3e0;">
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