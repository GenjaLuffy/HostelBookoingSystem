<?php
session_start();
include './includes/connect.php';
include './includes/header.php';

// Make sure user is logged in and user_id session is set
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in to see your hostels.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch hostels created by logged-in user with status approved
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
                $name = htmlspecialchars($row['name']);
                $price = 'Rs ' . number_format($row['fee']);
                $location = htmlspecialchars($row['location']);
                $image = htmlspecialchars($row['image']);
                $type = htmlspecialchars($row['gender']);
                $desc = htmlspecialchars($row['description']);
                ?>
                <div class="hostel-card" 
                     data-name="<?= $name ?>" 
                     data-price="<?= $price ?>" 
                     data-location="<?= $location ?>"
                     data-image="<?= $image ?>" 
                     data-type="<?= $type ?>"
                     data-desc="<?= $desc ?>">
                    <img src="<?= $image ?>" alt="<?= $name ?>" />
                    <h3><?= $name ?></h3>
                    <p><?= $price ?> ★★★★☆<br><?= $location ?></p>
                </div>
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
