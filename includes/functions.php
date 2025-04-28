<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Enums/Environment.php';

use Enums\Environment;
use JetBrains\PhpStorm\NoReturn;

function getCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function checkCsrfToken(): void {
    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Invalid CSRF token.");
    }
}

function checkMethodPost() : void {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        die("Invalid request method.");
    }
}

function getUserById(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null; // Si aucun utilisateur n'est trouvé, retourne `null`
}

//Nettoie une entrée
function sanitize($str) : string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

//Valide du texte simple
function validateString(string $str) : string {
    return preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $str);
}

#[NoReturn] function redirectToLogin() : void {
    global $base_url;
    header("Location: " . $base_url . "login.php");
    exit;
}

#[NoReturn] function logoutAndRedirect(string $message): void {
    session_unset();
    session_destroy();
    header("Location: ../login.php?message=" . urlencode($message));
    exit;
}

//Colorie la note en fonction de son montant
function colorDebt(float $note): string {
    $color = $note <= 5 ? 'green' : ($note <= 10 ? 'darkorange' : 'red');
    return sprintf('<span style="color: %s;">%s</span>', $color, htmlspecialchars($note));
}

function checkNoteIsNull() : void {
    if ($_SESSION['note'] == 0) {
    header("Location: ../user/dashboard.php");
    exit;
    }
}

// Vérifie si l'utilisateur est connecté
function checkConnect() : void {
    global $pdo;

    if (!isset($_SESSION['id'])) {
        redirectToLogin();
    }

    $user = getUserById($pdo, $_SESSION['id']);

    if (!$user) {
        logoutAndRedirect('Account deleted');
    }
    $_SESSION['username'] = $user['username']; // Met à jour le bon nom d'utilisateur
    $_SESSION['note'] = $user['note'];
    $_SESSION['locked'] = $user['locked']; // Update l'état de lock
}

// Vérifie si l'utilisateur est admin.
function checkAdmin() : void {
    checkConnect();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    redirectToLogin();
    }
}

function displayLockedStatus() : string {
    if (isset($_SESSION['id']) && isset($_SESSION['locked']) && $_SESSION['locked']) {
        return '<p class="alert">Your account is locked. You cannot place an order.</p>';
    }
    return '';
}

function displayActiveStatus($isActive) : string {
    if ($isActive) {
        return 'Active';
    } else {
        return 'Inactive';
    }
}

// fonction de lien retour
function backupLink(string $default, string $label = '🔙 Back'): string {
    $backupUrl = $default;

    // Vérifier si HTTP_REFERER est présent et appartient à notre domaine
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $parsedUrl = parse_url($_SERVER['HTTP_REFERER']);

        // Vérifier que le domaine du REFERER est bien celui du site
        if (!empty($parsedUrl['host']) && $parsedUrl['host'] === $_SERVER['SERVER_NAME']) {
            $backupUrl = $_SERVER['HTTP_REFERER'];
        }
    }

    return sprintf('<a href="%s" class="backup-button">%s</a>', htmlspecialchars($backupUrl, ENT_QUOTES, 'UTF-8'), htmlspecialchars($label, ENT_QUOTES, 'UTF-8'));
}

function restrictedAdminActions($user) : string {
    $userId = htmlspecialchars($user['id']);
    $csrfToken = getCsrfToken();
    $lockIcon = !$user['locked'] ? '🔒' : '🔓';


    return sprintf('
        <td>
            <form action="user_details.php" method="GET" style="display:inline;">
                <input type="hidden" name="id" value="%s">
                <button type="submit">🔎</button>
            </form>
            | <form action="lock_user.php" method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="%s">
                <input type="hidden" name="id" value="%s">
                <button type="submit">%s</button>
            </form>
            | <form action="bill_user.php" method="GET" style="display:inline;">
                <input type="hidden" name="id" value="%s">
                <button type="submit">💲</button>
            </form>
        </td>',
        $userId,
        $csrfToken, $userId, $lockIcon,
        $userId
    );
}

// actions sur les events par les users
function userEventActions($event) : string {
    $eventID = (int) $event['id'];

    return sprintf('
            <td>
            <form action="view_event.php" method="GET" style="display:inline;">
                <input type="hidden" name="id" value="%s">
                <button type="submit" title="view event">🔎</button>
            </form>
            </td>
    ',
    $eventID);
}

// fonction de barre de gestion des users
function advancedAdminActions($user) : string {
    $userId = htmlspecialchars($user['id']);
    $csrfToken = getCsrfToken(); // 🔹 Stocker le token pour éviter plusieurs appels
    $lockIcon = !$user['locked'] ? '🔒' : '🔓';

    return sprintf('
        <div class="OEB">
             <form action="lock_user.php" method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="%s">
                <input type="hidden" name="id" value="%s">
                <button type="submit" title="Lock/Unlock user">%s</button>
            </form>
            | <form action="bill_user.php" method="GET" style="display:inline;">
                <input type="hidden" name="csrf_token" value="%s">
                <input type="hidden" name="id" value="%s">
                <button type="submit" title="Bill user">💲</button>
            </form>
            | <form action="edit_user.php" method="GET" style="display:inline;">
                <input type="hidden" name="csrf_token" value="%s">
                <input type="hidden" name="id" value="%s">
                <button type="submit" title="Edit user">✏️</button>
            </form>
            | <form action="delete_user.php" method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="%s">
                <input type="hidden" name="id" value="%s">
                <button type="submit" title="Delete user" onclick="return confirm(\'Are you sure you want to delete this user?\')">🗑️</button>
              </form>
        </div>',
        $csrfToken, $userId, $lockIcon,
        $csrfToken, $userId,
        $csrfToken, $userId,
        $csrfToken, $userId
    );
}


function productAdminActions($product) : string {
    $productId = htmlspecialchars($product['id']);
    $csrfToken = getCsrfToken();
    $isRestricted = $product['restricted'];
    $restrictIcon = !$isRestricted ? '⛔' : '✅';
    $restrictTitle = !$isRestricted ? 'Restrict product' : 'Allow product';

    return sprintf('
    <td class="col-actions">
    <div class="desktop-action">
        <form action="edit_product.php" method="GET" style="display:inline;">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="Edit product">✏️</button>
        </form>
        |
        <form action="restrict_product.php" method="POST" style="display:inline;">
            <input type="hidden" name="csrf_token" value="%s">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="%s">%s</button>
        </form>
        |
        <form action="delete_product.php" method="POST" style="display:inline;">
            <input type="hidden" name="csrf_token" value="%s">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="Delete product" onclick="return confirm(\'Are you sure you want to delete this product?\')">🗑️</button>
        </form>
        </div>
        <div class="mobile-action">
                <form action="edit_product.php" method="GET" style="display:inline;">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="Show product">🔎</button>
        </form>
        </div>
    </td>',
        $productId,
        $csrfToken, $productId, $restrictTitle, $restrictIcon,
        $csrfToken, $productId,
        $productId
    );
}

function newsAdminActions($article) : string
{
    $articleId = htmlspecialchars($article['id']);
    $csrfToken = getCsrfToken();

    return sprintf('
    <td>
        <form action="edit_article.php" method="GET" style="display:inline;">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="Edit article">✏️</button>
        </form>
        |
        <form action="../../news/view_article.php" method="GET" style="display:inline;">
            <input type="hidden" name="csrf_token" value="%s">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="View article">🔎</button>
        </form>
        |
        <form action="delete_article.php" method="POST" style="display:inline;">
            <input type="hidden" name="csrf_token" value="%s">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="Delete article" onclick="return confirm(\'Are you sure you want to delete this article?\')">🗑️</button>
        </form>
    </td>',
        $articleId,
        $csrfToken, $articleId,
        $csrfToken, $articleId
    );
}

function eventAdminActions($event) : string {
    $eventId = htmlspecialchars($event['id']);
    $csrfToken = getCsrfToken();

    return sprintf('
    <td>
        <form action="edit_event.php" method="GET" style="display:inline;">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="Edit event">✏️</button>
        </form>
        |
        <form action="event_details.php" method="GET" style="display:inline;">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="View event">🔎</button>
        </form>
        |
        <form method="POST" action="process_event.php" style="display:inline;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="%s">
            <input type="hidden" name="csrf_token" value="%s">
            <button type="submit" onclick="return confirm(\'Are you sure?\')">🗑️</button>
        </form>
    </td>',
        $eventId,
        $eventId,
        $eventId,
        $csrfToken
    );
}

// Formulaire polyvalent (comme tutu)
function adminActions(array $item, string $type) : string {
    $itemId = htmlspecialchars($item['id']);
    $csrfToken = getCsrfToken();

    $editPage = "edit_" . $type . ".php";
    $detailsPage = $type . "_details.php";
    $processPage = "process_" . $type . ".php";

    return sprintf('
    <td>
        <form action="%s" method="GET" style="display:inline;">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="Edit %s">✏️</button>
        </form>
        |
        <form action="%s" method="GET" style="display:inline;">
            <input type="hidden" name="id" value="%s">
            <button type="submit" title="View %s">🔎</button>
        </form>
        |
        <form method="POST" action="%s" style="display:inline;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="%s">
            <input type="hidden" name="csrf_token" value="%s">
            <button type="submit" title="Delete %s" onclick="return confirm(\'Are you sure ?\')">🗑️</button>
        </form>
    </td>',
        htmlspecialchars($editPage),
        $itemId,
        ucfirst($type),
        htmlspecialchars($detailsPage),
        $itemId,
        ucfirst($type),
        htmlspecialchars($processPage),
        $itemId,
        $csrfToken,
        ucfirst($type)
    );
}

function logAction($pdo, $admin_id, $target_id, $action, $description) {
    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $pdo->prepare("INSERT INTO logs (admin_id, target_id, action, description, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$admin_id, $target_id, $action, $description, $ip]);
}

function displayEnvMessage(): string {
    $env = Environment::fromEnv();

    return match($env) {
        Environment::Development => 'development',
        Environment::Production => 'production',
    };
}

function displayErrorOrSuccessMessage() : string {
    $message = '';

if (isset($_SESSION['success'])) {
    $message = sprintf('<p class="success_message">%s</p>', $_SESSION['success']);
    unset($_SESSION['success']);
} elseif (isset($_SESSION['error'])) {
    $message = sprintf('<p class="error_message">%s</p>', $_SESSION['error']);
    unset($_SESSION['error']);
}
return $message;
}

function sendRegisterMail($email, $firstname, $activation_token) : void {
    $activation_link = "https://cks.aulong.fr/user/activate.php?token=" . urlencode($activation_token);
    $subject = "Activation de votre compte";
    $message = "Bonjour $firstname,\n\nVotre compte a bien été créé.\nCliquez sur le lien suivant pour activer votre compte :\n$activation_link\n\nMerci.";
    $headers = "From: no-reply@aulong.fr";

    mail($email, $subject, $message, $headers);
}

function selfRegisterActivatedOrNot($selfRegistration) : string {
    if (!$selfRegistration) {
        return sprintf('
        <p class="small_message">No account yet ? <a href="mailto:contact@aulong.fr">Contact me</a></p><br>
        <p class="small_message">(self registration is disabled)</p>
        ');
    } else {
        return sprintf('
        <p class="small_message">No account yet ?</p> 
        <a class="small_message" href="../user/register.php">Register</a>
        ');
    }
}

function redirectWithError(string $message, string $location): void {
    $_SESSION['error'] = $message;
    header("Location: $location");
    exit;
}

function redirectWithSuccess(string $message, string $location): void {
    $_SESSION['success'] = $message;
    header("Location: $location");
    exit;
}