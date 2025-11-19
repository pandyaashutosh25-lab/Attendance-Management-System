<?php
// public/index.php (Login)
require_once __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
  $stmt->execute([$email]); 
  $user = $stmt->fetch();
  if ($user && password_verify($pass, $user['password'])) {
    $_SESSION['user_id']=$user['id']; 
    $_SESSION['role']=$user['role']; 
    $_SESSION['name']=$user['name'];
    
    // Set success message in session
    $_SESSION['flash_message'] = 'Logged in successfully!';
    $_SESSION['flash_type'] = 'success';

    header('Location: ' . ($user['role']==='admin' ? '/attendance-app/admin/dashboard.php' : '/attendance-app/user/dashboard.php'));
    exit;
  } else { 
    $error = "Invalid email or password"; 
    // Set error message in session
    $_SESSION['flash_message'] = $error;
    $_SESSION['flash_type'] = 'danger';
    header('Location: /attendance-app/public/index.php'); // Redirect back to login with error
    exit;
  }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="mb-3">Login</h4>
        <?php // The flash message is now handled by header.php ?>
        <form method="post" class="needs-validation" novalidate>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" style="background-color: white; color:black;" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" style="background-color: white; color:black;" required>
          </div>
          <button class="btn btn-primary w-100">Login</button>
          <!-- <p class="mt-3 small text-muted">Tip: run <code>admin_seed.php</code> once to create the first admin.</p> -->
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>