<?php
include './includes/header.php';
include './includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data from users table
$sqlUser = "SELECT name, phone, address, gender FROM users WHERE id = ?";
$stmtUser = $con->prepare($sqlUser);
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userData = $resultUser->fetch_assoc();
$stmtUser->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $imagePath = $uploadPath;
        }
    }

    $seater = $_POST['seater'];
    $hostel_id = $_POST['hostel_id'];
    $room_no = $_POST['room_no'];
    $food_status = $_POST['food_status'];
    $stay_from = $_POST['stay_from'];
    $stay_duration = $_POST['stay_duration'];
    $fee_per_month = $_POST['fee_per_month'];

    // Since you don't have separate first/middle/last name, just use full name from form or $userData['name']
    $full_name = $_POST['full_name'] ?? $userData['name'];
    $gender = $_POST['gender'] ?? $userData['gender'];
    $contact_no = $_POST['contact_no'] ?? $userData['phone'];
    $guardian_name = $_POST['guardian_name'] ?? '';
    $guardian_contact_no = $_POST['guardian_contact_no'] ?? '';
    $corr_address = $_POST['corr_address'] ?? '';
    $corr_city = $_POST['corr_city'] ?? '';
    $corr_district = $_POST['corr_district'] ?? '';
    $perm_address = $_POST['perm_address'] ?? '';
    $perm_city = $_POST['perm_city'] ?? '';
    $perm_district = $_POST['perm_district'] ?? '';

    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO bookings (
        seater, room_no, food_status, stay_from, stay_duration, fee_per_month,
        full_name, gender, contact_no,
        guardian_name, guardian_contact_no, image,
        corr_address, corr_city, corr_district,
        perm_address, perm_city, perm_district,
        user_id, hostel_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $con->error);
    }

    $stmt->bind_param(
        "isssidsissssssssssii",
        $seater,
        $room_no,
        $food_status,
        $stay_from,
        $stay_duration,
        $fee_per_month,
        $full_name,
        $gender,
        $contact_no,
        $guardian_name,
        $guardian_contact_no,
        $imagePath,
        $corr_address,
        $corr_city,
        $corr_district,
        $perm_address,
        $perm_city,
        $perm_district,
        $user_id,
        $hostel_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Booking submitted successfully!'); window.location='book.php';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $con->close();
}
?>

<style>
    .form-banner-box {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fdfdfd;
        border: 2px solid #ccc;
        border-radius: 10px;
    }
</style>

<h2>Book Now</h2>

<div class="form-banner-box">
    <div class="banner-title">Fill All Info</div>

    <form action="book.php" method="POST" enctype="multipart/form-data">
        <div class="form-wrapper">
            <div class="form-left">
                <fieldset>
                    <legend>Room Related Info</legend>
                    <div class="flex-row">
                        <div class="form-group">
                            <label>Seater</label>
                            <select name="seater" required>
                                <option value="">Select Seater</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Hostel</label>
                            <select name="hostel_id" required>
                                <option value="">Select Hostel</option>
                                <?php
                                $hostels = mysqli_query($con, "SELECT id, name FROM hostels");
                                while ($row = mysqli_fetch_assoc($hostels)) {
                                    echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Room no.</label>
                            <input type="text" name="room_no" required>
                        </div>
                        <div class="form-group">
                            <label>Food Status</label><br>
                            <label><input type="radio" name="food_status" value="Without Food" checked> Without Food</label>
                            <label><input type="radio" name="food_status" value="With Food"> With Food (Rs 2000/Month)</label>
                        </div>
                        <div class="form-group">
                            <label>Stay From</label>
                            <input type="date" name="stay_from" required>
                        </div>
                        <div class="form-group">
                            <label>Stay Duration</label>
                            <select name="stay_duration" required>
                                <option value="">Select Duration in Month</option>
                                <option value="3">3</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fees Per Month</label>
                            <input type="text" name="fee_per_month" required>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Personal Info</legend>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?php if ($userData['gender'] == 'male') echo 'selected'; ?>>Male</option>
                            <option value="female" <?php if ($userData['gender'] == 'female') echo 'selected'; ?>>Female</option>
                            <option value="other" <?php if ($userData['gender'] == 'other') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Contact No</label>
                        <input type="text" name="contact_no" value="<?php echo htmlspecialchars($userData['phone']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Guardian Name</label>
                        <input type="text" name="guardian_name">
                    </div>
                    <div class="form-group">
                        <label>Guardian Contact No</label>
                        <input type="text" name="guardian_contact_no">
                    </div>
                </fieldset>
            </div>

            <div class="form-right">
                <fieldset>
                    <legend>Correspondence Address</legend>
                    <div class="flex-row">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="corr_address" value="<?php echo htmlspecialchars($userData['corr_address'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="corr_city" value="<?php echo htmlspecialchars($userData['corr_city'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <input type="text" name="corr_district" value="<?php echo htmlspecialchars($userData['corr_district'] ?? ''); ?>">
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Permanent Address</legend>
                    <div class="flex-row">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="perm_address" value="<?php echo htmlspecialchars($userData['perm_address'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="perm_city" value="<?php echo htmlspecialchars($userData['perm_city'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <input type="text" name="perm_district" value="<?php echo htmlspecialchars($userData['perm_district'] ?? ''); ?>">
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <button type="submit" class="submit-btn">Submit</button>
    </form>
</div>
</body>

</html>