<?php
require_once 'auth.php';
requireLogin();
require_once 'db.php';

$userId = (int)($_SESSION['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$commandeId = isset($_POST['commande_id']) ? (int)$_POST['commande_id'] : 0;
$cancelReason = isset($_POST['cancel_reason']) ? trim((string)$_POST['cancel_reason']) : '';

if ($commandeId <= 0 || $cancelReason === '') {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, user_id, status_current FROM commandes WHERE id = ?");
$stmt->execute([$commandeId]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande || (int)$commande['user_id'] !== $userId) {
    header('Location: dashboard.php');
    exit;
}

if ($commande['status_current'] === 'accepté') {
    header('Location: dashboard.php');
    exit;
}

$pdo->beginTransaction();

$upd = $pdo->prepare("UPDATE commandes SET status_current = 'annulée' WHERE id = ?");
$upd->execute([$commandeId]);

$ins = $pdo->prepare("
    INSERT INTO commande_historiques (commande_id, status, cancel_reason, contact_mode, changed_by, changed_at)
    VALUES (?, 'annulée', ?, NULL, 'user', NOW())
");
$ins->execute([$commandeId, $cancelReason]);

$pdo->commit();

header('Location: dashboard.php');
exit;