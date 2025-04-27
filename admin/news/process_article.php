<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAdmin();
checkMethodPost();
checkCsrfToken();

if (!isset($_POST['action'])) {
    redirectWithError('Unknown action method', 'news_management');
}

$action = $_POST['action'];

switch ($action) {
    case 'add':
        $title = htmlspecialchars($_POST['title']);
        $content = htmlspecialchars($_POST['content']);
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
        break;
    case 'edit':
        if (!isset($_POST['id'])) {
            redirectWithError('Unknown article ID', 'news_management.php');
        }

        $id = (int) $_POST['id'];

        $title = $_POST['title'];
        $content = $_POST['content'];

        $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
        if($stmt->execute([$title, $content, $id])) {
            logAction($pdo, $_SESSION['id'], null, 'edit_article', 'Title: ' . $title);
            redirectWithSuccess('Article has been updated', 'news_management.php');
        }
        break;
    case 'delete':
        if (!isset($_POST['id'])) {
            redirectWithError('Unknown article ID', 'news_management.php');
        }

        $id = (int) $_POST['id'];
        $title = $_POST['title'];

        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        if($stmt->execute([$id])) {
            logAction($pdo, $_SESSION['id'], null, 'delete_article', 'Title: ' . $title);
            redirectWithSuccess('Article has been deleted', 'news_management.php');
        }else {
            redirectWithError('An error occured while deleting article', 'news_management.php');
        }
        break;
    default:
        redirectWithError('Unknown action method', 'news_management');
}