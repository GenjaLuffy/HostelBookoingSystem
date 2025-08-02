<?php
include_once './includes/header.php';
include_once './includes/connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Bookings</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="./assets/css/bhistory.css">
</head>
<body>
<div class="main-content">
  <h1>Your Booking History</h1>
  <div class="card">
    <h3>All Booking Records</h3>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Sno.</th>
          <th>Hostel</th>
          <th>Room No</th>
          <th>Seater</th>
          <th>Stay From</th>
          <th>Duration</th>
          <th>Status</th>
          <th>Total Fee</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT 
                  b.*, 
                  h.name AS hostel_name
                FROM bookings b
                LEFT JOIN hostels h ON b.hostel_id = h.id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
          $sno = 1;
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$sno}</td>
                    <td>{$row['hostel_name']}</td>
                    <td>{$row['room_no']}</td>
                    <td>{$row['seater']}</td>
                    <td>{$row['stay_from']}</td>
                    <td>{$row['stay_duration']} Months</td>
                    <td><span class='status-{$row['status']}'>{$row['status']}</span></td>
                    <td>Rs. {$row['total_fee']}</td>
                    <td>";

            if ($row['status'] != 'Cancelled') {
              echo "<form action='cancel_booking.php' method='POST' onsubmit='return confirm(\"Are you sure you want to cancel this booking?\")'>
                      <input type='hidden' name='booking_id' value='{$row['id']}'>
                      <button type='submit' class='btn btn-danger btn-sm'>Cancel</button>
                    </form>";
            } else {
              echo "<span class='text-muted'>Cancelled</span>";
            }

            echo "</td></tr>";
            $sno++;
          }
        } else {
          echo "<tr><td colspan='9'>No bookings found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
