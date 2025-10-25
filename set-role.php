<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Log-in-page.html");
    exit;
}

$role = $_GET['role'] ?? '';

if (!in_array($role, ['student', 'teacher'])) {
    die("Invalid role selected.");
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "class_records";
$port = 3309;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];
$sql = "UPDATE users SET role = ? WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $role, $userId);
$stmt->execute();

$_SESSION['role'] = $role;

// Redirect to respective dashboard
if ($role == 'teacher') {
    header("Location: teacher-dashboard.html");
} else {
    header("Location: student-dashboard.html");
}

$conn->close();
?>
