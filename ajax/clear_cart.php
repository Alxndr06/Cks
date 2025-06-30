<?php
require_once '../config/config.php';

unset($_SESSION['cks_cart']);

echo json_encode(['success' => true]);
