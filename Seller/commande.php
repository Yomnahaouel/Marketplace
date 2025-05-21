<?php
ob_start();
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
<div class="content">
    <h2 style="color:#1976d2;text-align:center;margin-bottom:32px;font-size:2.1rem;letter-spacing:1px;">Mes Commandes</h2>
    <div style="overflow-x:auto;">
        <?php
        // Get all commandes for this seller (grouped by commande)
        $sql_commandes = "SELECT DISTINCT c.id, c.date_commande, c.statut FROM commande c JOIN commande_items ci ON c.id = ci.commande_id JOIN produit p ON ci.product_id = p.id WHERE p.id_vendeur = $id_vendeur ORDER BY c.date_commande DESC";
        $result_commandes = $conn->query($sql_commandes);
        if ($result_commandes && $result_commandes->num_rows > 0) {
            while ($commande = $result_commandes->fetch_assoc()) {
                echo '<div style="background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.07);margin-bottom:32px;padding:18px 22px 10px 22px;">';
                echo '<div style="font-size:1.13rem;font-weight:500;color:#1976d2;margin-bottom:8px;">Commande #'.htmlspecialchars($commande['id']).' | Date: '.htmlspecialchars($commande['date_commande']).' </span></div>';
                // Items for this commande
                $commande_id = $commande['id'];
                $sql_items = "SELECT p.nom AS produit_nom, ci.quantity, ci.price FROM commande_items ci JOIN produit p ON ci.product_id = p.id WHERE ci.commande_id = $commande_id";
                $result_items = $conn->query($sql_items);
                if ($result_items && $result_items->num_rows > 0) {
                    echo '<table style="width:100%;margin-bottom:10px;border-radius:8px;overflow:hidden;border-collapse:separate;border-spacing:0;">';
                    echo '<thead style="background:#f2f6fa;color:#1976d2;">';
                    echo '<tr style="font-size:1.05rem;">';
                    echo '<th style="padding:10px 8px;">Produit</th>';
                    echo '<th>Quantité</th>';
                    echo '<th>Prix unitaire</th>';
                    echo '<th>Prix total</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    $total_commande = 0;
                    while ($item = $result_items->fetch_assoc()) {
                        $total = $item['price'] * $item['quantity'];
                        $total_commande += $total;
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($item['produit_nom']) . '</td>';
                        echo '<td>' . htmlspecialchars($item['quantity']) . '</td>';
                        echo '<td>' . number_format($item['price'], 2) . ' TND</td>';
                        echo '<td>' . number_format($total, 2) . ' TND</td>';
                        echo '</tr>';
                    }
                    echo '<tr style="background:#f9fafb;font-weight:500;color:#1976d2;">';
                    echo '<td colspan="3" style="text-align:right;padding-right:12px;">Total commande:</td>';
                    echo '<td>' . number_format($total_commande, 2) . ' TND</td>';
                    echo '</tr>';
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<div style="color:#888;margin-bottom:10px;">Aucun article pour cette commande.</div>';
                }
                echo '</div>';
            }
        } else {
            echo '<div style="color:#888;text-align:center;">Aucune commande trouvée.</div>';
        }
        ?>
    </div>
</div>
