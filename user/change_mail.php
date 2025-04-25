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

    if ($_POST['email'] === $_POST['confirmEmail']) {

        $email = !empty($_POST['email']) ? ($_POST['email']) : $user['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirectWithError('Invalid email address.', 'change_email.php');
        }

        // Vérification de l'unicité du mail
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            redirectWithError('Email is already taken', 'change_email.php');
        }

    //Mise à jour de l'user sur la bdd
    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
    if ($stmt->execute([$email, $id])) {
        redirectWithSuccess('Your email has been changed', 'dashboard.php');
    } else {
        redirectWithError('Error changing email', 'change_email.php');
    }
    } else {
        redirectWithError('Emails do not match', 'change_email.php');
    }
}
?>

    <div id="main-part">
        <h2>Change my email</h2>
        <?= displayErrorOrSuccessMessage() ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <label for="email">New email :</label>
            <input type="email" id="email" name="email" ><br><br>

            <label for="confirmEmail">Confirm email :</label>
            <input type="email" id="confirmEmail" name="confirmEmail" ><br><br>

            <button type="submit">Change email</button><br><br>
        </form>
        <?= backupLink("dashboard.php?id=$id", '🔙back to dashboard'); ?>
    </div>

<?php require __DIR__ . '/../includes/footer.php'; ?>