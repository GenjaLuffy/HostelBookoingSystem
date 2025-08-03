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
  body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f5f6fa;
}

.main-content {
  max-width: 960px;
  margin: 50px auto;
  background: #ffffff;
  padding: 40px 50px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
  border: 1px solid #e4e4e4;
}

.main-content h1 {
  font-size: 28px;
  color: #333;
  margin-bottom: 30px;
  text-align: center;
  font-weight: 600;
}

.main-content p {
  font-size: 16px;
  color: #555;
  margin: 14px 0;
  line-height: 1.6;
}

.main-content strong {
  color: #333;
  font-weight: 600;
}

ul {
  padding-left: 20px;
  margin-top: 10px;
  margin-bottom: 20px;
  list-style-type: disc; /* Dots */
}

ul li {
  font-size: 15px;
  color: #555;
  margin-bottom: 6px;
  line-height: 1.5;
}

.images-section {
  margin-top: 30px;
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  justify-content: center;
}

.images-section img {
  max-width: 220px;
  height: auto;
  border-radius: 8px;
  border: 1px solid #ccc;
  transition: transform 0.3s ease;
}

.images-section img:hover {
  transform: scale(1.05);
}

a {
  display: inline-block;
  margin-top: 30px;
  color: #8667F2;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s ease;
}

a:hover {
  color: #654ac6;
  text-decoration: underline;
}

/* Responsive design */
@media (max-width: 600px) {
  .main-content {
    padding: 30px 20px;
    margin: 20px;
  }

  .main-content h1 {
    font-size: 24px;
  }

  .main-content p,
  ul li {
    font-size: 15px;
  }

  .images-section img {
    max-width: 100%;
  }
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

  <p><strong>Amenities:</strong></p>
  <ul>
    <?php
    $amenities = preg_split('/\r\n|\r|\n|,/', $hostel['amenities']);
    foreach ($amenities as $item) {
        $item = trim($item);
        if (!empty($item)) {
            echo '<li>' . htmlspecialchars($item) . '</li>';
        }
    }
    ?>
  </ul>

  <p><strong>Rules:</strong></p>
  <ul>
    <?php
    $rules = preg_split('/\r\n|\r|\n|,/', $hostel['rules']);
    foreach ($rules as $rule) {
        $rule = trim($rule);
        if (!empty($rule)) {
            echo '<li>' . htmlspecialchars($rule) . '</li>';
        }
    }
    ?>
  </ul>

  <p><a href="shostel.php">← Back to Manage Hostels</a></p>
</main>
</body>
</html>
