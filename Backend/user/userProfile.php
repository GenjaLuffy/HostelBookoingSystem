<?php
include 'includes/connect.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/userProfile.css" />
      <link rel="stylesheet" href="./assets/css/style.css" />
    <title>Profile</title>
</head>
<body>

<div class="profile-container">
    <?php if (!empty($user['profile_picture'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture" />
    <?php else: ?>
        <div class="profile-picture" style="background:#ddd; line-height:140px; color:#aaa; font-size:24px;">N/A</div>
    <?php endif; ?>

    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
<dl class="two-column">
    <dt>Username:</dt>
    <dd><?php echo htmlspecialchars($user['username']); ?></dd>

    <dt>Email:</dt>
    <dd><?php echo htmlspecialchars($user['email']); ?></dd>

    <dt>Date of Birth:</dt>
    <dd><?php echo htmlspecialchars($user['dob']); ?></dd>

    <dt>Phone:</dt>
    <dd><?php echo htmlspecialchars($user['phone']); ?></dd>

    <dt>Address:</dt>
    <dd><?php echo nl2br(htmlspecialchars($user['address'])); ?></dd>

    <dt>Gender:</dt>
    <dd><?php echo ucfirst(htmlspecialchars($user['gender'])); ?></dd>

    <dt>Type:</dt>
    <dd><?php echo ucfirst(htmlspecialchars($user['type'])); ?></dd>
</dl>

    <div class="edit-link">
      <a href="userEdit.php">Edit Profile</a>
    </div>
</div>
</body>
</html>
