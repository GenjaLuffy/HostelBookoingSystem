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

    if (isset($_POST['not_approve_id'])) {
        $booking_id = (int)$_POST['not_approve_id'];
        $stmt = $con->prepare("UPDATE bookings SET status = 'Not Approved' WHERE id = ?");
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

<div class="main-content">
    <h1>Manage Student Bookings</h1>
    <div class="card">
        <h3>All Booking Records</h3>
        <table class="table table-bordered">
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
                while ($row = $result->fetch_assoc()) {
                    $booking_id = $row['id'];
                    $full_name = htmlspecialchars($row['full_name']);
                    $contact_no = htmlspecialchars($row['contact_no']);
                    $room_no = htmlspecialchars($row['room_no']);
                    $seater = htmlspecialchars($row['seater']);
                    $stay_from = htmlspecialchars($row['stay_from']);
                    $duration = htmlspecialchars($row['stay_duration']);
                    $status = htmlspecialchars($row['status']);

                    echo "<tr>
                        <td>{$sno}</td>
                        <td>{$full_name}</td>
                        <td>{$contact_no}</td>
                        <td>{$room_no}</td>
                        <td>{$seater}</td>
                        <td>{$stay_from}</td>
                        <td>{$duration} months</td>
                        <td>{$status}</td>
                        <td>";

                    if ($status !== 'Cancelled') {
                        if ($status !== 'Approved') {
                            echo "<form method='POST' action='' style='display:inline; margin-right:5px;'>
                                    <input type='hidden' name='approve_id' value='{$booking_id}' />
                                    <button type='submit' onclick='return confirm(\"Approve this booking?\");'>Approve</button>
                                  </form>";
                        } else {
                            echo "<form method='POST' action='' style='display:inline; margin-right:5px;'>
                                    <input type='hidden' name='not_approve_id' value='{$booking_id}' />
                                    <button type='submit' onclick='return confirm(\"Mark as Not Approved?\");'>Not Approve</button>
                                  </form>";
                        }

                        echo "<form method='GET' action='view_student.php' style='display:inline;'>
                                <input type='hidden' name='id' value='{$booking_id}' />
                                <button type='submit'>View</button>
                              </form>";
                    } else {
                        echo "<span style='color:gray;'>No actions available</span>";
                    }

                    echo "</td></tr>";
                    $sno++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
