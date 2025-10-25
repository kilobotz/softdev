<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "class_records";
$port = 3309; // change to your XAMPP MySQL port

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$idNumber = $_POST['student-id'] ?? '';
$passwordInput = $_POST['student-password'] ?? '';

if (empty($idNumber) || empty($passwordInput)) {
    echo "<script>alert('Please fill all fields.'); window.history.back();</script>";
    exit;
}

$sql = "SELECT * FROM users WHERE studentNumber = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $idNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($passwordInput, $user['password'])) {
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['studentNumber'] = $user['studentNumber'];
        $_SESSION['firstName'] = $user['firstName'];
        $_SESSION['role'] = $user['role'];

        // If the user doesn’t have a role yet, ask them to choose
        if ($user['role'] == NULL || $user['role'] == '') {
            header("Location: choose-role.html");
            exit;
        }

        // Redirect based on role
        if ($user['role'] == 'teacher') {
            header("Location: teacher-dashboard.html");
        } else {
            header("Location: student-dashboard.html");
        }
        exit;

    } else {
        echo "<script>alert('Incorrect password.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Account not found.'); window.history.back();</script>";
}

$conn->close();
?>
