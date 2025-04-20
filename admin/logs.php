<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

checkAdmin();

$stmt = $pdo->query("
    SELECT logs.id, logs.admin_id, logs.target_id, 
           adminUser.username AS admin_username, 
           targetUser.username AS target_username,
           logs.action, logs.description, logs.ip_address, logs.created_at 
    FROM logs 
    LEFT JOIN users AS adminUser ON logs.admin_id = adminUser.id
    LEFT JOIN users AS targetUser ON logs.target_id = targetUser.id
    ORDER BY logs.created_at DESC
");
$logs = $stmt->fetchAll();
?>

    <div id="main-part">
        <h2>Server logs</h2>
        <div id="nav_dashboard">
            <table class="user-table">
                <tr>
                    <th>ID</th>
                    <th>Admin</th>
                    <th>Target</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP adress</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['id'] ?></td>
                        <td><?= htmlspecialchars($log['admin_username']) ?></td>
                        <td><?= $log['target_username'] ? : 'User' ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['description']) ?></td>
                        <td><?= htmlspecialchars($log['ip_address']) ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($log['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?= backupLink('admin_dashboard.php', '🔙back admin dashboard'); ?>
        </div>
    </div>


<?php require __DIR__ . '/../includes/footer.php'; ?>