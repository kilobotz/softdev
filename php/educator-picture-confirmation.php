<?php
session_start();

if (!isset($_SESSION['uploaded_photo'])) {
    echo "<script>alert('No uploaded photo found.'); window.location.href='educator-picture-upload.html';</script>";
    exit;
}

$photoPath = $_SESSION['uploaded_photo'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f5f5f5;
            padding: 50px;
        }
        .photo-preview {
            border-radius: 10px;
            border: 2px solid #ccc;
            width: 200px;
            height: 200px;
            object-fit: cover;
        }
        .btn {
            display: inline-block;
            background-color: #f0c43d;
            color: #333;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Photo Confirmation</h1>
    <p>Here’s the picture you uploaded:</p>
    <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Uploaded Photo" class="photo-preview">
    <br>
    <a href="Log-in-page.html" class="btn">Proceed to Login</a>
</body>
</html>
