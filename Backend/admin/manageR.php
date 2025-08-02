<?php
session_start();
include 'includes/auth.php'; 
include './includes/header.php';
include './includes/connect.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Unauthorized access!'); window.location.href = '../login.php';</script>";
    exit;
}

$admin_id = $_SESSION['user_id'];

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $roomId = $_GET['delete'];

    // Prepare and execute delete query
    $stmtDelete = $con->prepare("DELETE FROM rooms WHERE id = ?");
    $stmtDelete->bind_param("i", $roomId);
    if ($stmtDelete->execute()) {
        $message = "Room deleted successfully.";
    } else {
        $message = "Error deleting room.";
    }
    $stmtDelete->close();
}

// Fetch room list for this admin
$sql = "SELECT r.id, r.room_no, r.seater, r.fee_per_student, h.name AS hostel_name
        FROM rooms r
        INNER JOIN hostels h ON r.hostel_id = h.id
        WHERE h.created_by = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="assets/css/manageR.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
<main class="main-content">
    <h1>Manage Rooms</h1>

    <?php if (isset($message)): ?>
        <div style="color: green; margin-bottom: 10px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="table-container">
        <h2>All Room Details</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Sno.</th>
                    <th>Hostel Name</th>
                    <th>Seater</th>
                    <th>Room No.</th>
                    <th>Fee Per Months</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sno = 1;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$sno}</td>
                            <td>" . htmlspecialchars($row['hostel_name']) . "</td>
                            <td>" . htmlspecialchars($row['seater']) . "</td>
                            <td>" . htmlspecialchars($row['room_no']) . "</td>
                            <td>" . htmlspecialchars($row['fee_per_student']) . "</td>
                            <td>
                                <a href='editRoom.php?id=" . $row['id'] . "' title='Edit'><i class='fas fa-edit'></i></a>
                                &nbsp;
                                <a href='manageR.php?delete=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this room?');\" title='Delete'><i class='fas fa-times'></i></a>
                            </td>
                        </tr>";
                        $sno++;
                    }
                } else {
                    echo "<tr><td colspan='6'>No rooms found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
