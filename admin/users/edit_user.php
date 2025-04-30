<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

// Récupérer l'utilisateur
if (!isset($_GET['id'])) {
    redirectWithError('Unknown user ID', 'user_list.php');
}
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    redirectWithError('Unknown user', 'user_list.php');
}

?>

    <div id="main-part">
        <h2>Edit user</h2>
        <?= displayErrorOrSuccessMessage() ?>
        <form method="POST" action="process_user.php">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <label for="username">Username :</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>

            <label for="lastname">Lastname :</label>
            <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required><br><br>

            <label for="firstname">Firstname :</label>
            <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required><br><br>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

            <label for="password">Password :</label>
            <input type="password" id="password" name="password" ><br><br>

            <label for="role">Select role :</label>
            <select name="role" id="role" required>
                <option value="">--Select role--</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select><br><br>

            <button type="submit">Edit user</button><br><br>
        </form>
        <div class="backupLinkContainer">
            <?= backupLink("user_details.php?id=$id"); ?>
        </div>
    </div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>