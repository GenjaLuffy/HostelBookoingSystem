<?php
session_start();
include './includes/auth.php';    // Auth check should be here or inside this file
include './includes/connect.php';
include './includes/sheader.php';

// Only allow superadmin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    echo "<script>alert('Access denied. Only superadmins allowed.'); window.location.href = '../user/login.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $address = trim($_POST['address']);
    $gender = $_POST['gender'];
    $type = $_POST['type'];

    // Handle profile picture upload
    $profile_picture = "";
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['profile_picture']['tmp_name']);
        if (in_array($fileType, $allowedTypes)) {
            $targetDir = "admin/uploads/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = time() . "_" . basename($_FILES["profile_picture"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
                $profile_picture = $targetFilePath;
            } else {
                echo "<script>alert('Error uploading profile picture.');</script>";
            }
        } else {
            echo "<script>alert('Invalid profile picture type. Only JPG, PNG, GIF allowed.');</script>";
        }
    }

    $stmt = $con->prepare("INSERT INTO admins (name, username, email, password, dob, phone, address, gender, profile_picture, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $name, $username, $email, $password, $dob, $phone, $address, $gender, $profile_picture, $type);

    if ($stmt->execute()) {
        echo "<script> alert('Admin added successfully!'); </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Owner</title>
  <link rel="stylesheet" href="assets/css/addAdmin.css" />
</head>
<body>
<h2>Book Now</h2>

<main class="main-content">
  <div class="form-card">
    <div class="form-header">Add Owner</div>
    <form action="addAdmin.php" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" required />
      </div>
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required />
      </div>
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" required />
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required />
      </div>
      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="dob" required />
      </div>
      <div class="form-group">
        <label>Phone</label>
        <input type="tel" name="phone" pattern="[0-9]{10}" maxlength="10" required />
      </div>
      <div class="form-group">
        <label>Address</label>
        <input type="text" name="address" required />
      </div>
      <div class="form-group">
        <label>Gender</label>
        <select name="gender" required>
          <option value="">Select Gender</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label>Type</label>
        <select name="type" required>
          <option value="admin">Admin</option>
          <option value="superadmin">Superadmin</option>
        </select>
      </div>
      <div class="full-width">
        <button type="submit" name="submit">Add Admin</button>
      </div>
    </form>
  </div>
</main>
</body>
</html>
