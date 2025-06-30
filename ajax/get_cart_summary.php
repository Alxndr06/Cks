<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../config/config.php';


$cart = $_SESSION['cks_cart'] ?? [];

$totalItems = 0;
$totalPrice = 0;
$summaryList = [];

foreach ($cart as $productId => $qty) {
$stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if ($product) {
$lineTotal = $qty * $product['price'];
$totalItems += $qty;
$totalPrice += $lineTotal;
$summaryList[] = $product['name'] . " x" . $qty;
}
}

echo json_encode([
    'items' => $totalItems,
    'price' => number_format($totalPrice, 2),
    'list_html' => $summaryList ? implode('<br>', $summaryList) : 'No item selected',
    'list_text' => $summaryList ? implode("\n", $summaryList) : 'No item selected'
]);
