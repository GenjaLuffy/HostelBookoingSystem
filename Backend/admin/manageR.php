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

$sql = "SELECT r.id, r.room_no, r.seater, r.fee_per_student 
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
                        echo "<tr>
                            <td>{$sno}</td>
                            <td>{$row['seater']}</td>
                            <td>{$row['room_no']}</td>
                            <td>{$row['fee_per_student']}</td>
                            <td>
                                <a href='editRoom.php?id={$row['id']}'><i class='fas fa-edit'></i></a>
                                <a href='deleteRoom.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this room?\")'><i class='fas fa-times'></i></a>
                            </td>
                        </tr>";
                        $sno++;
                    }
                } else {
                    echo "<tr><td colspan='5'>No rooms found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

</div>
</body>
</html>
