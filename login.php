<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db_connect.php';

// Récupération du Token CSRF
$csrf_token = getCsrfToken();

//On s'assure que l'user ne soit pas déjà log
if (isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

// Traitement du formulaire (On s'assure de la méthode post)
if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
    checkCsrfToken();

    //Récupération de manière sécurisée pour éviter toute injection d'html du seigneur dans mes variables (nettoyage)
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // On prépare notre requête SQL
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        if (!$user['is_active']) {
            redirectWithError('Your account is not activated yet. Check your email to activate your account.', 'login.php');
        }
        // Génére un nouvel ID pour éviter le session fixation
        session_regenerate_id(true);

        //Création de la session
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['note'] = $user['note'];
        $_SESSION['total_spent'] = $user['total_spent'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['locked'] = $user['locked'];

        // Régénérer le token CSRF pour la prochaine requête
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header("Location: user/dashboard.php");
        exit();
    } else {
        redirectWithError('Username or password is incorrect.', 'login.php');
    }
}

?>

<div id="main-part">
    <h2>Login</h2>
    <?= displayErrorOrSuccessMessage(); ?>
    <div class="login_form">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <label for="username">Username :</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password :</label>
        <input type="password" id="password" name="password" autocomplete="off" required><br><br>

        <button class="login_button" type="submit">Login</button><br><br>
        <?= selfRegisterActivatedOrNot($selfRegistration) ?>
    </form>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>