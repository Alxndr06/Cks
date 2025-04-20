<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAdmin();
//Vérification de la méthode POST
checkMethodPost();
// Vérification du token CSRF
checkCsrfToken();
// récupération du produit
if (!isset($_POST['id'])) die('Unknown product');
$id = $_POST['id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    die('Product not found');
}

$targetId = $product['id'];
$name = $product['name'];
$description = $product['description'];


// logique de suppression de la bdd
$stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
if ($stmt->execute([$id])) {
    logAction($pdo, $_SESSION['id'], "Stock", 'delete_product', "Name: " . $name . " description: " . $description);
    header('Location: stock_management.php');
    exit;
} else {
    echo "<div class='alert'>Error deleting product</div>";
}