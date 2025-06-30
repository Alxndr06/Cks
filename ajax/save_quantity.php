<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Enums/Environment.php';
header('Content-Type: application/json');

// On initialise le panier si besoin
header('Content-Type: application/json');

if (!isset($_SESSION['cks_cart'])) {
    $_SESSION['cks_cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);

    if ($productId > 0) {
        if ($quantity > 0) {
            $_SESSION['cks_cart'][$productId] = $quantity;
        } else {
            unset($_SESSION['cks_cart'][$productId]);
        }

        echo json_encode(['success' => true, 'cart' => $_SESSION['cks_cart']]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid product ID or quantity']);