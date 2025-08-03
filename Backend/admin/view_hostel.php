<?php
session_start();
include 'includes/auth.php';
include './includes/sheader.php';
include './includes/connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid hostel ID.</p>";
    exit;
}

$hostel_id = intval($_GET['id']);
$stmt = $con->prepare("SELECT * FROM hostels WHERE id = ?");
$stmt->bind_param("i", $hostel_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Hostel not found.</p>";
    exit;
}

$hostel = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($hostel['name']) ?> - Hostel Details</title>
  <link rel="stylesheet" href="assets/css/shostel.css" />
  <style>
    .images-section img {
        max-width: 200px;
        margin: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }
    .main-content {
        padding: 20px;
        font-family: Arial, sans-serif;
    }
    .main-content h1 {
        margin-bottom: 15px;
    }
  </style>
</head>
<body>
<main class="main-content">
  <h1><?= htmlspecialchars($hostel['name']) ?> - Details</h1>
  <p><strong>Location:</strong> <?= htmlspecialchars($hostel['location']) ?></p>
  <p><strong>Gender:</strong> <?= htmlspecialchars($hostel['gender']) ?></p>
  <p><strong>Fee:</strong> Rs. <?= htmlspecialchars($hostel['fee']) ?></p>
  <p><strong>Status:</strong> <?= htmlspecialchars($hostel['status']) ?></p>
  <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($hostel['description'])) ?></p>
  <p><strong>Amenities:</strong><br><?= nl2br(htmlspecialchars($hostel['amenities'])) ?></p>
  <p><strong>Rules:</strong><br><?= nl2br(htmlspecialchars($hostel['rules'])) ?></p>
  <p><a href="shostel.php">← Back to Manage Hostels</a></p>
</main>
</body>
</html>
