<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAdmin();
checkMethodPost();
checkCsrfToken();

if (!isset($_POST['id'])) {
    redirectWithError('Unknown user ID', 'register_system.php');
}

$id = (int) $_POST['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    redirectWithError('Unknown user', 'register_system.php');
}

$stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
if ($stmt->execute([$id])) {
    logAction($pdo, $_SESSION['id'], $user['id'], 'activate_user', "User activated");
    redirectWithSuccess('User activated successfully', 'register_system.php');
} else {
    redirectWithError('Error while activate user', 'register_system.php');
}