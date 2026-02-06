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
        min_people AS min_persons,
        base_price AS price,
        stock,
        created_at
    FROM menus
";
$stmt = $pdo->query($sql);
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'menu_locked'): ?>
    <p style="padding:10px;background:#ffe7e7;border:1px solid #ffb3b3;">
        Impossible de supprimer : ce menu est déjà lié à une ou plusieurs commandes. Stock mis à 0.
    </p>
<?php endif; ?>
<h2>Liste des menus</h2>
<p><a href="admin_menu_create.php">Ajouter un menu</a></p>

<table border="1" cellpadding="8">
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
            <td><?= $menu['id'] ?></td>
            <td><?= htmlspecialchars($menu['title']) ?></td>
            <td><?= htmlspecialchars($menu['theme']) ?></td>
            <td><?= htmlspecialchars($menu['diet']) ?></td>
            <td><?= $menu['min_persons'] ?></td>
            <td><?= number_format($menu['price'], 2, ',', ' ') ?></td>
            <td><?= $menu['stock'] ?></td>
            <td><?= $menu['created_at'] ?></td>
            <td>
                <a href="admin_menu_edit.php?id=<?= $menu['id'] ?>">Modifier</a> |
                <a href="admin_menu_delete.php?id=<?= $menu['id'] ?>"
                   onclick="return confirm('Supprimer ce menu ?');">
                   Supprimer
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'partials/footer.php'; ?>