<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Marketplace</title>
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a0ca3;
            --error: #f72585;
            --text: #2b2d42;
            --light: #f8f9fa;
            --gray: #adb5bd;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text);
        }
        
        .login-card {
            width: 100%;
            max-width: 380px;
            padding: 2.5rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        
        .logo {
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .welcome {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .subtitle {
            color: var(--gray);
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .form-input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .remember {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: var(--gray);
        }
        
        .remember input {
            margin-right: 0.5rem;
        }
        
        .login-btn {
            width: 100%;
            padding: 0.9rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .login-btn:hover {
            background-color: var(--primary-dark);
        }
        
        .error-message {
            color: var(--error);
            background: #fff0f3;
            padding: 0.8rem;
            border-radius: 8px;
            margin: 1rem 0;
            font-size: 0.85rem;
            display: none; /* Cachez par défaut */
        }
        
        .signup-link {
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .signup-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<?php
session_start();

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inclure la connexion à la base de données
    include("db.php");
    
    // Récupère les données du formulaire
    $username = $_POST['username'];
    $password = $_POST['pwd'];
    
    // Requête sécurisée (préparation pour éviter les injections SQL)
    $sql = "SELECT * FROM utilisateur WHERE nom = ? AND mot_de_passe = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Si un utilisateur est trouvé
    if ($user = mysqli_fetch_assoc($result)) {
        // Stocke les infos utilisateur en session
        $_SESSION['user'] = $user;
        
        // Redirection selon le type d'utilisateur
        switch ($user['type']) {
            case 'Admin':
                header("Location: admin.php");
                break;
            case 'Vendeur':
                header("Location: seller.php");
                break;
            case 'Client':
                header("Location: client.php");
                break;
            default:
                header("Location: index.php");
        }
        exit();
    } else {
        // Si échec de connexion
        $error = "Nom d'utilisateur ou mot de passe incorrect";
    }
}

?>
<body>
         <div class="login-card">
        <div class="logo">Marketplace</div>
        <h1 class="welcome">Welcome back !!!</h1>
        <p class="subtitle">Sign in to access your account</p>
          <form method="POST">
            <div class="form-group">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" id="name" name="username" class="form-input" >
            </div>
            
            <div class="form-group">
                <label for="nom" class="form-label">Password</label>
                <input type="password" id="pwd" name="pwd" class="form-input" >
            </div>
            
            <div class="remember">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Keep me signed in</label>
            </div>
            
            <button type="submit" class="login-btn">Sign in</button>
        </form>
        
        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign up</a>
        </div>
    </div>
</body>
</html>




