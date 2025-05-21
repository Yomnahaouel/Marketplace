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
<?php
// Handle product deletion
if (isset($_GET['supprimer'])) {
    $idp = intval($_GET['supprimer']);
    if ($conn->query("DELETE FROM produit WHERE id = $idp AND id_vendeur = $id_vendeur")) {
        $_SESSION['message'] = '<div style="color:green;margin-bottom:10px;text-align:center;">Produit supprimé avec succès !</div>';
    } else {
        $_SESSION['message'] = '<div style="color:red;margin-bottom:10px;text-align:center;">Erreur lors de la suppression du produit.</div>';
    }
    // Redirect to avoid infinite refresh
    header("Location: products.php");
    exit();
}

// Handle product modification
if (isset($_POST['modifier_produit'])) {
    $idp = intval($_POST['id_produit']);
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $prix = floatval($_POST['prix']);
    $quantite_stock = intval($_POST['quantite_stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoTmpPath = $_FILES['photo']['tmp_name'];
        $photoName = basename($_FILES['photo']['name']);
        $photoPath = 'uploads/' . uniqid() . '_' . $photoName;
        move_uploaded_file($photoTmpPath, __DIR__ . '/uploads/' . uniqid() . '_' . $photoName); // Utilisation de __DIR__ pour garantir le bon emplacement
    }

    $sql = "UPDATE produit SET nom='$nom', prix=$prix, quantite_stock=$quantite_stock, description='$description'";
    if ($photoPath) {
        $sql .= ", photo='$photoPath'";
    }
    $sql .= " WHERE id=$idp AND id_vendeur=$id_vendeur";

    if ($conn->query($sql)) {
        $_SESSION['message'] = '<div style="color:green;margin-bottom:10px;">Produit modifié avec succès !</div>';
    } else {
        $_SESSION['message'] = '<div style="color:red;margin-bottom:10px;">Erreur SQL : ' . $conn->error . '</div>';
    }
    // Redirect to avoid infinite refresh
    header("Location: products.php");
    exit();
}
?>
<?php include 'header.php'; ?>
<div class="content" style="background:#f8fafc;padding:32px 24px 40px 24px;border-radius:16px;box-shadow:0 4px 18px rgba(25,118,210,0.07);max-width:1100px;margin:32px auto 0 auto;">
    <h2 style="color:#1976d2;text-align:center;margin-bottom:32px;font-size:2.1rem;letter-spacing:1px;">Mes produits</h2>
    <button id="openAddProductModal" class="add-product-btn">+ Ajouter un produit</button>
    <!-- Modal pour ajouter un produit -->
    <div id="addProductModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:1000;align-items:center;justify-content:center;">
        <div style="background:white;padding:32px 28px 24px 28px;border-radius:14px;max-width:420px;width:95vw;box-shadow:0 8px 32px rgba(25,118,210,0.18);position:relative;">
            <span onclick="closeAddProductModal()" style="position:absolute;top:12px;right:18px;font-size:22px;cursor:pointer;color:#888;">&times;</span>
            <h3 style="color:#1976d2;text-align:center;margin-bottom:18px;">Ajouter un produit</h3>
            <form id="addProductForm" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nom du produit</label>
                    <input type="text" name="nom" required style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;">
                </div>
                <div class="form-group">
                    <label>Prix (€)</label>
                    <input type="number" step="0.01" name="prix" required style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;">
                </div>
                <div class="form-group">
                    <label>Quantité en stock</label>
                    <input type="number" name="quantite_stock" required style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;min-height:70px;"></textarea>
                </div>
                <div class="form-group">
                    <label>Photo du produit</label>
                    <input type="file" name="photo" accept="image/*" style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;">
                </div>
                <button type="submit" name="ajouter_produit" class="submit-btn" style="width:100%;margin-top:10px;background:#1976d2;color:white;padding:10px 0;border:none;border-radius:7px;font-size:16px;">Ajouter</button>
            </form>
        </div>
    </div>
    <script>
    function openAddProductModal() {
        document.getElementById('addProductModal').style.display = 'flex';
    }
    function closeAddProductModal() {
        document.getElementById('addProductModal').style.display = 'none';
    }
    document.getElementById('openAddProductModal').addEventListener('click', openAddProductModal);
    window.onclick = function(event) {
        var modal = document.getElementById('addProductModal');
        if (event.target === modal) {
            closeAddProductModal();
        }
    };
    </script>
    <div style="overflow-x:auto;">
    <table style="width:100%;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.07);overflow:hidden;border-collapse:separate;border-spacing:0;">
        <thead style="background:#1976d2;color:#fff;">
            <tr style="font-size:1.08rem;">
                <th style="padding:16px 10px;">Nom</th>
                <th>Prix (€)</th>
                <th>Quantité</th>
                <th>Description</th>
                <th>Photo</th>
                <th style="width:160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Traitement de l'ajout de produit
        if (isset($_POST['ajouter_produit'])) {
            $nom = mysqli_real_escape_string($conn, $_POST['nom']);
            $prix = floatval($_POST['prix']);
            $quantite_stock = intval($_POST['quantite_stock']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);

            $photoPath = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photoTmpPath = $_FILES['photo']['tmp_name'];
                $photoName = basename($_FILES['photo']['name']);
                $uploadDir = __DIR__ . '/uploads';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $uniqueFileName = uniqid() . '_' . $photoName;
                $photoPath = 'Seller/uploads/' . $uniqueFileName;
                move_uploaded_file($photoTmpPath, $uploadDir . '/' . $uniqueFileName);
            }

            // Fix: match DB columns (category, image) and not 'photo'
            $category = null;
            $image = $photoPath;
            $sql = "INSERT INTO produit (nom, description, prix, quantite_stock, id_vendeur, category, image) VALUES ('$nom', '$description', $prix, $quantite_stock, $id_vendeur, " . ($category ? "'$category'" : "NULL") . ", " . ($image ? "'$image'" : "NULL") . ")";
            if ($conn->query($sql)) {
                $_SESSION['message'] = '<div style="color:green;margin-bottom:10px;">Produit ajouté avec succès !</div>';
                header("Location: products.php");
                exit();
            } else {
                echo '<div style="color:red;margin-bottom:10px;">Erreur lors de l\'ajout du produit : ' . $conn->error . '</div>';
            }
        }
        // Affichage des produits
        $res = $conn->query("SELECT * FROM produit WHERE id_vendeur = $id_vendeur");
        if ($res && $res->num_rows > 0) {
            while ($prod = $res->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($prod['nom']) . '</td>';
                echo '<td>' . number_format($prod['prix'],2) . '</td>';
                echo '<td>' . intval($prod['quantite_stock']) . '</td>';
                echo '<td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' . htmlspecialchars($prod['description']) . '</td>';
                echo '<td>';
                // Update the image path to include the base URL
                if (!empty($prod['photo'])) {
                    $imageUrl = 'http://localhost/Marketplace/' . htmlspecialchars($prod['photo']);
                    echo '<img src="' . $imageUrl . '" alt="Photo du produit" style="max-width:80px;max-height:80px;border-radius:8px;">';
                } else {
                    echo '<span style="color:#888;">Pas de photo</span>'; // Message par défaut si aucune photo n'est disponible
                }
                echo '</td>';
                echo '<td style="text-align:center;">';
                echo '<div style="display:flex;justify-content:center;gap:10px;">';
                echo '<button type="button" style="background:#e0e0e0;color:#333;padding:7px 16px;border:none;border-radius:4px;font-size:1rem;cursor:pointer;" onclick="if(confirm(\'Supprimer ce produit ?\')){window.location=\'?supprimer=' . $prod['id'] . '\';}">Supprimer</button>';
                echo '<button class="edit-btn" data-id="' . $prod['id'] . '" data-nom="' . htmlspecialchars($prod['nom']) . '" data-prix="' . $prod['prix'] . '" data-quantite="' . $prod['quantite_stock'] . '" data-description="' . htmlspecialchars($prod['description']) . '" style="background:#e3f2fd;color:#1976d2;padding:7px 16px;border:none;border-radius:4px;font-size:1rem;cursor:pointer;">Modifier</button>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6" style="color:#888;text-align:center;">Aucun produit pour le moment.</td></tr>';
        }
        ?>
        </tbody>
    </table>
    </div>
    <!-- Modal de modification -->
    <div id="editModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:1000;align-items:center;justify-content:center;">
        <form method="POST" enctype="multipart/form-data" style="background:#fff;padding:32px 32px 24px 32px;border-radius:14px;min-width:320px;max-width:95vw;box-shadow:0 6px 24px rgba(25,118,210,0.13);display:flex;flex-direction:column;gap:18px;" onsubmit="document.getElementById('editModal').style.display='none';">
            <h3 style="color:#1976d2;text-align:center;margin-bottom:10px;">Modifier le produit</h3>
            <input type="hidden" name="id_produit" id="edit_id_produit">
            <div style="display:flex;gap:14px;">
                <div style="flex:1;display:flex;flex-direction:column;">
                    <label style="font-weight:600;color:#333;">Nom</label>
                    <input type="text" name="nom" id="edit_nom" required style="padding:10px 12px;border-radius:7px;border:1px solid #bdbdbd;">
                </div>
                <div style="flex:1;display:flex;flex-direction:column;">
                    <label style="font-weight:600;color:#333;">Prix (€)</label>
                    <input type="number" step="0.01" name="prix" id="edit_prix" required style="padding:10px 12px;border-radius:7px;border:1px solid #bdbdbd;">
                </div>
                <div style="flex:1;display:flex;flex-direction:column;">
                    <label style="font-weight:600;color:#333;">Quantité</label>
                    <input type="number" name="quantite_stock" id="edit_quantite" required style="padding:10px 12px;border-radius:7px;border:1px solid #bdbdbd;">
                </div>
            </div>
            <div style="display:flex;flex-direction:column;">
                <label style="font-weight:600;color:#333;">Description</label>
                <textarea name="description" id="edit_description" required style="width:100%;padding:10px 12px;border-radius:7px;border:1px solid #bdbdbd;min-height:60px;"></textarea>
            </div>
            <div style="display:flex;flex-direction:column;">
                <label style="font-weight:600;color:#333;">Photo du produit</label>
                <input type="file" name="photo" accept="image/*" style="padding:10px 12px;border-radius:7px;border:1px solid #bdbdbd;">
            </div>
            <div style="text-align:right;">
                <button type="button" onclick="document.getElementById('editModal').style.display='none';" style="background:#bdbdbd;color:#333;padding:10px 22px;border:none;border-radius:7px;margin-right:10px;font-size:1rem;">Annuler</button>
                <button type="submit" name="modifier_produit" style="background:#1976d2;color:white;padding:10px 22px;border:none;border-radius:7px;font-size:1rem;">Enregistrer</button>
            </div>
        </form>
    </div>
    <style>
    .add-product-btn {
        background: #1976d2;
        color: white;
        padding: 13px 30px;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 24px;
        cursor: pointer;
        transition: background 0.2s;
        box-shadow:0 2px 8px rgba(25,118,210,0.08);
    }
    .add-product-btn:hover {
        background: #1251a3;
    }
    table th, table td {
        text-align:center;
        font-size:1.05rem;
    }
    table tr {
        transition: background 0.15s;
    }
    table tbody tr:hover {
        background: #f1f7fd;
    }
    table td {
        padding: 13px 8px;
    }
    table thead th {
        border-bottom:2px solid #1976d2;
    }
    .action-btn {
        padding: 8px 18px;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        margin: 0 4px;
        cursor: pointer;
        transition: background 0.18s, color 0.18s;
        box-shadow: 0 1px 4px rgba(25,118,210,0.07);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .delete-btn {
        background: #fbe9e7;
        color: #d32f2f;
    }
    .delete-btn:hover {
        background: #d32f2f;
        color: #fff;
    }
    .edit-btn {
        background: #e3f2fd;
        color: #1976d2;
    }
    .edit-btn:hover {
        background: #1976d2;
        color: #fff;
    }
    </style>
    <script>
    // Function to open the edit modal and populate it with product data
    function openEditModal(product) {
        document.getElementById('edit_id_produit').value = product.id;
        document.getElementById('edit_nom').value = product.nom;
        document.getElementById('edit_prix').value = product.prix;
        document.getElementById('edit_quantite').value = product.quantite;
        document.getElementById('edit_description').value = product.description;
        document.getElementById('editModal').style.display = 'flex';
    }

    // Close the edit modal
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Attach click event listeners to all "Modifier" buttons
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const product = {
                id: button.getAttribute('data-id'),
                nom: button.getAttribute('data-nom'),
                prix: button.getAttribute('data-prix'),
                quantite: button.getAttribute('data-quantite'),
                description: button.getAttribute('data-description')
            };
            openEditModal(product);
        });
    });

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            closeEditModal();
        }
    };
    </script>
    <?php
    // Traitement des messages de session
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>
</div>