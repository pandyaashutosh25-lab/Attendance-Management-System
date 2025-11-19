<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function require_login() {
  if (!isset($_SESSION['user_id'])) {
    header('Location: /attendance-app/public/index.php');
    exit;
  }
}

function require_admin() {
  require_login();
  if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied');
  }
}

function require_user() {
  require_login();
  if (($_SESSION['role'] ?? '') !== 'user') {
    http_response_code(403);
    exit('Access denied');
  }
}
