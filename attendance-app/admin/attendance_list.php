<?php
require_once __DIR__ . '/../includes/auth.php'; 
require_admin();
require_once __DIR__ . '/../config/db.php';

// Fetch all dropdown data
$subjects = $pdo->query("SELECT id, code, name FROM subjects ORDER BY name")->fetchAll();
$classes = $pdo->query("SELECT id, class_name, section FROM class_mst ORDER BY class_name")->fetchAll();

// If a class filter is selected, fetch only those students
$class_id = (int)($_GET['class_id'] ?? 0);

if ($class_id) {
  $stmtStudents = $pdo->prepare("SELECT id, name FROM users WHERE role='user' AND class_id=? ORDER BY name");
  $stmtStudents->execute([$class_id]);
  $students = $stmtStudents->fetchAll();
} else {
  $students = $pdo->query("SELECT id, name FROM users WHERE role='user' ORDER BY name")->fetchAll();
}

// Get filters
$subject_id = (int)($_GET['subject_id'] ?? 0);
$user_id = (int)($_GET['user_id'] ?? 0);
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

// Build WHERE clause dynamically
$where = []; 
$params = [];

if ($class_id) { 
  $where[] = "u.class_id=?"; 
  $params[] = $class_id; 
}
if ($subject_id) { 
  $where[] = "a.subject_id=?"; 
  $params[] = $subject_id; 
}
if ($user_id) { 
  $where[] = "a.user_id=?"; 
  $params[] = $user_id; 
}
if ($from) { 
  $where[] = "a.attendance_date>=?"; 
  $params[] = $from; 
}
if ($to) { 
  $where[] = "a.attendance_date<=?"; 
  $params[] = $to; 
}

// Main query with class info
$sql = "SELECT a.id, a.attendance_date, a.status, a.remark, 
               u.name AS student, s.name AS subject,
               c.class_name, c.section
        FROM attendance a
        JOIN users u ON a.user_id = u.id
        JOIN subjects s ON a.subject_id = s.id
        LEFT JOIN class_mst c ON u.class_id = c.id";

if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY a.attendance_date DESC, u.name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h4 class="mb-3">Attendance List</h4>

<form method="get" class="row g-3 mb-3">
  <!-- Class Filter -->
  <div class="col-md-3">
    <label class="form-label">Class</label>
    <select name="class_id" class="form-select" onchange="this.form.submit()">
      <option value="">All</option>
      <?php foreach ($classes as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $class_id == $c['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['class_name']) ?> <?= htmlspecialchars($c['section']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Subject Filter -->
  <div class="col-md-3">
    <label class="form-label">Subject</label>
    <select name="subject_id" class="form-select">
      <option value="">All</option>
      <?php foreach ($subjects as $s): ?>
        <option value="<?= $s['id'] ?>" <?= $subject_id == $s['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($s['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Student Filter -->
  <div class="col-md-3">
    <label class="form-label">Student</label>
    <select name="user_id" class="form-select">
      <option value="">All</option>
      <?php foreach ($students as $u): ?>
        <option value="<?= $u['id'] ?>" <?= $user_id == $u['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($u['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Date Filters -->
  <div class="col-md-2">
    <label class="form-label">From</label>
    <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
  </div>
  <div class="col-md-2">
    <label class="form-label">To</label>
    <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
  </div>

  <div class="col-md-2 d-flex align-items-end">
    <button class="btn btn-secondary w-100">Filter</button>
  </div>
</form>

<input id="tableSearch" class="form-control mb-2" placeholder="Search in table...">

<div class="table-responsive">
  <table class="table table-striped table-hover align-middle">
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>Student</th>
        <th>Class</th>
        <th>Subject</th>
        <th>Status</th>
        <th>Remark</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $i => $r): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= htmlspecialchars($r['attendance_date']) ?></td>
          <td><?= htmlspecialchars($r['student']) ?></td>
          <td><?= htmlspecialchars($r['class_name'] ?? '—') . ' ' . htmlspecialchars($r['section'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['subject']) ?></td>
          <td><?= htmlspecialchars($r['status']) ?></td>
          <td><?= htmlspecialchars($r['remark']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>