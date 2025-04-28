<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

?>

<div id="main-part">
    <h2>Create an event</h2>
    <form action="process_event.php" method="post" enctype="multipart/form-data" class="login_form">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <label for="date">Choose a date :</label><br>
        <input type="datetime-local" id="date" name="date" min="<?= date('Y-m-d\TH:i') ?>" required><br><br>

        <label for="address">Location :</label><br>
        <input type="text" id="address" name="address"><br><br>

        <label for="title">Titre :</label><br>
        <input type="text" id="title" name="title" maxlength="50" required><br><br>

        <label for="description">Description :</label><br>
        <textarea id="description" name="description" rows="10" required></textarea><br><br>

        <button type="submit">Create Event</button>
    </form>
    <div class="backupLinkContainer">
        <?= backupLink('event_management.php'); ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
