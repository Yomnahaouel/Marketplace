<!DOCTYPE html>
<html lang="en">
 <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - Marketplace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a0ca3;
            --danger: #d9534f;
            --success: #4bbf73;
            --warning: #f0ad4e;
            --dark: #212529;
            --light: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f5f7fa;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--dark);
            color: white;
            padding: 1.5rem 0;
            height: 100vh;
            position: fixed;
        }
        
        .admin-profile {
            text-align: center;
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid #495057;
            margin-bottom: 1.5rem;
        }
        
        .admin-profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 0.5rem;
            border: 3px solid var(--primary);
        }
        
        .admin-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .admin-role {
            color: #adb5bd;
            font-size: 0.9rem;
        }
        
        .nav-menu {
            list-style: none;
        }
        
        .nav-menu li a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #adb5bd;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .nav-menu li a:hover, .nav-menu li a.active {
            background-color: #343a40;
            color: white;
        }
        
        .nav-menu li a i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
        }
        
        /* Top Bar */
        .top-bar {
            background-color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .search-bar {
            display: flex;
            align-items: center;
            background-color: var(--light);
            border-radius: 5px;
            padding: 0.5rem 1rem;
            width: 300px;
        }
        
        .search-bar input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 0.25rem;
            outline: none;
        }
        
        .user-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .notification {
            position: relative;
            cursor: pointer;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Dashboard Content */
        .dashboard {
            padding: 1.5rem;
        }
        
        .page-title {
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: var(--dark);
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .stat-icon.primary {
            background-color: var(--primary);
        }
        
        .stat-icon.success {
            background-color: var(--success);
        }
        
        .stat-icon.warning {
            background-color: var(--warning);
        }
        
        .stat-icon.danger {
            background-color: var(--danger);
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Recent Orders */
        .recent-orders {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .status {
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status.completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status.cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .btn {
            padding: 0.375rem 0.75rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
</head>
<?php 
//session_start();
include("db.php");



?>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="admin-profile">
            <img src="https://via.placeholder.com/150" alt="Admin Photo">
            <h3 class="admin-name">Admin Name</h3>
            <p class="admin-role">Administrateur</p>
        </div>
        
        <ul class="nav-menu">
            <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="#"><i class="fas fa-shopping-bag"></i> Produits</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Utilisateurs</a></li>
            <li><a href="#"><i class="fas fa-store"></i> Vendeurs</a></li>
            <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Commandes</a></li>
            <li><a href="#"><i class="fas fa-tags"></i> Promotions</a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> Statistiques</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> Paramètres</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Rechercher...">
            </div>
            
            <div class="user-actions">
                <div class="notification">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <a href="logout.php" class="btn btn-primary btn-sm">Déconnexion</a>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard">
            <h1 class="page-title">Tableau de Bord</h1>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value">1,254</div>
                            <div class="stat-label">Commandes</div>
                        </div>
                        <div class="stat-icon primary">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value">$24,560</div>
                            <div class="stat-label">Revenus</div>
                        </div>
                        <div class="stat-icon success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value">356</div>
                            <div class="stat-label">Produits</div>
                        </div>
                        <div class="stat-icon warning">
                            <i class="fas fa-box-open"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div>
                            <div class="stat-value">124</div>
                            <div class="stat-label">Vendeurs</div>
                        </div>
                        <div class="stat-icon danger">
                            <i class="fas fa-store"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="recent-orders">
                <div class="section-header">
                    <h2 class="section-title">Commandes Récentes</h2>
                    <a href="#" class="btn btn-primary btn-sm">Voir toutes</a>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID Commande</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD-2023-001</td>
                            <td>Jean Dupont</td>
                            <td>15/06/2023</td>
                            <td>$120.00</td>
                            <td><span class="status completed">Complétée</span></td>
                            <td><a href="#" class="btn btn-primary btn-sm">Détails</a></td>
                        </tr>
                        <tr>
                            <td>#ORD-2023-002</td>
                            <td>Marie Martin</td>
                            <td>14/06/2023</td>
                            <td>$85.50</td>
                            <td><span class="status pending">En cours</span></td>
                            <td><a href="#" class="btn btn-primary btn-sm">Détails</a></td>
                        </tr>
                        <tr>
                            <td>#ORD-2023-003</td>
                            <td>Pierre Durand</td>
                            <td>13/06/2023</td>
                            <td>$210.75</td>
                            <td><span class="status completed">Complétée</span></td>
                            <td><a href="#" class="btn btn-primary btn-sm">Détails</a></td>
                        </tr>
                        <tr>
                            <td>#ORD-2023-004</td>
                            <td>Sophie Lambert</td>
                            <td>12/06/2023</td>
                            <td>$65.00</td>
                            <td><span class="status cancelled">Annulée</span></td>
                            <td><a href="#" class="btn btn-primary btn-sm">Détails</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>




