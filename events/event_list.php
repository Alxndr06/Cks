<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

// On récupère tous les événements actifs
$stmt = $pdo->query("SELECT * FROM events WHERE is_active = 1 ORDER BY date ASC");
$events = $stmt->fetchAll();
?>

    <div id="main-part">
        <h2>Upcoming Events</h2>
        <table class="user-table">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Title</th>
                <th>Description</th>
                <th>Participants</th>
                <th>Actions</th>
            </tr>
            <?php if (!empty($events)) :  ?>
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
                    <td>Incoming</td>
                </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <p>No pending event</p>
            <?php endif; ?>
        </table>

        <?= backupLink('../index.php', "🔙 Back to homepage"); ?>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>