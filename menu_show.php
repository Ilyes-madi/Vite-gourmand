<?php
require_once 'auth.php';
require_once 'db.php';
require_once 'partials/header.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
  echo "<p>Menu invalide.</p>";
  require_once 'partials/footer.php';
  exit;
}

$menuId = (int)$_GET['id'];


$sql = "
  SELECT
    id,
    title,
    theme,
    diet,
    min_people,
    base_price,
    stock
  FROM menus
  WHERE id = :id
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $menuId]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
  echo "<p>Menu introuvable.</p>";
  require_once 'partials/footer.php';
  exit;
}
?>

<h2><?= htmlspecialchars($menu['title']) ?></h2>

<ul>
  <li><strong>Thème :</strong> <?= htmlspecialchars($menu['theme'] ?? '') ?></li>
  <li><strong>Régime :</strong> <?= htmlspecialchars($menu['diet'] ?? '') ?></li>
  <li><strong>Minimum personnes :</strong> <?= (int)$menu['min_people'] ?></li>
  <li><strong>Prix de base :</strong>
    <?= number_format((float)$menu['base_price'], 2, ',', ' ') ?> €
  </li>
  <li><strong>Stock :</strong> <?= (int)$menu['stock'] ?></li>
</ul>

<?php if ($menu['stock'] > 0): ?>
  <a class="btn btn-primary"
     href="commande_create.php?menu_id=<?= (int)$menu['id'] ?>">
     Commander ce menu
  </a>
<?php else: ?>
  <p><strong>Menu indisponible.</strong></p>
<?php endif; ?>

<?php require_once 'partials/footer.php'; ?>