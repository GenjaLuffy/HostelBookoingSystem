<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/auth.php';
include './includes/sheader.php'; 
include './includes/connect.php'; // adjust this path to your DB connection file

// Query to get owners and their hostels
$sql = "SELECT
            admins.id AS admin_id,
            admins.name AS admin_name,
            hostels.name AS hostel_name,
            admins.phone AS admin_phone
        FROM admins
        JOIN hostels ON hostels.created_by = admins.id
        WHERE admins.type IN ('admin', 'superadmin')";

$result = $con->query($sql);
?>

<main class="main-content">
    <h1>Manage Owner</h1>
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
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $count++; ?></td>
                            <td><?= htmlspecialchars($row['admin_name']); ?></td>
                            <td><?= htmlspecialchars($row['hostel_name']); ?></td>
                            <td><?= htmlspecialchars($row['admin_phone']); ?></td>
                            <td>
                                <a href="edit_owner.php?id=<?= $row['admin_id']; ?>"><i class="fas fa-edit"></i></a>
                                <a href="delete_owner.php?id=<?= $row['admin_id']; ?>" onclick="return confirm('Are you sure to delete this owner?');"><i class="fas fa-times"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No owners found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
