<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

checkAdmin();
$csrf_token = getCsrfToken();

if (!isset($_GET['id'])) {
    redirectWithError('Unknown article ID', 'news_management.php');
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    redirectWithError('Article not found', 'news_management.php');
}

?>

<div id="main-part">
    <h2>Edit article</h2>
    <form action="process_article.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= $article['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <label for="title">Titre :</label><br>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($article['title']) ?>" required><br><br>

        <label for="content">Content :</label><br>
        <textarea id="content" name="content" rows="10"  title="content" required><?= htmlspecialchars($article['content']) ?></textarea><br><br>

        <button type="submit" title="Edit Article">Edit</button>
    </form>
    <div class="backupLinkContainer">
        <?= backupLink('news_management.php'); ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
