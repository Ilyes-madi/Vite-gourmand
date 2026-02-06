<?php
require_once 'auth.php';
requireLogin();
require_once 'db.php';
require_once 'partials/header.php';
$userId = (int)$_SESSION['user_id'];
$commandeId = (int)($_GET['commande_id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT id
    FROM commandes
    WHERE id = ?
    AND user_id = ?
    AND status_current = 'terminée'
");
$stmt->execute([$commandeId, $userId]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM avis WHERE commande_id = ?");
$stmt->execute([$commandeId]);
if ($stmt->fetch()) {
    header('Location: dashboard.php');
    exit;
}
?>

<h2>Donner un avis</h2>

<form method="post" action="avis_store.php">
    <input type="hidden" name="commande_id" value="<?= $commandeId ?>">

    <label>Note (1 à 5)</label>
    <select name="rating" required>
        <option value="">Choisir</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>

    <br><br>

    <label>Commentaire</label><br>
    <textarea name="comment" required></textarea>

    <br><br>

    <button type="submit">Envoyer l’avis</button>
</form>