<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';
checkAdmin();
$csrf_token = getCsrfToken();

if (!isset($_GET['id'])) {
    redirectWithError('Invalid product ID', 'stock_management.php');
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    redirectWithError('Product not found', 'stock_management.php');
}

?>

<div id="main-part">
    <h2>Edit product</h2>

    <form method="POST" action="process_product.php" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <label for="name">Product name :</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>

        <label for="description">Description :</label>
        <input type="text" id="description" name="description" min="1" max="999" value="<?= htmlspecialchars($product['description']) ?>"><br><br>

        <label for="price">Price :</label>
        <input type="number" step="0.01" id="price" name="price" min="0" max="999" value="<?= htmlspecialchars((float)$product['price']) ?>" required><br><br>

        <label for="stock_quantity">Stock quantity :</label>
        <input type="number" id="stock_quantity" name="stock_quantity" value="<?= htmlspecialchars($product['stock_quantity']) ?>" required><br><br>

        <label for="restricted" title="Should this product be restricted to users ?">Restrict product to users ?</label>
        <select name="restricted" id="restricted" required>
            <option value="0" <?= $product['restricted'] == 0 ? 'selected' : '' ?>>Unrestricted</option>
            <option value="1" <?= $product['restricted'] == 1 ? 'selected' : '' ?>>Restricted</option>
        </select><br><br>

        <label for="image">Product image:</label>
        <input type="file" name="image" id="image" accept="image/*"><br><br>

        <?php if (!empty($product['image'])): ?>
            <p>Current image:</p>
            <img alt="Uploaded image thumbnail" src="<?= $base_url . '/' . $product['image'] ?>" style="max-width: 150px; border:1px solid #ccc; border-radius:5px;"><br><br>
        <?php endif; ?>

        <button type="submit" title="Edit product">Edit product</button><br><br>
    </form>
    <div class="backupLinkContainer">
        <?= backupLink('stock_management.php'); ?>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>
