<?php
global $selfRegistration;
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

// Récupération des utilisateurs inactifs
$stmt = $pdo->query("SELECT id, username, email, firstname, lastname FROM users WHERE is_active = 0");
$inactiveUsers = $stmt->fetchAll();
?>

<div id="main-part">
    <h2>Register system</h2>
    <?= displayErrorOrSuccessMessage() ?>

    <div class="selfRegStatus">
        <form method="POST" action="toggle_self_registration.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <button type="submit" class="btn">
                <?= $selfRegistration ? '🔒 Disable' : '🔓 Enable' ?> self-registration
            </button>
        </form>
    </div>

    <table class="user-table">
        <tr>
            <th class="col-id">ID</th>
            <th class="col-username">Username</th>
            <th class="col-name">Name</th>
            <th class="col-email">Email</th>
            <th>Action</th>
        </tr>
        <?php foreach ($inactiveUsers as $user): ?>
            <tr>
                <td class="col-id"><?= $user['id'] ?></td>
                <td class="col-username"><?= htmlspecialchars(ucfirst(strtolower($user['username']))) ?></td>
                <td class="col-name">
                    <?= htmlspecialchars(ucfirst(strtolower($user['firstname']))) . ' ' . htmlspecialchars(ucfirst(strtolower($user['lastname']))) ?>
                </td>
                <td class="col-email"><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <form method="POST" action="activate_user.php">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                        <button type="submit" class="btn" title="Activate user">✅ Activate</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="backupLinkContainer">
        <?= backupLink('../admin_dashboard.php'); ?>
    </div>
</div>
