<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

$csrf_token = getCsrfToken();

if ($isLoggedIn) {
    header('Location: ../index.php');
    exit;
}

//LOGIQUE ajout d'utilisateur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCsrfToken();

    if ($_POST['password'] !== $_POST['confirmPassword']) {
        redirectWithError("Passwords do not match.", 'register.php');
    }

    $username = preg_replace('/\s+/', '', trim($_POST['username']));
    $lastname = trim($_POST['lastname']);
    $firstname = trim($_POST['firstname']);
    $email = strtolower(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_active = false; // Faux par défaut
    $activation_token = bin2hex(random_bytes(32));

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
        redirectWithError(implode('<br>', $errors), 'register.php');

    }

    //Vérification de l'unicité du mail
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        redirectWithError('Email is already taken', 'register.php');
    }

    //Vérification de l'unicité de l'username
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        redirectWithError('Username is already taken', 'register.php');
    }

    $stmt = $pdo->prepare("INSERT INTO users (username, lastname, firstname, email, password, is_active, activation_token) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $lastname, $firstname, $email, $password, $is_active, $activation_token])) {
        sendRegisterMail($email, $firstname, $activation_token);
        redirectWithSuccess('Your account has been created. You will receive an activation link by mail.', 'index.php');
    } else {
        redirectWithError('Something went wrong. Please try again later.', 'register.php');
    }
}
?>

<div id="main-part">
    <h2>Register</h2>
    <?php echo displayErrorOrSuccessMessage() ?>
    <div class="register_form">
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
        <input type="password" id="password" name="password" autocomplete="new-password" required><br><br>

        <label for="confirmPassword">Confirm password :</label>
        <input type="password" id="confirmPassword" name="confirmPassword" autocomplete="new-password" ><br><br>

        <button type="submit">Create account</button><br><br>
    </form>
    </div>
    <?= backupLink('../login.php', '🔙back to login page'); ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
