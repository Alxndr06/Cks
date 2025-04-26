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

<table class="user-table">
<tr>
    <th>ID</th>
    <th>Date</th>
    <th>Title</th>
    <th>Description</th>
    <th>Participants</th>
    <th>Actions</th>
</tr>
    <?php foreach ($events as $event) : ?>
        <tr>
            <td><?= $event['id'] ?></td>
            <td><?= date("d/m/Y H:i", strtotime($event['date'])) ?></td>
            <td><?= htmlspecialchars($event['title']) ?></td>
            <td><?= htmlspecialchars($event['description']) ?></td>
            <?php
            $participants = json_decode($event['participants']);
            $participantCount = (is_array($participants)) ? count($participants) : 0;
            ?>
            <td><?= $participantCount ?> participants</td>
            <?= eventAdminActions($event) ?>
        </tr>
    <?php endforeach; ?>
</table>

    <?= backupLink('../admin_dashboard.php', '🔙back to admin dashboard'); ?>
</div>

<?php require '../../includes/footer.php'; ?>