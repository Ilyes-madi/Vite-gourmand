<?php
require_once 'auth.php';
requireEmploye();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: employe_avis.php');
    exit;
}

$avisId = (int)($_POST['avis_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($avisId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    header('Location: employe_avis.php');
    exit;
}

if ($action === 'approve') {
    $stmt = $pdo->prepare("UPDATE avis SET is_approved = 1 WHERE id = ?");
    $stmt->execute([$avisId]);
} else {
    $stmt = $pdo->prepare("DELETE FROM avis WHERE id = ?");
    $stmt->execute([$avisId]);
}

header('Location: employe_avis.php');
exit;