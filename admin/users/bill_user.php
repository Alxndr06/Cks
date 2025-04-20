<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

// On récupère l'user
if (!isset($_GET['id'])) die('Unknown user');
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) die('Unknown user');

//On s'assure de la méthode post du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkCsrfToken();
    $note = $user['note'];
    $billAmount = (float)$_POST['billAmount'];
    $reason = sanitize($_POST['reason']);

    if ($billAmount <= 0) {
        redirectWithError('The amount must be greater than 0.', 'user_list.php');
    }

    $updatedNote = $note + $billAmount;

    $stmt = $pdo->prepare("UPDATE users SET note = ? WHERE id = ?");
    if ($stmt->execute([$updatedNote, $id])) {
        logAction($pdo, $_SESSION['id'], $user['id'], 'bill_user', "Amount: " . $billAmount . " € | Reason: " . $reason);
        redirectWithSuccess('User has been billed', 'user_list.php');
    } else {
        redirectWithError('Something went wrong', 'user_list.php');
    }

}

?>

<div id="main-part">
    <h2>Bill <?= ucfirst(strtolower($user['username'])) ?></h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <h3>Manual billing</h3>
        <label for="billAmount">Amount to bill :</label>
        <input type="number" placeholder="Enter amount" step="0.01" id="billAmount" name="billAmount" required>

        <label for="reason">Reason :</label>
        <input type="text" placeholder="Reason for billing" id="reason" name="reason" required><br><br>

        <h3>Billing with products</h3>
        <p>Product list incoming</p>

        <button type="submit" onclick="return confirm('Bill <?= ucfirst(strtolower($user['username'])) ?> ?')">✅ Bill user</button>
        <button type="reset">❌ Clear form</button>
    </form>
    <br><br><br>
   </div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
