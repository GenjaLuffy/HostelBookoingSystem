<?php
include './includes/header.php';
include './includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data from DB
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();

// Image path (with fallback if not set)
$profilePicPath = !empty($user['profile_picture']) ? './uploads/' . $user['profile_picture'] : null;
?>

<!-- Main Content -->
<div class="main-content">
        <div class="profile-top">
            <div class="profile-pic" style="width:120px; height:120px; border-radius:50%; overflow:hidden; background:#ddd; display:flex; align-items:center; justify-content:center; font-size:72px; color:#999;">
                <?php if ($profilePicPath && file_exists($profilePicPath)): ?>
                    <img src="<?= htmlspecialchars($profilePicPath) ?>" alt="Profile Picture" style="width:100%; height:100%; object-fit:cover;">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>

            <div class="profile-details" style="margin-left: 20px;">
                <h3><?= htmlspecialchars($user['name']) ?></h3>
                <p><?= htmlspecialchars($user['address']) ?></p>
                <p><?= htmlspecialchars(ucfirst($user['type'])) ?></p>
            </div>
        </div>

        <!-- Display Profile Data as Text -->
        <div class="profile-info-display" style="margin-top:20px;">
            <div class="info-row"><strong>Full Name:</strong> <?= htmlspecialchars($user['name']) ?></div>
            <div class="info-row"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></div>
            <div class="info-row"><strong>Date of Birth:</strong> <?= htmlspecialchars($user['dob']) ?></div>
            <div class="info-row"><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></div>
            <div class="info-row"><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></div>
            <div class="info-row"><strong>Gender:</strong> <?= htmlspecialchars($user['gender']) ?></div>
            <div style="margin-top: 30px; text-align: right;">
                <a href="./userEdit.php" class="edit-link-button" style="text-decoration:none; background:#4CAF50; color:#fff; padding:10px 18px; border-radius:6px; margin-left:15px;">Edit Profile</a>
            </div>
        </div>

</div>

</body>

</html>