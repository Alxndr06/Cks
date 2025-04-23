<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
checkMethodPost();
checkCsrfToken();

// Récupération des informations de l'utilisateur
if (!isset($_POST['id'])) {
    redirectWithError('Unknown user ID.', 'user_list.php');
}

$id = (int) $_POST['id'];


//On récupére l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    redirectWithError('User does not exist.', 'user_list.php');
}
?>

<div id="main-part">
    <h2><?= htmlspecialchars(strtoupper($user['username'])) ?></h2>
    <?php if ($user['locked'] == 1) : ?>
    <p class="alert">USER LOCKED</p>
    <?php endif; ?>
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
            <th>ROLE</th>
            <td><?= htmlspecialchars(ucfirst(strtolower($user['role']))) ?></td>
        </tr>
        <tr>
            <th>CREATED</th>
            <td><?= htmlspecialchars(date("d/m/Y H:i", strtotime($user['created_at']))) ?></td>
        </tr>
        <tr>
            <th>DEBT</th>
            <td><?= colorDebt((float)$user['note']) ?> €</td>
        </tr>
        <tr>
            <th>LAST PAYMENT</th>
            <td><em style="color: gray;">Coming soon</em></td>
        </tr>
        <tr>
            <th>TOTAL SPENT</th>
            <td><?= (float)$user['total_spent'] ?> €</td>
        </tr>
        <tr>
            <th>ACCOUNT STATUS</th>
            <td><?= displayActiveStatus($user['is_active']) ?></td>
        </tr>
    </table>
    <?= AdvancedAdminActions($user) ?>
    <br><br>
    <?= backupLink('user_list.php','🔙back to list'); ?>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>