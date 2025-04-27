<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

checkConnect();

// On récupère tous les événements actifs
$stmt = $pdo->query("SELECT * FROM events WHERE is_active = 1 ORDER BY date ASC");
$events = $stmt->fetchAll();
?>

    <div id="main-part">
        <h2>Upcoming Events</h2>
        <table class="user-table">
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php if (!empty($events)) :  ?>
            <?php foreach ($events as $event) : ?>
                <tr>
                    <td><?= date("d/m/Y H:i", strtotime($event['date'])) ?></td>
                    <td><?= htmlspecialchars($event['title']) ?></td>
                    <td><?= nl2br(substr(htmlspecialchars($event['description']), 0, 50)) ?>...</td>
                    <?= userEventActions($event) ?>
                </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <p>No pending event</p>
            <?php endif; ?>
        </table>

        <?= backupLink('../index.php', "🔙 Back to homepage"); ?>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>