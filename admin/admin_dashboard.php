<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

checkAdmin();

$stmt = $pdo->query("SELECT SUM(note) FROM `users`");
$totalDebt = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT count(*) FROM `users`");
$totalUser = $stmt->fetchColumn();

?>

<div id="main-part">
    <h2>Admin dashboard</h2>
    <div class="stats-container">
        <div class="display-stat">💵 Total debt: <?= number_format($totalDebt, 2) ?>€</div>
        <div class="display-stat">👥 Total users: <?= $totalUser ?></div>
    </div>
    <div class="dashboard_container">
        <a class="dashboard_item" title="News management" href="news/news_management.php">📰News Management</a></li>
        <a class="dashboard_item" title="User management" href="users/user_management.php">👮🏻User Management</a>
        <a class="dashboard_item" title="Stock management" href="stock/stock_management.php">📦Stock Management</a>
        <a class="dashboard_item" title="Event management" href="events/event_management.php">📅Event Management</a>
        <a class="dashboard_item" title="Finance" href="users/debts_management.php">💵Finance</a>
        <a class="dashboard_item" title="Order management" href="orders/order_management.php">📋Order Management</a>
        <a class="dashboard_item" title="Server logs" href="logs.php">📜Server logs</a>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>