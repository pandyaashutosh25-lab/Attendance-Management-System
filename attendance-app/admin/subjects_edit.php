<?php
require_once __DIR__ . '/../includes/auth.php'; require_admin();
require_once __DIR__ . '/../config/db.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE id=?");
$stmt->execute([$id]); $row = $stmt->fetch();
if (!$row) { http_response_code(404); exit('Subject not found'); }

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $code = trim($_POST['code']); $name = trim($_POST['name']);
  $pdo->prepare("UPDATE subjects SET code=?, name=? WHERE id=?")->execute([$code,$name,$id]);
  header('Location: subjects.php?success=' . urlencode('Subject updated successfully!')); exit;
}

include __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-3">Edit Subject</h4>
<form method="post" class="card card-body shadow-sm">
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Code</label>
      <input name="code" class="form-control" value="<?= htmlspecialchars($row['code']) ?>" required>
    </div>
    <div class="col-md-8">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
    </div>
  </div>
  <div class="mt-3">
    <button class="btn btn-primary">Update</button>
    <a class="btn btn-secondary" href="subjects.php">Back</a>
  </div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>