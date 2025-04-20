<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../config/db_connect.php';
checkConnect();

$user_id = $_SESSION['id'];
$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

    <div id="main-part">
        <h2>My order history</h2>
        <table class="user-table">
            <tr>
                <th>Date</th>
                <th>ID</th>
                <th>Items</th>
                <th>Order total</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= date("d/m/Y H:i", strtotime($order['datetime'])) ?></td>
                    <td><?= $order['id'] ?></td>
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
                </tr>
            <?php endforeach; ?>
        </table>
        <?= backupLink( '../user/dashboard.php', '🔙back to dashboard') ?>
    </div>

<?php include '../includes/footer.php'; ?>