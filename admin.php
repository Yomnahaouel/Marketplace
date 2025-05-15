<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Administrateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #d32f2f;
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
        .content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Espace Administrateur</h1>
        <p>Connecté en tant que: <?= $_SESSION['user']['nom'] ?> (Admin)</p>
    </div>
    
    <div class="menu">
        <a href="dashboard.php">Tableau de bord</a>
        <a href="users.php">Gestion des utilisateurs</a>
        <a href="products.php">Gestion des produits</a>
        <a href="logout.php">Déconnexion</a>
    </div>
    
    <div class="content">
        <h2>Statistiques</h2>
        <p>Bienvenue dans votre espace d'administration. Vous pouvez gérer l'ensemble de la plateforme.</p>
        
        <div style="display: flex; gap: 20px; margin-top: 30px;">
            <div style="flex: 1; background: #e3f2fd; padding: 15px; border-radius: 5px;">
                <h3>Utilisateurs</h3>
                <p>150 inscrits</p>
            </div>
            <div style="flex: 1; background: #e8f5e9; padding: 15px; border-radius: 5px;">
                <h3>Vendeurs</h3>
                <p>25 actifs</p>
            </div>
            <div style="flex: 1; background: #fff3e0; padding: 15px; border-radius: 5px;">
                <h3>Produits</h3>
                <p>500 en ligne</p>
            </div>
        </div>
    </div>
</body>
</html>