<?php
require_once __DIR__ . '/../auth.php';
requireAdmin();
require_once __DIR__ . '/../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');

  if ($name === '') {
    $error = "Le nom est obligatoire.";
  } else {
    $stmt = $pdo->prepare("INSERT INTO allergenes (name) VALUES (?)");
    $stmt->execute([$name]);
    header("Location: admin_allergenes.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un allergène</title>
</head>
<body>
  <h1>Ajouter un allergène</h1>
  <p><a href="admin_allergenes.php">← Retour</a></p>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Nom</label><br>
    <input type="text" name="name" required><br><br>
    <button type="submit">Créer</button>
  </form>
</body>
</html>