<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/auth.php';
include './includes/connect.php'; // adjust path to your DB connection

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: sowner.php");
    exit();
}

$ownerId = (int)$_GET['id'];

// Prepare statement to fetch owner data
$stmt = $con->prepare("SELECT id, name, username, email, dob, phone, address, gender, profile_picture, type, created_at FROM admins WHERE id = ?");
$stmt->bind_param("i", $ownerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Owner not found.";
    exit();
}

$owner = $result->fetch_assoc();
$stmt->close();

// Gender mapping for display
$genderMap = [
    'male' => 'Male',
    'female' => 'Female',
    'other' => 'Other'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Owner Details - <?= htmlspecialchars($owner['name']); ?></title>
    <link rel="stylesheet" href="assets/css/sowner.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .owner-details {
            max-width: 600px;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #fff;
        }
        .owner-details img {
            max-width: 150px;
            height: auto;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        .owner-details h2 {
            margin-bottom: 20px;
        }
        .owner-details dl {
            display: flex;
            flex-wrap: wrap;
        }
        .owner-details dt, .owner-details dd {
            width: 50%;
            margin: 0;
            padding: 5px 0;
        }
        .owner-details dt {
            font-weight: bold;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main class="owner-details">
        <h2>Owner Details</h2>

       <?php if (!empty($admin['profile_picture']) && file_exists($profilePicPath)): ?>
          <img src="<?= htmlspecialchars($profilePicPath) ?>" alt="Profile Picture" style="width:100%; height:100%; object-fit:cover;">
        <?php else: ?>
          <i class="fas fa-user"></i>
        <?php endif; ?>
        <dl>
            <dt>Name:</dt><dd><?= htmlspecialchars($owner['name']); ?></dd>
            <dt>Username:</dt><dd><?= htmlspecialchars($owner['username']); ?></dd>
            <dt>Email:</dt><dd><?= htmlspecialchars($owner['email']); ?></dd>
            <dt>Date of Birth:</dt><dd><?= !empty($owner['dob']) ? date('F j, Y', strtotime($owner['dob'])) : 'Not specified'; ?></dd>
            <dt>Phone:</dt><dd><?= htmlspecialchars($owner['phone']); ?></dd>
            <dt>Address:</dt><dd><?= nl2br(htmlspecialchars($owner['address'])); ?></dd>
            <dt>Gender:</dt><dd><?= $genderMap[$owner['gender']] ?? 'Not specified'; ?></dd>
            <dt>Role:</dt><dd><?= htmlspecialchars(ucfirst($owner['type'])); ?></dd>
            <dt>Account Created At:</dt><dd><?= date('F j, Y, g:i a', strtotime($owner['created_at'])); ?></dd>
        </dl>

        <a href="sowner.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Owner List</a>
    </main>
</body>
</html>
