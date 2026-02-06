<?php
require_once '../auth.php';
requireAdmin();
require_once '../db.php';

$stmt = $pdo->query("
    SELECT id, name, type, description
    FROM plats
    ORDER BY id DESC
");
$plats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des plats</title>
</head>
<body>

<h1>Liste des plats</h1>

<p>
    <a href="../admin.php">← Retour admin</a> |
    <a href="admin_plat_create.php">Ajouter un plat</a>
</p>

<?php if (empty($plats)): ?>
    <p>Aucun plat pour le moment.</p>
<?php else: ?>
<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Type</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($plats as $plat): ?>
            <tr>
                <td><?= $plat['id'] ?></td>
                <td><?= htmlspecialchars($plat['name']) ?></td>
                <td><?= htmlspecialchars($plat['type']) ?></td>
                <td><?= htmlspecialchars($plat['description']) ?></td>
                <td>
                    <a href="admin_plat_edit.php?id=<?= $plat['id'] ?>">Modifier</a>

                    <form action="admin_plat_delete.php" method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $plat['id'] ?>">
                        <button type="submit"
                                onclick="return confirm('Supprimer ce plat ?')">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>