<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/auth.php';
include './includes/sheader.php';
include './includes/connect.php'; // adjust this path to your DB connection file

// Handle delete request for hostel only
if (isset($_GET['delete_hostel']) && is_numeric($_GET['delete_hostel'])) {
    $hostelId = $_GET['delete_hostel'];

    // Step 1: Delete bookings linked to that hostel
    $stmtBookings = $con->prepare("DELETE FROM bookings WHERE hostel_id = ?");
    $stmtBookings->bind_param("i", $hostelId);
    $stmtBookings->execute();
    $stmtBookings->close();

    // Step 2: Delete hostel
    $stmtHostel = $con->prepare("DELETE FROM hostels WHERE id = ?");
    $stmtHostel->bind_param("i", $hostelId);
    $stmtHostel->execute();
    $stmtHostel->close();

    // Redirect to avoid resubmission
    header("Location: sowner.php?deleted=1");
    exit();
}

// Query to get owners and their hostels
$sql = "SELECT
            admins.id AS admin_id,
            admins.name AS admin_name,
            hostels.id AS hostel_id,
            hostels.name AS hostel_name,
            admins.phone AS admin_phone
        FROM admins
        JOIN hostels ON hostels.created_by = admins.id
        WHERE admins.type IN ('admin', 'superadmin')";

$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Admins/Owners</title>
    <link rel="stylesheet" href="./assets/css/superprofile.css" />

    <link rel="stylesheet" href="assets/css/sowner.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
    <main class="main-content">
        <h1>Manage Owner</h1>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="success-message" style="color: green; margin-bottom: 10px;">Hostel deleted successfully.</div>
        <?php endif; ?>

        <div class="table-container">
            <h2>All Owner Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Owner Name</th>
                        <th>Hostel Name</th>
                        <th>Contact no.</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php $count = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $count++; ?></td>
                                <td><?= htmlspecialchars($row['admin_name']); ?></td>
                                <td><?= htmlspecialchars($row['hostel_name']); ?></td>
                                <td><?= htmlspecialchars($row['admin_phone']); ?></td>
                                <td>
                                    <a href="view_owner.php?id=<?= $row['admin_id']; ?>" title="View Owner Details"><i class="fas fa-eye"></i></a>
                                    <a href="edit_owner.php?id=<?= $row['admin_id']; ?>" title="Edit Owner"><i class="fas fa-edit"></i></a>
                                    <a href="sowner.php?delete_hostel=<?= $row['hostel_id']; ?>" onclick="return confirm('Are you sure you want to delete this owner?');" title="Delete Owner"><i class="fas fa-times"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No owners or hostels found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>