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

    $full_name = $_POST['full_name'] ?? $userData['name'];
    $gender = $_POST['gender'] ?? $userData['gender'];
    $contact_no = $_POST['contact_no'] ?? $userData['phone'];
    $guardian_name = $_POST['guardian_name'] ?? '';
    $guardian_contact_no = $_POST['guardian_contact_no'] ?? '';
    $corr_address = $_POST['corr_address'] ?? '';
    $perm_address = $_POST['perm_address'] ?? '';

    // NOTE: city and district fields are ignored and not processed here intentionally

    $sql = "INSERT INTO bookings (
        seater, room_no, food_status, stay_from, stay_duration, fee_per_month,
        full_name, gender, contact_no,
        guardian_name, guardian_contact_no, image,
        corr_address,
        perm_address,
        user_id, hostel_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $con->error);
    }

    $stmt->bind_param(
        "isssidsissssssii",
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
        $perm_address,
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
                            <label for="seater">Seater</label>
                            <select name="seater" id="seater" required>
                                <option value="">Select Seater</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="hostel_id">Hostel</label>
                            <select name="hostel_id" id="hostel_id" required>
                                <option value="">Select Hostel</option>
                                <?php
                                $hostels = mysqli_query($con, "SELECT id, name FROM hostels");
                                while ($row = mysqli_fetch_assoc($hostels)) {
                                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="room_no">Room no.</label>
                            <input type="text" name="room_no" id="room_no" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Food Status</label><br>
                        <label><input type="radio" name="food_status" value="Without Food" checked> Without Food</label>
                        <label><input type="radio" name="food_status" value="With Food"> With Food (Rs 2000/Month)</label>
                    </div>
                    <div class="flex-row">
                        <div class="form-group">
                            <label for="stay_from">Stay From</label>
                            <input type="date" name="stay_from" id="stay_from" required>
                        </div>
                        <div class="form-group">
                            <label for="stay_duration">Stay Duration</label>
                            <select name="stay_duration" id="stay_duration" required>
                                <option value="">Select Duration in Month</option>
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="9">9 Months</option>
                                <option value="12">12 Months</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fee_per_month">Fees Per Month</label>
                            <input type="text" name="fee_per_month" id="fee_per_month" required pattern="[0-9]+(\.[0-9]{1,2})?" title="Enter a valid amount (e.g., 5000 or 5000.00)">
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Personal Info</legend>
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?php if (($userData['gender'] ?? '') == 'male') echo 'selected'; ?>>Male</option>
                            <option value="female" <?php if (($userData['gender'] ?? '') == 'female') echo 'selected'; ?>>Female</option>
                            <option value="other" <?php if (($userData['gender'] ?? '') == 'other') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contact_no">Contact No</label>
                        <input type="text" name="contact_no" id="contact_no" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" required pattern="[0-9]{7,15}" title="Enter a valid phone number (7-15 digits)">
                    </div>
                    <div class="form-group">
                        <label for="guardian_name">Guardian Name</label>
                        <input type="text" name="guardian_name" id="guardian_name">
                    </div>
                    <div class="form-group">
                        <label for="guardian_contact_no">Guardian Contact No</label>
                        <input type="text" name="guardian_contact_no" id="guardian_contact_no" pattern="[0-9]{7,15}" title="Enter a valid phone number (7-15 digits)">
                    </div>
                </fieldset>
            </div>

            <div class="form-right">
                <fieldset>
                    <legend>Correspondence Address</legend>
                    <div class="form-group">
                        <label for="corr_address">Address</label>
                        <input type="text" name="corr_address" id="corr_address" value="<?php echo htmlspecialchars($userData['address'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="corr_city">City</label>
                        <input type="text" name="city" id="corr_city" placeholder="Enter your city">
                    </div>
                    <div class="form-group">
                        <label for="corr_district">District</label>
                        <input type="text" name="district" id="corr_district" placeholder="Enter your district">
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Permanent Address</legend>
                    <div class="form-group">
                        <label for="perm_address">Address</label>
                        <input type="text" name="city_perm" id="perm_city" placeholder="Enter your Address">
                    </div>
                    <div class="form-group">
                        <label for="perm_city">City</label>
                        <input type="text" name="city_perm" id="perm_city" placeholder="Enter your city">
                    </div>
                    <div class="form-group">
                        <label for="perm_district">District</label>
                        <input type="text" name="district_perm" id="perm_district" placeholder="Enter your district">
                    </div>
                </fieldset>
            </div>
        </div>

        <button type="submit" class="submit-btn">Submit</button>
    </form>
</div>
</body>

</html>
