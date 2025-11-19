<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Initialize flash message variables
$flash_message = '';
$flash_type = '';

// Check for success or error messages in session or GET parameters
if (isset($_SESSION['flash_message'])) {
  $flash_message = $_SESSION['flash_message'];
  $flash_type = $_SESSION['flash_type'] ?? 'info'; // Default to info
  unset($_SESSION['flash_message']); // Clear after displaying
  unset($_SESSION['flash_type']);
} elseif (isset($_GET['success'])) {
  $flash_message = $_GET['success'];
  $flash_type = 'success';
} elseif (isset($_GET['error'])) {
  $flash_message = $_GET['error'];
  $flash_type = 'danger';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/attendance-app/public/assets/css/style.css" rel="stylesheet">
    </head>
  <body>
    <!-- Add this inside .navbar -->
<button id="themeToggle" class="btn" style="padding: 8px 16px; border-radius: 6px;">
    🌞 Light Mode
</button>

  <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="../public/assets/img/logo.jpeg">Attendance</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarsExample">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
            <li class="nav-item"><a class="nav-link" href="/attendance-app/admin/dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/attendance-app/admin/users.php">Students</a></li>
            <li class="nav-item"><a class="nav-link" href="/attendance-app/admin/classes.php">Classes</a></li>
            <li class="nav-item"><a class="nav-link" href="/attendance-app/admin/subjects.php">Subjects</a></li>
            <li class="nav-item"><a class="nav-link" href="/attendance-app/admin/attendance_mark.php">Mark Attendance</a></li>
            <li class="nav-item"><a class="nav-link" href="/attendance-app/admin/attendance_list.php">Attendance List</a></li>
          <?php elseif(isset($_SESSION['role']) && $_SESSION['role']==='user'): ?>
            <li class="nav-item"><a class="nav-link" href="/attendance-app/user/dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/attendance-app/user/my_attendance.php">My Attendance</a></li>
            <li class="nav-item"><a class="nav-link" href="/attendance-app/user/profile.php">Profile</a></li>
          <?php endif; ?>
        </ul>
        <ul class="navbar-nav ms-auto">
          <?php if(isset($_SESSION['user_id'])): ?>
            <li class="nav-item"><span class="navbar-text me-3">Hi, <?= htmlspecialchars($_SESSION['name'] ?? '') ?></span></li>
            <li class="nav-item"><a class="btn btn-outline-danger btn-sm" href="/attendance-app/public/logout.php">Logout</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <main class="container py-3">
    <?php if ($flash_message): ?>
      <div class="alert alert-<?= $flash_type ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>