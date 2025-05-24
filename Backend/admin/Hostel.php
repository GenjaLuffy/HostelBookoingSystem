<?php
session_start();
include './includes/connect.php';
include './includes/header.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in to see your hostels.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch hostels created by logged-in user with status 'Approved'
$sql = "SELECT * FROM hostels WHERE created_by = ? AND status = 'Approved' ORDER BY created_at DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="featured">
    <div class="hostels">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = (int)$row['id'];
                $name = htmlspecialchars($row['name']);
                $price = 'Rs ' . number_format($row['fee']);
                $location = htmlspecialchars($row['location']);
                $image = htmlspecialchars($row['image']);
                ?>
                <a class="hostel-card" href="addRoom.php?hostel_id=<?= $id ?>">
                    <img src="<?= $image ?>" alt="<?= $name ?>" />
                    <h3><?= $name ?></h3>
                    <p><?= $price ?> ★★★★☆<br><?= $location ?></p>
                </a>
                <?php
            }
        } else {
            echo "<p>No hostels found created by you.</p>";
        }
        ?>
    </div>
</section>
</body>
</html>
