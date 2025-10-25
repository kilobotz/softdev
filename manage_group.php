<?php
session_start();

// DIRECT DATABASE CONNECTION
$servername = "localhost";
$username = "root";
$password = "";
$database = "class_records";
$port = 3309;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Simulate teacher session for testing (remove later)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'teacher';
}

if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit;
}

$group_id = $_GET['group_id'] ?? 0;

// ADD STUDENT
if (isset($_POST['add_student'])) {
    $student_id = $_POST['student_id'];
    $stmt = $conn->prepare("INSERT INTO group_members (group_id, student_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $group_id, $student_id);
    $stmt->execute();
}

// REMOVE STUDENT
if (isset($_POST['remove_student'])) {
    $student_id = $_POST['student_id'];
    $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ? AND student_id = ?");
    $stmt->bind_param("ii", $group_id, $student_id);
    $stmt->execute();
}

// FETCH GROUP INFO
$group = $conn->query("SELECT group_name FROM groups WHERE group_id = $group_id")->fetch_assoc();

// FETCH STUDENTS IN THE GROUP
$members = $conn->query("
    SELECT u.ID, u.firstName, u.lastName
    FROM group_members gm
    JOIN users u ON gm.student_id = u.ID
    WHERE gm.group_id = $group_id
");

// FETCH STUDENTS NOT IN THE GROUP
$available = $conn->query("
    SELECT ID, firstName, lastName
    FROM users
    WHERE role = 'student'
    AND ID NOT IN (SELECT student_id FROM group_members WHERE group_id = $group_id)
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Manage Group | Class Records</title>
  <link rel="stylesheet" href="style2.css">
</head>
<body>
  <header class="site-header">
    <h1 class="brand">Manage Group: <?= htmlspecialchars($group['group_name']); ?></h1>
  </header>

  <main class="group-section">
    <section class="group-list">
      <h3>Group Members</h3>
      <div class="groups-container">
        <?php while ($row = $members->fetch_assoc()): ?>
          <div class="group-card">
            <h4><?= htmlspecialchars($row['firstName'] . " " . $row['lastName']); ?></h4>
            <form method="POST">
              <input type="hidden" name="student_id" value="<?= $row['ID']; ?>">
              <button type="submit" name="remove_student" class="btn leave-btn">Remove</button>
            </form>
          </div>
        <?php endwhile; ?>
      </div>
    </section>

    <section class="group-controls">
      <h3>Add Student</h3>
      <form method="POST" class="group-form">
        <label>Select a Student:</label>
        <select name="student_id" required>
          <option value="">Select</option>
          <?php while ($s = $available->fetch_assoc()): ?>
            <option value="<?= $s['ID']; ?>"><?= htmlspecialchars($s['firstName'] . " " . $s['lastName']); ?></option>
          <?php endwhile; ?>
        </select>
        <button type="submit" name="add_student" class="btn create-btn">Add to Group</button>
      </form>
    </section>
  </main>
</body>
</html>

<?php $conn->close(); ?>
