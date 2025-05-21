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
<?php include 'header.php'; ?>
<link rel="stylesheet" href="ads.css">
<div class="content" style="background:#f8fafc;padding:32px 24px 40px 24px;border-radius:16px;box-shadow:0 4px 18px rgba(25,118,210,0.07);max-width:1100px;margin:32px auto 0 auto;">
    <h2 style="color:#1976d2;text-align:center;margin-bottom:32px;font-size:2.1rem;letter-spacing:1px;">Mes Publicités</h2>
    <button id="openAddAdModal" class="add-product-btn">+ Ajouter une publicité</button>
    <!-- Modal pour ajouter une publicité -->
    <div id="addAdModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:1000;align-items:center;justify-content:center;">
        <div style="background:white;padding:32px 28px 24px 28px;border-radius:14px;max-width:420px;width:95vw;box-shadow:0 8px 32px rgba(25,118,210,0.18);position:relative;">
            <span onclick="closeAddAdModal()" style="position:absolute;top:12px;right:18px;font-size:22px;cursor:pointer;color:#888;">&times;</span>
            <h3 style="color:#1976d2;text-align:center;margin-bottom:18px;">Ajouter une publicité</h3>
            <form id="addAdForm" method="POST">
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
                    <label>Date début</label>
                    <input type="date" name="date_debut" required style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;">
                </div>
                <div class="form-group">
                    <label>Date fin</label>
                    <input type="date" name="date_fin" required style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;">
                </div>
                <div class="form-group">
                    <label>Budget (TND)</label>
                    <input type="number" step="0.01" min="0" name="budget" required style="width:100%;padding:10px 12px;border:1px solid #bdbdbd;border-radius:7px;">
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
    <div style="overflow-x:auto;">
        <table style="width:100%;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.07);overflow:hidden;border-collapse:separate;border-spacing:0;">
            <thead style="background:#1976d2;color:#fff;">
                <tr style="font-size:1.08rem;">
                    <th style="padding:16px 10px;">Produit</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Statut</th>
                    <th>Budget</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $ads = $conn->query("SELECT p.*, pr.nom AS produit_nom, pr.image AS produit_image FROM publicite p JOIN produit pr ON p.id_produit = pr.id WHERE pr.id_vendeur = $id_vendeur ORDER BY p.id DESC");
            if ($ads && $ads->num_rows > 0) {
                while ($ad = $ads->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($ad['produit_nom']) . '</td>';
                    echo '<td>' . htmlspecialchars($ad['date_debut']) . '</td>';
                    echo '<td>' . htmlspecialchars($ad['date_fin']) . '</td>';
                    echo '<td>' . htmlspecialchars($ad['statut']) . '</td>';
                    echo '<td>' . number_format($ad['budget'], 2) . ' TND</td>';
                    echo '<td>';
                    if (!empty($ad['produit_image'])) {
                        $imageUrl = 'http://localhost/Marketplace/' . htmlspecialchars($ad['produit_image']);
                        echo '<img src="' . $imageUrl . '" alt="Image produit" style="max-width:80px;max-height:80px;border-radius:8px;">';
                    } else {
                        echo '<span style="color:#888;">Pas d\'image</span>';
                    }
                    echo '</td>';
                    // Actions: Edit & Delete
                    echo '<td>';
                    echo '<button class="edit-ad-btn" data-id="' . $ad['id'] . '" data-date_debut="' . htmlspecialchars($ad['date_debut']) . '" data-date_fin="' . htmlspecialchars($ad['date_fin']) . '" data-budget="' . $ad['budget'] . '" style="margin-right:7px;background:#ffb300;color:#fff;border:none;padding:7px 18px;border-radius:6px;cursor:pointer;font-weight:600;box-shadow:0 2px 8px rgba(255,179,0,0.10);transition:background 0.18s;">';
                    echo '<span style="display:inline-flex;align-items:center;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="margin-right:5px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-1.414.828l-4.243 1.414 1.414-4.243a4 4 0 01.828-1.414z"/></svg>Éditer</span>';
                    echo '</button>';
                    echo '<form method="POST" style="display:inline;" onsubmit="return confirm(\'Supprimer cette publicité ?\');">';
                    echo '<input type="hidden" name="delete_ad_id" value="' . $ad['id'] . '">';
                    echo '<button type="submit" class="delete-ad-btn" style="background:#e53935;color:white;border:none;padding:6px 14px;border-radius:6px;cursor:pointer;">Supprimer</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7" style="color:#888;text-align:center;">Aucune publicité pour le moment.</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
    <!-- Modal de modification -->
    <div id="editAdModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:1000;align-items:center;justify-content:center;">
        <div style="background:white;padding:32px 28px 24px 28px;border-radius:14px;max-width:420px;width:95vw;box-shadow:0 8px 32px rgba(25,118,210,0.18);position:relative;">
            <span onclick="closeEditAdModal()" style="position:absolute;top:12px;right:18px;font-size:22px;cursor:pointer;color:#888;">&times;</span>
            <h3 style="color:#1976d2;text-align:center;margin-bottom:18px;">Modifier la publicité</h3>
            <form id="editAdForm" method="POST">
                <input type="hidden" name="edit_ad_id" id="edit_ad_id">
                <div class="form-group">
                    <label>Date début</label>
                    <input type="date" name="edit_date_debut" id="edit_date_debut" required>
                </div>
                <div class="form-group">
                    <label>Date fin</label>
                    <input type="date" name="edit_date_fin" id="edit_date_fin" required>
                </div>
                <div class="form-group">
                    <label>Budget (TND)</label>
                    <input type="number" step="0.01" min="0" name="edit_budget" id="edit_budget" required>
                </div>
                <button type="submit" name="update_ad" class="submit-btn" style="width:100%;margin-top:10px;background:#1976d2;color:white;padding:10px 0;border:none;border-radius:7px;font-size:16px;">Enregistrer</button>
            </form>
        </div>
    </div>
    <script>
    // Edit button click handler
    const editBtns = document.querySelectorAll('.edit-ad-btn');
    const editAdModal = document.getElementById('editAdModal');
    const editAdForm = document.getElementById('editAdForm');
    function openEditAdModal(id, date_debut, date_fin, budget) {
        document.getElementById('edit_ad_id').value = id;
        document.getElementById('edit_date_debut').value = date_debut;
        document.getElementById('edit_date_fin').value = date_fin;
        document.getElementById('edit_budget').value = budget;
        editAdModal.style.display = 'flex';
    }
    function closeEditAdModal() {
        editAdModal.style.display = 'none';
    }
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            openEditAdModal(
                this.getAttribute('data-id'),
                this.getAttribute('data-date_debut'),
                this.getAttribute('data-date_fin'),
                this.getAttribute('data-budget')
            );
        });
    });
    window.onclick = function(event) {
        if (event.target === editAdModal) {
            closeEditAdModal();
        }
    };
    </script>
    <div>
    <?php
    // Traitement de l'ajout de publicité
    if (isset($_POST['ajouter_publicite'])) {
        $produit_id = intval($_POST['produit_id']);
        $date_debut = isset($_POST['date_debut']) ? mysqli_real_escape_string($conn, $_POST['date_debut']) : '';
        $date_fin = isset($_POST['date_fin']) ? mysqli_real_escape_string($conn, $_POST['date_fin']) : '';
        $budget = isset($_POST['budget']) ? floatval($_POST['budget']) : 0.0;
        if ($date_debut && $date_fin) {
            $sql = "INSERT INTO publicite (id_produit, date_debut, date_fin, statut, budget) VALUES ($produit_id, '$date_debut', '$date_fin', 'En attente', $budget)";
            if ($conn->query($sql)) {
                $_SESSION['message'] = '<div style=\"color:green;margin-bottom:10px;\">Publicité ajoutée avec succès !</div>';
                header("Location: ads.php");
                exit();
            } else {
                echo '<div style="color:red;margin-bottom:10px;">Erreur lors de l\'ajout de la publicité : ' . $conn->error . '</div>';
            }
        } else {
            echo '<div style="color:red;margin-bottom:10px;">Veuillez remplir tous les champs obligatoires.</div>';
        }
    }

    if (isset($_POST['update_ad'])) {
        $ad_id = intval($_POST['edit_ad_id']);
        $date_debut = mysqli_real_escape_string($conn, $_POST['edit_date_debut']);
        $date_fin = mysqli_real_escape_string($conn, $_POST['edit_date_fin']);
        $budget = floatval($_POST['edit_budget']);
        $conn->query("UPDATE publicite SET date_debut='$date_debut', date_fin='$date_fin', budget=$budget WHERE id = $ad_id AND id_produit IN (SELECT id FROM produit WHERE id_vendeur = $id_vendeur)");
        $_SESSION['message'] = '<div style="color:green;margin-bottom:10px;">Publicité modifiée avec succès !</div>';
        header("Location: ads.php");
        exit();
    }

    if (isset($_POST['delete_ad_id'])) {
        $ad_id = intval($_POST['delete_ad_id']);
        $conn->query("DELETE FROM publicite WHERE id = $ad_id AND id_produit IN (SELECT id FROM produit WHERE id_vendeur = $id_vendeur)");
        $_SESSION['message'] = '<div style="color:green;margin-bottom:10px;">Publicité supprimée avec succès !</div>';
        header("Location: ads.php");
        exit();
    }

    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>
    </div>
</div>
