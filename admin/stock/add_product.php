<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

?>

<div id="main-part">
    <h2>Add a product</h2>

    <div class="login_form">
        <form method="POST" action="process_product.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <label for="name">Name :</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="description">Description :</label>
            <input type="text" id="description" name="description"><br><br>

            <label for="price">Price :</label>
            <input type="number" step="0.01" id="price" name="price" min="0" max="999" required><br><br>

            <label for="quantity">Quantity :</label>
            <input type="number" id="quantity" name="quantity" min="1" max="999" required><br><br>

            <label for="category" title="Choose product category">Product category</label>
            <select name="category" id="category" required>
                <option value="drinks" title="Drinks">Drinks</option>
                <option value="snacking" title="Snacking">Snack</option>
                <option value="coffee" title="coffee">Coffee</option>
            </select><br><br>

            <label for="restricted" title="Should this product be restricted to users ?">Restrict product to users ?</label>
            <select name="restricted" id="restricted" required>
                <option value="0" title="Users can see and purchase this product in snack shop">Unrestricted</option>
                <option value="1" title="Users can't see and purchase this product in snack shop">Restricted</option>
            </select><br><br>

            <label for="image">Product image:</label>
            <input type="file" name="image" id="image" accept="image/*"><br><br>

            <button type="submit">Add product</button>
            <button type="reset">Reset form</button><br><br>
        </form>
    </div>
    <div class="backupLinkContainer">
        <?= backupLink('stock_management.php'); ?>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
