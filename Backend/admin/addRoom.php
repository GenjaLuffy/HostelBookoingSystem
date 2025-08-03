<?php
session_start();
include 'includes/auth.php';
include './includes/connect.php';
include './includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "<script>alert('Unauthorized access.'); window.location.href='login.php';</script>";
    exit;
}

$admin_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if (!isset($_GET['hostel_id'])) {
    echo "<script>alert('No hostel selected.'); window.location.href='manageHostel.php';</script>";
    exit;
}

$hostel_id = intval($_GET['hostel_id']);

$stmt = $con->prepare("SELECT id FROM hostels WHERE id = ? AND created_by = ? AND created_by_role = ? LIMIT 1");
$stmt->bind_param("iis", $hostel_id, $admin_id, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Invalid hostel or you are not the owner.'); window.location.href='manageHostel.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_no = trim($_POST["room_no"]);
    $seater = intval($_POST["seater"]);
    $fee = floatval($_POST["fee"]);

    $insert = $con->prepare("INSERT INTO rooms (room_no, seater, fee_per_student, admin_id, hostel_id) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("siddi", $room_no, $seater, $fee, $admin_id, $hostel_id);

    if ($insert->execute()) {
        echo "<script>alert('Room added successfully.'); window.location.href='manageR.php';</script>";
    } else {
        echo "<script>alert('Failed to add room.');</script>";
    }

    $insert->close();
}

$stmt->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Room</title>
    
</head>
<style>
    /* General Reset */
body {
  margin: 0;
  padding: 0;
  font-family: "Segoe UI", Arial, sans-serif;
  background-color: #f8f4ff;
}

/* Main container */
.main-content {
  display: flex;
  justify-content: center;
  align-items: flex-start; /* align content to top */
  min-height: 100vh;
  padding: 30px 10px 10px 10px; /* top padding = 30px */
}


/* Card container */
.form-card {
  background-color: #ffffff;
  padding: 40px 30px;
  border-radius: 15px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 450px;
}


/* Title */
.form-card h1 {
  font-size: 26px;
  margin-bottom: 25px;
  text-align: center;
  color: #5a41c6;
}

form label {
  display: block;
  font-weight: 600;
  color: #444;
  font-size: 14px;
  margin-top: 20px; /* adds 20px gap above every label */
}

form input[type="text"],
form input[type="number"],
form select {
  width: 100%;
  padding: 14px 16px;
  margin-top: 8px; /* slight gap below label */
  border: 1.8px solid #ccc;
  border-radius: 12px;
  font-size: 15px;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
  box-sizing: border-box;
}

form input:focus,
form select:focus {
  border-color: #8667f2;
  box-shadow: 0 0 0 3px rgba(134, 103, 242, 0.2);
  outline: none;
}

.btn-submit {
  margin-top: 30px;
  width: 100%;
  background-color: #8667f2;
  color: white;
  font-weight: bold;
  font-size: 16px;
  padding: 14px;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-submit:hover {
  background-color: #654ac6;
  transform: translateY(-2px);
}


/* Responsive Design */
@media (max-width: 500px) {
  .form-card {
    padding: 30px 20px;
  }

  .form-card h1 {
    font-size: 22px;
  }

  form input,
  form select {
    font-size: 14px;
  }

  .btn-submit {
    font-size: 15px;
    padding: 12px;
  }
}

</style>
<body>
    <div class="main-content">
    <div class="form-card">
            <h1>Add Room</h1>
        <form method="POST" action="addRoom.php?hostel_id=<?= htmlspecialchars($_GET['hostel_id']) ?>">
            <label for="roomNo">Room No.</label>
            <input type="text" id="roomNo" name="room_no" placeholder="Enter Room Number" required />

            <label for="seater">Select Seater</label>
            <select id="seater" name="seater" required>
                <option value="" disabled selected>-- Select --</option>
                <option value="1">1 Seater</option>
                <option value="2">2 Seater</option>
                <option value="3">3 Seater</option>
                <option value="4">4 Seater</option>
            </select>

            <label for="fee">Fee Per Month</label>
            <input
                type="number"
                id="fee"
                name="fee"
                placeholder="Enter Monthly Fee"
                min="1000"
                step="0.01"
                required />

            <button type="submit" class="btn-submit">Create Room</button>
        </form>
    </div>
    </div>
        </div>
</body>

</html>