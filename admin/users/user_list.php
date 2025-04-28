<?php
require_once __DIR__ . '/../../includes/header.php';
getCsrfToken();
checkAdmin();

//Récupération de la liste des utilisateurs
$stmt = $pdo->query("SELECT id, username, email, note, total_spent, role, locked, is_active FROM users");
$users = $stmt->fetchAll();
?>

<div id="main-part">
    <h2>User list</h2>
    <?= displayErrorOrSuccessMessage(); ?>
    <a title="Create a new user" href="add_user.php" class="interface-button">➕Create new user</a>
    <table class="user-table">
        <tr>
            <th class="col-id">ID</th>
            <th class="col-username">Username</th>
            <th class="col-note">Note</th>
            <th class="col-role">Role</th>
            <th class="col-locked">Status</th>
            <th class="col-total-spent">Total spent</th>
            <th class="col-acc-status">Account status</th>
            <th>Quick actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td class="col-id"><?= $user['id'] ?></td>
                <td class="col-username"><?= htmlspecialchars(ucfirst(strtolower($user['username']))) ?></td>
                <td class="col-note"><?= colorDebt($user['note']) ?> €</td>
                <td class="col-role"><?= htmlspecialchars(ucfirst(strtolower($user['role']))) ?></td>
                <td class="col-locked"><?php if (!$user['locked']): ?>Unlocked<?php else: ?>Locked<?php endif; ?></td>
                <td class="col-total-spent"><?= $user['total_spent'] ?> €</td>
                <td class="col-acc-status"><?= displayActiveStatus($user['is_active']) ?></td>
                <?= restrictedAdminActions($user) ?>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="backupLinkContainer">
        <?= backupLink('user_management.php'); ?>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
