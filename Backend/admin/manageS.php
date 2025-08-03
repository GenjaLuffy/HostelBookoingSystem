<?php
session_start();
include 'includes/auth.php';
include './includes/connect.php';
include './includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized access.";
    exit;
}

$admin_id = $_SESSION['user_id'];

// Handle Approve/Not Approve actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_id'])) {
        $booking_id = (int)$_POST['approve_id'];
        $stmt = $con->prepare("UPDATE bookings SET status = 'Approved' WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
    }
}

// Fetch bookings created by this admin
$stmt = $con->prepare("
    SELECT 
        b.id, b.full_name, b.contact_no, b.room_no, b.seater, b.stay_from, 
        b.stay_duration, b.status
    FROM bookings b
    JOIN hostels h ON b.hostel_id = h.id
    WHERE h.created_by = ?
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Student Bookings</title>
    <link rel="stylesheet" href="assets/css/manageS.css" />
</head>

<body>
    <main class="main-content">
        <h1>All Booking Records</h1>
        <div class="table-wrapper">
            <table class="booking-table" aria-label="Student Bookings Table">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Student Name</th>
                        <th>Contact No.</th>
                        <th>Room No.</th>
                        <th>Seater</th>
                        <th>Staying From</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sno = 1;
                    while ($row = $result->fetch_assoc()):
                        $booking_id = (int)$row['id'];
                        $full_name = htmlspecialchars($row['full_name']);
                        $contact_no = htmlspecialchars($row['contact_no']);
                        $room_no = htmlspecialchars($row['room_no']);
                        $seater = htmlspecialchars($row['seater']);
                        $stay_from = htmlspecialchars($row['stay_from']);
                        $duration = htmlspecialchars($row['stay_duration']);
                        $status = htmlspecialchars($row['status']);
                    ?>
                        <tr>
                            <td><?= $sno ?></td>
                            <td><?= $full_name ?></td>
                            <td><?= $contact_no ?></td>
                            <td><?= $room_no ?></td>
                            <td><?= $seater ?></td>
                            <td><?= $stay_from ?></td>
                            <td><?= $duration ?> months</td>
                            <td><?= $status ?></td>
                            <td class="actions">
                                <?php if ($status !== 'Cancelled'): ?>
                                    <?php if ($status !== 'Approved'): ?>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('Approve this booking?');" style="display:inline;">
                                            <input type="hidden" name="approve_id" value="<?= $booking_id ?>" />
                                            <button type="submit" class="btn approve">Approve</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('Mark as Not Approved?');" style="display:inline;">
                                            <input type="hidden" name="not_approve_id" value="<?= $booking_id ?>" />
                                            <button type="submit" class="btn not-approve">Not Approve</button>
                                        </form>
                                    <?php endif; ?>

                                    <a href="view_student.php?id=<?= $booking_id ?>" class="btn view">View</a>
                                <?php else: ?>
                                    <span class="no-actions">No actions available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                        $sno++;
                    endwhile;
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>
