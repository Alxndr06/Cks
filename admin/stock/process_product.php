<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAdmin();
checkMethodPost();
checkCsrfToken();

if (!isset($_POST['action'])) {
    redirectWithError('Unknown action method', 'stock_management.php');
}

$action = $_POST['action'];

switch ($action) {
    case 'add':
        checkCsrfToken();
        $nameRaw = trim($_POST['name']); // Stocké tel quel
        $nameCheck = strtolower($nameRaw); // Pour la vérification unicité
        $name = $nameRaw;
        $description = trim($_POST['description']);
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
        $stmt = $pdo->prepare('SELECT id FROM products WHERE LOWER(name) = ?');
        $stmt->execute([$nameCheck]);
        if ($stmt->fetch()) {
            redirectWithError('Product already exists: "' . htmlspecialchars($nameRaw) . '"', 'add_product.php');
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
        break;
    case 'edit':
        checkCsrfToken();
        if (!isset($_POST['id'])) {
            redirectWithError('Unknown product ID', 'edit_product.php');
        }

        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (double)$_POST['price'];
        $stock_quantity = (int) $_POST['stock_quantity'];
        $restricted = $_POST['restricted'];

        // On récup l'image afin de ne pas écraser l'ancienne.
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            redirectWithError('Product not found.', 'stock_management.php');
        }

        $imagePath = $product['image'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
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

        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock_quantity = ?, restricted = ?, image = ? WHERE id = ?");
        if ($stmt->execute([$name, $description, $price, $stock_quantity, $restricted, $imagePath, $id])) {
            logAction($pdo, $_SESSION['id'], null, 'update_product', 'Updated product: ' . htmlspecialchars($name));
            redirectWithSuccess('Product edited successfully', 'stock_management.php');
        } else {
            redirectWithError('Error updating product', 'stock_management.php');
        }
        break;
    case'restrict':
        if (!isset($_POST['id'])) {
            redirectWithError('Unknown product ID', 'edit_product.php');
        }

        $id = (int) $_POST['id'];

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            redirectWithError('Product not found.', 'stock_management.php');
        }

        if (!$product['restricted']) {
            $stmt = $pdo->prepare('UPDATE products SET restricted = 1 WHERE id = ?');
            if ($stmt->execute([$id])) {
                $_SESSION['product_id_redirect'] = $id;
                $product['restricted'] = true;
                logAction($pdo, $_SESSION['id'], $product['name'], 'restrict_product', "Restricted product " . strtoupper(htmlspecialchars($product['name'])));
                redirectWithSuccess('Product restricted successfully.', 'stock_management.php');
            } else {
                redirectWithError('Error updating product', 'stock_management.php');
            }
        } else {
            $stmt = $pdo->prepare('UPDATE products SET restricted = 0 WHERE id = ?');
            if ($stmt->execute([$id])) {
                $_SESSION['product_id_redirect'] = $id;
                $product['restricted'] = false;
                logAction($pdo, $_SESSION['id'], $product['name'], 'allow_product', "Allowed product " . strtoupper(htmlspecialchars($product['name'])));
                redirectWithSuccess('Product Unrestricted successfully.', 'stock_management.php');
            } else {
                redirectWithError('Error updating product', 'stock_management.php');
            }
        }
        break;
    case 'delete':
        checkCsrfToken();

        if (!isset($_POST['id'])) {
            redirectWithError('Unknown product ID', 'edit_product.php');
        }
        $id = (int) $_POST['id'];

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            redirectWithError('Product not found.', 'stock_management.php');
        }

        $name = $product['name'];
        $description = $product['description'];

        // logique de suppression de l'image du serveur
        if (!empty($product['image'])) {
            $imagePath = __DIR__ . '/../../' . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // logique de suppression du produit de la bdd
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        if ($stmt->execute([$id])) {
            logAction($pdo, $_SESSION['id'], null, 'delete_product', "Name: " . $name . " description: " . $description);
            redirectWithSuccess('Product deleted successfully.', 'stock_management.php');
        } else {
            redirectWithError('Error deleting product.', 'stock_management.php');
        }
        break;
    default:
        redirectWithError('Unknown action method', 'stock_management.php');
}