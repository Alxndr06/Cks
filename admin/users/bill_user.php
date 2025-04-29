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

if (!$products) {
    redirectWithError('Products not found', 'user_list.php');
}

?>

<div id="main-part">
    <h2>Bill <?= ucfirst(strtolower($user['username'])) ?></h2>
    <?= displayErrorOrSuccessMessage() ?>
<h3>PAYMENT</h3>
    <div class="billing-actions">
        <h3>Settle total user debt</h3>
        <form method="POST" action="process_user.php" style="display:inline;">
            <input type="hidden" name="action" value="settle">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <button type="submit" onclick="return confirm('Settle all debt for <?= htmlspecialchars(ucfirst(strtolower($user['username']))) ?> ?')">
                ✅ Settle debt (<?= number_format($user['note'], 2) ?> €)
            </button><br><br>
        </form>

        <h3>Pay off part of user debt</h3>
        <form method="POST" action="process_user.php" style="display:inline;">
            <input type="hidden" name="action" value="pay">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <label for="payAmount">Payment amount :</label>
            <input type="number" name="payAmount" id="payAmount" step="0.01" min="1" max="<?= number_format($user['note'], 2, '.', '') ?>" placeholder="Amount">
            <button type="submit" onclick="return confirm('Validate payment for <?= htmlspecialchars(ucfirst(strtolower($user['username']))) ?> ?')">✅ Enter payment</button>
        </form>
    </div>

<h3>BILLING</h3>
    <div class="billing-actions">
        <h3>Manual billing</h3>
        <form method="POST" action="process_user.php">
            <input type="hidden" name="action" value="bill">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="manual-billing">
                <label for="billAmount">Amount to bill :</label>
                <input type="number" name="billAmount" id="billAmount" step="0.01" placeholder="Enter amount">

                <label for="reason">Reason :</label>
                <input type="text" name="reason" id="reason" placeholder="Reason for billing"><br><br>
            </div>

            <h3>Billing with products</h3>
            <table class="user-table">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Price (€)</th>
                    <th>Stock</th>
                    <th>Quantity</th>
                </tr>
                </thead>
                <tbody>
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
                </tbody>
            </table><br>

            <button type="submit" onclick="return confirm('Bill <?= ucfirst(strtolower($user['username'])) ?> ?')">✅ Bill user</button>
            <button type="reset">❌ Clear form</button>
        </form>
    </div>

    <div class="backupLinkContainer">
        <?= backupLink("user_details.php?id=$id"); ?>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
