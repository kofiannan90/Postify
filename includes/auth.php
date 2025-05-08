<?php
session_start();

// Set timeout duration (in seconds)
$timeout = 1800; // 30 minutes

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

?>