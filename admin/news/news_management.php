<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';
checkAdmin();

$stmt = $pdo->prepare("SELECT * FROM news");
$stmt->execute();
$news= $stmt->fetchAll();

?>
<div id="main-part">
    <h2>News manager</h2>
    <?php echo displayErrorOrSuccessMessage() ?>
    <a title="Write a new article" href="add_article.php" class="interface-button">➕Write Article</a>
    <table class="user-table">
    <tr>
        <th>Date</th>
        <th>Title</th>
        <th>Author</th>
        <th>Quick actions</th>
    </tr>
    <?php foreach($news as $article): ?>
    <tr>
        <td><?= htmlspecialchars($article['created_at']); ?></td>
        <td><?= htmlspecialchars($article['title']) ?> </td>
        <td><?= htmlspecialchars($article['author']) ?></td>
        <?= adminActions($article, 'article') ?>
    </tr>
    <?php endforeach; ?>
    </table>
    <?= backupLink('../admin_dashboard.php', '🔙back to admin dashboard'); ?>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>