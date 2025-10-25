<?php
// ===============================
// DATABASE CONNECTION
// ===============================
$servername = "localhost";
$username = "root";
$password = "";
$database = "class_records";
$port = 3309;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

session_start();

// TEMPORARY TEACHER SESSION (remove after login integration)
if (!isset($_SESSION['user_id'])) {
  $_SESSION['user_id'] = 1;
  $_SESSION['role'] = 'teacher';
}

$teacher_id = $_SESSION['user_id'];

// ===============================
// CRUD LOGIC
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // CREATE GROUP
  if (isset($_POST['create'])) {
    $group_name = trim($_POST['group_name']);
    if ($group_name !== '') {
      $stmt = $conn->prepare("INSERT INTO groups (group_name, teacher_id) VALUES (?, ?)");
      $stmt->bind_param("si", $group_name, $teacher_id);
      $stmt->execute();
    }
  }

  // EDIT GROUP
  if (isset($_POST['edit'])) {
    $group_id = $_POST['group_id'];
    $new_name = trim($_POST['new_name']);
    if ($new_name !== '') {
      $stmt = $conn->prepare("UPDATE groups SET group_name = ? WHERE group_id = ? AND teacher_id = ?");
      $stmt->bind_param("sii", $new_name, $group_id, $teacher_id);
      $stmt->execute();
    }
  }

  // DELETE GROUP
  if (isset($_POST['delete'])) {
    $group_id = $_POST['group_id'];
    $stmt = $conn->prepare("DELETE FROM groups WHERE group_id = ? AND teacher_id = ?");
    $stmt->bind_param("ii", $group_id, $teacher_id);
    $stmt->execute();
  }

  header("Location: groupmanagement.php");
  exit;
}

// ===============================
// FETCH EXISTING GROUPS
// ===============================
$groups = $conn->query("SELECT * FROM groups WHERE teacher_id = $teacher_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Group Management | Class Records</title>
  <link rel="stylesheet" href="css/style2.css" />
</head>
<body>
  <!-- HEADER -->
  <header class="site-header">
    <div class="brand-container">
      <img src="apple-touch-icon.png" alt="Logo" class="site-logo" />
      <div class="brand">Class Records</div>
    </div>
  </header>

  <!-- MAIN SECTION -->
  <main class="group-section">
    <!-- INTRO -->
    <div class="intro">
      <h2>Manage Your Groups</h2>
      <p>Create, rename, or delete your class groups below.</p>
    </div>

    <!-- CREATE NEW GROUP -->
    <section class="group-controls">
      <h3>Create New Group</h3>
      <form method="POST" class="group-form">
        <label for="group_name">Group Name</label>
        <input type="text" id="group_name" name="group_name" placeholder="Enter group name" required />
        <button type="submit" name="create" class="btn create-btn">Create Group</button>
      </form>
    </section>

    <!-- EXISTING GROUPS -->
    <section class="group-list">
      <h3>Your Existing Groups</h3>

      <div class="groups-container">
        <?php if ($groups->num_rows > 0): ?>
          <?php while ($group = $groups->fetch_assoc()): ?>
            <div class="group-card">
              <h4><?= htmlspecialchars($group['group_name']); ?></h4>

              <form method="POST" class="group-form" style="margin-top: 12px;">
                <input type="hidden" name="group_id" value="<?= $group['group_id']; ?>" />
                <input type="text" name="new_name" placeholder="Rename group" />
                <div class="actions">
                  <button type="submit" name="edit" class="btn view-btn">Rename</button>
                  <button type="submit" name="delete" class="btn leave-btn">Delete</button>
                </div>
              </form>

              <form action="manage_group.php" method="GET" style="margin-top: 10px;">
                <input type="hidden" name="group_id" value="<?= $group['group_id']; ?>" />
                <button type="submit" class="btn view-btn" style="width: 100%;">Manage Members</button>
              </form>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No groups created yet. Create one above.</p>
        <?php endif; ?>
      </div>
    </section>
  </main>
</body>
</html>

<?php $conn->close(); ?>
