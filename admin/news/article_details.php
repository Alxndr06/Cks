<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/db_connect.php';

if (!isset($_GET['id'])) {
    redirectWithError('Article is not found!', 'news_management.php');
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    redirectWithError('Article is not found!', 'news_management.php');
}
?>

<div id="main-part">
    <h2>Article details</h2>
        <div class="news_read">

            <h1><?= htmlspecialchars($article['title']) ?></h1>
            <p><em>Par <?= htmlspecialchars($article['author']) ?>, le <?= date('d/m/Y à H:i', strtotime($article['created_at'])) ?></em></p>

            <?php if (!empty($article['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($article['image']) ?>" alt="Illustration" width="400">
            <?php endif; ?>

            <p><?= nl2br(htmlspecialchars($article['content'])) ?></p>

        </div>

    <?= backupLink('news_management.php', "🔙Back to news management"); ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>