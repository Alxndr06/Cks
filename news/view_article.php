<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_GET['id'])) {
    redirectWithError('News article not found!', 'index.php');
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();
?>

<div id="main-part">
    <h2>Read article</h2>
    <div class="news_read">

    <h1><?= htmlspecialchars($event['title']) ?></h1>
    <p><em>Par <?= htmlspecialchars($event['author']) ?>, le <?= date('d/m/Y à H:i', strtotime($event['created_at'])) ?></em></p>

    <?php if (!empty($event['image'])): ?>
        <img src="../uploads/<?= htmlspecialchars($event['image']) ?>" alt="Illustration" width="400">
    <?php endif; ?>

    <p><?= nl2br(htmlspecialchars($event['content'])) ?></p>

    </div>

    <?= backupLink('../index.php', "🔙Back to homepage"); ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>