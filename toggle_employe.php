<?php
require_once 'auth.php';
requireAdmin();
require_once 'db.php';
require_once 'partials/header.php';
require_once 'partials/footer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

if (!isset($_POST['id'])) {
    header('Location: admin.php');
    exit;
}

$id = (int) $_POST['id'];

$stmt = $pdo->prepare("
    UPDATE users
    SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END
    WHERE id = ? AND role_id = 2
");
$stmt->execute([$id]);

header('Location: admin.php');
exit;