<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../includes/functions.php';

checkAdmin();
checkMethodPost();
checkCsrfToken();

if (!isset($_POST['action'])) {
    redirectWithError('Unknown action', 'event_management.php');
}

$action = $_POST['action'];

switch ($action) {
    case 'add':
        $title = sanitize($_POST['title']);
        $address = sanitize($_POST['address']);
        $description = sanitize($_POST['description']);
        $date = $_POST['date'];
        $isActive = 1;

        if (strtotime($date) < time()) {
            redirectWithError('The date must be in the future.', 'event_management.php');
        }

        $stmt = $pdo->prepare("INSERT INTO events (title, address, description, date, author_id, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $address ,$description, $date, $_SESSION['id'], $isActive]);

        logAction($pdo, $_SESSION['id'], null, 'add_event', 'Created event: ' . $title);
        redirectWithSuccess('Event created successfully', 'event_management.php');
        break;

    case 'edit':
        if (!isset($_POST['id'])) {
            redirectWithError('Unknown event ID', 'event_management.php');
        }
        $id = (int) $_POST['id'];
        $title = sanitize($_POST['title']);
        $address = sanitize($_POST['address']);
        $description = sanitize($_POST['description']);
        $date = $_POST['date'];

        if (strtotime($date) < time()) {
            redirectWithError('The date must be in the future.', 'event_management.php');
        }

        $stmt = $pdo->prepare("UPDATE events SET title = ?, address = ?, description = ?, date = ? WHERE id = ?");
        $stmt->execute([$title, $address, $description, $date, $id]);

        logAction($pdo, $_SESSION['id'], $id, 'edit_event', 'Edited event ID:' . $id);
        redirectWithSuccess('Event updated successfully', 'event_management.php');
        break;

    case 'delete':
        if (!isset($_POST['id'])) {
            redirectWithError('Unknown event ID', 'event_management.php');
        }
        $id = (int) $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);

        logAction($pdo, $_SESSION['id'], $id, 'delete_event', 'Deleted event ID:' . $id);
        redirectWithSuccess('Event deleted successfully', 'event_management.php');
        break;

    default:
        redirectWithError('Invalid action', 'event_management.php');
}
