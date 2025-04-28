<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../Enums/Environment.php';

$script_version = filemtime(__DIR__ . '/../assets/js/script.js');
?>
<!--DEBUT DE PAGE HTML-->
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="CKS is a private online coffee shop designed for company staff.">
    <meta name="keywords" content="Coffee, snack, beer, office, CKS">
    <meta name="author" content="Alexander AULONG">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/styles.css">
    <script src="<?= $base_url ?>/assets/js/script.js?v=<?= $script_version ?>" defer></script>
    <title>Cks App - Your business coffee shop</title>
</head>

<body>
<!--HEADER-->
<header>
    <button id="burger" aria-label="Toggle navigation" class="toggle_menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <h1><a href="<?= htmlspecialchars($base_url . 'index.php') ?>">Cks App</a></h1>
    <?php if ($isLoggedIn): ?>
        <a id="disconnect_button" title="Disconnect" aria-label="Disconnect" href="<?= $base_url ?>logout.php">
            Disconnect (<?= htmlspecialchars($userUsername) ?>)
        </a>
        <a id="disconnect_button_responsive" title="Disconnect" aria-label="Disconnect" href="<?= $base_url ?>logout.php">
            🔐
        </a>
    <?php else: ?>
        <a id="connect_button" title="Connect" aria-label="Connect" href="<?= $base_url ?>login.php">
            Connect
        </a>
        <a id="connect_button_responsive" title="Connect" aria-label="Connect" href="<?= $base_url ?>login.php">
            🔓
        </a>
    <?php endif; ?>
</header>
<!--BARRE DE NAVIGATION-->
<nav id="navbar-header">
    <ul>
        <li><a title="Home" href="<?= $base_url ?>index.php">Home</a></li>
        <li><a title="Snack shop" href="<?= $base_url ?>snack_shop.php">Buy a snack</a></li>
        <li><a title="User dashboard" href="<?= $base_url ?>user/dashboard.php">Dashboard</a> </li>
        <li><a title="Events" href="<?= $base_url ?>events/event_list.php">Events</a> </li>
        <?php if ($isLoggedIn && $isAdmin): ?>
        <li><a title="Admin dashboard" id="admin_button" href="<?= $base_url ?>admin/admin_dashboard.php">Admin</a> </li>
        <?php endif; ?>
    </ul>
</nav>



