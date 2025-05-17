<?php
include './includes/sheader.php';
include './includes/connect.php'; 
// Handle status update from form submission
if (isset($_POST['update_status'])) {
    $hostel_id = intval($_POST['hostel_id']);
    $new_status = $_POST['status'] === 'Approved' ? 'Approved' : 'Pending';

    $stmt = $con->prepare("UPDATE hostels SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $hostel_id);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch hostels with creator info from admins
$sql = "SELECT h.id, h.name as hostel_name, h.status, a.name as creator_name 
        FROM hostels h
        JOIN admins a ON h.created_by = a.id";
$result = $con->query($sql);
?>

<main class="main-content">
  <h1>Manage Hostels</h1>
  <div class="table-container">
    <h2>All Hostel Details</h2>
    <table>
      <thead>
        <tr>
          <th>Sno.</th>
          <th>Hostel Name</th>
          <th>Created By</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
            $sno = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $sno++ . "</td>";
                echo "<td>" . htmlspecialchars($row['hostel_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['creator_name']) . "</td>";
                echo "<td><span class='status " . strtolower($row['status']) . "'>" . $row['status'] . "</span></td>";
                echo "<td>";
                echo "<form method='POST' style='display:inline'>";
                echo "<input type='hidden' name='hostel_id' value='" . $row['id'] . "'>";
                if ($row['status'] === 'Approved') {
                    echo "<button type='submit' name='update_status' value='Pending'>Set Pending</button>";
                } else {
                    echo "<button type='submit' name='update_status' value='Approved'>Approve</button>";
                }
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No hostels found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</main>
