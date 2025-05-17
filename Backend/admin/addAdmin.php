<?php
include './includes/connect.php';
include './includes/sheader.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
  $name = $_POST['name'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $dob = $_POST['dob'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];
  $gender = $_POST['gender'];
  $type = $_POST['type'];

  // Handle profile picture
  $profile_picture = "";
  if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
      mkdir($targetDir, 0777, true);
    }
    $fileName = basename($_FILES["profile_picture"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
      $profile_picture = $targetFilePath;
    }
  }

  $stmt = $con->prepare("INSERT INTO admins (name, username, email, password, dob, phone, address, gender, profile_picture, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssssss", $name, $username, $email, $password, $dob, $phone, $address, $gender, $profile_picture, $type);

  if ($stmt->execute()) {
    echo "<script> alert('Admin added Successful !'); window.location.href = '../admin/index.php';</script>";
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
}
$con->close();
?>


    <!-- Main Content -->
    <main class="main-content">
      <h1>Add Owner</h1>
      <div class="form-card">
        <div class="form-header">Add Admin</div>

          <form action="addAdmin.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label>Name</label>
              <input type="text" name="name" required />
            </div>
            <div class="form-group">
              <label>Username</label>
              <input type="text" name="username" required />
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" required />
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="password" name="password" required />
            </div>
            <div class="form-group">
              <label>Date of Birth</label>
              <input type="date" name="dob" required />
            </div>
            <div class="form-group">
              <label>Phone</label>
              <input type="tel" name="phone" pattern="[0-9]{10}" maxlength="10" required />
            </div>
            <div class="form-group">
              <label>Address</label>
              <input type="text" name="address" required />
            </div>
            <div class="form-group">
              <label>Gender</label>
              <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label>Type</label>
              <select name="type" required>
                <option value="admin">Admin</option>
                <option value="superadmin">Superadmin</option>
              </select>
            </div>
            <div class="full-width">
              <button type="submit" name="submit">Add Admin</button>
            </div>
          </form>
      </div>
    </main>
  </div>

</body>

</html>