<?php
require_once 'auth.php';
requireAdmin();
require_once 'db.php';


$menuId = 0;
if (isset($_GET['id'])) {
    $menuId = (int)$_GET['id'];
} elseif (isset($_POST['id'])) {
    $menuId = (int)$_POST['id'];
}

if ($menuId <= 0) {
    header('Location: admin_menus.php?error=invalid');
    exit;
}

try {
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE menu_id = :id");
    $stmt->execute([':id' => $menuId]);
    $count = (int)$stmt->fetchColumn();

    if ($count > 0) {
        header('Location: admin_menus.php?error=menu_in_use');
        exit;
    }

    $pdo->beginTransaction();

    
    try {
        $stmt = $pdo->prepare("DELETE FROM menu_plats WHERE menu_id = :id");
        $stmt->execute([':id' => $menuId]);
    } catch (Throwable $e) {
        
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM menu_images WHERE menu_id = :id");
        $stmt->execute([':id' => $menuId]);
    } catch (Throwable $e) {
       
    }

    
    $stmt = $pdo->prepare("DELETE FROM menus WHERE id = :id");
    $stmt->execute([':id' => $menuId]);

    $pdo->commit();

    header('Location: admin_menus.php?deleted=1');
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: admin_menus.php?error=db');
    exit;
}