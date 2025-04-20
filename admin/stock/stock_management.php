<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';
checkAdmin();

$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll();
?>
    <div id="main-part">
        <h2>Stock management</h2>
        <?= displayErrorOrSuccessMessage(); ?>
        <a title="Add new product" href="add_product.php">➕Add new product</a>
        <table class="user-table">
            <tr>
                <th class="col-id">ID</th>
                <th class="col-name">Name</th>
                <th class="col-desc">Description</th>
                <th class="col-price">Price</th>
                <th class="col-qty">Quantity available</th>
                <th class="col-access">Access</th>
                <th class="col-actions">Actions</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td class="col-id"><?= $product['id'] ?></td>
                    <td class="col-name"><?= htmlspecialchars(ucfirst(strtolower($product['name']))) ?></td>
                    <td class="col-desc"><?= htmlspecialchars($product['description']) ?></td>
                    <td class="col-price"><?=$product['price'] ?> €</td>
                    <td class="col-qty"><?= ($product['stock_quantity']) ?></td>
                    <td class="col-access"><?php if (!$product['restricted']): ?>Unrestricted<?php else: ?>Restricted<?php endif; ?></td>
                    <?= productAdminActions($product) ?>
                </tr>
            <?php endforeach; ?>
        </table>
        <?= backupLink('../admin_dashboard.php', '🔙back to admin dashboard'); ?>
    </div>

<?php require '../../includes/footer.php'; ?>