<?php
require_once __DIR__ . '/../auth.php';
requireAdmin();
require_once __DIR__ . '/../db.php';

$plat_id = (int)($_POST['plat_id'] ?? 0);
$allergene_id = (int)($_POST['allergene_id'] ?? 0);

if ($plat_id <= 0 || $allergene_id <= 0) {
  header("Location: ../plats/admin_plats.php");
  exit;
}


$stmt = $pdo->prepare("SELECT 1 FROM plat_allergenes WHERE plat_id = ? AND allergene_id = ? LIMIT 1");
$stmt->execute([$plat_id, $allergene_id]);

if (!$stmt->fetchColumn()) {
  $stmt = $pdo->prepare("INSERT INTO plat_allergenes (plat_id, allergene_id) VALUES (?, ?)");
  $stmt->execute([$plat_id, $allergene_id]);
}

header("Location: ../plats/admin_plat_edit.php?id=" . $plat_id);
exit;