<?php
require_once '../includes/header.php';
require_once '../config/config.php';

checkConnect();
?>

    <div id="main-part">
        <h2>My profile</h2>
        <?= displayLockedStatus() ?>
        <div class="dashboard_container">
            <a class="dashboard_item" title="My informations" href="#">📋My informations</a>
            <a class="dashboard_item" title="Change password" href="change_password.php">🔑Change my password</a>
            <a class="dashboard_item" title="Delete my account" href="#">❌Delete my account</a>
        </div>
        <?= backupLink('dashboard.php', '🔙back to dashboard') ?>
    </div>

<?php include '../includes/footer.php'; ?>