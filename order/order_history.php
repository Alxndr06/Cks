<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../config/db_connect.php';
checkConnect();

$user_id = $_SESSION['id'];

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$queryBase = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC";
$countQuery = "SELECT COUNT(*) FROM orders WHERE user_id = :user_id";

$result = paginateQuery($pdo, $queryBase, $countQuery, $page, 10, [':user_id' => $user_id]);

$orders = $result['items']
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
        <div class="pagination">
            <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
                <?php if ($i === $result['current_page']): ?>
                    <strong><?= $i ?></strong>
                <?php else: ?>
                    <a href="?page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?= backupLink( '../user/dashboard.php', '🔙back to dashboard') ?>
    </div>

<?php include '../includes/footer.php'; ?>