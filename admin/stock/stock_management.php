<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';
checkAdmin();

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$result = paginate($pdo, 'products', $page, 10, 'id ASC');
$products = $result['items'];
?>
    <div id="main-part">
        <h2>Stock management</h2>
        <?= displayErrorOrSuccessMessage(); ?>
        <a title="Add new product" href="add_product.php" class="interface-button">➕Add new product</a>
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
                    <?= adminActions($product, 'product') ?>
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
        <div class="backupLinkContainer">
            <?= backupLink('../admin_dashboard.php'); ?>
        </div>
    </div>

<?php require '../../includes/footer.php'; ?>