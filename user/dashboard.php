<?php
require_once __DIR__ . '/../includes/header.php';
checkConnect();
?>

<div id="main-part">
    <?php echo displayErrorOrSuccessMessage() ?>
    <h2>Dashboard</h2>
    <div id="user_resume_bis">
        <?= displayLockedStatus() ?>
        <p>Hello <?= htmlspecialchars(ucfirst(strtolower($userUsername))) ?> - <?php if (!$noteIsNull): ?>You owe <?= colorDebt((float)$userNote) ?> € <?php else: ?> You have no debt <?php endif; ?>.</p>
    </div>
    <div class="dashboard_container">
        <a class="dashboard_item" title="My profile" href="profile.php">🙋‍♂️️My profile</a>
        <a class="dashboard_item" title="Orders logs" href="../order/order_history.php">🧺My orders</a>
        <?php if (!$noteIsNull): ?><a class="dashboard_item" title="Pay my bill" href="payment.php">💵Pay my bill</a><?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>