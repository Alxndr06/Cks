<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

checkConnect();
$csrf_token = getCsrfToken();

// Récupérer l'utilisateur
if (!isset($_SESSION['id'])) die("Unknown user");
$id = $_SESSION['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) die("Unknown user");

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['password'] === $_POST['confirmPassword']) {
    checkCsrfToken();
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];


//Mise à jour de l'user sur la bdd
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    if ($stmt->execute([$password, $id])) {
        $_SESSION['success'] = 'Your password has been changed';
        header('Location: dashboard.php?success=1');
        exit;
    } else {
        echo '<div class="error_message">Error when changing password</div>';
    }
}
?>

    <div id="main-part">
        <h2>Edit user</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <label for="password">New password :</label>
            <input type="password" id="password" name="password" ><br><br>
            <label for="confirmPassword">Confirm password :</label>
            <input type="password" id="confirmPassword" name="confirmPassword" ><br><br>
            <button type="submit">Change password</button><br><br>
        </form>
        <?= backupLink("dashboard.php?id=$id", '🔙back to dashboard'); ?>
    </div>

<?php require __DIR__ . '/../includes/footer.php'; ?>