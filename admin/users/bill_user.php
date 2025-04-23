<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

if (!isset($_GET['id'])) {
    redirectWithError('Unknown user ID', 'user_list.php');
}
$id = (int)$_GET['id'];

// Récupère l'user'
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    redirectWithError('User not found', 'user_list.php');
}

// Récupération des produits
$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrfToken();

    $manualAmount = isset($_POST['billAmount']) ? (float)$_POST['billAmount'] : 0;
    $reason = sanitize($_POST['reason'] ?? '');
    $quantities = $_POST['quantities'] ?? [];

    if (!is_array($quantities)) {
        redirectWithError('Invalid form data', 'user_list.php');
    }

    $filteredQuantities = [];
    foreach ($quantities as $productId => $qty) {
        $qty = (int)$qty;
        if ($qty > 0) {
            $filteredQuantities[(int)$productId] = $qty;
        }
    }

    $totalProductAmount = 0;
    $productDetails = [];
    $purchaseList = [];

    foreach ($filteredQuantities as $productId => $qty) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            redirectWithError("Unknown product ID: $productId", 'user_list.php');
        }

        if ($qty > $product['stock_quantity']) {
            redirectWithError("Not enough stock for product: " . htmlspecialchars($product['name']), 'user_list.php');
        }

        $lineTotal = $qty * $product['price'];
        $totalProductAmount += $lineTotal;

        $purchaseList[$productId] = $qty;
        $productDetails[] = "{$product['name']} x{$qty} ({$lineTotal} €)";
    }

    $finalAmount = $manualAmount + $totalProductAmount;

    if ($finalAmount <= 0) {
        redirectWithError("You must bill something greater than 0.", 'user_list.php');
    }

    // Gestion du stock
    foreach ($purchaseList as $productId => $qty) {
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        $stmt->execute([$qty, $productId]);
    }

    // Mise à jour de la note et total_spent
    $stmt = $pdo->prepare("UPDATE users SET note = note + ?, total_spent = total_spent + ? WHERE id = ?");
    $stmt->execute([$finalAmount, $finalAmount, $id]);

    // Insertion dans orders
    $items_json = json_encode($purchaseList);
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, datetime, items, total_price) VALUES (?, NOW(), ?, ?)");
    $stmt->execute([$id, $items_json, $finalAmount]);

    // On log
    $logMessage = "Total: {$finalAmount} €";
    if ($manualAmount > 0) $logMessage .= " | Manual: {$manualAmount} €";
    if (!empty($productDetails)) $logMessage .= " | Products: " . implode(', ', $productDetails);
    if (!empty($reason)) $logMessage .= " | Reason: $reason";

    logAction($pdo, $_SESSION['id'], $user['id'], 'bill_user', $logMessage);

    redirectWithSuccess('User has been billed and order recorded', 'user_list.php');
} else {
    redirectWithError('Invalid form method', 'user_list.php');
}
?>

<div id="main-part">
    <h2>Bill <?= ucfirst(strtolower($user['username'])) ?></h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <h3>Manual billing</h3>
        <label for="billAmount">Amount to bill :</label>
        <input type="number" name="billAmount" id="billAmount" step="0.01" placeholder="Enter amount">

        <label for="reason">Reason :</label>
        <input type="text" name="reason" id="reason" placeholder="Reason for billing"><br><br>

        <h3>Billing with products</h3>
        <table class="user-table">
            <tr>
                <th>Product</th>
                <th>Price (€)</th>
                <th>Stock</th>
                <th>Quantity</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($product['price'], 2) ?></td>
                    <td><?= (int)$product['stock_quantity'] ?></td>
                    <td>
                        <input type="number" name="quantities[<?= $product['id'] ?>]" min="0" max="<?= (int)$product['stock_quantity'] ?>" value="0">
                    </td>
                </tr>
            <?php endforeach; ?>
        </table><br>

        <button type="submit" onclick="return confirm('Bill <?= ucfirst(strtolower($user['username'])) ?> ?')">✅ Bill user</button>
        <button type="reset">❌ Clear form</button>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
