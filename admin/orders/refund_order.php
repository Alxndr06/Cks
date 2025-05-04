<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

checkMethodPost();
checkCsrfToken();
checkConnect();

$orderId = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    redirectWithError('Unknown order', 'order_management.php');
}

if ($order['status'] === 'refunded') {
    redirectWithError('This order has already been refunded.', 'order_management.php');
}

$orderDate = new DateTime($order['datetime']);
$now = new DateTime();
$interval = $orderDate->diff($now);
$daysElapsed = (int)$interval->format('%a');

if ($daysElapsed > 30) {
    redirectWithError('This order is too old to be refunded (more than 30 days).', 'order_management.php');
}

$purchaseList = json_decode($order['items'], true);
$totalPrice = $order['total_price'];
$userId = $order['user_id'];

foreach ($purchaseList as $productId => $qty) {
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
    $stmt->execute([$qty, $productId]);
}

$stmt = $pdo->prepare("UPDATE users SET note = note - ? WHERE id = ?");
$stmt->execute([$totalPrice, $userId]);

$stmt = $pdo->prepare("UPDATE users SET total_spent = total_spent - ? WHERE id = ?");
$stmt->execute([$totalPrice, $userId]);

$stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
if ($stmt->execute(['refunded', $order['id']])) {
    logAction($pdo, $_SESSION['id'], $userId, 'refund_user' , 'Commande n°' . $order['id'] .
        ' du ' . date("d/m/Y H:i", strtotime($order['datetime'])) . 'Montant : ' . $totalPrice . '€');
    redirectWithSuccess('User refunded successfully', 'order_management.php');
} else {
    redirectWithError('Error when refunding user', 'order_management.php');
}
