<?php
session_start();
include 'includes/auth.php'; 
include './includes/header.php';
include './includes/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Unauthorized access!'); window.location.href = '../login.php';</script>";
    exit;
}

$admin_id = $_SESSION['user_id'];

$sql = "SELECT r.id, r.room_no, r.seater, r.fee_per_student, h.name AS hostel_name
        FROM rooms r
        INNER JOIN hostels h ON r.hostel_id = h.id
        WHERE h.created_by = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<main class="main-content">
    <h1>Manage Rooms</h1>
    <div class="table-container">
        <h2>All Room Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Sno.</th>
                    <th>Hostel Name</th>
                    <th>Seater</th>
                    <th>Room No.</th>
                    <th>Fee (Per Student)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sno = 1;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr data-room-id='{$row['id']}'>
                            <td>{$sno}</td>
                            <td>{$row['hostel_name']}</td>
                            <td class='seater'>{$row['seater']}</td>
                            <td class='room_no'>{$row['room_no']}</td>
                            <td class='fee_per_student'>{$row['fee_per_student']}</td>
                            <td class='action-icons'>
                                <i class='fas fa-edit' onclick='openEditModal(this)'></i>
                                <i class='fas fa-times' onclick='deleteRoom(this)'></i>
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
