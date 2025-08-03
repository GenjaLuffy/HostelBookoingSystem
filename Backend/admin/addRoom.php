<?php
session_start();
include 'includes/auth.php';
include './includes/connect.php';
include './includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "<script>alert('Unauthorized access.'); window.location.href='login.php';</script>";
    exit;
}

$admin_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if (!isset($_GET['hostel_id'])) {
    echo "<script>alert('No hostel selected.'); window.location.href='manageHostel.php';</script>";
    exit;
}

$hostel_id = intval($_GET['hostel_id']);

$stmt = $con->prepare("SELECT id FROM hostels WHERE id = ? AND created_by = ? AND created_by_role = ? LIMIT 1");
$stmt->bind_param("iis", $hostel_id, $admin_id, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Invalid hostel or you are not the owner.'); window.location.href='manageHostel.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_no = trim($_POST["room_no"]);
    $seater = intval($_POST["seater"]);
    $fee = floatval($_POST["fee"]);

    $insert = $con->prepare("INSERT INTO rooms (room_no, seater, fee_per_student, admin_id, hostel_id) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("siddi", $room_no, $seater, $fee, $admin_id, $hostel_id);

    if ($insert->execute()) {
        echo "<script>alert('Room added successfully.'); window.location.href='manageR.php';</script>";
    } else {
        echo "<script>alert('Failed to add room.');</script>";
    }

    $insert->close();
}

$stmt->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Room</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/addRoom.css" />
</head>
<body>
    <div class="main-content">
    <div class="form-card">
            <h1>Add Room</h1>
        <form method="POST" action="addRoom.php?hostel_id=<?= htmlspecialchars($_GET['hostel_id']) ?>">
            <label for="roomNo">Room No.</label>
            <input type="text" id="roomNo" name="room_no" placeholder="Enter Room Number" required />

            <label for="seater">Select Seater</label>
            <select id="seater" name="seater" required>
                <option value="" disabled selected>-- Select --</option>
                <option value="1">1 Seater</option>
                <option value="2">2 Seater</option>
                <option value="3">3 Seater</option>
                <option value="4">4 Seater</option>
            </select>

            <label for="fee">Fee Per Month</label>
            <input
                type="number"
                id="fee"
                name="fee"
                placeholder="Enter Monthly Fee"
                min="1000"
                step="0.01"
                required />

            <button type="submit" class="btn-submit">Create Room</button>
        </form>
    </div>
    </div>
        </div>
</body>

</html>