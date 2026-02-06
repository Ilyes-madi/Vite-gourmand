<?php
require_once 'auth.php';
requireLogin();
require_once 'db.php';
require_once 'partials/header.php';

$userId = (int)($_SESSION['user_id'] ?? 0);
$roleId = (int)($_SESSION['role_id'] ?? 0);

if ($roleId !== 3) {
  header('Location: index.php');
  exit;
}

$stmt = $pdo->prepare("
  SELECT
    c.id,
    m.title AS menu_title,
    c.event_date,
    c.event_time,
    c.people_count,
    c.event_address,
    c.total_price,
    c.status_current,
    c.created_at
  FROM commandes c
  JOIN menus m ON m.id = c.menu_id
  WHERE c.user_id = :uid
  ORDER BY c.id DESC
");
$stmt->execute([':uid' => $userId]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Mes commandes</h2>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Menu</th>
      <th>Date</th>
      <th>Heure</th>
      <th>Personnes</th>
      <th>Adresse</th>
      <th>Total</th>
      <th>Statut</th>
      <th>Historique</th>
      <th>Annuler</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$commandes): ?>
      <tr><td colspan="10">Aucune commande.</td></tr>
    <?php else: ?>
      <?php foreach ($commandes as $c): ?>
        <tr>
          <td><?= (int)$c['id'] ?></td>
          <td><?= htmlspecialchars($c['menu_title']) ?></td>
          <td><?= htmlspecialchars($c['event_date']) ?></td>
          <td><?= htmlspecialchars($c['event_time']) ?></td>
          <td><?= (int)$c['people_count'] ?></td>
          <td><?= htmlspecialchars($c['event_address']) ?></td>
          <td><?= number_format((float)$c['total_price'], 2, '.', '') ?> €</td>
          <td><?= htmlspecialchars($c['status_current']) ?></td>
          <td>
            <a href="commande_historique.php?commande_id=<?= (int)$c['id'] ?>">Historique</a>
          </td>
          <td>
            <?php
              $status = (string)$c['status_current'];
              $canCancel = !in_array($status, ['annulée', 'livrée'], true);
            ?>
            <?php if ($canCancel): ?>
              <form method="post" action="commande_cancel.php" style="display:flex; flex-direction:column; gap:6px; min-width:220px;">
                <input type="hidden" name="commande_id" value="<?= (int)$c['id'] ?>">
                <input type="text" name="cancel_reason" placeholder="Motif d'annulation" required>
                <select name="contact_mode" required>
                  <option value="">Mode de contact</option>
                  <option value="email">Email</option>
                  <option value="telephone">Téléphone</option>
                </select>
                <button type="submit">Annuler</button>
              </form>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<?php require_once 'partials/footer.php'; ?>