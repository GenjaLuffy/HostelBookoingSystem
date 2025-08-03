<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/auth.php';
include './includes/connect.php'; // adjust path to your DB connection
include './includes/sheader.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: sowner.php");
    exit();
}

$ownerId = (int)$_GET['id'];

// Prepare statement to fetch owner data including document
$stmt = $con->prepare("SELECT * FROM admins WHERE id = ?");
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

// Prepare profile picture path, check if file exists
$profilePicPath = '';
if (!empty($owner['profile_picture'])) {
    $picPath = 'uploads/profile_pictures/' . $owner['profile_picture'];
    if (file_exists($picPath)) {
        $profilePicPath = $picPath;
    }
}

// Prepare document path, check if file exists
$documentPath = '';
if (!empty($owner['document'])) {
    // Adjust the folder path according to your file location
    $relativeWebPath = 'documents/' . $owner['document'];
    $absoluteFilePath = __DIR__ . '/' . $relativeWebPath;

    if (file_exists($absoluteFilePath)) {
        $documentPath = $relativeWebPath;
    }
}
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
            max-width: 650px;
            margin: 50px auto;
            padding: 30px 40px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .owner-details h2 {
            font-size: 28px;
            color: #333;
        }

        .owner-details img,
        .owner-details i.fas.fa-user {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #e0e0e0;
            color: #888;
            font-size: 64px;
            line-height: 130px;
            display: inline-block;
            margin-bottom: 25px;
        }

        .owner-details dl {
            display: grid;
            grid-template-columns: 1fr 2fr;
            row-gap: 15px;
            column-gap: 20px;
            text-align: left;
        }

        .owner-details dt {
            background-color: #f0ebfc;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .owner-details dd {
            margin: 0;
            padding: 10px;
            background-color: #fafafa;
            border-radius: 8px;
            color: #444;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            color: #8667f2;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .back-link i {
            margin-right: 6px;
        }

        .back-link:hover {
            color: #654ac6;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <main class="owner-details">
        <h2>Owner Details</h2>

        <?php if (!empty($profilePicPath)): ?>
            <img src="<?= htmlspecialchars($profilePicPath) ?>" alt="Profile Picture" />
        <?php else: ?>
            <i class="fas fa-user"></i>
        <?php endif; ?>

        <dl>
            <dt>Name:</dt>
            <dd><?= htmlspecialchars($owner['name']); ?></dd>

            <dt>Username:</dt>
            <dd><?= htmlspecialchars($owner['username']); ?></dd>

            <dt>Email:</dt>
            <dd><?= htmlspecialchars($owner['email']); ?></dd>

            <dt>Date of Birth:</dt>
            <dd><?= !empty($owner['dob']) ? date('F j, Y', strtotime($owner['dob'])) : 'Not specified'; ?></dd>

            <dt>Phone:</dt>
            <dd><?= htmlspecialchars($owner['phone']); ?></dd>

            <dt>Address:</dt>
            <dd><?= nl2br(htmlspecialchars($owner['address'])); ?></dd>

            <dt>Gender:</dt>
            <dd><?= $genderMap[$owner['gender']] ?? 'Not specified'; ?></dd>

            <dt>Document:</dt>
            <dd>
                <?php if (!empty($documentPath)): ?>
                    <a href="<?= htmlspecialchars($documentPath); ?>" download><?= htmlspecialchars(basename($documentPath)); ?></a>
                <?php else: ?>
                    No document uploaded
                <?php endif; ?>
            </dd>

            <dt>Role:</dt>
            <dd><?= htmlspecialchars(ucfirst($owner['type'])); ?></dd>

            <dt>Account Created At:</dt>
            <dd><?= date('F j, Y, g:i a', strtotime($owner['created_at'])); ?></dd>
        </dl>

        <a href="sowner.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Owner List</a>
    </main>
</body>

</html>
