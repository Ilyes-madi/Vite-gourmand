<?php
require_once 'auth.php';
requireLogin();
require_once 'db.php';
require_once 'partials/header.php';
$userId = (int)$_SESSION['user_id'];


$sql = "
    SELECT 
        c.id,
        c.status_current,
        c.created_at,
        c.event_date,
        c.event_time,
        c.people_count,
        c.price_total,
        m.title AS menu_title
    FROM commandes c
    JOIN menus m ON m.id = c.menu_id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);


$avisStmt = $pdo->prepare("SELECT id FROM avis WHERE commande_id = ?");
?>
<link rel="stylesheet" href="assets/css/style.css">
<h2>Mes commandes</h2>

<?php if (empty($commandes)): ?>
    <p>Aucune commande pour le moment.</p>
<?php else: ?>

<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>Menu</th>
            <th>Date</th>
            <th>Heure</th>
            <th>Personnes</th>
            <th>Prix</th>
            <th>Statut</th>
            <th>Historique</th>
            <th>Annulation</th>
            <th>Avis</th>
        </tr>
    </thead>
    <tbody>

    <?php foreach ($commandes as $commande): ?>

        <?php
       
        $avisStmt->execute([$commande['id']]);
        $hasAvis = $avisStmt->fetch();

        
        $canCancel = !in_array($commande['status_current'], ['acceptée', 'annulée', 'terminée'], true);
        ?>

        <tr>
            <td><?= htmlspecialchars($commande['menu_title']) ?></td>
            <td><?= htmlspecialchars($commande['event_date']) ?></td>
            <td><?= htmlspecialchars($commande['event_time']) ?></td>
            <td><?= (int)$commande['people_count'] ?></td>
            <td><?= number_format((float)$commande['price_total'], 2, ',', ' ') ?> €</td>
            <td><strong><?= htmlspecialchars($commande['status_current']) ?></strong></td>

           
            <td>
                <a href="commande_historique.php?id=<?= (int)$commande['id'] ?>">
                    Voir
                </a>
            </td>

            
            <td>
                <?php if ($canCancel): ?>
                    <form method="POST" action="commande_cancel.php">
                        <input type="hidden" name="commande_id" value="<?= (int)$commande['id'] ?>">
                        <input type="text" name="cancel_reason" placeholder="Motif" required>
                        <input type="hidden" name="contact_mode" value="site">
                        <button type="submit">Annuler</button>
                    </form>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>

            
            <td>
                <?php if ($commande['status_current'] === 'terminée' && !$hasAvis): ?>
                    <a href="avis_create.php?commande_id=<?= (int)$commande['id'] ?>">
                        Donner un avis
                    </a>
                <?php elseif ($hasAvis): ?>
                    Avis envoyé
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>

    <?php endforeach; ?>

    </tbody>
</table>

<?php endif; ?>