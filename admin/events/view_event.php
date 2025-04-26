<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_GET['id'])) {
    redirectWithError('Unknown event ID', '../index.php');
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    redirectWithError('Event not found.', '../index.php');
}
?>

    <div id="main-part">
        <h2>View event</h2>
        <div class="view_event">
            <p><strong>Title:</strong> <?= htmlspecialchars($event['title']) ?></p>
            <p><strong>Date:</strong> <?= date("d/m/Y H:i", strtotime($event['date'])) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($event['address']) ?></p>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>

            </div>

            <?= backupLink('event_list.php', "🔙Back to event list"); ?>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>