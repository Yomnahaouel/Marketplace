<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Vendeur') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Vendeur</title>
    <link rel="stylesheet" href="seller.css">
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Espace Vendeur</h1>
        <p>Connecté en tant que: <?= $_SESSION['user']['nom'] ?> (Vendeur)</p>
    </div>
    
    <div class="menu">
        <a href="dashboard.php">Tableau de bord</a>
        <a href="products.php">Mes produits</a>
        <a href="orders.php">Commandes</a>
        <a href="../compte/login.php">Déconnexion</a>
    </div>
    
    <div class="content">
        <h2>Mes produits</h2>
        
        <div class="products">
            <div class="product-card">
                <h3>Produit 1</h3>
                <p>Prix: 50€</p>
                <p>10 en stock</p>
            </div>
            <div class="product-card">
                <h3>Produit 2</h3>
                <p>Prix: 75€</p>
                <p>5 en stock</p>
            </div>
        </div>
        
        <button style="margin-top: 20px; padding: 10px 15px; background: #1976d2; color: white; border: none; border-radius: 5px;">
            Ajouter un produit
        </button>
    </div>
</body>
</html>