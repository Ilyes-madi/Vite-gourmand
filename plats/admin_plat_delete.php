<?php
require_once '../auth.php';
requireAdmin();
require_once '../db.php';

if (!isset($_POST['id'])) {
    header('Location: admin_plats.php');
    exit;
}

$id = (int) $_POST['id'];

$stmt = $pdo->prepare("DELETE FROM plats WHERE id = ?");
$stmt->execute([$id]);

header('Location: admin_plats.php');
exit;