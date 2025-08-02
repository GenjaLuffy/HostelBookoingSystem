<?php
include './includes/connect.php';

$currentDate = date('Y-m-d');

// Cancel booking by ID (POST)
if (isset($_POST['cancel_booking_id'])) {
    $booking_id = (int)$_POST['cancel_booking_id'];

    // Soft cancel by updating status
    $stmt = $con->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo "Booking cancelled successfully.";
    } else {
        echo "Error cancelling booking.";
    }
    exit;
}

// Fetch available rooms based on seater and hostel (POST)
if (isset($_POST['seater']) && isset($_POST['hostel_id'])) {
    $seater = (int)$_POST['seater'];
    $hostel_id = (int)$_POST['hostel_id'];

    // Rooms not currently booked (i.e., booking is still active and not cancelled)
    $stmt = $con->prepare("
        SELECT r.id, r.room_no 
        FROM rooms r
        WHERE r.seater = ?
          AND r.hostel_id = ?
          AND r.room_no NOT IN (
              SELECT b.room_no 
              FROM bookings b
              WHERE b.hostel_id = ?
              AND b.status != 'Cancelled'
              AND DATE_ADD(b.stay_from, INTERVAL b.stay_duration MONTH) > ?
          )
    ");
    $stmt->bind_param("iiis", $seater, $hostel_id, $hostel_id, $currentDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = "<option value=''>Select Room</option>";
    while ($row = $result->fetch_assoc()) {
        $roomNo = htmlspecialchars($row['room_no']);
        $options .= "<option value='{$roomNo}'>{$roomNo}</option>";
    }
    echo $options;
    exit;
}

// Fetch fee_per_student for a selected room (POST)
if (isset($_POST['room_no']) && isset($_POST['hostel_id'])) {
    $room_no = $_POST['room_no'];
    $hostel_id = (int)$_POST['hostel_id'];

    $stmt = $con->prepare("SELECT fee_per_student FROM rooms WHERE room_no = ? AND hostel_id = ?");
    $stmt->bind_param("si", $room_no, $hostel_id);
    $stmt->execute();
    $stmt->bind_result($fee);
    $stmt->fetch();
    echo $fee ?: '';
    exit;
}
?>
