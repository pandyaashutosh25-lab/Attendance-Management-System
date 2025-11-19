<?php
require_once __DIR__ . '/../includes/auth.php'; require_admin();
require_once __DIR__ . '/../config/db.php';

$subjects = $pdo->query("SELECT id,code,name FROM subjects ORDER BY name")->fetchAll();
$date = $_GET['date'] ?? date('Y-m-d');
$subject_id = (int)($_GET['subject_id'] ?? 0);

$students = [];
if ($subject_id) {
  $students = $pdo->query("SELECT id,name,enroll_no FROM users WHERE role='user' ORDER BY name")->fetchAll();
}

// handle messages - REMOVE THIS BLOCK, header.php now handles it
// $flash = '';
// if (isset($_GET['success'])) { $flash = 'Attendance saved successfully.'; }
// if (isset($_GET['error'])) { $flash = 'Error saving attendance.'; }

include __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-3">Mark Attendance</h4>
<?php // if($flash): ?><?php // htmlspecialchars($flash) ?></div><?php // endif; ?> 
<!-- The flash message is now handled by header.php -->

<form method="get" class="row g-3 mb-3">
  <div class="col-md-3">
    <label class="form-label">Date</label>
    <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>" required>
  </div>
  <div class="col-md-5">
    <label class="form-label">Subject</label>
    <select name="subject_id" class="form-select" required>
      <option value="">-- Select Subject --</option>
      <?php foreach($subjects as $s): ?>
        <option value="<?= $s['id'] ?>" <?= $subject_id==$s['id']?'selected':'' ?>>
          <?= htmlspecialchars($s['code'].' - '.$s['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <button class="btn btn-secondary w-100">Load Students</button>
  </div>
</form>

<?php if($subject_id): ?>
<form method="post" action="/attendance-app/actions/attendance_create.php">
  <input type="hidden" name="attendance_date" value="<?= htmlspecialchars($date) ?>">
  <input type="hidden" name="subject_id" value="<?= (int)$subject_id ?>">
  <div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead><tr><th>#</th><th>Name</th><th>Enrollment No</th><th>Status</th><th>Remark</th></tr></thead>
    <tbody>
      <?php foreach($students as $i=>$st): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($st['name']) ?></td>
          <td><?= htmlspecialchars($st['enroll_no']) ?></td>
          <td>
            <div class="d-flex gap-2">
              <?php foreach(['Present','Absent','Late','Leave'] as $status): ?>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="rows[<?= $i ?>][status]" value="<?= $status ?>" id="s<?= $st['id'].$status ?>" required>
                  <label class="form-check-label" for="s<?= $st['id'].$status ?>"><?= $status ?></label>
                </div>
              <?php endforeach; ?>
            </div>
            <input type="hidden" name="rows[<?= $i ?>][user_id]" value="<?= $st['id'] ?>">
          </td>
          <td><input name="rows[<?= $i ?>][remark]" class="form-control" placeholder="Optional"></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <button class="btn btn-primary">Save Attendance</button>
</form>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>