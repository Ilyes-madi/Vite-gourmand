<?php
require_once 'auth.php';
requireLogin();
require_once 'db.php';

$userId = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$commandeId = (int)($_POST['commande_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($commandeId <= 0 || $rating < 1 || $rating > 5 || $comment === '') {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT id
    FROM commandes
    WHERE id = ?
    AND user_id = ?
    AND status_current = 'terminée'
");
$stmt->execute([$commandeId, $userId]);
if (!$stmt->fetch()) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM avis WHERE commande_id = ?");
$stmt->execute([$commandeId]);
if ($stmt->fetch()) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO avis (user_id, commande_id, rating, comment, is_approved, created_at)
    VALUES (?, ?, ?, ?, 0, NOW())
");
$stmt->execute([$userId, $commandeId, $rating, $comment]);

header('Location: dashboard.php');
exit;