<?php
require_once '../auth.php';
requireAdmin();
require_once '../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: admin_plats.php');
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM plats WHERE id = ?");
$stmt->execute([$id]);
$plat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plat) {
    header('Location: admin_plats.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_plat'])) {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '' || $type === '') {
        $error = "Nom et type obligatoires.";
    } else {
        $stmt = $pdo->prepare("
            UPDATE plats
            SET name = ?, type = ?, description = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $type, $description, $id]);

        header('Location: admin_plats.php');
        exit;
    }
}


$stmt = $pdo->prepare("
    SELECT a.id, a.name
    FROM allergenes a
    INNER JOIN plat_allergenes pa ON pa.allergene_id = a.id
    WHERE pa.plat_id = ?
    ORDER BY a.name
");
$stmt->execute([$id]);
$allergenesDuPlat = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare("
    SELECT a.id, a.name
    FROM allergenes a
    WHERE a.id NOT IN (
        SELECT allergene_id FROM plat_allergenes WHERE plat_id = ?
    )
    ORDER BY a.name
");
$stmt->execute([$id]);
$allergenesDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un plat</title>
</head>
<body>

<h1>Modifier un plat</h1>

<p><a href="admin_plats.php">← Retour liste des plats</a></p>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="update_plat" value="1">

    <label>Nom</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($plat['name']) ?>" required><br><br>

    <label>Type</label><br>
    <input type="text" name="type" value="<?= htmlspecialchars($plat['type']) ?>" required><br><br>

    <label>Description</label><br>
    <textarea name="description" rows="4" cols="50"><?= htmlspecialchars($plat['description'] ?? '') ?></textarea><br><br>

    <button type="submit">Enregistrer</button>
</form>

<hr>

<h2>Allergènes du plat</h2>

<?php if (empty($allergenesDuPlat)): ?>
    <p>Aucun allergène associé.</p>
<?php else: ?>
    <ul>
        <?php foreach ($allergenesDuPlat as $a): ?>
            <li>
                <?= htmlspecialchars($a['name']) ?>
                <form method="post" action="../relations/plat_allergenes_delete.php" style="display:inline;">
                    <input type="hidden" name="plat_id" value="<?= (int)$id ?>">
                    <input type="hidden" name="allergene_id" value="<?= (int)$a['id'] ?>">
                    <button type="submit">Retirer</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h3>Ajouter un allergène</h3>

<?php if (empty($allergenesDisponibles)): ?>
    <p>Tous les allergènes sont déjà associés.</p>
<?php else: ?>
    <form method="post" action="../relations/plat_allergenes_add.php">
        <input type="hidden" name="plat_id" value="<?= (int)$id ?>">

        <select name="allergene_id" required>
            <?php foreach ($allergenesDisponibles as $a): ?>
                <option value="<?= (int)$a['id'] ?>">
                    <?= htmlspecialchars($a['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Ajouter</button>
    </form>
<?php endif; ?>

</body>
</html>