<?php
session_start();
include 'includes/auth.php';
include './includes/connect.php';
include './includes/sheader.php';

// Fetch total owners (admins with type 'admin')
$queryOwners = "SELECT COUNT(*) AS total_owners FROM admins WHERE type = 'admin'";
$resultOwners = $con->query($queryOwners);
$ownersCount = 0;
if ($resultOwners && $row = $resultOwners->fetch_assoc()) {
    $ownersCount = $row['total_owners'];
}

// Fetch total hostels
$queryHostels = "SELECT COUNT(*) AS total_hostels FROM hostels";
$resultHostels = $con->query($queryHostels);
$hostelsCount = 0;
if ($resultHostels && $row = $resultHostels->fetch_assoc()) {
    $hostelsCount = $row['total_hostels'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/super.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>

<main class="main-content">
  <h1>Dashboard</h1>

  <div class="dashboard-cards">
    <div class="dashboard-card" onclick="window.location.href='sowner.php'" style="cursor:pointer;">
      <h2><?php echo $ownersCount; ?></h2>
      <p>TOTAL OWNER</p>
      <div class="card-footer">
        FULL DETAIL <i class="fas fa-arrow-right"></i>
      </div>
    </div>

    <div class="dashboard-card" onclick="window.location.href='shostel.php'" style="cursor:pointer;">
      <h2><?php echo $hostelsCount; ?></h2>
      <p>TOTAL HOSTELS</p>
      <div class="card-footer">
        SEE ALL <i class="fas fa-arrow-right"></i>
      </div>
    </div>
  </div>
</main>
</div>
</body>
</html>
