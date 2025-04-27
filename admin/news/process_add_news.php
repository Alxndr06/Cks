<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAdmin();
checkCsrfToken();

$title = $_POST['title'];
$content = $_POST['content'];
$author = $_SESSION['username'];

if(empty($title) || empty($content)){
    redirectWithError('Title and/or content are missing', 'news_management.php');
}

$stmt = $pdo->prepare("INSERT INTO news (title, content, author) VALUES (?, ?, ?)");
if($stmt->execute([$title, $content, $author])) {
    logAction($pdo, $_SESSION['id'], null, 'add_article', 'Title: ' . $title);
    redirectWithSuccess('News has been added', 'news_management.php');
} else {
    redirectWithError('An error occured while adding news', 'news_management.php');
}

