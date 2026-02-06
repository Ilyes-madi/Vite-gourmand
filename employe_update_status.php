<?php
require_once 'auth.php';
requireEmploye();
require_once 'db.php';

$employeId = (int)($_SESSION['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: employe.php');
    exit;
}

$commandeId = (int)($_POST['commande_id'] ?? 0);
$newStatus = $_POST['new_status'] ?? '';

$allowedStatuses = ['acceptée', 'refusée', 'en_preparation', 'terminée'];

if ($commandeId <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
    header('Location: employe.php');
    exit;
}


$stmt = $pdo->prepare("SELECT status_current FROM commandes WHERE id = ?");
$stmt->execute([$commandeId]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    header('Location: employe.php');
    exit;
}

try {
    $pdo->beginTransaction();

    
    $stmt = $pdo->prepare("
        UPDATE commandes
        SET status_current = :status
        WHERE id = :id
    ");
    $stmt->execute([
        'status' => $newStatus,
        'id' => $commandeId
    ]);

    
    $stmt = $pdo->prepare("
        INSERT INTO commande_historiques
        (commande_id, status, changed_by, changed_at)
        VALUES (:commande_id, :status, :changed_by, NOW())
    ");
    $stmt->execute([
        'commande_id' => $commandeId,
        'status' => $newStatus,
        'changed_by' => $employeId
    ]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
}

header('Location: employe.php');
exit;