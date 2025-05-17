<?php
include './includes/connect.php';
include './includes/header.php';
session_start();
include 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $hostelName = trim($_POST['hostelName']);
  $description = trim($_POST['description']);
  $fee = floatval($_POST['fee']);
  $gender = $_POST['gender'];
  $location = trim($_POST['location']);

  $amenities = isset($_POST['amenities']) ? implode(",", $_POST['amenities']) : "";
  $rules = isset($_POST['rules']) ? implode(",", $_POST['rules']) : "";


  function uploadImage($fileKey)
  {
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === 0) {
      $imageName = basename($_FILES[$fileKey]['name']);
      $targetDir = 'uploads/';
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
      }
      $targetPath = $targetDir . time() . '_' . uniqid() . '_' . $imageName;
      if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
        return $targetPath;
      }
    }
    return '';
  }

  $image1 = uploadImage('image');
  $image2 = uploadImage('image2');
  $image3 = uploadImage('image3');
  $image4 = uploadImage('image4');

  $created_by = $_SESSION['user_id'];
  $created_by_role = $_SESSION['user_role'] ?? 'admin';

  $stmt = $con->prepare("INSERT INTO hostels (name, image, image2, image3, image4, description, amenities, rules, fee, gender, location, created_by, created_by_role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $stmt->bind_param(
    "ssssssssdssis",
    $hostelName,
    $image1,
    $image2,
    $image3,
    $image4,
    $description,
    $amenities,
    $rules,
    $fee,
    $gender,
    $location,
    $created_by,
    $created_by_role
  );


  if ($stmt->execute()) {
    echo "<script>alert('Hostel added successfully!'); window.location.href = 'addHostel.php';</script>";
    exit;
  } else {
    echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    exit;
  }

  $stmt->close();
  $con->close();
}
?>



<main class="main-content">
  <h1>Add Hostel</h1>
  <div class="form-card">
    <div class="form-header">Add Hostel</div>
    <form id="hostelForm" method="POST" enctype="multipart/form-data">
      <label for="hostelName">Hostel Name:</label>
      <input type="text" id="hostelName" name="hostelName" required />

      <label for="hostelName">Select Images:</label>
      <label for="image" class="custom-file-upload"><i class="fas fa-upload"></i> Upload Main Image</label>
      <input type="file" id="image" name="image" accept="image/*" style="display:none;" />

      <label for="image2" class="custom-file-upload"><i class="fas fa-upload"></i> Upload Additional Image 2</label>
      <input type="file" id="image2" name="image2" accept="image/*" style="display:none;" />

      <label for="image3" class="custom-file-upload"><i class="fas fa-upload"></i> Upload Additional Image 3</label>
      <input type="file" id="image3" name="image3" accept="image/*" style="display:none;" />

      <label for="image4" class="custom-file-upload"><i class="fas fa-upload"></i> Upload Additional Image 4</label>
      <input type="file" id="image4" name="image4" accept="image/*" style="display:none;" />

      <label for="location">Location:</label>
      <input type="text" id="location" name="location" required />

      <label for="description">Description:</label>
      <textarea id="description" name="description" rows="3"></textarea>

      <label>Room Amenities</label>
      <div class="checkbox-group">
        <label><input type="checkbox" name="amenities[]" value="Large bed" /> Large bed</label>
        <label><input type="checkbox" name="amenities[]" value="Storage of luggage" /> Storage of luggage</label>
        <label><input type="checkbox" name="amenities[]" value="Dry cleaning" /> Dry cleaning</label>
        <label><input type="checkbox" name="amenities[]" value="Attached bathrooms" /> Attached bathrooms</label>
        <label><input type="checkbox" name="amenities[]" value="Shared kitchen" /> Shared kitchen</label>
        <label><input type="checkbox" name="amenities[]" value="Free high speed WiFi" /> Free high speed WiFi</label>
        <label><input type="checkbox" name="amenities[]" value="Parking" /> Parking</label>
        <label><input type="checkbox" name="amenities[]" value="Air conditioning/24/7 support" /> Air conditioning 24/7 support</label>
      </div>

      <label>Hostel Rules</label>
      <div class="rules">
        <label><input type="checkbox" name="rules[]" value="Arrival after 14:00, departure by 12:00" /> Enter Hostel permises by 9:00 pm </label>
        <label><input type="checkbox" name="rules[]" value="No moving or damaging furniture" /> Furniture and fittings should not be moved or damaged.</label>
        <label><input type="checkbox" name="rules[]" value="No guests inside rooms" /> No guests or outsiders are allowed inside the hostel rooms.</label>
      </div>

      <label for="fee">Amount (Per Month):</label>
      <input type="number" id="fee" name="fee" required min="5000" step="0.01" />

      <label for="gender">Gender</label>
      <select id="gender" name="gender" required>
        <option value="">-- Select --</option>
        <option value="Boys Hostel">Boys Hostel</option>
        <option value="Girls Hostel">Girls Hostel</option>
        <option value="Other">Other</option>
      </select>

      <button type="submit">Add Hostel</button>
    </form>
  </div>
</main>
</div>
</body>

</html>