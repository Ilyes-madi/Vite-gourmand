<?php
require_once 'auth.php';
require_once 'db.php';
require_once 'partials/header.php';

if (!isset($_GET['id'])) {
    header('Location: admin_menus.php');
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT id, title, theme, diet, min_people, base_price, stock
    FROM menus
    WHERE id = ?
");
$stmt->execute([$id]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    header('Location: admin_menus.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("
        UPDATE menus
        SET title = ?, theme = ?, diet = ?, min_people = ?, base_price = ?, stock = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $_POST['title'],
        $_POST['theme'],
        $_POST['diet'],
        $_POST['min_people'],
        $_POST['base_price'],
        $_POST['stock'],
        $id
    ]);

    header('Location: admin_menus.php');
    exit;
}
?>

<h2>Modifier le menu</h2>

<form method="post">
    <label>Titre</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($menu['title']) ?>" required><br><br>

    <label>Thème</label><br>
    <input type="text" name="theme" value="<?= htmlspecialchars($menu['theme']) ?>"><br><br>

    <label>Régime</label><br>
    <input type="text" name="diet" value="<?= htmlspecialchars($menu['diet']) ?>"><br><br>

    <label>Minimum de personnes</label><br>
    <input type="number" name="min_people" value="<?= $menu['min_people'] ?>" required><br><br>

    <label>Prix de base (€)</label><br>
    <input type="number" step="0.01" name="base_price" value="<?= $menu['base_price'] ?>" required><br><br>

    <label>Stock</label><br>
    <input type="number" name="stock" value="<?= $menu['stock'] ?>" required><br><br>

    <button type="submit">Enregistrer</button>
</form>