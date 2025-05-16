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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #388e3c;
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
        .order {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mon Compte Client</h1>
        <p>Connecté en tant que: <?= $_SESSION['user']['nom'] ?> (Client)</p>
    </div>
    
    <div class="menu">
        <a href="dashboard.php">Mon compte</a>
        <a href="orders.php">Mes commandes</a>
        <a href="wishlist.php">Ma liste de souhaits</a>
        <a href="logout.php">Déconnexion</a>
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
            <p><strong>Commande #12345</strong> - 10/06/2023</p>
            <p>Statut: Livré</p>
        </div>
    </div>
</body>
</html>
</html>