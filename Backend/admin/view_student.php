<?php
include 'includes/auth.php';
include './includes/connect.php';
include './includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized access.";
    exit;
}

$admin_id = $_SESSION['user_id'];

// Handle Delete Booking POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking'])) {
    $del_booking_id = intval($_POST['booking_id']);

    // Verify booking exists and admin owns the related hostel
    $checkQuery = "SELECT b.id FROM bookings b INNER JOIN hostels h ON b.hostel_id = h.id WHERE b.id = ? AND h.created_by = ?";
    $checkStmt = $con->prepare($checkQuery);
    $checkStmt->bind_param("ii", $del_booking_id, $admin_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 1) {
        // Delete booking
        $deleteQuery = "DELETE FROM bookings WHERE id = ?";
        $deleteStmt = $con->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $del_booking_id);
        if ($deleteStmt->execute()) {
            $deleteStmt->close();
            $checkStmt->close();
            header("Location: manageS.php?msg=Booking deleted successfully");
            exit;
        } else {
            echo "Failed to delete booking.";
        }
    } else {
        echo "Booking not found or unauthorized.";
    }

    $checkStmt->close();
}

// Validate GET booking id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid booking ID.";
    exit;
}

$booking_id = intval($_GET['id']);

// Fetch booking detail with hostel join
$query = "
    SELECT b.*, h.name AS hostel_name
    FROM bookings b
    INNER JOIN hostels h ON b.hostel_id = h.id
    WHERE b.id = ? AND h.created_by = ?
";

$stmt = $con->prepare($query);
$stmt->bind_param("ii", $booking_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Booking not found or you're not authorized.";
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

// Duration Calculation
$start_date = new DateTime($data['stay_from']);
$today = new DateTime();
$interval = $start_date->diff($today);
$duration = $interval->format('%m months %d days');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Details</title>
  <link rel="stylesheet" href="assets/css/manageS.css">
  <style>
    .details-container {
      max-width: 800px;
      margin: 30px auto;
      padding: 25px;
      border: 1px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      background-color: #f9f9f9;
    }
    .details-container h2 {
      text-align: center;
      margin-bottom: 25px;
    }
    .details-table {
      width: 100%;
      border-collapse: collapse;
    }
    .details-table td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }
    .details-table td:first-child {
      font-weight: bold;
      width: 40%;
      background-color: #f0f0f0;
    }
    .back-link {
      text-align: center;
      margin-top: 20px;
    }
    form.delete-form {
      text-align: center;
      margin-top: 20px;
    }
    form.delete-form button {
      background-color: #d9534f;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    form.delete-form button:hover {
      background-color: #c9302c;
    }
  </style>
</head>
<body>
  <div class="details-container">
    <h2>Student Booking Details</h2>
    <table class="details-table">
      <tr><td>Full Name</td><td><?= htmlspecialchars($data['full_name']) ?></td></tr>
      <tr><td>Gender</td><td><?= htmlspecialchars($data['gender']) ?></td></tr>
      <tr><td>Contact No</td><td><?= htmlspecialchars($data['contact_no']) ?></td></tr>
      <tr><td>Guardian Name</td><td><?= htmlspecialchars($data['guardian_name']) ?></td></tr>
      <tr><td>Guardian Contact</td><td><?= htmlspecialchars($data['guardian_contact_no']) ?></td></tr>

      <tr><td>Correspondence Address</td><td>
        <?= nl2br(htmlspecialchars($data['corr_address'])) ?><br>
        <?= htmlspecialchars($data['corr_city']) ?>, <?= htmlspecialchars($data['corr_district']) ?>
      </td></tr>

      <tr><td>Permanent Address</td><td>
        <?= nl2br(htmlspecialchars($data['perm_address'])) ?><br>
        <?= htmlspecialchars($data['perm_city']) ?>, <?= htmlspecialchars($data['perm_district']) ?>
      </td></tr>

      <tr><td>Hostel Name</td><td><?= htmlspecialchars($data['hostel_name']) ?></td></tr>
      <tr><td>Room No</td><td><?= htmlspecialchars($data['room_no']) ?></td></tr>
      <tr><td>Seater</td><td><?= htmlspecialchars($data['seater']) ?></td></tr>
      <tr><td>Food Status</td><td><?= htmlspecialchars($data['food_status']) ?></td></tr>

      <tr><td>Stay From</td><td><?= htmlspecialchars($data['stay_from']) ?></td></tr>
      <tr><td>Stay Duration (Planned)</td><td><?= htmlspecialchars($data['stay_duration']) ?> month(s)</td></tr>

      <tr><td>Fee per Month</td><td>Rs. <?= number_format($data['fee_per_month'], 2) ?></td></tr>
      <tr><td>Total Fee</td><td>Rs. <?= number_format($data['total_fee'], 2) ?></td></tr>

      <tr><td>Status</td><td><?= htmlspecialchars($data['status']) ?></td></tr>
      <tr><td>Booking Date</td><td><?= htmlspecialchars($data['created_at']) ?></td></tr>
    </table>

    <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this booking?');">
      <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
      <button type="submit" name="delete_booking">Delete Booking</button>
    </form>

    <div class="back-link">
      <a href="manageS.php">← Back to Manage Students</a>
    </div>
  </div>
</body>
</html>
