<?php
// public/admin_seed.php -- run once, then delete. Creates initial admin user.
require_once __DIR__ . '/../config/db.php';
$name = 'Admin2';
$email = 'admin2@example.com';
$passPlain = '123456';
$hash = password_hash($passPlain, PASSWORD_DEFAULT);
try {
  $stmt = $pdo->prepare("INSERT INTO users (role,name,email,password) VALUES ('admin',?,?,?)");
  $stmt->execute([$name,$email,$hash]);
  echo "Admin user created. Email: {$email} Password: {$passPlain}";
} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
}
