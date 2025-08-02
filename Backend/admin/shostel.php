<?php
session_start();
include 'includes/auth.php';
include './includes/sheader.php';
include './includes/connect.php'; 

// Debug: uncomment to see POST data
// echo "<pre>"; print_r($_POST); echo "</pre>"; exit;

if (isset($_POST['update_status'])) {
    $hostel_id = intval($_POST['hostel_id']);
    $new_status = $_POST['update_status'] === 'Approved' ? 'Approved' : 'Pending';

    $stmt = $con->prepare("UPDATE hostels SET status = ? WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $con->error);
    }
    $stmt->bind_param("si", $new_status, $hostel_id);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();

    // Redirect to prevent form resubmission and show updated data
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT h.id, h.name as hostel_name, h.status, a.name as creator_name 
        FROM hostels h
        JOIN admins a ON h.created_by = a.id";
$result = $con->query($sql);

if (!$result) {
    die("Query failed: " . $con->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Hostels</title>
    <link rel="stylesheet" href="assets/css/shostel.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
<main class="main-content">
  <h1>Manage Hostels</h1>
  <div class="table-container">
    <h2>All Hostel Details</h2>
    <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
      <thead>
        <tr>
          <th>Sno.</th>
          <th>Hostel Name</th>
          <th>Created By</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php $sno = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $sno++; ?></td>
                    <td><?= htmlspecialchars($row['hostel_name']); ?></td>
                    <td><?= htmlspecialchars($row['creator_name']); ?></td>
                    <td><span class="status <?= strtolower($row['status']); ?>"><?= $row['status']; ?></span></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="hostel_id" value="<?= $row['id']; ?>">
                            <?php if ($row['status'] === 'Approved'): ?>
                                <button type="submit" name="update_status" value="Pending">Set Pending</button>
                            <?php else: ?>
                                <button type="submit" name="update_status" value="Approved">Approve</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No hostels found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
