<?php
global $base_url;
require_once __DIR__ . '/../../includes/header.php';

checkAdmin();
?>

    <div id="main-part">
        <h2>User management</h2>
        <div class="dashboard_container">
            <a class="dashboard_item" title="Create a new user" href="add_user.php">➕Create new user</a>
            <a class="dashboard_item" title="User list" href="user_list.php">👮🏼User list</a>
            <a class="dashboard_item" title="Enable or disable self register system" href="#">📋Self register system</a>
        </div>
        <div class="backupLinkContainer">
            <?= backupLink('../admin_dashboard.php'); ?>
        </div>
    </div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>