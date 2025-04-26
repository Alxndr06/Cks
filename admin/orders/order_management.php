<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();

// Récupérer les commandes avec nom utilisateur
$stmt = $pdo->prepare("
    SELECT orders.id, orders.user_id, users.username, orders.total_price, orders.datetime, orders.items, orders.status
    FROM orders
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.datetime DESC
");
$stmt->execute();
$orders = $stmt->fetchAll();

?>

<div id="main-part">
    <h2>Order list</h2>
    <?= displayErrorOrSuccessMessage(); ?>
    <table class="user-table">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Items</th>
            <th>Total</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['id'] ?></td>
                <td><?= htmlspecialchars($order['username']) ?></td>
                <td>
                    <?php
                    $items = json_decode($order['items'], true);

                    foreach ($items as $productId => $qty) {
                        $stmtProduct = $pdo->prepare("SELECT name FROM products WHERE id = ?");
                        $stmtProduct->execute([$productId]);
                        $product = $stmtProduct->fetch();

                        $productName = $product ? htmlspecialchars($product['name']) : "Unknown product";

                        echo "• $productName x $qty<br>";
                    }
                    ?>
                </td>
                <td><?= number_format($order['total_price'], 2) ?> €</td>
                <td><?= date("d/m/Y H:i", strtotime($order['datetime'])) ?></td>
                <td>
                    <?php if ($order['status'] === 'validated'): ?>
                        <form method="POST" action="refund_order.php?id=<?= $order['id'] ?>" onsubmit="return confirm('Are you sure you want to refund this order?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
                            <button type="submit">↩️ Refund</button>
                        </form>
                    <?php else: ?>
                        <span style="color: gray;">✅ Refunded</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>