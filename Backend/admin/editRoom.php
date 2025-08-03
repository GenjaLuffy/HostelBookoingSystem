<?php
session_start();
include 'includes/auth.php';
include './includes/connect.php';
include './includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Unauthorized access!'); window.location.href = '../login.php';</script>";
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid Room ID!'); window.location.href = 'manageR.php';</script>";
    exit;
}

$room_id = $_GET['id'];
$admin_id = $_SESSION['user_id'];

// Fetch room details
$stmt = $con->prepare("SELECT r.id, r.room_no, r.seater, r.fee_per_student, r.hostel_id 
                      FROM rooms r 
                      JOIN hostels h ON r.hostel_id = h.id 
                      WHERE r.id = ? AND h.created_by = ?");
$stmt->bind_param("ii", $room_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Room not found or unauthorized!'); window.location.href = 'manageR.php';</script>";
    exit;
}

$room = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seater = intval($_POST['seater']);
    $room_no = trim($_POST['room_no']);
    $fee = floatval($_POST['fee_per_student']);

    $updateStmt = $con->prepare("UPDATE rooms SET room_no = ?, seater = ?, fee_per_student = ? WHERE id = ?");
    $updateStmt->bind_param("sidi", $room_no, $seater, $fee, $room_id);

    if ($updateStmt->execute()) {
        echo "<script>alert('Room updated successfully!'); window.location.href = 'manageR.php';</script>";
        exit;
    } else {
        echo "<script>alert('Update failed: " . $updateStmt->error . "');</script>";
    }
}
?>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Rooms</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #f2e6ff;
  padding: 10px 30px;
}

.logo {
  font-size: 22px;
  max-height: fit-content;
  font-weight: bold;
  color: #8667F2;
  text-decoration: none;
  /* color: inherit; */
  font-family: Irish Grover;
  cursor: pointer; /* Makes it clear it's interactive */
  transition: color 0.3s ease, transform 0.3s ease;
}

.logo span {
  color: #c56cf0;
  transition: color 0.3s ease;
}

.logo:hover {
  color: #654ac6; /* Slightly darker or playful color */
  transform: scale(1.05); /* Slight grow effect */
}

.logo:hover span {
  color: #a05ce0;
}

nav a {
  margin: 0 10px;
  text-decoration: none;
  color: #333;
  transition: color 0.3s ease, border-bottom 0.3s ease;
  padding-bottom: 4px;
}

nav a:hover {
  color: #8667F2; /* Change to your theme color */
  border-bottom: 2px solid #8667F2; /* Optional underline effect */
}

nav a.active {
  color: #8667F2; /* Highlighted color */
  font-weight: bold;
}

.container {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 60px;
    background-color: #e8d6fb;
    padding-top: 20px;
    overflow-x: hidden;
    transition: width 0.3s ease;
}

.sidebar:hover {
    width: 200px;
}

.sidebar nav {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 0 10px;
}

.sidebar nav a {
    display: flex;
    align-items: center;
    color: rgb(61, 61, 61);
    padding: 12px;
    text-decoration: none;
    font-size: 14px;
    border-radius: 6px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    white-space: nowrap;
}

.sidebar nav a i {
    font-size: 16px;
    min-width: 20px;
    text-align: center;
}

.sidebar nav a span {
    margin-left: 10px;
    display: none;
}

.sidebar:hover nav a span {
    display: inline;
}

.sidebar nav a:hover {
    background-color: #a05ce0;
    transform: scale(1.05);
}
/* Main content */
.main-content {
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
  max-width: 550px;
  margin: 40px auto;
}



/* Form Card */
.form-card {
  max-width: 480px;
  margin: 0 auto;
}

form label {
  display: block;
  margin-bottom: 6px;
  font-weight: 600;
  color: #444;
}

form input[type="text"],
form input[type="number"],
form select {
  width: 90%;
  padding: 10px 14px;
  margin-bottom: 20px;
  border: 1.8px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

form input[type="text"]:focus,
form input[type="number"]:focus,
form select:focus {
  border-color: #8667f2;
  outline: none;
}

.btn-submit {
  width: 97%;
  background-color: #8667f2;
  color: white;
  font-weight: 700;
  font-size: 18px;
  padding: 14px 0;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-top: 40px;
}


.btn-submit:hover {
  background-color: #654ac6;
}

/* Responsive */
@media (max-width: 600px) {
  .main-content {
    padding: 20px;
  }

  header {
    flex-direction: column;
    gap: 10px;
  }

  nav a {
    margin-left: 0;
    margin-right: 15px;
  }
}

    </style>
</head>
<main class="main-content">
    
    <div class="form-card">
        <h1>Edit Room</h1>
        <form method="POST">
            <label for="seater">Seater:</label>
            <input type="number" id="seater" name="seater" required min="1" value="<?= htmlspecialchars($room['seater']); ?>" />

            <label for="room_no">Room Number:</label>
            <input type="text" id="room_no" name="room_no" required value="<?= htmlspecialchars($room['room_no']); ?>" />

            <label for="fee_per_student">Fee (Per Student):</label>
            <input type="number" id="fee_per_student" name="fee_per_student" required min="1000" step="0.01" value="<?= htmlspecialchars($room['fee_per_student']); ?>" />

            <button type="submit" class="btn-submit">Update Room</button>
        </form>
    </div>
</main>
</body>
</html>
