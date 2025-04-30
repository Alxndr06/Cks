<?php
global $selfRegistration;
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/config.php';

checkAdmin();
checkMethodPost();
checkCsrfToken();

$newValue = $selfRegistration ? '0' : '1';

    $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE name = 'allow_self_registration'");
    if($stmt->execute([$newValue])) {
        logAction($pdo, $_SESSION['id'], null, 'update_setting', 'Toggled self-registration to ' . $newValue);
        redirectWithSuccess('Self-registration setting updated.', 'register_system.php');
    } else {
    redirectWithError('Failed to update setting.', 'register_system.php');
}