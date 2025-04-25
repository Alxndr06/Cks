<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/includes/functions.php';

$csrf_token = getCsrfToken();

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === "admin";
$query = "SELECT * FROM products";
$query .= $isAdmin ? "" : " WHERE restricted = 0";

$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<div id="main-part">
    <h2>Snack Shop</h2>
    <?php echo displayErrorOrSuccessMessage(); ?>
    <?= displayLockedStatus(); ?>

    <form method="POST" action="order/process_order.php">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div id="live-summary">
            <p>🧾 Total items selected: <span id="total-items">0</span></p>
            <p>💶 Estimated total: <span id="total-price">0.00</span> €</p>
        </div>

        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php if (!empty($product['image'])): ?>
                        <img class="product-image" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars(ucfirst($product['name'])) ?></h3>
                    <p><strong><?= $product['price'] ?> €</strong></p>
                    <p class="desc"><?= htmlspecialchars($product['description']) ?></p>

                    <?php if ($isLoggedIn && !$isLocked): ?>
                        <?php if ($product['stock_quantity'] == 0): ?>
                            <p class="out-of-stock">❌ Out of stock</p>
                        <?php else: ?>
                            <label for="quantity_<?= $product['id'] ?>">Quantity:</label>
                            <input
                                    class="shop_quantity_input"
                                    type="number"
                                    name="quantity[<?= $product['id'] ?>]"
                                    id="quantity_<?= $product['id'] ?>"
                                    min="0"
                                    max="<?= $product['stock_quantity'] ?>"
                                    value="0"
                                    data-price="<?= $product['price'] ?>"
                            >
                            <p class="selected-count">Selected: <span>0</span></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($isLoggedIn && !$isLocked): ?>
            <div id="order-summary">
                <button class="shop_submit_button" type="submit" onclick="return confirm('Confirm order ?')">✅ Order</button>
                <button class="shop_clear_button" type="reset">❌ Clear</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>