<?php
require_once '../includes/header.php';

checkConnect();
checkNoteIsNull();

?>

    <div id="main-part">
        <h2>Payment</h2>
        <p>Available soon</p>

        <?= backupLink('dashboard.php', '🔙back to dashboard') ?>
    </div>

<?php include '../includes/footer.php'; ?>