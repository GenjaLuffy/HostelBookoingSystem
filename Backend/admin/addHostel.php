<?php
session_start();
include 'includes/auth.php';
include './includes/connect.php';
include './includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $hostelName = trim($_POST['hostelName']);
  $description = trim($_POST['description']);
  $fee = floatval($_POST['fee']);
  $gender = $_POST['gender'];
  $location = trim($_POST['location']);
  $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
  $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;

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

  $stmt = $con->prepare("INSERT INTO hostels (name, image, image2, image3, image4, description, amenities, rules, fee, gender, location, latitude, longitude, created_by, created_by_role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $stmt->bind_param(
    "ssssssssdssddss",
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
    $latitude,
    $longitude,
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

<!-- HTML Form Starts -->
<main class="main-content">
  <h1>Add Hostel</h1>
  <div class="form-card">
    <div class="form-header">Add Hostel</div>
    <form id="hostelForm" method="POST" enctype="multipart/form-data">
      <label for="hostelName">Hostel Name:</label>
      <input type="text" id="hostelName" name="hostelName" required />

      <label>Select Images:</label>
      <label for="image" class="custom-file-upload">Upload Main Image</label>
      <input type="file" id="image" name="image" accept="image/*" style="display:none;" />
      <label for="image2" class="custom-file-upload">Upload Additional Image 2</label>
      <input type="file" id="image2" name="image2" accept="image/*" style="display:none;" />
      <label for="image3" class="custom-file-upload">Upload Additional Image 3</label>
      <input type="file" id="image3" name="image3" accept="image/*" style="display:none;" />
      <label for="image4" class="custom-file-upload">Upload Additional Image 4</label>
      <input type="file" id="image4" name="image4" accept="image/*" style="display:none;" />

      <label for="location">Location (Address):</label>
      <input type="text" id="location" name="location" required />

      <label>Choose Location on Map:</label>
      <div id="map" style="height: 300px; margin-bottom: 1rem;"></div>
      <input type="hidden" id="latitude" name="latitude" />
      <input type="hidden" id="longitude" name="longitude" />

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
        <label><input type="checkbox" name="rules[]" value="Arrival after 14:00, departure by 12:00" /> Enter Hostel by 9:00 pm</label>
        <label><input type="checkbox" name="rules[]" value="No moving or damaging furniture" /> Do not damage furniture</label>
        <label><input type="checkbox" name="rules[]" value="No guests inside rooms" /> No guests allowed inside rooms</label>
      </div>

      <label for="fee">Amount (Per Month):</label>
      <input type="number" id="fee" name="fee" required min="1000" step="0.01" />

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

<!-- Leaflet CSS + JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  const map = L.map('map').setView([27.7172, 85.3240], 13); // Default center: Kathmandu

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  const marker = L.marker([27.7172, 85.3240], { draggable: true }).addTo(map);

  function updateLatLng(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
  }

  updateLatLng(27.7172, 85.3240); // Set initial

  marker.on('dragend', function (e) {
    const latLng = e.target.getLatLng();
    updateLatLng(latLng.lat, latLng.lng);
  });
</script>


</body>
</html>