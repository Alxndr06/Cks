<?php
require_once '../includes/header.php';
require_once '../config/config.php';

checkConnect();

$id = (int) $_SESSION['id'];

$stmt = $pdo->prepare("SELECT id, lastname, firstname, email, note FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
?>

    <div id="main-part">
        <h2>My profile</h2>
        <?= displayLockedStatus() ?>
        <table class="user-table-vertical">
            <tr>
                <th>ID</th>
                <td><?= $user['id'] ?></td>
            </tr>
            <tr>
                <th>LASTNAME</th>
                <td><?= htmlspecialchars(ucfirst(strtolower($user['lastname']))) ?></td>
            </tr>
            <tr>
                <th>FIRSTNAME</th>
                <td><?= htmlspecialchars(ucfirst(strtolower($user['firstname']))) ?></td>
            </tr>
            <tr>
                <th>MAIL</th>
                <td><?= htmlspecialchars($user['email']) ?></td>
            </tr>
            <tr>
                <th>DEBT</th>
                <td><?= colorDebt((float)$user['note']) ?> €</td>
            </tr>
        </table>
        <div class="dashboard_container">
            <a class="dashboard_item" title="Change password" href="change_password.php">🔑Change my password</a>
            <a class="dashboard_item" title="Change mail" href="change_mail.php">📧Change mail</a>
            <a class="dashboard_item" title="Delete my account" href="#">❌Delete my account</a>
        </div>
        <?= backupLink('dashboard.php', '🔙back to dashboard') ?>
    </div>

<?php include '../includes/footer.php'; ?>