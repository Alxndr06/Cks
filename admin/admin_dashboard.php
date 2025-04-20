<?php
require_once __DIR__ . '/../includes/header.php';
checkAdmin();
?>

<div id="main-part">
    <h2>Admin dashboard</h2>
    <div class="dashboard_container">
        <a class="dashboard_item" title="News manager" href="news/news_management.php">📰News management</a></li>
        <a class="dashboard_item" title="Users management" href="users/user_management.php">👮User management</a>
        <a class="dashboard_item" title="Stock management" href="stock/stock_management.php">📦Stock management</a>
        <a class="dashboard_item" title="Debts management" href="users/debts_management.php">💵Debts management</a>
        <a class="dashboard_item" title="Order management" href="orders/order_management.php">📋Order management</a>
        <a class="dashboard_item" title="Server logs" href="logs.php">📜Server logs</a>
    </div>
</div>


<?php require __DIR__ . '/../includes/footer.php'; ?>