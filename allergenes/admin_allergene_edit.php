<?php
require_once __DIR__ . '/../auth.php';
requireAdmin();
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  header("Location: admin_allergenes.php");
  exit;
}

$stmt = $pdo->prepare("SELECT id, name FROM allergenes WHERE id = ?");
$stmt->execute([$id]);
$allergene = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$allergene) {
  header("Location: admin_allergenes.php");
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');

  if ($name === '') {
    $error = "Le nom est obligatoire.";
  } else {
    $stmt = $pdo->prepare("UPDATE allergenes SET name = ? WHERE id = ?");
    $stmt->execute([$name, $id]);
    header("Location: admin_allergenes.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier allergène</title>
</head>
<body>
  <h1>Modifier allergène</h1>
  <p><a href="admin_allergenes.php">← Retour</a></p>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Nom</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($allergene['name']) ?>" required><br><br>
    <button type="submit">Enregistrer</button>
  </form>
</body>
</html>