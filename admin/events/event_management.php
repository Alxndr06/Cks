<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();

// On récupère tous les événements actifs
$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll();

?>

<div id="main-part">
    <h2>Event management</h2>
    <?= displayErrorOrSuccessMessage() ?>
    <a title="Create a new event" href="add_event.php" class="interface-button">➕Create new event</a>
<table class="user-table">
<tr>
    <th>ID</th>
    <th>Date</th>
    <th>Title</th>
    <th>Description</th>
    <th>Actions</th>
</tr>
    <?php foreach ($events as $event) : ?>
        <tr>
            <td><?= $event['id'] ?></td>
            <td><?= date("d/m/Y H:i", strtotime($event['date'])) ?></td>
            <td><?= htmlspecialchars($event['title']) ?></td>
            <td><?= nl2br(substr(htmlspecialchars($event['description']), 0, 50)) ?>...</td>
            <?php
            $participants = (!empty($event['participants'])) ? json_decode($event['participants']) : [];
            $participantCount = (is_array($participants)) ? count($participants) : 0;
            ?>
            <?= adminActions($event, 'event') ?>
        </tr>
    <?php endforeach; ?>
</table>
    <div class="backupLinkContainer">
    <?= backupLink('../admin_dashboard.php'); ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>