<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$date = $_POST['attendance_date'] ?? null;
$subject_id = (int)($_POST['subject_id'] ?? 0);
$rows = $_POST['rows'] ?? [];

if (!$date || !$subject_id || !$rows) {
  header('Location: /attendance-app/admin/attendance_mark.php?error=' . urlencode('Missing attendance data.'));
  exit;
}

$pdo->beginTransaction();
try {
  foreach ($rows as $r) {
    $user_id = (int)$r['user_id'];
    $status  = trim($r['status'] ?? '');
    $remark  = trim($r['remark'] ?? '');

    // 🔹 Fetch the class_id of this student
    $stmtClass = $pdo->prepare("SELECT class_id FROM users WHERE id=?");
    $stmtClass->execute([$user_id]);
    $class_id = $stmtClass->fetchColumn();

    // 🔹 Insert or update attendance record
    $stmt = $pdo->prepare("
      INSERT INTO attendance (user_id, class_id, subject_id, attendance_date, status, remark, created_by)
      VALUES (?,?,?,?,?,?,?)
      ON DUPLICATE KEY UPDATE 
        status = VALUES(status),
        remark = VALUES(remark),
        updated_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([$user_id, $class_id, $subject_id, $date, $status, $remark, $_SESSION['user_id']]);
  }

  $pdo->commit();
  header('Location: /attendance-app/admin/attendance_mark.php?subject_id=' . (int)$subject_id . '&date=' . $date . '&success=' . urlencode('Attendance saved successfully!'));
  exit;
} catch (Exception $e) {
  $pdo->rollBack();
  header('Location: /attendance-app/admin/attendance_mark.php?subject_id=' . (int)$subject_id . '&date=' . $date . '&error=' . urlencode('Error saving attendance: ' . $e->getMessage()));
  exit;
}