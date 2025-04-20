<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAdmin();
checkMethodPost();
checkCsrfToken();

if (!isset($_POST['id'])) die('Unknown article');
$articleId = $_POST['id'];

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$_POST['id']]);
$article = $stmt->fetch();

if (!$article){
    $_SESSION['error'] = 'Article not found';
    header('Location: news_management.php');
    exit;
}

$targetId = $article['id'];
$title = $article['title'];
$description = "Title = " . $title;

$stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
if ($stmt->execute([$article['id']])){
    logAction($pdo, $_SESSION['id'], null, 'delete_article', $description);
    $_SESSION['success'] = 'Article deleted successfully';
    header('Location: news_management.php');
    exit;
} else {
    $_SESSION['error'] = 'Article could not be deleted';
    header('Location: news_management.php');
    exit;
}
