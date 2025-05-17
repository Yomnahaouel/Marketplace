<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Marketplace</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<?php
session_start();

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include("../db.php");
    $username = $_POST['username'];
    $password = $_POST['pwd'];
    $sql = "SELECT * FROM utilisateur WHERE nom = ? AND mot_de_passe = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($user = mysqli_fetch_assoc($result)) {
        $_SESSION['user'] = $user;
        switch ($user['type']) {
            case 'Admin':
                header("Location: ../Admin/admin.php");
                break;
            case 'Vendeur':
                header("Location: ../Seller/seller.php");
                break;
            case 'Client':
                header("Location: ../Client/client.php");
                break;
            default:
                header("Location: ../index.php");
        }
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect";
    }
}
?>
<body>
    <div class="content">
        <div class="login-card">
            <div class="logo">Marketplace</div>
            <h1 class="welcome">Welcome Back!</h1>
            <p class="subtitle">Sign in to access your account</p>
            <form method="POST">
                <div class="form-group">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" id="name" name="username" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="pwd" class="form-label">Password</label>
                    <input type="password" id="pwd" name="pwd" class="form-input" required>
                </div>
                <div class="remember">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Keep me signed in</label>
                </div>
                <button type="submit" class="login-btn">Sign In</button>
            </form>
            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <div class="signup-link">
                Don't have an account? <a href="signup.php">Sign up</a>
            </div>
        </div>
    </div>
</body>
</html>




