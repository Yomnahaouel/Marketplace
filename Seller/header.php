<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Vendeur') {
    header("Location: ../compte/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Vendeur</title>
    <link rel="stylesheet" href="seller.css">
</head>
<body>
    <div class="header">
        <h1>Espace Vendeur</h1>
        <p>Connecté en tant que: <?= $_SESSION['user']['nom'] ?> (Vendeur)</p>
    </div>
    <div class="menu">
        <a href="seller.php" class="<?= basename($_SERVER['PHP_SELF']) === 'seller.php' ? 'active' : '' ?>">Tableau de bord</a>
        <a href="products.php" class="<?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '' ?>">Mes produits</a>
        <a href="ads.php" class="<?= basename($_SERVER['PHP_SELF']) === 'ads.php' ? 'active' : '' ?>">Mes publicités</a>
        <a href="commande.php" class="<?= basename($_SERVER['PHP_SELF']) === 'commande.php' ? 'active' : '' ?>">Commandes</a>
        <a href="../compte/logout.php" class="logout">Déconnexion</a>
    </div>
    <style>
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
        .menu a.logout {
            float: right;
            background-color: #d32f2f;
            border-radius: 5px;
        }
        .menu a.logout:hover {
            background-color: #b71c1c;
        }
    </style>
