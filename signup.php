<?php
session_start();
include("db.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type']; // 'client' ou 'vendeur'

    // Validation basique
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        // Vérifier si l'utilisateur existe déjà
        $check_sql = "SELECT * FROM utilisateur WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "Cet email est déjà utilisé";
        } else {
            // Insertion simple (sans hashage)
            $sql = "INSERT INTO utilisateur (nom, email, mot_de_passe, type) 
                    VALUES ('$username', '$email', '$password', '$user_type')";

            if (mysqli_query($conn, $sql)) {
                // Inscription réussie
                $_SESSION['message'] = "Inscription réussie! Vous pouvez maintenant vous connecter.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Erreur: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Marketplace</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-box {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 350px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 1.5rem;
        }
        .error {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 0.7rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .login-link a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Créer un compte</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="user_type">Type de compte</label>
                <select id="user_type" name="user_type" required>
                    <option value="client">Client</option>
                    <option value="vendeur">Vendeur</option>
                </select>
            </div>
            
            <button type="submit">S'inscrire</button>
        </form>
        
        <div class="login-link">
            Déjà un compte? <a href="login.php">Connectez-vous</a>
        </div>
    </div>
</body>
</html>