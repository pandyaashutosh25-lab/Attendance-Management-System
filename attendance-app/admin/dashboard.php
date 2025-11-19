<?php
require_once __DIR__ . '/../includes/auth.php'; require_admin();
require_once __DIR__ . '/../config/db.php';

$users = $pdo->query("SELECT COUNT(*) c FROM users WHERE role='user'")->fetch()['c'] ?? 0;
$subjects = $pdo->query("SELECT COUNT(*) c FROM subjects")->fetch()['c'] ?? 0;
$today = date('Y-m-d');
$today_att = $pdo->prepare("SELECT COUNT(*) c FROM attendance WHERE attendance_date=?");
$today_att->execute([$today]);
$today_att = $today_att->fetch()['c'] ?? 0;

include __DIR__ . '/../includes/header.php';
?>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card shadow-sm"><div class="card-body">
      <div class="fs-5">Students</div><div class="display-6"><?= (int)$users ?></div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm"><div class="card-body">
      <div class="fs-5">Subjects</div><div class="display-6"><?= (int)$subjects ?></div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm"><div class="card-body">
      <div class="fs-5">Today's Attendance</div><div class="display-6"><?= (int)$today_att ?></div>
    </div></div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
