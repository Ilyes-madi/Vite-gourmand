<?php
require_once 'auth.php';
requireAdmin();
require_once 'db.php';
require_once 'partials/header.php';
require_once 'partials/footer.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $theme = trim($_POST['theme'] ?? '');
    $diet = trim($_POST['diet'] ?? '');
    $min_people = (int) ($_POST['min_people'] ?? 0);
    $base_price = (float) ($_POST['base_price'] ?? 0);
    $conditions = trim($_POST['conditions'] ?? '');
    $stock = (int) ($_POST['stock'] ?? 0);
    $created_by = $_SESSION['user_id'];

    
    if ($title === '') $errors[] = "Le titre est obligatoire.";
    if ($base_price <= 0) $errors[] = "Le prix doit être supérieur à 0.";
    if ($min_people <= 0) $errors[] = "Le nombre minimum de personnes est invalide.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO menus 
            (title, description, theme, diet, min_people, base_price, conditions, stock, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $title,
            $description,
            $theme,
            $diet,
            $min_people,
            $base_price,
            $conditions,
            $stock,
            $created_by
        ]);

        header('Location: admin_menus.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un menu</title>
</head>
<body>

<h1>Créer un menu</h1>

<p><a href="admin_menus.php">← Retour à la liste</a></p>

<?php if (!empty($errors)): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post">

    <p>
        <label>Titre</label><br>
        <input type="text" name="title" required>
    </p>

    <p>
        <label>Description</label><br>
        <textarea name="description"></textarea>
    </p>

    <p>
        <label>Thème</label><br>
        <input type="text" name="theme">
    </p>

    <p>
        <label>Régime</label><br>
        <input type="text" name="diet">
    </p>

    <p>
        <label>Nombre minimum de personnes</label><br>
        <input type="number" name="min_people" min="1" required>
    </p>

    <p>
        <label>Prix de base (€)</label><br>
        <input type="number" step="0.01" name="base_price" required>
    </p>

    <p>
        <label>Conditions</label><br>
        <textarea name="conditions"></textarea>
    </p>

    <p>
        <label>Stock</label><br>
        <input type="number" name="stock" min="0" value="0">
    </p>

    <button type="submit">Créer le menu</button>

</form>

</body>
</html>