<?php
session_start();


$servername = "localhost";
$username = "root";
$password = "";
$database = "class_records";
$port = 3309;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['educator_id'])) {
    die("No educator session found. Please register first.");
}

$idNumber = $_SESSION['educator_id'];
$targetDir = "uploads/";

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$fileName = basename($_FILES["photo"]["name"]);
$targetFile = $targetDir . time() . "_" . $fileName; // unique filename
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

// ✅ Check allowed types
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($imageFileType, $allowedTypes)) {
    die("<script>alert('Invalid file type. Please upload JPG, JPEG, PNG, or GIF.'); window.history.back();</script>");
}

if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
    // ✅ Update database
    $sql = "UPDATE users SET photo = ? WHERE studentNumber = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $targetFile, $idNumber);

    if ($stmt->execute()) {
        $_SESSION['uploaded_photo'] = $targetFile;
        echo "<script>alert('Photo uploaded successfully!'); window.location.href='educator-picture-confirmation.php';</script>";
    } else {
        echo "Database error: " . $stmt->error;
    }
} else {
    echo "<script>alert('Error uploading file.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
