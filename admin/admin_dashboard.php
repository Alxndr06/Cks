<?php
require_once __DIR__ . '/../includes/header.php';
checkAdmin();
?>

<div id="main-part">
    <h2>Admin dashboard</h2>
    <div class="dashboard_container">
        <a class="dashboard_item" title="News management" href="news/news_management.php">📰News Management</a></li>
        <a class="dashboard_item" title="User management" href="users/user_management.php">👮🏻User Management</a>
        <a class="dashboard_item" title="Stock management" href="stock/stock_management.php">📦Stock Management</a>
        <a class="dashboard_item" title="Event management" href="events/event_management.php">📅Event Management</a>
        <a class="dashboard_item" title="Debt management" href="users/debts_management.php">💵Debts Management</a>
        <a class="dashboard_item" title="Order management" href="orders/order_management.php">📋Order Management</a>
        <a class="dashboard_item" title="Server logs" href="logs.php">📜Server logs</a>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>