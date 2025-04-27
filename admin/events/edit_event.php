<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

if (!isset($_GET['id'])) {
    redirectWithError('Unknown event ID', 'event_manager.php');
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    redirectWithError('Unknown event', 'event_manager.php');
}

?>

<div id="main-part">
    <h2>Edit event</h2>
    <form action="process_event.php" method="post" enctype="multipart/form-data" class="login_form">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $event['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <label for="date">Change date :</label><br>
        <input type="datetime-local" id="date" name="date" min="<?= date('Y-m-d\TH:i') ?>" value="<?= date('Y-m-d\TH:i', strtotime($event['date'])) ?>" required><br><br>

        <label for="address">Change location :</label><br>
        <input type="text" id="address" name="address" value="<?= htmlspecialchars($event['address']) ?>"><br><br>

        <label for="title">Change title :</label><br>
        <input type="text" id="title" name="title" maxlength="50" value="<?= htmlspecialchars($event['title']) ?>" required><br><br>

        <label for="description">Description :</label><br>
        <textarea id="description" name="description" rows="10" required><?= htmlspecialchars($event['description']) ?></textarea><br><br>

        <button type="submit">Update Event</button>
    </form>
    <?= backupLink('event_management.php', "🔙Back to event management"); ?>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
