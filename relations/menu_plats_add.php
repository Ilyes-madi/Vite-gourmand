<?php
require_once '../auth.php';
requireAdmin();
require_once '../db.php';

if (!isset($_POST['menu_id'], $_POST['plat_id'])) {
    header('Location: ../admin_menus.php');
    exit;
}

$menu_id = (int) $_POST['menu_id'];
$plat_id = (int) $_POST['plat_id'];

$stmt = $pdo->prepare("
    INSERT IGNORE INTO menu_plats (menu_id, plat_id)
    VALUES (?, ?)
");
$stmt->execute([$menu_id, $plat_id]);

header("Location: ../admin_menu_edit.php?id=$menu_id");
exit;