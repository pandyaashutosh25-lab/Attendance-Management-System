<?php
require_once __DIR__ . '/../includes/auth.php'; require_user();
require_once __DIR__ . '/../config/db.php';

$id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name,email,enroll_no FROM users WHERE id=?"); $stmt->execute([$id]);
$row = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name = trim($_POST['name']); $email = trim($_POST['email']); $enroll = trim($_POST['enroll_no']);
  $pdo->prepare("UPDATE users SET name=?, email=?, enroll_no=? WHERE id=?")->execute([$name,$email,$enroll,$id]);
  if (!empty($_POST['password'])) {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hash,$id]);
  }
  header('Location: profile.php?success=' . urlencode('Profile updated successfully!')); exit;
}

include __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-3">My Profile</h4>
<form method="post" class="card card-body shadow-sm">
  <div class="row g-3">
    <div class="col-md-4"><label class="form-label" style="color: black;">Name</label>
      <input name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required></div>
    <div class="col-md-4"><label class="form-label" style="color: black;">Email</label>
      <input type="email" name="email" class="form-control" style="background-color: white; color:black;" value="<?= htmlspecialchars($row['email']) ?>" required></div>
    <!-- <div class="col-md-4"><label class="form-label">Enrollment No</label>
      <input name="enroll_no" class="form-control" value="<?= htmlspecialchars($row['enroll_no']) ?>"></div> -->
    <div class="col-md-4"><label class="form-label" style="color: black;">New Password (optional)</label>
      <input type="password" name="password" class="form-control" style="background-color: white; color:black;"></div>
  </div>
  <div class="mt-3">
    <button class="btn btn-primary">Save</button>
  </div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>