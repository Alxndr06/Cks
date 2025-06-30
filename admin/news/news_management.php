<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';
checkAdmin();

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$result = paginate($pdo, 'news', $page, 5, 'id DESC');
$news = $result['items'];

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
    <div class="pagination">
        <?php for ($i = 1; $i <= $result['total_pages']; $i++): ?>
            <?php if ($i === $result['current_page']): ?>
                <strong><?= $i ?></strong>
            <?php else: ?>
                <a href="?page=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <div class="backupLinkContainer">
        <?= backupLink('../admin_dashboard.php'); ?>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>