<?php
require_once '../auth.php';
requireAdmin();
require_once '../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '' || $type === '') {
        $error = "Nom et type obligatoires.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO plats (name, type, description)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$name, $type, $description !== '' ? $description : null]);

        header('Location: admin_plats.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un plat</title>
</head>
<body>

<h1>Ajouter un plat</h1>

<p><a href="admin_plats.php">← Retour liste des plats</a></p>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <p>
        <label>Nom *</label><br>
        <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
    </p>

    <p>
        <label>Type *</label><br>
        <input type="text" name="type" required placeholder="ex: entrée / plat / dessert"
               value="<?= htmlspecialchars($_POST['type'] ?? '') ?>">
    </p>

    <p>
        <label>Description</label><br>
        <textarea name="description" rows="4" cols="40"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </p>

    <button type="submit">Créer</button>
</form>

</body>
</html>