<?php
require_once 'auth.php';
requireEmploye();
require_once 'db.php';

$stmt = $pdo->query("
    SELECT 
        a.id,
        a.rating,
        a.comment,
        a.created_at,
        u.email AS user_email,
        c.id AS commande_id,
        m.title AS menu_title
    FROM avis a
    JOIN users u ON u.id = a.user_id
    JOIN commandes c ON c.id = a.commande_id
    JOIN menus m ON m.id = c.menu_id
    WHERE a.is_approved = 0
    ORDER BY a.created_at DESC
");
$avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Avis en attente</h2>

<?php if (empty($avis)): ?>
    <p>Aucun avis à modérer.</p>
<?php else: ?>
<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>Commande</th>
            <th>Client</th>
            <th>Menu</th>
            <th>Note</th>
            <th>Commentaire</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($avis as $a): ?>
        <tr>
            <td>#<?= (int)$a['commande_id'] ?></td>
            <td><?= htmlspecialchars($a['user_email']) ?></td>
            <td><?= htmlspecialchars($a['menu_title']) ?></td>
            <td><?= (int)$a['rating'] ?>/5</td>
            <td><?= htmlspecialchars($a['comment']) ?></td>
            <td>
                <form method="post" action="employe_avis_update.php" style="display:inline;">
                    <input type="hidden" name="avis_id" value="<?= (int)$a['id'] ?>">
                    <input type="hidden" name="action" value="approve">
                    <button type="submit">Valider</button>
                </form>
                <form method="post" action="employe_avis_update.php" style="display:inline;">
                    <input type="hidden" name="avis_id" value="<?= (int)$a['id'] ?>">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit">Refuser</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>