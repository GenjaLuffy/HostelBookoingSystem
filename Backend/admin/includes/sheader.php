<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include './includes/connect.php';


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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/dashboard.css" />
    <link rel="stylesheet" href="./assets/css/superprofile.css" />
    <link rel="stylesheet" href="assets/css/sowner.css" />
    <link rel="stylesheet" href="assets/css/editProfile.css" />
    <link rel="stylesheet" href="assets/css/seeHostel.css" />
    <link rel="stylesheet" href="assets/css/super.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
    <header class="header">
        <a href="super.php" class="logo">Book<br><span>Mate</span></a>
           <div class="user-name" style="color: black; margin-left: 20px; font-weight: bold; cursor: pointer;">
            Welcome,<?= htmlspecialchars($admin['name']) ?>
        </div>
    </header>

    <div class="container">

        <aside class="sidebar">
            <nav>
                <a href="super.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="sowner.php"><i class="fas fa-users"></i><span>Managed Owner</span></a>
                <a href="shostel.php"><i class="fas fa-cogs"></i><span>Manage Hostel</span></a>
                <a href="addAdmin.php"><i class="fas fa-user-plus"></i><span>Add Owners</span></a>
                <a href="superprofile.php"><i class="fas fa-user"></i><span>Profile</span></span></a>
                <a href="./includes/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>
        </aside>