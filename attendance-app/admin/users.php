<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

// Helper to set flash (you already have header showing flash in includes/header.php)
function set_flash($msg, $type = 'success') {
    $_SESSION['flash_message'] = $msg;
    $_SESSION['flash_type'] = $type;
}

// CREATE - run only when form submitted with action=create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    // collect and sanitize inputs safely
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $enroll  = trim($_POST['enroll_no'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;

    // Basic validation
    if ($name === '' || $email === '' || $password_raw === '') {
        set_flash('Please fill required fields (Name, Email, Password).', 'danger');
        header('Location: users.php');
        exit;
    }

    // Hash password
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    // Prepare and execute insert (role fixed as 'user')
    try {
        // If class_id is null, we still include column and bind null
        $stmt = $pdo->prepare("INSERT INTO users (role, name, email, password, enroll_no, class_id) VALUES ('user', ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $enroll, $class_id]);
        set_flash('Student created successfully!', 'success');
        header('Location: users.php');
        exit;
    } catch (PDOException $e) {
        // check for duplicate email or other DB error
        set_flash('Error creating student: ' . $e->getMessage(), 'danger');
        header('Location: users.php');
        exit;
    }
}

// DELETE - safe delete by id (only users with role='user')
if (!empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'")->execute([$id]);
        set_flash('Student deleted successfully!', 'success');
        header('Location: users.php');
        exit;
    } catch (PDOException $e) {
        set_flash('Error deleting student: ' . $e->getMessage(), 'danger');
        header('Location: users.php');
        exit;
    }
}

// FETCH - fetch students with their class (if any)
$rows = $pdo->query("
    SELECT users.id, users.name, users.email, users.enroll_no, users.class_id,
           class_mst.class_name, class_mst.section
    FROM users
    LEFT JOIN class_mst ON users.class_id = class_mst.id
    WHERE users.role = 'user'
    ORDER BY users.id DESC
")->fetchAll();

// Fetch classes for the modal dropdown
$classes = $pdo->query("SELECT id, class_name, section FROM class_mst ORDER BY class_name")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h4 class="mb-0">Students</h4>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Add Student</button>
</div>

<input id="tableSearch" class="form-control mb-2" placeholder="Search in table...">

<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
  <thead>
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Email</th>
      <th>Enrollment No</th>
      <th>Class</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($rows as $i => $r): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td><?= htmlspecialchars($r['enroll_no']) ?></td>
        <td><?= htmlspecialchars(($r['class_name'] ?? '—') . (!empty($r['section']) ? ' - ' . $r['section'] : '')) ?></td>
        <td>
          <a class="btn btn-sm btn-outline-secondary" href="users_edit.php?id=<?= $r['id'] ?>">Edit</a>
          <a class="btn btn-sm btn-outline-danger" href="?delete=<?= $r['id'] ?>" onclick="return confirm('Delete this student?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content" novalidate>
      <div class="modal-header">
        <h5 class="modal-title">Add Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="create">
        <div class="mb-2">
          <label class="form-label">Name</label>
          <input name="name" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Enrollment No</label>
          <input name="enroll_no" class="form-control">
        </div>
        <div class="mb-2">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <!-- Class dropdown (optional) -->
        <div class="mb-2">
          <label class="form-label">Select Class</label>
          <select name="class_id" class="form-select">
            <option value="">-- Select Class --</option>
            <?php foreach ($classes as $class): ?>
              <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name'] . ' - ' . $class['section']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>