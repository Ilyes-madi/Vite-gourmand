<?php
require_once 'auth.php';
requireAdmin();
require_once 'db.php';
require_once 'partials/header.php';


$sql = "
    SELECT 
        id,
        title,
        theme,
        diet,
        min_people,
        base_price,
        stock,
        created_at
    FROM menus
    ORDER BY created_at DESC
";

$stmt = $pdo->query($sql);
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Liste des menus</h2>

<p>
    <a href="admin.php">← Retour admin</a> |
    <a href="admin_menu_create.php">Ajouter un menu</a>
</p>

<?php if (empty($menus)): ?>
    <p>Aucun menu enregistré.</p>
<?php else: ?>
<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Thème</th>
            <th>Régime</th>
            <th>Min pers.</th>
            <th>Prix (€)</th>
            <th>Stock</th>
            <th>Créé le</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($menus as $menu): ?>
            <tr>
                <td><?= (int)$menu['id'] ?></td>
                <td><?= htmlspecialchars($menu['title']) ?></td>
                <td><?= htmlspecialchars($menu['theme']) ?></td>
                <td><?= htmlspecialchars($menu['diet']) ?></td>
                <td><?= (int)$menu['min_people'] ?></td>
                <td><?= number_format((float)$menu['base_price'], 2, ',', ' ') ?> €</td>
                <td><?= (int)$menu['stock'] ?></td>
                <td><?= htmlspecialchars($menu['created_at']) ?></td>
                <td>
                    <a href="admin_menu_edit.php?id=<?= (int)$menu['id'] ?>">Modifier</a>
                    |
                    <a href="admin_menu_delete.php?id=<?= (int)$menu['id'] ?>"
                       onclick="return confirm('Supprimer ce menu ?');">
                        Supprimer
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require_once 'partials/footer.php'; ?>