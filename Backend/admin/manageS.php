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

// Handle Approve or Not Approve actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_id'])) {
        $approve_id = intval($_POST['approve_id']);

        $checkStmt = $con->prepare("
            SELECT b.id FROM bookings b 
            INNER JOIN hostels h ON b.hostel_id = h.id 
            WHERE b.id = ? AND h.created_by = ?
        ");
        $checkStmt->bind_param("ii", $approve_id, $admin_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 1) {
            $updateStmt = $con->prepare("UPDATE bookings SET status = 'Approved' WHERE id = ?");
            $updateStmt->bind_param("i", $approve_id);
            $updateStmt->execute();
            $updateStmt->close();
        }
        $checkStmt->close();

        header("Location: manageS.php");
        exit;
    } elseif (isset($_POST['not_approve_id'])) {
        $not_approve_id = intval($_POST['not_approve_id']);

        $checkStmt = $con->prepare("
            SELECT b.id FROM bookings b 
            INNER JOIN hostels h ON b.hostel_id = h.id 
            WHERE b.id = ? AND h.created_by = ?
        ");
        $checkStmt->bind_param("ii", $not_approve_id, $admin_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 1) {
            $updateStmt = $con->prepare("UPDATE bookings SET status = 'Not Approved' WHERE id = ?");
            $updateStmt->bind_param("i", $not_approve_id);
            $updateStmt->execute();
            $updateStmt->close();
        }
        $checkStmt->close();

        header("Location: manageS.php");
        exit;
    }
}

// Handle Delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    $checkStmt = $con->prepare("
        SELECT b.id FROM bookings b 
        INNER JOIN hostels h ON b.hostel_id = h.id 
        WHERE b.id = ? AND h.created_by = ?
    ");
    $checkStmt->bind_param("ii", $delete_id, $admin_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 1) {
        $deleteStmt = $con->prepare("DELETE FROM bookings WHERE id = ?");
        $deleteStmt->bind_param("i", $delete_id);
        $deleteStmt->execute();
        $deleteStmt->close();
    }
    $checkStmt->close();

    header("Location: manageS.php");
    exit;
}

// Fetch bookings
$query = "
    SELECT 
        b.id, b.full_name, b.contact_no, b.room_no, b.seater, b.stay_from, b.status
    FROM 
        bookings b
    INNER JOIN 
        hostels h ON b.hostel_id = h.id
    WHERE 
        h.created_by = ?
";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="main-content">
    <h1>Manage Student</h1>
    <div class="table-container">
        <h2>All Student Details</h2>
        <table border="1" cellpadding="8" cellspacing="0" style="border-collapse:collapse; width:100%;">
            <thead>
                <tr>
                    <th>Sno.</th>
                    <th>Student Name</th>
                    <th>Contact No.</th>
                    <th>Room No.</th>
                    <th>Seater</th>
                    <th>Staying From</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows === 0) {
                    echo "<tr><td colspan='8' style='text-align:center;'>No bookings found for your hostels.</td></tr>";
                } else {
                    $sno = 1;
                    while ($row = $result->fetch_assoc()) {
                        $full_name = htmlspecialchars($row['full_name']);
                        $contact_no = htmlspecialchars($row['contact_no']);
                        $room_no = htmlspecialchars($row['room_no']);
                        $seater = htmlspecialchars($row['seater']);
                        $stay_from = htmlspecialchars($row['stay_from']);
                        $status = htmlspecialchars($row['status']);
                        $booking_id = $row['id'];

                        echo "<tr>
                            <td>{$sno}</td>
                            <td>{$full_name}</td>
                            <td>{$contact_no}</td>
                            <td>{$room_no}</td>
                            <td>{$seater}</td>
                            <td>{$stay_from}</td>
                            <td>{$status}</td>
                            <td>";

                        // Only show actions if not Cancelled
                        if ($status !== 'Cancelled') {
                            if ($status !== 'Approved') {
                                echo "<form method='POST' action='' style='display:inline; margin-right:5px;'>
                                        <input type='hidden' name='approve_id' value='{$booking_id}' />
                                        <button type='submit' onclick='return confirm(\"Approve this booking?\");'>Approve</button>
                                      </form>";
                            } else {
                                echo "<form method='POST' action='' style='display:inline; margin-right:5px;'>
                                        <input type='hidden' name='not_approve_id' value='{$booking_id}' />
                                        <button type='submit' onclick='return confirm(\"Mark this booking as Not Approved?\");'>Not Approved</button>
                                      </form>";
                            }
                        } else {
                            echo "<span style='color:gray;'>No actions available</span>";
                        }

                        echo "</td></tr>";
                        $sno++;
                    }
                }
                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
