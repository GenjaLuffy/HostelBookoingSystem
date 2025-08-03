<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include './includes/connect.php';
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/dashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
    <header class="header">
        <a href="index.php" class="logo">Book<br><span>Mate</span></a>
        <div class="user-name" style="color: black; margin-left: 20px; font-weight: bold;">
            Welcome,<?= htmlspecialchars($admin['name']) ?>
        </div>
    </header>

    <div class="container">
        <aside class="sidebar">
            <nav>
                <a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a>
                <a href="Hostel.php"><i class="fas fa-building"></i><span>My Hostels</span></a>
                <a href="addHostel.php"><i class="fas fa-bed"></i><span>Add Hostel</span></a>
                <a href="manageR.php"><i class="fas fa-cogs"></i><span>Managed Rooms</span></a>
                <a href="manageS.php"><i class="fas fa-users"></i><span>Manage Students</span></a>
                <a href="./includes/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>
        </aside>
