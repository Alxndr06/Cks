<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

//LOGIQUE ajout d'utilisateur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCsrfToken();
    $username = preg_replace('/\s+/', '', trim($_POST['username']));
    $lastname = trim($_POST['lastname']);
    $firstname = trim($_POST['firstname']);
    $email = strtolower(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $is_active = true;

    $allowed_roles = ['user', 'admin'];
    if (!in_array($role, $allowed_roles)) {
        redirectWithError("Invalid role selected.", "add_user.php");
    }

    $errors = [];

    if (strlen($lastname) < 2 || strlen($firstname) < 2) {
        $errors[] = "Firstname and Lastname must be at least 2 characters long.";
    }

    if (!validateString($lastname)) {
        $errors[] = "Lastname contains invalid characters.";
    }

    if (!validateString($firstname)) {
        $errors[] = "Firstname contains invalid characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (strlen($username) < 3 || strlen($username) > 20) {
        $errors[] = "Username must be between 3 and 20 characters.";
    }

    if (!empty($errors)) {
        redirectWithError(implode('<br>', $errors), 'add_user.php');
    }

    //Vérification de l'unicité du mail
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        redirectWithError('Email is already taken', 'add_user.php');
    }

    //Vérification de l'unicité de l'username
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        redirectWithError('Username is already taken', 'add_user.php');
    }

    $stmt = $pdo->prepare("INSERT INTO users (username, lastname, firstname, email, password, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $lastname, $firstname, $email, $password, $role, $is_active])) {
        logAction($pdo, $_SESSION['id'], $username , 'create_user', "User: " . $username . " mail: " . $email . " Role: " . $role . "Account: " . displayActiveStatus($is_active));
        header('Location: user_list.php');
        exit;
    } else {
        redirectWithError("There was a problem adding user.", "add_user.php");
    }
}
?>

<div id="main-part">
    <h2>Create a user</h2>
    <?php echo displayErrorOrSuccessMessage() ?>

    <form method="POST">
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
    <?= backupLink('user_management.php', '🔙back to user management'); ?>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
