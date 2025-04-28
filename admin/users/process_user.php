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
        $username = preg_replace('/\s+/', '', trim($_POST['username']));
        $lastname = trim($_POST['lastname']);
        $firstname = trim($_POST['firstname']);
        $email = strtolower(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
        $is_active = true;

        $allowed_roles = ['user', 'admin'];
        if (!in_array($role, $allowed_roles)) {
            redirectWithError("Invalid role selected.", "add_user.php");
        }

        $errors = [];

        if (strlen($lastname) < 2 || strlen($firstname) < 2) {
            $errors[] = "Firstname and Lastname must be at least 2 characters long.";
        }

        if (!validateString($lastname)) {
            $errors[] = "Lastname contains invalid characters.";
        }

        if (!validateString($firstname)) {
            $errors[] = "Firstname contains invalid characters.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address.";
        }

        if (strlen($username) < 3 || strlen($username) > 20) {
            $errors[] = "Username must be between 3 and 20 characters.";
        }

        if (!empty($errors)) {
            redirectWithError(implode('<br>', $errors), 'add_user.php');
        }

        //Vérification de l'unicité du mail
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            redirectWithError('Email is already taken', 'add_user.php');
        }

        //Vérification de l'unicité de l'username
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            redirectWithError('Username is already taken', 'add_user.php');
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, lastname, firstname, email, password, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $lastname, $firstname, $email, $password, $role, $is_active])) {
            logAction($pdo, $_SESSION['id'], $username , 'create_user', "User: " . $username . " mail: " . $email . " Role: " . $role . "Account: " . displayActiveStatus($is_active));
            redirectWithSuccess('User added successfully', 'user_list.php');
        } else {
            redirectWithError("There was a problem adding user.", "add_user.php");
        }
        break;
    case 'edit':
        checkCsrfToken();
        $id = (int) $_POST['id'];
        $username = preg_replace('/\s+/', '', trim($_POST['username']));
        $lastname = trim($_POST['lastname']);
        $firstname = trim($_POST['firstname']);
        $email = strtolower(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
        $role = $_POST['role'];

        $allowed_roles = ['user', 'admin'];
        if (!in_array($role, $allowed_roles)) {
            redirectWithError("Invalid role selected.", "edit_user.php");
        }

        //Mise à jour de l'user sur la bdd
        $stmt = $pdo->prepare("UPDATE users SET username = ?, lastname = ?, firstname = ?, email = ?, password = ?, role = ? WHERE id = ?");
        if ($stmt->execute([$username, $lastname, $firstname, $email, $password, $role, $id])) {
            logAction($pdo, $_SESSION['id'], $user['id'], 'edit_user', "New values: ID: " . $user['id']);
            redirectWithSuccess('User has been updated', 'user_list.php');
        } else {
            {
                redirectWithError('Error when updating user', 'user_list.php');
            }
        }
        break;
    case 'bill':
        checkCsrfToken();

        $id = (int) $_POST['id'];
        $manualAmount = isset($_POST['billAmount']) ? (float)$_POST['billAmount'] : 0;
        $reason = sanitize($_POST['reason'] ?? '');
        $quantities = $_POST['quantities'] ?? [];

        if (!is_array($quantities)) {
            redirectWithError('Invalid form data', 'user_list.php');
        }

        $filteredQuantities = [];
        foreach ($quantities as $productId => $qty) {
            $qty = (int)$qty;
            if ($qty > 0) {
                $filteredQuantities[(int)$productId] = $qty;
            }
        }

        $totalProductAmount = 0;
        $productDetails = [];
        $purchaseList = [];

        foreach ($filteredQuantities as $productId => $qty) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product) {
                redirectWithError("Unknown product ID: $productId", 'user_list.php');
            }

            if ($qty > $product['stock_quantity']) {
                redirectWithError("Not enough stock for product: " . htmlspecialchars($product['name']), 'user_list.php');
            }

            $lineTotal = $qty * $product['price'];
            $totalProductAmount += $lineTotal;

            $purchaseList[$productId] = $qty;
            $productDetails[] = "{$product['name']} x{$qty} ({$lineTotal} €)";
        }

        $finalAmount = $manualAmount + $totalProductAmount;

        if ($manualAmount == 0 && $finalAmount == 0) {
            redirectWithError("You must bill something greater or lower than 0.", "bill_user.php?id=$id");
        }

        // Gestion du stock
        foreach ($purchaseList as $productId => $qty) {
            $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stmt->execute([$qty, $productId]);
        }

        // Mise à jour de la note et total spent
        $stmt = $pdo->prepare("UPDATE users SET note = note + ?, total_spent = total_spent + ? WHERE id = ?");
        $stmt->execute([$finalAmount, $finalAmount, $id]);

        // Insertion dans orders
        $items_json = json_encode($purchaseList);
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, datetime, items, total_price) VALUES (?, NOW(), ?, ?)");
        $stmt->execute([$id, $items_json, $finalAmount]);

        // On log
        $logMessage = "Total: {$finalAmount} €";
        if ($manualAmount > 0) $logMessage .= " | Manual: {$manualAmount} €";
        if (!empty($productDetails)) $logMessage .= " | Products: " . implode(', ', $productDetails);
        if (!empty($reason)) $logMessage .= " | Reason: $reason";

        logAction($pdo, $_SESSION['id'], $id, 'bill_user', $logMessage);

        redirectWithSuccess('User has been billed and log is recorded', 'user_list.php');
        break;
    case 'lock':
        checkCsrfToken();

        if (!isset($_POST['id'])){
            redirectWithError("Unknown user ID", 'user_list.php');
        }

        $id = (int) $_POST['id'];

        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            redirectWithError("Unknown user", 'user_list.php');
        }

        // Lock l'user
        if (!$user['locked']) {
            $stmt = $pdo->prepare('UPDATE users SET locked = 1 WHERE id = ?');
            if ($stmt->execute([$id])) {
                $_SESSION['user_id_redirect'] = $id;
                $user['locked'] = true;
                logAction($pdo, $_SESSION['id'], $user['id'], 'lock_user', "Locked user " . strtoupper(htmlspecialchars($user['username'])));
                redirectWithSuccess("User locked successfully.", 'user_list.php');
            } else {
                redirectWithError('Error when updating the user', 'user_list.php');
            }
        } else {
            // Unlock l'user
            $stmt = $pdo->prepare('UPDATE users SET locked = 0 WHERE id = ?');
            if ($stmt->execute([$id])) {
                $_SESSION['user_id_redirect'] = $id;
                $user['locked'] = false;
                logAction($pdo, $_SESSION['id'], $user['id'], 'unlock_user', "Unlocked user " . strtoupper(htmlspecialchars($user['username'])));
                redirectWithSuccess("User unlocked successfully.", 'user_list.php');
            } else {
                redirectWithError('Error when updating the user', 'user_list.php');
            }
        }
        break;
    case 'delete':
        checkCsrfToken();

// récupération de l'user
        if (!isset($_POST['id'])) {
            redirectWithError("Unknown user ID", 'user_list.php');
        }

        $id = (int) $_POST['id'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            redirectWithError('Unknown user', 'user_list.php');
        }

        $targetId = $user['id'];
        $username = $user['username'];
        $email = $user['email'];

        // vérification que le compte supprimé ne soit pas celui d'un admin
        if ($user['role'] !== 'admin') {

            // On éclate la session de l'user supprimé
            if (isset($_SESSION['id']) && $_SESSION['id'] == $id) {
                $_SESSION = [];
                session_unset();
                session_destroy();
            }

            // logique de suppression de la bdd
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            if ($stmt->execute([$id])) {
                logAction($pdo, $_SESSION['id'], $username, 'delete_user', "Username: " . $username . " mail: " . $email);
                redirectWithSuccess('User has been deleted.', 'user_list.php');
            } else {
                redirectWithError('Error while deleting account', 'user_list.php');
            }
        } else {
            redirectWithError("You can't delete admin account.", 'user_list.php');
        }
        break;
    default:
        redirectWithError("Unknown action method", 'user_list.php');
}