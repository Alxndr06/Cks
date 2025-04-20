<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../includes/functions.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE activation_token = ? AND is_active = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        echo "✅ Votre compte a été activé avec succès !";
    } else {
        echo "❌ Ce lien est invalide ou le compte est déjà activé.";
    }
} else {
    echo "Aucun token fourni.";
}

