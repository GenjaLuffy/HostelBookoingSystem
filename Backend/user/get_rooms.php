<?php
include './includes/connect.php';

// Fetch available rooms based on seater and hostel
if (isset($_POST['seater']) && isset($_POST['hostel_id'])) {
    $seater = $_POST['seater'];
    $hostel_id = $_POST['hostel_id'];

    $stmt = $con->prepare("
        SELECT r.id, r.room_no 
        FROM rooms r
        WHERE r.seater = ?
          AND r.hostel_id = ?
          AND r.room_no NOT IN (
              SELECT b.room_no 
              FROM bookings b 
              WHERE b.hostel_id = ?
          )
    ");
    $stmt->bind_param("iii", $seater, $hostel_id, $hostel_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = "<option value=''>Select Room</option>";
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='" . htmlspecialchars($row['room_no']) . "'>" . htmlspecialchars($row['room_no']) . "</option>";
    }
    echo $options;
    exit;
}

// Fetch fee_per_student for a selected room
if (isset($_POST['room_no']) && isset($_POST['hostel_id'])) {
    $room_no = $_POST['room_no'];
    $hostel_id = $_POST['hostel_id'];

    $stmt = $con->prepare("SELECT fee_per_student FROM rooms WHERE room_no = ? AND hostel_id = ?");
    $stmt->bind_param("si", $room_no, $hostel_id);
    $stmt->execute();
    $stmt->bind_result($fee);
    $stmt->fetch();
    echo $fee ?: '';
    exit;
}
?>
