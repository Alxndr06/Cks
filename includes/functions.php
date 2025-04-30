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

    // Durée max de la session (15mn)
    $timeout = 900;

    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $timeout) {
        logoutAndRedirect('Session expired');
    }
    // Mise à jour de l'activité
    $_SESSION['last_activity'] = time();

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

// Formulaire polyvalent (comme tutu)
function adminActions(array $item, string $type): string {
    $itemId = htmlspecialchars($item['id']);
    $csrfToken = getCsrfToken();

    $editPage = "edit_" . $type . ".php";
    $detailsPage = $type . "_details.php";
    $processPage = "process_" . $type . ".php";

    $actions = '<td>';

    $buttons = [];

    // Bouton "Edit"
    $buttons[] = '
        <form action="' . htmlspecialchars($editPage) . '" method="GET" style="display:inline;">
            <input type="hidden" name="id" value="' . $itemId . '">
            <button type="submit" title="Edit ' . ucfirst($type) . '">✏️</button>
        </form>';

    // Bouton "View" (pas pour les produits)
    if ($type !== 'product') {
        $buttons[] = '
        <form action="' . htmlspecialchars($detailsPage) . '" method="GET" style="display:inline;">
            <input type="hidden" name="id" value="' . $itemId . '">
            <button type="submit" title="View ' . ucfirst($type) . '">🔎</button>
        </form>';
    }

    // Bouton "Delete"
    $buttons[] = '
        <form method="POST" action="' . htmlspecialchars($processPage) . '" style="display:inline;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="' . $itemId . '">
            <input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken) . '">
            <button type="submit" title="Delete ' . ucfirst($type) . '" onclick="return confirm(\'Are you sure?\')">🗑️</button>
        </form>';

    // Si c'est un produit : bouton "Restrict/Unrestrict"
    if ($type === 'product') {
        $isRestricted = $item['restricted'];
        $restrictIcon = !$isRestricted ? '⛔' : '✅';
        $restrictTitle = !$isRestricted ? 'Restrict product' : 'Allow product';

        $buttons[] = '
        <form method="POST" action="' . htmlspecialchars($processPage) . '" style="display:inline;">
            <input type="hidden" name="action" value="restrict">
            <input type="hidden" name="id" value="' . $itemId . '">
            <input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken) . '">
            <button type="submit" title="' . $restrictTitle . '" onclick="return confirm(\'Are you sure?\')">' . $restrictIcon . '</button>
        </form>';
    }

    // Si c'est un user : bouton "Lock/Unlock" + bouton "Bill"
    if ($type === 'user') {
        $isLocked = $item['locked'];
        $lockIcon = !$isLocked ? '🔒' : '🔓';
        $lockTitle = !$isLocked ? 'Lock user' : 'Unlock user';

        $buttons[] = '
        <form method="POST" action="' . htmlspecialchars($processPage) . '" style="display:inline;">
            <input type="hidden" name="action" value="lock">
            <input type="hidden" name="id" value="' . $itemId . '">
            <input type="hidden" name="csrf_token" value="' . $csrfToken . '">
            <button type="submit" title="' . $lockTitle . '">' . $lockIcon . '</button>
        </form>';

        $buttons[] = '
        <form method="GET" action="bill_user.php" style="display:inline;">
            <input type="hidden" name="id" value="' . $itemId . '">
            <input type="hidden" name="csrf_token" value="' . $csrfToken . '">
            <button type="submit" title="Bill user">💲</button>
        </form>';
    }

    // On assemble les boutons avec ' | ' entre eux
    $actions .= implode(' | ', $buttons);

    $actions .= '</td>';

    return $actions;
}


function simpleUserActions(array $item, string $type): string {
    $itemId = htmlspecialchars($item['id']);
    $csrfToken = getCsrfToken();

    $detailsPage = $type . "_details.php";
    $processPage = "process_" . $type . ".php";

    $actions = '<td>';

    $buttons = [];

    if ($type !== 'product') {
        $buttons[] = '
            <form action="' . htmlspecialchars($detailsPage) . '" method="GET" style="display:inline;">
                <input type="hidden" name="id" value="' . $itemId . '">
                <button type="submit" title="View ' . ucfirst($type) . '">🔎</button>
            </form>';
    }

    if ($type === 'user') {
        $isLocked = $item['locked'];
        $lockIcon = !$isLocked ? '🔒' : '🔓';
        $lockTitle = !$isLocked ? 'Lock user' : 'Unlock user';

        $buttons[] = '
            <form method="POST" action="' . htmlspecialchars($processPage) . '" style="display:inline;">
                <input type="hidden" name="action" value="lock">
                <input type="hidden" name="id" value="' . $itemId . '">
                <input type="hidden" name="csrf_token" value="' . $csrfToken . '">
                <button type="submit" title="' . $lockTitle . '">' . $lockIcon . '</button>
            </form>';

        $buttons[] = '
            <form method="GET" action="bill_user.php" style="display:inline;">
                <input type="hidden" name="id" value="' . $itemId . '">
                <input type="hidden" name="csrf_token" value="' . $csrfToken . '">
                <button type="submit" title="Bill and payment">💲</button>
            </form>';
    }

    $actions .= implode(' | ', $buttons);
    $actions .= '</td>';

    return $actions;
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
    $activation_link = "https://cks.aulong.fr/user/self_activate.php?token=" . urlencode($activation_token);
    $subject = "Activation de votre compte";
    $message = "Bonjour $firstname,\n\nVotre compte a bien été créé.\nCliquez sur le lien suivant pour activer votre compte :\n$activation_link\n\nMerci.";
    $headers = "From: no-reply@aulong.fr";

    mail($email, $subject, $message, $headers);
}

function get_setting($pdo, $name) {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = ?");
    $stmt->execute([$name]);
    return $stmt->fetchColumn();
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

function recordPayment($pdo, int $paymentAuthorId, float $amountPaid, int $adminId): bool {
    $stmt = $pdo->prepare("INSERT INTO payments (payment_author_id, amount_paid, admin_id) VALUES (?, ?, ?)");
    return $stmt->execute([
        $paymentAuthorId,
        $amountPaid,
        $adminId
    ]);
}

function formatLastPayment($date): string
{
    return $date ? date('d/m/Y H:i', strtotime($date)) : 'No payment yet.';
}