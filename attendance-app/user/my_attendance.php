<?php
require_once __DIR__ . '/../includes/auth.php'; require_user();
require_once __DIR__ . '/../config/db.php';

$id = $_SESSION['user_id'];

$subjects = $pdo->query("SELECT id,name FROM subjects ORDER BY name")->fetchAll();
$subject_id = (int)($_GET['subject_id'] ?? 0);
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = ["a.user_id=?"]; $params = [$id];
if ($subject_id) { $where[] = "a.subject_id=?"; $params[] = $subject_id; }
if ($from) { $where[] = "a.attendance_date>=?"; $params[] = $from; }
if ($to) { $where[] = "a.attendance_date<=?"; $params[] = $to; }

$sql = "SELECT a.attendance_date,a.status,s.name AS subject
        FROM attendance a JOIN subjects s ON a.subject_id=s.id
        WHERE " . implode(" AND ", $where) . " ORDER BY a.attendance_date DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $rows = $stmt->fetchAll();

// percentage
$total = count($rows);
$present = 0;
foreach ($rows as $r) { if ($r['status']==='Present') $present++; }
$percent = $total ? round(($present/$total)*100,2) : 0;

include __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-3">My Attendance</h4>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="fs-6 text-muted">Total Records</div><div class="display-6"><?= $total ?></div></div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="fs-6 text-muted">Present</div><div class="display-6"><?= $present ?></div></div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="fs-6 text-muted">Absent</div><div class="display-6"><?= $total-$present ?></div></div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="fs-6 text-muted">Percentage</div><div class="display-6"><?= $percent ?>%</div></div></div></div>
</div>

<form method="get" class="row g-3 mb-3">
  <div class="col-md-4"><label class="form-label">Subject</label>
    <select name="subject_id" class="form-select">
      <option value="">All</option>
      <?php foreach($subjects as $s): ?>
        <option value="<?= $s['id'] ?>" <?= $subject_id==$s['id']?'selected':'' ?>><?= htmlspecialchars($s['name']) ?></option>
      <?php endforeach; ?>
    </select></div>
  <div class="col-md-3"><label class="form-label">From</label><input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>"></div>
  <div class="col-md-3"><label class="form-label">To</label><input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>"></div>
  <div class="col-md-2 d-flex align-items-end"><button class="btn btn-secondary w-100">Filter</button></div>
</form>

<input id="tableSearch" class="form-control mb-2" placeholder="Search in table...">

<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
  <thead><tr><th>#</th><th>Date</th><th>Subject</th><th>Status</th></tr></thead>
  <tbody>
    <?php foreach($rows as $i=>$r): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($r['attendance_date']) ?></td>
        <td><?= htmlspecialchars($r['subject']) ?></td>
        <td><?= htmlspecialchars($r['status']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
