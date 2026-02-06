<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;
$roleId = $_SESSION['role_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite Gourmand</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container header-flex">
        <h1>Vite Gourmand</h1>

        <nav class="nav">
            <a href="index.php">Accueil</a>

            <?php if ($userId): ?>
                <a href="dashboard.php">Mes commandes</a>

                <?php if ($roleId == 1): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>

                <?php if ($roleId == 2): ?>
                    <a href="employe.php">Employé</a>
                <?php endif; ?>

                <a class="btn btn-danger" href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Connexion</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container"></main>