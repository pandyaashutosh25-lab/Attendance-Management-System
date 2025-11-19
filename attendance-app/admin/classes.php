<?php
include_once(__DIR__ . '/../config/db.php');
include_once(__DIR__ . '/../includes/header.php');

// Add new class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_class'])) {
    $class_name = $_POST['class_name'];
    $section = $_POST['section'];
    $year = $_POST['year'];

    $sql = "INSERT INTO class_mst (class_name, section, year) VALUES (:class_name, :section, :year)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute(['class_name' => $class_name, 'section' => $section, 'year' => $year])) {
        $_SESSION['flash_message'] = "Class added successfully!";
        $_SESSION['flash_type'] = "success";
        header("Location: classes.php");
        exit;
    } else {
        $_SESSION['flash_message'] = "Error adding class!";
        $_SESSION['flash_type'] = "danger";
    }
}

// Delete class
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM class_mst WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $_SESSION['flash_message'] = "Class deleted successfully!";
    $_SESSION['flash_type'] = "success";
    header("Location: classes.php");
    exit;
}

// Fetch all classes
$stmt = $pdo->query("SELECT * FROM class_mst ORDER BY id DESC");
$classes = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h2>Manage Classes</h2>
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="class_name" class="form-control" placeholder="Class Name" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="section" class="form-control" placeholder="Section">
        </div>
        <div class="col-md-3">
            <input type="text" name="year" class="form-control" placeholder="Year">
        </div>
        <div class="col-md-2">
            <button type="submit" name="add_class" class="btn btn-primary w-100">Add</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Class Name</th>
                <th>Section</th>
                <th>Year</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($classes as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['class_name']) ?></td>
                    <td><?= htmlspecialchars($row['section']) ?></td>
                    <td><?= htmlspecialchars($row['year']) ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this class?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include_once(__DIR__ . '/../includes/footer.php'); ?>