<?php
include './includes/header.php';
include './includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sqlUser = "SELECT name, phone, address, gender FROM users WHERE id = ?";
$stmtUser = $con->prepare($sqlUser);
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userData = $resultUser->fetch_assoc();
$stmtUser->close();

$selectedHostelId = $_GET['hostel_id'] ?? '';
$hostelName = '';

if (!empty($selectedHostelId)) {
    $stmtHostel = $con->prepare("SELECT name FROM hostels WHERE id = ?");
    $stmtHostel->bind_param("i", $selectedHostelId);
    $stmtHostel->execute();
    $stmtHostel->bind_result($hostelName);
    $stmtHostel->fetch();
    $stmtHostel->close();
}

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
    $total_fee = $_POST['total_fee'];

    $full_name = $_POST['full_name'] ?? $userData['name'];
    $gender = $_POST['gender'] ?? $userData['gender'];
    $contact_no = $_POST['contact_no'] ?? $userData['phone'];
    $guardian_name = $_POST['guardian_name'] ?? '';
    $guardian_contact_no = $_POST['guardian_contact_no'] ?? '';
    $corr_address = $_POST['corr_address'] ?? '';
    $perm_address = $_POST['perm_address'] ?? '';

    $sql = "INSERT INTO bookings (
        seater, room_no, food_status, stay_from, stay_duration, fee_per_month, total_fee,
        full_name, gender, contact_no,
        guardian_name, guardian_contact_no, image,
        corr_address, perm_address, user_id, hostel_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($sql);
    $stmt->bind_param(
        "isssiddissssssiii",
        $seater, $room_no, $food_status, $stay_from, $stay_duration, $fee_per_month, $total_fee,
        $full_name, $gender, $contact_no,
        $guardian_name, $guardian_contact_no, $imagePath,
        $corr_address, $perm_address, $user_id, $hostel_id
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
    <form action="book.php?hostel_id=<?php echo htmlspecialchars($selectedHostelId); ?>" method="POST" enctype="multipart/form-data">
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
                            <label>Hostel</label>
                            <input type="text" value="<?php echo htmlspecialchars($hostelName); ?>" readonly class="form-control" />
                            <input type="hidden" name="hostel_id" value="<?php echo htmlspecialchars($selectedHostelId); ?>" />
                        </div>

                        <div class="form-group">
                            <label for="room_no">Room no.</label>
                            <select name="room_no" id="room_no" required>
                                <option value="">Select Room</option>
                            </select>
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
                            <input type="text" name="fee_per_month" id="fee_per_month" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="total_fee">Total Fee</label>
                            <input type="text" name="total_fee" id="total_fee" readonly>
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
                        <input type="text" name="contact_no" id="contact_no" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="guardian_name">Guardian Name</label>
                        <input type="text" name="guardian_name" id="guardian_name">
                    </div>
                    <div class="form-group">
                        <label for="guardian_contact_no">Guardian Contact No</label>
                        <input type="text" name="guardian_contact_no" id="guardian_contact_no">
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
                        <input type="text" name="city_corr" id="corr_city" placeholder="Enter your city">
                    </div>
                    <div class="form-group">
                        <label for="corr_district">District</label>
                        <input type="text" name="district_corr" id="corr_district" placeholder="Enter your district">
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Permanent Address</legend>
                    <div class="form-group">
                        <label for="perm_address">Address</label>
                        <input type="text" name="perm_address" id="perm_address">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    function loadRooms() {
        var seater = $("#seater").val();
        var hostel_id = $("input[name='hostel_id']").val();

        if (seater && hostel_id) {
            $.post("get_rooms.php", { seater: seater, hostel_id: hostel_id }, function (data) {
                $("#room_no").html(data);
                $("#fee_per_month").val('');
                $("#total_fee").val('');
            });
        } else {
            $("#room_no").html('<option value="">Select Room</option>');
            $("#fee_per_month").val('');
            $("#total_fee").val('');
        }
    }

    function calculateTotal() {
        var feePerMonth = parseFloat($("#fee_per_month").val()) || 0;
        var stayDuration = parseInt($("#stay_duration").val()) || 0;
        var foodStatus = $("input[name='food_status']:checked").val();

        var foodCostPerMonth = (foodStatus === "With Food") ? 2000 : 0;
        var totalFee = (feePerMonth + foodCostPerMonth) * stayDuration;

        $("#total_fee").val(totalFee > 0 ? totalFee.toFixed(2) : '');
    }

    $("#seater").change(function () {
        loadRooms();
    });

    $("#room_no").change(function () {
        var room_no = $(this).val();
        var hostel_id = $("input[name='hostel_id']").val();

        if (room_no && hostel_id) {
            $.post("get_rooms.php", { room_no: room_no, hostel_id: hostel_id }, function (fee) {
                $("#fee_per_month").val(fee);
                calculateTotal();
            });
        } else {
            $("#fee_per_month").val('');
            $("#total_fee").val('');
        }
    });

    $("#stay_duration, input[name='food_status']").change(function () {
        calculateTotal();
    });
});
</script>

</body>
</html>
