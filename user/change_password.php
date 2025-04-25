<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

checkConnect();
$csrf_token = getCsrfToken();

// Récupérer l'utilisateur
if (!isset($_SESSION['id'])) {
    redirectWithError('Unknown user ID', '../index.php');
}
$id = (int) $_SESSION['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    redirectWithError('Unknown user', '../index.php');
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCsrfToken();

    if ($_POST['password'] === $_POST['confirmPassword']) {
        if (empty($_POST['password'])) {
            redirectWithError('Password cannot be empty.', 'change_password.php');
        }

        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    //Mise à jour de l'user sur la bdd
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    if ($stmt->execute([$password, $id])) {
        redirectWithSuccess('Your password has been changed', 'dashboard.php');
    } else {
        redirectWithError('Error changing password', '../index.php');
    }
    } else {
        redirectWithError('Passwords do not match', 'change_password.php');
    }
}
?>

    <div id="main-part">
        <h2>Change my password</h2>
        <?= displayErrorOrSuccessMessage() ?>
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