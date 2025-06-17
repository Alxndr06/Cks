<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

checkMethodPost();
checkCsrfToken();
checkConnect();

$user_id = $_SESSION['id'];
$quantities = $_POST['quantity'];

// Vérification que la variable est bien un tableau
if (!is_array($quantities)) {
    redirectWithError('Invalid form data', '../snack_shop.php');
}

$filteredQuantities = [];

foreach ($quantities as $productId => $qty) {
    $qty = (int) $qty;
    if ($qty > 0) {
        $filteredQuantities[(int)$productId] = $qty;
    }
}

$total_price = 0;
$purchaseList = [];

foreach ($filteredQuantities as $productId => $qty) {
    $stmt = $pdo->prepare("SELECT * FROM `products` WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product)  {
        redirectWithError('Unknown product', '../snack_shop.php');
    }

    if ($qty > $product['stock_quantity']) {
        redirectWithError('Not enough items in stock for ' . htmlspecialchars($product['name']), '../snack_shop.php');
    }

    $total_price += $qty * $product['price'];

    $purchaseList[$productId] = $qty;
}

if ($total_price == 0 || empty($purchaseList)) {
    redirectWithError('No item selected', '../snack_shop.php');
}

foreach ($purchaseList as $productId => $qty) {
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
    $stmt->execute([$qty, $productId]);
}

$stmt = $pdo->prepare("UPDATE users SET note = note + ? WHERE id = ?");
$stmt->execute([$total_price, $user_id]);

$stmt = $pdo->prepare("UPDATE users SET total_spent = total_spent + ? WHERE id = ?");
$stmt->execute([$total_price, $user_id]);

$items_json = json_encode($purchaseList);

$stmt = $pdo->prepare("INSERT INTO orders (user_id, datetime, items, total_price) VALUES (?, NOW(), ?, ?)");
if ($stmt->execute([$user_id, $items_json, $total_price])) {
    redirectWithSuccess('Order placed successfully', '../snack_portal.php');
} else {
    redirectWithError('Error when placing order', '../snack_portal.php');
}
