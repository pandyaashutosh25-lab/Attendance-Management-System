<?php
// public/logout.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
session_unset();
session_destroy();

// Set logout message in session
$_SESSION['flash_message'] = 'You have been logged out successfully.';
$_SESSION['flash_type'] = 'info';

header('Location: /attendance-app/public/index.php');
exit;