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
    <a title="Create a new user" href="add_user.php" class="add_user_button">➕Create new user</a>
    <table class="user-table">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Note</th>
            <th>Role</th>
            <th>Status</th>
            <th>Total spent</th>
            <th>Account status</th>
            <th>Quick actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars(ucfirst(strtolower($user['username']))) ?></td>
                <td><?= colorDebt($user['note']) ?> €</td>
                <td><?= htmlspecialchars(ucfirst(strtolower($user['role']))) ?></td>
                <td><?php if (!$user['locked']): ?>Unlocked<?php else: ?>Locked<?php endif; ?></td>
                <td><?= $user['total_spent'] ?> €</td>
                <td><?= displayActiveStatus($user['is_active']) ?></td>
                <?= restrictedAdminActions($user) ?>
            </tr>
        <?php endforeach; ?>
    </table>
    <?= backupLink('user_management.php', '🔙back to user management'); ?>
</div>


<?php require __DIR__ . '/../../includes/footer.php'; ?>
