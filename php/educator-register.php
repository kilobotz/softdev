<?php
// ✅ Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "class_records";
$port = 3309; // use only if your MySQL is on 3309

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ✅ Collect form data
$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$middleInitial = $_POST['middleInitial'] ?? '';
$idNumber = $_POST['idNumber'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// ✅ Basic validation
if (empty($firstName) || empty($lastName) || empty($idNumber) || empty($password) || empty($confirmPassword)) {
    echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
    exit;
}

if ($password !== $confirmPassword) {
    echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
    exit;
}

// ✅ Hash password securely
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// ✅ Default placeholder photo
$photo = "uploads/default.jpg";

// ✅ Insert educator data
$sql = "INSERT INTO users (studentNumber, password, firstName, lastName, middleInitial, role, photo)
        VALUES (?, ?, ?, ?, ?, 'educator', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $idNumber, $hashedPassword, $firstName, $lastName, $middleInitial, $photo);

if ($stmt->execute()) {
    // Save ID temporarily in session so we know which educator to update photo for
    session_start();
    $_SESSION['educator_id'] = $idNumber;
    echo "<script>
        alert('Registration successful! Please upload your profile photo.');
        window.location.href='educator-picture-upload.html';
    </script>";
} else {
    if ($conn->errno == 1062) {
        echo "<script>alert('That ID number is already registered.'); window.history.back();</script>";
    } else {
        echo 'Database error: ' . $conn->error;
    }
}

$stmt->close();
$conn->close();
?>
