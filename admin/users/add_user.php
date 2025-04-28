<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

?>

<div id="main-part">
    <h2>Create a user</h2>
    <?php echo displayErrorOrSuccessMessage() ?>

    <form method="POST" action="process_user.php">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <label for="username">Username :</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="lastname">Lastname :</label>
        <input type="text" id="lastname" name="lastname" required><br><br>

        <label for="firstname">Firstname :</label>
        <input type="text" id="firstname" name="firstname" required><br><br>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password :</label>
        <input type="password" id="password" name="password" autocomplete="off" required><br><br>

        <label for="role">Select role :</label>
        <select name="role" id="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br><br>

        <button type="submit">Create account</button><br><br>
    </form>
    <div class="backupLinkContainer">
        <?= backupLink('user_management.php'); ?>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
