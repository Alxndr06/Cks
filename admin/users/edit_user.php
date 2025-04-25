<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

// Récupérer l'utilisateur
if (!isset($_GET['id'])) die("Unknown user");
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) die("Unknown user");

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCsrfToken();
    $username = $_POST['username'];
    $lastName = $_POST['lastname'];
    $firstName = $_POST['firstname'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
    $role = $_POST['role'];


//Mise à jour de l'user sur la bdd
    $stmt = $pdo->prepare("UPDATE users SET username = ?, lastname = ?, firstname = ?, email = ?, password = ?, role = ? WHERE id = ?");
    if ($stmt->execute([$username, $lastName, $firstName, $email, $password, $role, $id])) {
        logAction($pdo, $_SESSION['id'], $user['id'], 'edit_user', "New values: ID: " . $user['id']);
        header('Location: user_list.php');
        exit;
    } else {
        echo '<div class="error_message">Error when updating the user</div>';
    }
}
?>

    <div id="main-part">
        <h2>Edit user</h2>
        <form method="POST">
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
        <?= backupLink("user_list.php", '🔙back to list'); ?>
    </div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>