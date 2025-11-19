<?php
require_once __DIR__ . '/../includes/auth.php'; require_admin();
require_once __DIR__ . '/../config/db.php';

// Create
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='create') {
  $code = trim($_POST['code']); $name = trim($_POST['name']);
  $stmt = $pdo->prepare("INSERT INTO subjects (code,name) VALUES (?,?)");
  $stmt->execute([$code,$name]);
  header('Location: subjects.php?success=' . urlencode('Subject created successfully!')); exit;
}

// Delete
if (($_GET['delete'] ?? '') !== '') {
  $id = (int)$_GET['delete'];
  $pdo->prepare("DELETE FROM subjects WHERE id=?")->execute([$id]);
  header('Location: subjects.php?success=' . urlencode('Subject deleted successfully!')); exit;
}

$rows = $pdo->query("SELECT * FROM subjects ORDER BY id DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <h4 class="mb-0">Subjects</h4>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Add Subject</button>
</div>

<input id="tableSearch" class="form-control mb-2" placeholder="Search in table...">

<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
  <thead><tr><th>#</th><th>Code</th><th>Name</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach($rows as $i=>$r): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($r['code']) ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td>
          <a class="btn btn-sm btn-outline-secondary" href="subjects_edit.php?id=<?= $r['id'] ?>">Edit</a>
          <a class="btn btn-sm btn-outline-danger" href="?delete=<?= $r['id'] ?>" onclick="return confirm('Delete this subject?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add Subject</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="create">
        <div class="mb-2"><label class="form-label">Code</label><input name="code" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>