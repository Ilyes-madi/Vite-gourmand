<?php
require_once 'auth.php';
requireEmploye();
require_once 'db.php';
require_once 'partials/header.php';
$sql = "
    SELECT 
        c.id,
        c.status_current,
        c.created_at,
        u.email AS client_email,
        m.title AS menu_title
    FROM commandes c
    JOIN users u ON u.id = c.user_id
    JOIN menus m ON m.id = c.menu_id
    ORDER BY c.created_at DESC
";

$stmt = $pdo->query($sql);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Espace Employé – Commandes</h2>
<link rel="stylesheet" href="assets/css/style.css">
<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Menu</th>
            <th>Statut</th>
            <th>Changer statut</th>
            <th>Historique</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($commandes as $c): ?>
            <tr>
                <td><?= (int)$c['id'] ?></td>
                <td><?= htmlspecialchars($c['client_email']) ?></td>
                <td><?= htmlspecialchars($c['menu_title']) ?></td>
                <td><strong><?= htmlspecialchars($c['status_current']) ?></strong></td>

                <td>
                    <form method="POST" action="employe_update_status.php">
                        <input type="hidden" name="commande_id" value="<?= (int)$c['id'] ?>">

                        <select name="new_status" required>
                            <option value="">-- Choisir --</option>

                            <?php
                            $statuts = ['acceptée', 'refusée', 'en_preparation', 'terminée'];
                            foreach ($statuts as $status):
                            ?>
                                <option value="<?= $status ?>">
                                    <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit">Valider</button>
                    </form>
                </td>

                <td>
                    <a href="commande_historique.php?id=<?= (int)$c['id'] ?>">
                        Voir
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>