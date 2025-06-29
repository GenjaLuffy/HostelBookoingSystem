<?php
include_once './includes/header.php';
include_once './includes/connect.php';
?>

<div class="main-content">
  <h1>Booking History</h1>
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
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT 
                  b.*, 
                  h.name AS hostel_name
                FROM bookings b
                LEFT JOIN hostels h ON b.hostel_id = h.id
                ORDER BY b.created_at DESC";

        $result = $con->query($sql);
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
                  </tr>";
            $sno++;
          }
        } else {
          echo "<tr><td colspan='8'>No bookings found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
