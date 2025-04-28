<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

$stmt = $pdo->prepare("SELECT * FROM `news` ORDER BY created_at DESC");
$stmt->execute();
$news = $stmt->fetchAll();
?>

    <div id="main-part">
        <h2>All articles</h2>
        <?php echo displayErrorOrSuccessMessage() ?>
        <?php foreach ($news as $article): ?>
            <div class="news">
                <h3><?= htmlspecialchars($article['title']) ?></h3>

                <?php if (!empty($article['image'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($article['image']) ?>" alt="Illustration" width="200">
                <?php endif; ?>

                <p><?= nl2br(substr(htmlspecialchars($article['content']), 0, 250)) ?>...</p>
                <a class="news_readmore" href="view_article.php?id=<?= $article['id'] ?>">Lire plus</a>
                <p><em>Par <?= htmlspecialchars($article['author']) ?>, le <?= date('d/m/Y à H:i', strtotime($article['created_at'])) ?></em></p>
            </div>

        <?php endforeach; ?>
        <div class="backupLinkContainer">
            <?= backupLink('../index.php'); ?>
        </div>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>