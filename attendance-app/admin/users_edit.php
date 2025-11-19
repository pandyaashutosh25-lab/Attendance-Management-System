<?php
require_once __DIR__ . '/../includes/auth.php'; 
require_admin();
require_once __DIR__ . '/../config/db.php';

$id = (int)($_GET['id'] ?? 0);

// Fetch student details including class info
$stmt = $pdo->prepare("
  SELECT id, name, email, enroll_no, class_id 
  FROM users 
  WHERE id=? AND role='user'
");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
  http_response_code(404);
  exit('Student not found');
}

// Fetch all available classes for dropdown
$classes = $pdo->query("SELECT id, class_name, section FROM class_mst ORDER BY class_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $enroll = trim($_POST['enroll_no']);
  $class_id = $_POST['class_id'] ?? null;

  // Update basic info and class
  $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, enroll_no=?, class_id=? WHERE id=?");
  $stmt->execute([$name, $email, $enroll, $class_id, $id]);

  // Optional password update
  if (!empty($_POST['password'])) {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hash, $id]);
  }

  header('Location: users.php?success=' . urlencode('Student updated successfully!'));
  exit;
}

include __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-3">Edit Student</h4>

<form method="post" class="card card-body shadow-sm">
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label" style="color:black;">Name</label>
      <input name="name" style="background-color: white; color:black;" class="form-control" 
             value="<?= htmlspecialchars($row['name']) ?>" required>
    </div>

    <div class="col-md-4">
      <label class="form-label" style="color:black;">Email</label>
      <input type="email" name="email" style="background-color: white; color:black;" class="form-control" 
             value="<?= htmlspecialchars($row['email']) ?>" required>
    </div>

    <div class="col-md-4">
      <label class="form-label" style="color:black;">Enrollment No</label>
      <input name="enroll_no" style="background-color: white; color:black;" class="form-control" 
             value="<?= htmlspecialchars($row['enroll_no']) ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label" style="color:black;">Class</label>
      <select name="class_id" class="form-select" required>
        <option value="">-- Select Class --</option>
        <?php foreach ($classes as $class): ?>
          <option value="<?= $class['id'] ?>" 
            <?= $row['class_id'] == $class['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($class['class_name']) ?> <?= htmlspecialchars($class['section']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label" style="color:black;">New Password (optional)</label>
      <input type="password" name="password" style="background-color: white; color:black;" class="form-control">
    </div>
  </div>

  <div class="mt-3">
    <button class="btn btn-primary">Update</button>
    <a class="btn btn-secondary" href="users.php">Back</a>
  </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>