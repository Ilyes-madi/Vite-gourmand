<?php
require_once 'auth.php';
requireLogin();
require_once 'db.php';
require_once 'partials/header.php';

$commandeId = filter_input(INPUT_GET, 'commande_id', FILTER_VALIDATE_INT);
if (!$commandeId) {
    $commandeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
}

if (!$commandeId) {
    echo "<main class='container'><p>Commande introuvable.</p><p><a href='employe.php'>← Retour</a></p></main>";
    require_once 'partials/footer.php';
    exit;
}

$sqlCommande = "
    SELECT
        c.id,
        c.event_date,
        c.event_time,
        c.people_count,
        c.event_address,
        c.price_total,
        c.status_current,
        u.email AS client_email,
        m.title AS menu_title
    FROM commandes c
    LEFT JOIN users u ON u.id = c.user_id
    LEFT JOIN menus m ON m.id = c.menu_id
    WHERE c.id = :id
    LIMIT 1
";
$stmt = $pdo->prepare($sqlCommande);
$stmt->execute(['id' => $commandeId]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    echo "<main class='container'><p>Commande introuvable.</p><p><a href='employe.php'>← Retour</a></p></main>";
    require_once 'partials/footer.php';
    exit;
}

$sqlHist = "
    SELECT
        ch.status,
        ch.cancel_reason,
        ch.contact_mode,
        ch.changed_at,
        u.email AS changed_by_email
    FROM commande_historiques ch
    LEFT JOIN users u ON u.id = ch.changed_by
    WHERE ch.commande_id = :commande_id
    ORDER BY ch.changed_at DESC
";
$stmt = $pdo->prepare($sqlHist);
$stmt->execute(['commande_id' => $commandeId]);
$historiques = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container">
    <h2>Historique de la commande #<?= (int)$commandeId ?></h2>

    <p>
        Date : <?= htmlspecialchars($commande['event_date'] ?? '—') ?>
        — Heure : <?= htmlspecialchars($commande['event_time'] ?? '—') ?>
        — Personnes : <?= htmlspecialchars($commande['people_count'] ?? '—') ?>
        — Adresse : <?= htmlspecialchars($commande['event_address'] ?? '—') ?>
        — Total : <?= htmlspecialchars($commande['price_total'] ?? '0') ?> €
    </p>

    <?php if (empty($historiques)): ?>
        <p>Aucun historique pour cette commande.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Modifié par</th>
                    <th>Motif d’annulation</th>
                    <th>Mode de contact</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historiques as $h): ?>
                    <tr>
                        <td><?= htmlspecialchars($h['changed_at'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($h['status'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($h['changed_by_email'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($h['cancel_reason'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($h['contact_mode'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="employe.php">← Retour à la gestion des commandes</a></p>
</main>

<?php require_once 'partials/footer.php'; ?>