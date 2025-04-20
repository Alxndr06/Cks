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
if (!isset($_POST['id'])) die('Unknown user');
$id = $_POST['id'];

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) die('Unknown user');

// Logique de lock à refactoriser
if (!$user['locked']) {
    $stmt = $pdo->prepare('UPDATE users SET locked = 1 WHERE id = ?');
    if ($stmt->execute([$id])) {
        $_SESSION['user_id_redirect'] = $id;
        $user['locked'] = true;
        session_write_close();
        logAction($pdo, $_SESSION['id'], $user['id'], 'lock_user', "Locked user " . strtoupper(htmlspecialchars($user['username'])));
        header("Location: user_list.php");
        exit;
    } else {
        echo '<div class="error_message">Error when updating the user</div>';
    }
} else {
    $stmt = $pdo->prepare('UPDATE users SET locked = 0 WHERE id = ?');
    if ($stmt->execute([$id])) {
        $_SESSION['user_id_redirect'] = $id;
        $user['locked'] = false;
        session_write_close();
        logAction($pdo, $_SESSION['id'], $user['id'], 'unlock_user', "Unlocked user " . strtoupper(htmlspecialchars($user['username'])));
        header("Location: user_list.php");
        exit;
    } else {
        echo '<div class="error_message">Error when updating the user</div>';
    }
}


