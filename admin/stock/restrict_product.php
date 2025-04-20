<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAdmin();

//Vérification de la méthode POST
checkMethodPost();
// Vérification du token CSRF
checkCsrfToken();
// récupération de l'user
if (!isset($_POST['id'])) die('Unknown product');
$id = $_POST['id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) die ('Unknown product');

if (!$product['restricted']) {
    $stmt = $pdo->prepare('UPDATE products SET restricted = 1 WHERE id = ?');
    if ($stmt->execute([$id])) {
        $_SESSION['product_id_redirect'] = $id;
        $product['restricted'] = true;
        logAction($pdo, $_SESSION['id'], $product['name'], 'restrict_product', "Restricted product " . strtoupper(htmlspecialchars($product['name'])));
        header("Location: stock_management.php");
        exit;
    } else {
        echo '<div class="error_message">Error when updating the product</div>';
    }
} else {
    $stmt = $pdo->prepare('UPDATE products SET restricted = 0 WHERE id = ?');
    if ($stmt->execute([$id])) {
        $_SESSION['product_id_redirect'] = $id;
        $product['restricted'] = false;
        logAction($pdo, $_SESSION['id'], $product['name'], 'allow_product', "Allowed product " . strtoupper(htmlspecialchars($product['name'])));
        header("Location: stock_management.php");
        exit;
    } else {
        echo '<div class="error_message">Error when updating the product</div>';
    }
}
