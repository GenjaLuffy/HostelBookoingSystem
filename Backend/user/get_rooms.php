<?php
include './includes/connect.php';

if (isset($_POST['seater']) && isset($_POST['hostel_id'])) {
    $seater = $_POST['seater'];
    $hostel_id = $_POST['hostel_id'];

    $stmt = $con->prepare("SELECT id, room_no FROM rooms WHERE seater = ? AND hostel_id = ?");
    $stmt->bind_param("ii", $seater, $hostel_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = "<option value=''>Select Room</option>";
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='" . $row['room_no'] . "'>" . $row['room_no'] . "</option>";
    }
    echo $options;
    exit;
}

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
