<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
    redirectWithError('Unknown event ID.', '../index.php');
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT events.*, users.firstname, users.lastname, users.username
    FROM events
    JOIN users ON events.author_id = users.id
    WHERE events.id = ?
");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    redirectWithError('Event not found.', '../index.php');
}
?>

<div id="main-part">
    <h2>View event</h2>

    <div class="view_event">
        <p><strong>Author:</strong> <?= htmlspecialchars($event['firstname'] . ' ' . $event['lastname']) ?> (<?= htmlspecialchars($event['username']) ?>)</p>
        <p><strong>Title:</strong> <?= htmlspecialchars($event['title']) ?></p>
        <p><strong>Date:</strong> <?= date("d/m/Y H:i", strtotime($event['date'])) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($event['address']) ?></p>
        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($event['description'])) ?></p>
    </div>

    <?= backupLink('event_list.php', "🔙Back to event list"); ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
