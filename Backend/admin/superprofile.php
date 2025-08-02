<?php
include './includes/sheader.php';
include './includes/connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in and is superadmin
if (!isset($_SESSION['user_id'])) {
  
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['user_id'];
$query = "SELECT * FROM admins WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
} else {
    echo "Admin not found.";
    exit();
}

// Profile picture path
$profilePicPath = './uploads/' . $admin['profile_picture'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SuperAdmin Profile</title>
    <link rel="stylesheet" href="./assets/css/superprofile.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
<!-- Main Content -->
<div class="main-content">
  <div class="edit-profile-container">
    <div class="profile-top">
      <div class="profile-pic" style="width:120px; height:120px; border-radius:50%; overflow:hidden; background:#ddd; display:flex; align-items:center; justify-content:center; font-size:72px; color:#999;">
        <?php if (!empty($admin['profile_picture']) && file_exists($profilePicPath)): ?>
          <img src="<?= htmlspecialchars($profilePicPath) ?>" alt="Profile Picture" style="width:100%; height:100%; object-fit:cover;">
        <?php else: ?>
          <i class="fas fa-user"></i>
        <?php endif; ?>
      </div>
      <div class="profile-details" style="margin-left:20px;">
        <h3><?= htmlspecialchars($admin['name']) ?></h3>
        <p><?= htmlspecialchars($admin['address']) ?></p>
        <p><?= htmlspecialchars($admin['type']) ?></p>
      </div>
    </div>

    <!-- Profile Info Display -->
    <div class="profile-info-display" style="margin-top:20px;">
      <div class="info-row"><strong>Full Name:</strong> <?= htmlspecialchars($admin['name']) ?></div>
      <div class="info-row"><strong>Username:</strong> <?= htmlspecialchars($admin['username']) ?></div>
      <div class="info-row"><strong>Date of Birth:</strong> <?= htmlspecialchars($admin['dob']) ?></div>
      <div class="info-row"><strong>Phone:</strong> <?= htmlspecialchars($admin['phone']) ?></div>
      <div class="info-row"><strong>Address:</strong> <?= htmlspecialchars($admin['address']) ?></div>
      <div class="info-row"><strong>Gender:</strong> <?= htmlspecialchars($admin['gender']) ?></div>
    </div>

    <div style="margin-top: 30px; text-align: right;">
      <a href="./superEditProfile.php" class="edit-link-button" style="text-decoration:none; background:#4CAF50; color:#fff; padding:10px 18px; border-radius:6px;">Edit Profile</a>
    </div>
  </div>
</div>

</body>
</html>
