<?php
session_start();
include 'includes/auth.php';
include './includes/connect.php';
include './includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_no = trim($_POST["room_no"]);
    $seater = intval($_POST["seater"]);
    $fee = floatval($_POST["fee"]);

    // ✅ Use correct session keys
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        echo "<script>alert('Unauthorized access.'); window.location.href='login.php';</script>";
        exit;
    }

    $admin_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // ✅ Get hostel_id created by this admin
    $stmt = $con->prepare("SELECT id FROM hostels WHERE created_by = ? AND created_by_role = ? LIMIT 1");
    $stmt->bind_param("is", $admin_id, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('No hostel found for this admin. Please create one first.'); window.location.href='addHostel.php';</script>";
        exit;
    }

    $hostel = $result->fetch_assoc();
    $hostel_id = $hostel['id'];

    // ✅ Insert room
    $insert = $con->prepare("INSERT INTO rooms (room_no, seater, fee_per_student, user_id, hostel_id) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("siddi", $room_no, $seater, $fee, $admin_id, $hostel_id);

    if ($insert->execute()) {
        echo "<script>alert('Room added successfully.'); window.location.href='manageR.php';</script>";
    } else {
        echo "<script>alert('Failed to add room.');</script>";
    }

    $insert->close();
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Room | Book Mate</title>
    <link rel="stylesheet" href="assets/css/addRoom.css" />
</head>
<body>
<div class="main-content">
    <h1>Add Room</h1>
    <div class="form-card">
        <div class="form-header">Add Room</div>
        <form method="POST" action="addRoom.php">
            <label for="roomNo">Room No.</label>
            <input type="text" id="roomNo" name="room_no" required>

            <label for="seater">Select Seater</label>
            <select id="seater" name="seater" required>
                <option value="">-- Select --</option>
                <option value="1">1 Seater</option>
                <option value="2">2 Seater</option>
                <option value="3">3 Seater</option>
                <option value="4">4 Seater</option>
            </select>

            <label for="fee">Fee (Per Student)</label>
            <input type="number" id="fee" name="fee" required min="1000" step="0.01">

            <button type="submit">Create Room</button>
        </form>
    </div>
</div>
</body>
</html>
