<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

//LOGIQUE ajout d'utilisateur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCsrfToken();
    $name = sanitize(strtolower($_POST['name']));
    $description = sanitize($_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $restricted = $_POST['restricted'];
    $imagePath = null;

    if (!is_numeric($price) || $price < 0) {
        redirectWithError('Invalid price.', 'add_product.php');
    }
    if (!is_numeric($quantity) || $quantity < 1) {
        redirectWithError('Invalid quantity.', 'add_product.php');
    }
    if (!in_array($restricted, ['0', '1'])) {
        redirectWithError('Invalid restriction setting.', 'add_product.php');
    }

    //Vérification de l'unicité du produit
    $stmt = $pdo->prepare('SELECT id FROM products WHERE name = ?');
    $stmt->execute([$name]);
    if ($stmt->fetch()) {
        redirectWithError('Product already exists: "' . htmlspecialchars($name) . '"', 'add_product.php');
    }

    //Upload de l'image
    if (isset($_FILES['image']) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/';
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $filename;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $filename;
            } else {
                redirectWithError('Image upload failed.', 'add_product.php');
            }
        } else {
            redirectWithError('Only JPG, PNG, and GIF images are allowed.', 'add_product.php');
        }
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock_quantity, restricted, image) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $description, $price, $quantity, $restricted, $imagePath])) {
        logAction($pdo, $_SESSION['id'], "Stock", 'add_product', "Name: " . $name . " description: " . $description . " price: " . $price . " quantity: " . $quantity . " restricted: " . $restricted);
        redirectWithSuccess('Added product successfully.', 'stock_management.php');
    } else {
        redirectWithError('Product upload failed.', 'add_product.php');
    }
}
?>

<div id="main-part">
    <h2>Add a product</h2>
    <div class="login_form">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <label for="name">Name :</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="description">Description :</label>
            <input type="text" id="description" name="description"><br><br>

            <label for="price">Price :</label>
            <input type="number" step="0.01" id="price" name="price" max="999" required><br><br>

            <label for="quantity">Quantity :</label>
            <input type="number" id="quantity" name="quantity" min="1" max="999" required><br><br>

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
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
