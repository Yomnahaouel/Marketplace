<?php
ob_start(); // Démarre la mise en tampon de sortie pour éviter les erreurs de header
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'Vendeur') {
    header("Location: login.php");
    exit();
}
include '../db.php';
$id_vendeur = $_SESSION['user']['id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Publicités</title>
    <link rel="stylesheet" href="../seller.css">
</head>
<body>
    <div class="menu" style="background:#1976d2;display:flex;gap:18px;padding:0 24px 0 24px;border-radius:10px 10px 0 0;box-shadow:0 2px 8px rgba(25,118,210,0.08);margin-bottom:32px;align-items:center;">
        <a href="./seller.php" style="color:#fff;text-decoration:none;font-size:1.1rem;padding:18px 22px;display:inline-block;font-weight:600;transition:background 0.2s;border-radius:8px 8px 0 0;" onmouseover="this.style.background='#1251a3'" onmouseout="this.style.background='none'">Tableau de bord</a>
        <a href="products.php" style="color:#fff;text-decoration:none;font-size:1.1rem;padding:18px 22px;display:inline-block;font-weight:600;transition:background 0.2s;border-radius:8px 8px 0 0;" onmouseover="this.style.background='#1251a3'" onmouseout="this.style.background='none'">Mes produits</a>
        <a href="ads.php" class="active" style="color:#fff;text-decoration:none;font-size:1.1rem;padding:18px 22px;display:inline-block;font-weight:600;background:#1251a3;border-radius:8px 8px 0 0;">Mes publicités</a>
        <a href="../compte/logout.php" style="color:#fff;text-decoration:none;font-size:1.1rem;padding:18px 22px;display:inline-block;font-weight:600;transition:background 0.2s;border-radius:8px 8px 0 0;margin-left:auto;background:#d32f2f;" onmouseover="this.style.background='#b71c1c'" onmouseout="this.style.background='#d32f2f'">Déconnexion</a>
    </div>
    <div class="content" style="background:#f8fafc;padding:32px 24px 40px 24px;border-radius:16px;box-shadow:0 4px 18px rgba(25,118,210,0.07);max-width:1100px;margin:32px auto 0 auto;">
        <h2 style="color:#1976d2;text-align:center;margin-bottom:32px;font-size:2.1rem;letter-spacing:1px;">Mes Publicités</h2>
        <button id="openAddAdModal" class="add-product-btn">+ Ajouter une publicité</button>
        <!-- Modal pour ajouter une publicité -->
        <div id="addAdModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:1000;align-items:center;justify-content:center;">
            <div style="background:white;padding:32px 28px 24px 28px;border-radius:14px;max-width:420px;width:95vw;box-shadow:0 8px 32px rgba(25,118,210,0.18);position:relative;">
                <span onclick="closeAddAdModal()" style="position:absolute;top:12px;right:18px;font-size:22px;cursor:pointer;color:#888;">&times;</span>
                <h3 style="color:#1976d2;text-align:center;margin-bottom:18px;">Ajouter une publicité</h3>
                <form id="addAdForm" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Produit associé</label>
                        <select name="produit_id" required style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;">
                            <?php
                            $produits = $conn->query("SELECT id, nom FROM produit WHERE id_vendeur = $id_vendeur");
                            if ($produits && $produits->num_rows > 0) {
                                while ($produit = $produits->fetch_assoc()) {
                                    echo '<option value="' . $produit['id'] . '">' . htmlspecialchars($produit['nom']) . '</option>';
                                }
                            } else {
                                echo '<option disabled>Aucun produit disponible</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Titre de la publicité</label>
                        <input type="text" name="titre" required style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;min-height:70px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image de la publicité</label>
                        <input type="file" name="image" accept="image/*" style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;">
                    </div>
                    <button type="submit" name="ajouter_publicite" class="submit-btn" style="width:100%;margin-top:10px;background:#1976d2;color:white;padding:10px 0;border:none;border-radius:7px;font-size:16px;">Ajouter</button>
                </form>
            </div>
        </div>
        <script>
        function openAddAdModal() {
            document.getElementById('addAdModal').style.display = 'flex';
        }
        function closeAddAdModal() {
            document.getElementById('addAdModal').style.display = 'none';
        }
        document.getElementById('openAddAdModal').addEventListener('click', openAddAdModal);
        window.onclick = function(event) {
            var modal = document.getElementById('addAdModal');
            if (event.target === modal) {
                closeAddAdModal();
            }
        };
        </script>

        <?php
        // Traitement de l'ajout de publicité
        if (isset($_POST['ajouter_publicite'])) {
            $produit_id = intval($_POST['produit_id']);
            $titre = mysqli_real_escape_string($conn, $_POST['titre']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);

            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageTmpPath = $_FILES['image']['tmp_name'];
                $imageName = basename($_FILES['image']['name']);

                // Vérification et création du dossier uploads si nécessaire
                $uploadDir = __DIR__ . '/uploads';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $uniqueFileName = uniqid() . '_' . $imageName;
                $imagePath = 'uploads/' . $uniqueFileName;
                move_uploaded_file($imageTmpPath, $uploadDir . '/' . $uniqueFileName);
            }

            $sql = "INSERT INTO publicite (produit_id, titre, description, image, id_vendeur) VALUES ($produit_id, '$titre', '$description', '$imagePath', $id_vendeur)";
            if ($conn->query($sql)) {
                $_SESSION['message'] = '<div style="color:green;margin-bottom:10px;">Publicité ajoutée avec succès !</div>';
                header("Location: ads.php");
                exit();
            } else {
                echo '<div style="color:red;margin-bottom:10px;">Erreur lors de l\'ajout de la publicité.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
