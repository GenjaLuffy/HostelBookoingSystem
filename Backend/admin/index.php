<?php
include './includes/header.php';
include './includes/connect.php';
include 'includes/auth.php'; 

$user_id = $_SESSION['user_id'] ?? null;

$total_students = 0;
$total_hostels = 0;

if ($user_id) {
    // Count distinct students who have booked hostels created by this admin
    $stmt1 = $con->prepare("
        SELECT COUNT(DISTINCT b.user_id) 
        FROM bookings b
        INNER JOIN hostels h ON b.hostel_id = h.id
        WHERE h.created_by = ?
    ");
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $stmt1->bind_result($total_students);
    $stmt1->fetch();
    $stmt1->close();

    // Count total hostels created by this user (admin)
    $stmt2 = $con->prepare("SELECT COUNT(*) FROM hostels WHERE created_by = ?");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $stmt2->bind_result($total_hostels);
    $stmt2->fetch();
    $stmt2->close();
} else {
    echo "<p>Please log in to view your dashboard.</p>";
    exit;
}
?>
<style>
  .a{
    text-decoration: none;
    color: black;
  }
  a.no-underline {
      text-decoration: none; 
  }
  /* Basic styling for cards */
  .dashboard-cards {
    display: flex;
    gap: 20px;
    margin-top: 20px;
  }
  .dashboard-card {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 20px;
    width: 200px;
    box-shadow: 0 2px 5px rgb(0 0 0 / 0.1);
    text-align: center;
  }
  .dashboard-card h2 {
    font-size: 3rem;
    margin: 0;
    color: #333;
  }
  .dashboard-card p {
    margin: 10px 0;
    font-weight: 600;
    color: #555;
  }
  .card-footer a {
    font-weight: 600;
    color: #007bff;
  }
  .card-footer a:hover {
    text-decoration: underline;
  }
</style>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
<main class="main-content">
  <h1>Dashboard</h1>

  <div class="dashboard-cards">
    <div class="dashboard-card">
      <h2><?= htmlspecialchars($total_students) ?></h2>
      <p>STUDENTS</p>
      <div class="card-footer">
        <a href="manageS.php" class="no-underline">FULL DETAIL <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>

    <div class="dashboard-card">
      <h2><?= htmlspecialchars($total_hostels) ?></h2>
      <p>HOSTELS</p>
      <div class="card-footer">
         <a href="Hostel.php" class="no-underline">SEE ALL <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</main>

</body>
</html>
