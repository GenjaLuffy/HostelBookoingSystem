<?php
session_start();
include 'includes/auth.php';
include './includes/connect.php';
include './includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Unauthorized access!'); window.location.href = '../login.php';</script>";
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid Room ID!'); window.location.href = 'manageR.php';</script>";
    exit;
}

$room_id = $_GET['id'];
$admin_id = $_SESSION['user_id'];

// Fetch room details
$stmt = $con->prepare("SELECT r.id, r.room_no, r.seater, r.fee_per_student, r.hostel_id 
                      FROM rooms r 
                      JOIN hostels h ON r.hostel_id = h.id 
                      WHERE r.id = ? AND h.created_by = ?");
$stmt->bind_param("ii", $room_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Room not found or unauthorized!'); window.location.href = 'manageR.php';</script>";
    exit;
}

$room = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seater = intval($_POST['seater']);
    $room_no = trim($_POST['room_no']);
    $fee = floatval($_POST['fee_per_student']);

    $updateStmt = $con->prepare("UPDATE rooms SET room_no = ?, seater = ?, fee_per_student = ? WHERE id = ?");
    $updateStmt->bind_param("sidi", $room_no, $seater, $fee, $room_id);

    if ($updateStmt->execute()) {
        echo "<script>alert('Room updated successfully!'); window.location.href = 'manageR.php';</script>";
        exit;
    } else {
        echo "<script>alert('Update failed: " . $updateStmt->error . "');</script>";
    }
}
?>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Rooms</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<main class="main-content">
    <h1>Edit Room</h1>
    <div class="form-card">
        <div class="form-header">Edit Room</div>
        <form method="POST">
            <label for="seater">Seater:</label>
            <input type="number" id="seater" name="seater" required min="1" value="<?= htmlspecialchars($room['seater']); ?>" />

            <label for="room_no">Room Number:</label>
            <input type="text" id="room_no" name="room_no" required value="<?= htmlspecialchars($room['room_no']); ?>" />

            <label for="fee_per_student">Fee (Per Student):</label>
            <input type="number" id="fee_per_student" name="fee_per_student" required min="1000" step="0.01" value="<?= htmlspecialchars($room['fee_per_student']); ?>" />

            <button type="submit">Update Room</button>
        </form>
    </div>
</main>
</body>
</html>
