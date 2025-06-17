<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/includes/functions.php';

$csrf_token = getCsrfToken();
?>

<div id="main-part" role="main">
    <h2>Choose a category</h2>
    <?php echo displayErrorOrSuccessMessage() ?>
    <div class="category_container">
        <a href="snack_shop.php?cat=drinks"><img class="category_item" alt="Fresh Drinks" src="assets/img/drinks.png"></a>
        <a href="snack_shop.php?cat=snacking"><img class="category_item" alt="Snacks" src="assets/img/snacking.png"></a>
        <a href="snack_shop.php?cat=coffee"><img class="category_item" alt="Coffee" src="assets/img/coffee.png"></a>
        <a href="snack_shop.php?cat=all"><img class="category_item" alt="All products" src="assets/img/all.png"></a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>