<?php
session_start();
include './includes/header.php';
include './includes/connect.php';
include 'includes/auth.php'; 

$user_id = $_SESSION['user_id'] ?? null;

$total_students = 0;
$total_hostels = 0;

if ($user_id) {
    // Example: count total users (if you want to count students from users table)
    $stmt1 = $con->prepare("SELECT COUNT(*) FROM users");  // or your correct table
    $stmt1->execute();
    $stmt1->bind_result($total_students);
    $stmt1->fetch();
    $stmt1->close();

    // Count total hostels created by this user
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
</style>
<main class="main-content">
  <h1>Dashboard</h1>

  <div class="dashboard-cards">
    <div class="dashboard-card">
      <h2><?= $total_students ?></h2>
      <p>STUDENTS</p>
      <div class="card-footer">
      <a href="manageS.php" class="no-underline">FULL DETAIL <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>

    <div class="dashboard-card">
      <h2><?= $total_hostels ?></h2>
      <p>HOSTELS</p>
      <div class="card-footer">
         <a href="Hostel.php" class="no-underline">SEE All <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</main>
</div>
</body>
</html>
