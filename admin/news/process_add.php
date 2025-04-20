<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAdmin();
checkCsrfToken();

$title = $_POST['title'];
$content = $_POST['content'];
$author = $_SESSION['username'];

if(empty($title) || empty($content)){
    $_SESSION['error'] = 'Title and/or content are missing';
    header('Location: news_management.php');
    exit;
}

$stmt = $pdo->prepare("INSERT INTO news (title, content, author) VALUES (?, ?, ?)");
if($stmt->execute([$title, $content, $author])) {
    logAction($pdo, $_SESSION['id'], null, 'add_article', 'Title: ' . $title);
    $_SESSION['success'] = 'News added';
    header('Location: ../../index.php');
    exit;
} else {
    $_SESSION['error'] = 'News not added';
    header('Location: news_management.php');
    exit;
}

