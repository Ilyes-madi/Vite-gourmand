<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if ((int)$_SESSION['role_id'] !== 1) {
        header('Location: index.php');
        exit;
    }
}

function requireEmploye(): void {
    requireLogin();
    if (!in_array((int)$_SESSION['role_id'], [1, 2])) {
        header('Location: index.php');
        exit;
    }
}