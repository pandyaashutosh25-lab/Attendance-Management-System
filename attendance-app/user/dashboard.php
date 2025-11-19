<?php
require_once __DIR__ . '/../includes/auth.php'; require_user();
include __DIR__ . '/../includes/header.php';
?>
<div class="p-4 bg-white border rounded-3 shadow-sm">
  <h4 class="mb-1" style="color: black;">Student Dashboard</h4>
  <p class="text-muted mb-0">Use the menu to view your attendance and manage your profile.</p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
