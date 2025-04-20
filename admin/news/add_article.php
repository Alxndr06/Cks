<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

?>

<div id="main-part">
    <h2>Add an article</h2>
    <form action="process_add.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <label for="title">Titre :</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="content">Contenu :</label><br>
        <textarea id="content" name="content" rows="10" required></textarea><br><br>

        <button type="submit">Publier</button>
    </form>

</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
