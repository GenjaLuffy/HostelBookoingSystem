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
      max-width: 1100px;
      margin: 40px auto;
      padding: 30px;
      background-color: #f8f9fc;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .details-container h2 {
      text-align: center;
      margin-bottom: 35px;
      font-size: 28px;
      font-weight: bold;
      color: #343a40;
    }

    .details-grid {
      width: 800px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 25px 40px;
      font-family: 'Segoe UI', sans-serif;
      font-size: 15px;
      color: #212529;
    }

    .detail-item {
      background-color: #ffffff;
      padding: 18px 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .detail-item label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #495057;
    }

    .detail-item span {
      color: #343a40;
    }

    .back-link {
      text-align: center;
      margin-top: 30px;
    }

    .back-link a {
      color: #17a2b8;
      font-weight: 600;
      text-decoration: none;
      font-size: 16px;
      transition: color 0.3s ease;
    }

    .back-link a:hover {
      color: #117a8b;
      text-decoration: underline;
    }

    form.delete-form {
      text-align: center;
      margin-top: 30px;
    }

    form.delete-form button {
      background-color: #dc3545;
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    form.delete-form button:hover {
      background-color: #c82333;
      box-shadow: 0 6px 14px rgba(200, 35, 51, 0.5);
    }

    @media (max-width: 768px) {
      .details-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="details-container">
    <h2>Student Booking Details</h2>
    <div class="details-grid">
      <div class="detail-item"><label>Full Name</label><span><?= htmlspecialchars($data['full_name']) ?></span></div>
      <div class="detail-item"><label>Gender</label><span><?= htmlspecialchars($data['gender']) ?></span></div>
      <div class="detail-item"><label>Contact No</label><span><?= htmlspecialchars($data['contact_no']) ?></span></div>
      <div class="detail-item"><label>Guardian Name</label><span><?= htmlspecialchars($data['guardian_name']) ?></span></div>
      <div class="detail-item"><label>Guardian Contact</label><span><?= htmlspecialchars($data['guardian_contact_no']) ?></span></div>

      <div class="detail-item"><label>Correspondence Address</label>
        <span><?= nl2br(htmlspecialchars($data['corr_address'])) ?><br><?= htmlspecialchars($data['corr_city']) ?>, <?= htmlspecialchars($data['corr_district']) ?></span>
      </div>

      <div class="detail-item"><label>Permanent Address</label>
        <span><?= nl2br(htmlspecialchars($data['perm_address'])) ?><br><?= htmlspecialchars($data['perm_city']) ?>, <?= htmlspecialchars($data['perm_district']) ?></span>
      </div>

      <div class="detail-item"><label>Hostel Name</label><span><?= htmlspecialchars($data['hostel_name']) ?></span></div>
      <div class="detail-item"><label>Room No</label><span><?= htmlspecialchars($data['room_no']) ?></span></div>
      <div class="detail-item"><label>Seater</label><span><?= htmlspecialchars($data['seater']) ?></span></div>
      <div class="detail-item"><label>Food Status</label><span><?= htmlspecialchars($data['food_status']) ?></span></div>
      <div class="detail-item"><label>Stay From</label><span><?= htmlspecialchars($data['stay_from']) ?></span></div>
      <div class="detail-item"><label>Stay Duration (Planned)</label><span><?= htmlspecialchars($data['stay_duration']) ?> month(s)</span></div>
      <div class="detail-item"><label>Fee per Month</label><span>Rs. <?= number_format($data['fee_per_month'], 2) ?></span></div>
      <div class="detail-item"><label>Total Fee</label><span>Rs. <?= number_format($data['total_fee'], 2) ?></span></div>
      <div class="detail-item"><label>Status</label><span><?= htmlspecialchars($data['status']) ?></span></div>
      <div class="detail-item"><label>Booking Date</label><span><?= htmlspecialchars($data['created_at']) ?></span></div>
    </div>

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
