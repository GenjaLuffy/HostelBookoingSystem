<?php
include './includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
  $booking_id = intval($_POST['booking_id']);

  // Update status to 'Cancelled'
  $stmt = $con->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
  $stmt->bind_param("i", $booking_id);

  if ($stmt->execute()) {
    header("Location: bookinghistory.php?message=cancelled");
    exit();
  } else {
    echo "Error cancelling booking.";
  }
} else {
  echo "Invalid request.";
}
?>
