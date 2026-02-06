<?php
require_once 'auth.php';
require_once 'db.php';
require_once 'partials/header.php';


$sql = "
  SELECT
    id,
    title AS name,
    theme,
    diet,
    min_people,
    base_price,
    stock
  FROM menus
  WHERE stock > 0
";

$stmt = $pdo->query($sql);
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Nos menus</h2>

<?php if (empty($menus)): ?>
  <p>Aucun menu disponible pour le moment.</p>
<?php else: ?>
  <?php foreach ($menus as $menu): ?>
    <article class="card">
      <h3><?= htmlspecialchars($menu['name']) ?></h3>

      <ul>
        <li><strong>Thème :</strong> <?= htmlspecialchars($menu['theme'] ?? '') ?></li>
        <li><strong>Régime :</strong> <?= htmlspecialchars($menu['diet'] ?? '') ?></li>
        <li><strong>Minimum personnes :</strong> <?= (int)$menu['min_people'] ?></li>
        <li><strong>Prix de base :</strong>
          <?= number_format((float)$menu['base_price'], 2, ',', ' ') ?> €
        </li>
        <li><strong>Stock :</strong> <?= (int)$menu['stock'] ?></li>
      </ul>

      <div class="actions">
        <a class="btn" href="menu_show.php?id=<?= (int)$menu['id'] ?>">Voir le détail</a>
        <a class="btn btn-primary" href="commande_create.php?menu_id=<?= (int)$menu['id'] ?>">
          Commander
        </a>
      </div>
    </article>
  <?php endforeach; ?>
<?php endif; ?>
<?php
$stmt = $pdo->query("
    SELECT a.rating, a.comment, m.title AS menu_title
    FROM avis a
    JOIN commandes c ON c.id = a.commande_id
    JOIN menus m ON m.id = c.menu_id
    WHERE a.is_approved = 1
    ORDER BY a.created_at DESC
    LIMIT 6
");
$avisValides = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Avis clients</h2>

<?php if (empty($avisValides)): ?>
    <p>Aucun avis pour le moment.</p>
<?php else: ?>
    <?php foreach ($avisValides as $a): ?>
        <div>
            <strong><?= htmlspecialchars($a['menu_title']) ?></strong><br>
            <?= str_repeat('⭐', (int)$a['rating']) ?><br>
            <?= htmlspecialchars($a['comment']) ?>
        </div>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>


<?php require_once 'partials/footer.php'; ?>