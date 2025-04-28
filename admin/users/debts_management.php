<?php
require_once __DIR__ . '/../../includes/header.php';

checkAdmin();
?>


    <div id="main-part">
        <h2>Debts management</h2>
        <div class="dashboard_container">
            <a class="dashboard_item" title="Bill all users" href="#">💵Bill all users</a>
            <a class="dashboard_item" title="Print invoice summary" href="#">🧾Print invoice summary</a>
        </div>
        <div class="backupLinkContainer">
            <?= backupLink("../admin_dashboard.php"); ?>
        </div>
    </div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>