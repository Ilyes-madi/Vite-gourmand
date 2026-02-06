<?php
require_once __DIR__ . '/../auth.php';
requireAdmin();
require_once __DIR__ . '/../db.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
  header("Location: admin_allergenes.php");
  exit;
}



$pdo->prepare("DELETE FROM plat_allergenes WHERE allergene_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM allergenes WHERE id = ?")->execute([$id]);

header("Location: admin_allergenes.php");
exit;