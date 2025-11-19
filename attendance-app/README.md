# Attendance Management System (Student-Based)

Tech Stack: HTML, CSS, JavaScript, Bootstrap (Frontend), PHP + MySQL (phpMyAdmin) (Backend)

## Setup (XAMPP)
1. Start Apache & MySQL in XAMPP.
2. Open phpMyAdmin and **import** `sql/attendance_db.sql` (creates DB + tables).
3. Copy the folder `attendance-app` to `htdocs`.
4. Update DB credentials in `config/db.php` if needed.
5. Visit `http://localhost/attendance-app/public/admin_seed.php` (creates admin user `admin@example.com` / `admin123`). Then **delete this file**.
6. Go to `http://localhost/attendance-app/public/index.php` and log in.

## Admin Flow
- Add Subjects
- Add Students
- Mark Attendance (select Date + Subject → choose status for each student → Save)
- View Attendance List (filter by subject/student/date range)

## Student Flow
- Login with their account (created by admin)
- View **My Attendance** (percentage + records)
- Update **My Profile** and password

## Notes
- Passwords are hashed using `password_hash()`.
- PDO with prepared statements is used for DB operations.
- Basic filtering + search included.
- This is a minimal, clean starter you can extend.
