<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';
checkAdmin();

if (!isset($_GET['id'])) {
    redirectWithError('Unknown event ID.', 'event_management.php');
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
    redirectWithError('Event not found.', 'event_management.php');
}
?>

<div id="main-part">
    <h2>View event</h2>
        <table class="user-table-vertical">
            <tr>
                <th>ID</th>
                <td><?= $event['id'] ?></td>
            </tr>
            <tr>
                <th>AUTHOR</th>
                <td><?= htmlspecialchars($event['firstname'] . ' ' . $event['lastname']) ?> (<?= htmlspecialchars($event['username']) ?>)</td>
            </tr>
            <tr>
                <th>TITLE</th>
                <td><?= htmlspecialchars($event['title']) ?></td>
            </tr>
            <tr>
                <th>DATE</th>
                <td><?= date("d/m/Y H:i", strtotime($event['date'])) ?></td>
            </tr>
            <tr>
                <th>ADDRESS</th>
                <td><?= htmlspecialchars($event['address']) ?></td>
            </tr>
            <tr>
                <th>DESCRIPTION</th>
                <td><?= nl2br(htmlspecialchars($event['description'])) ?></td>
            </tr>
            <tr>
                <th>EVENT STATUS</th>
                <td><?= displayActiveStatus($event['is_active']) ?></td>
            </tr>
        </table>
        <div class="OEB">
            <?= adminActions($event, 'event') ?>
        </div>
    <div class="backupLinkContainer">
    <?= backupLink('event_management.php'); ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
