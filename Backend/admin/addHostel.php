<?php
include 'includes/auth.php';
include './includes/connect.php';
include './includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // your PHP form processing code unchanged...
  $hostelName = trim($_POST['hostelName']);
  $description = trim($_POST['description']);
  $fee = floatval($_POST['fee']);
  $gender = $_POST['gender'];
  $location = trim($_POST['location']);
  $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
  $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;

  $amenities = isset($_POST['amenities']) ? implode(",", $_POST['amenities']) : "";
  $rules = isset($_POST['rules']) ? implode(",", $_POST['rules']) : "";

  function uploadImage($fileKey) {
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === 0) {
      $imageName = basename($_FILES[$fileKey]['name']);
      $targetDir = 'uploads/';
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
      }
      $targetPath = $targetDir . time() . '' . uniqid() . '' . $imageName;
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Hostels</title>
    <link rel="stylesheet" href="assets/css/addHostel.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
      .image-upload-wrapper {
        display: flex;
        gap: 10px;
        margin-bottom: 1rem;
      }
      .image-upload-wrapper img {
        width: 120px;
        height: 90px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid #ccc;
        border-radius: 5px;
        transition: border-color 0.3s ease;
      }
      .image-upload-wrapper img:hover {
        border-color: #007bff;
      }
    </style>
</head>

<body>

<!-- HTML Form Starts -->
<main class="main-content">
  <h1>Add Hostel</h1>
  <div class="form-card">
    <div class="form-header">Add Hostel</div>
    <form id="hostelForm" method="POST" enctype="multipart/form-data">
      <label for="hostelName">Hostel Name:</label>
      <input type="text" id="hostelName" name="hostelName" required />

      <label>Select Images:</label>
      <div class="image-upload-wrapper">
        <img id="previewImage1" src="placeholder.png" alt="Main Image" title="Click to upload main image" />
        <img id="previewImage2" src="placeholder.png" alt="Image 2" title="Click to upload additional image 2" />
        <img id="previewImage3" src="placeholder.png" alt="Image 3" title="Click to upload additional image 3" />
        <img id="previewImage4" src="placeholder.png" alt="Image 4" title="Click to upload additional image 4" />
      </div>

      <input type="file" id="image" name="image" accept="image/*" style="display:none;" />
      <input type="file" id="image2" name="image2" accept="image/*" style="display:none;" />
      <input type="file" id="image3" name="image3" accept="image/*" style="display:none;" />
      <input type="file" id="image4" name="image4" accept="image/*" style="display:none;" />

      <label for="location">Location (Address):</label>
      <input type="text" id="location" name="location" required />
      
      <label for="locationSearch">Search Location:</label>
      <input type="text" id="locationSearch" placeholder="Search location..." autocomplete="off" />
      <div id="suggestions" style="background: white; border: 1px solid #ccc; max-height: 150px; overflow-y: auto; margin-bottom: 1rem;"></div>

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
        <label><input type="checkbox" name="amenities[]" value="cc-tv " /> CCTV</label>
        <label><input type="checkbox" name="amenities[]" value="water supply " /> Water Supply 24/7</label>
      </div>

      <label>Hostel Rules</label>
      <div class="rules">
        <label><input type="checkbox" name="rules[]" value="Arrival after 14:00, departure by 12:00" /> Enter Hostel by 9:00 pm</label>
        <label><input type="checkbox" name="rules[]" value="No moving or damaging furniture" /> Do not damage furniture</label>
        <label><input type="checkbox" name="rules[]" value="No guests inside rooms" /> No guests allowed inside rooms</label>
        <label><input type="checkbox" name="rules[]" value="Strictly no alcohol drugs cigarettes or illegal substances." /> Strictly no alcohol, drugs, cigarettes, or illegal substances.</label>
        <label><input type="checkbox" name="rules[]" value="Avoid loud noise; maintain silence during study and rest hours." /> Avoid loud noise; maintain silence during study and rest hours.</label>
        <label><input type="checkbox" name="rules[]" value="Visitors are allowed only during fixed hours with prior registration." /> Visitors are allowed only during fixed hours with prior registration.</label>
        <label><input type="checkbox" name="rules[]" value="Hostel is not responsible for personal items lock your room when leaving." /> Hostel is not responsible for personal items; lock your room when leaving.</label>
        <label><input type="checkbox" name="rules[]" value="Any misbehavior or rule violation may lead to disciplinary action." /> Any misbehavior or rule violation may lead to disciplinary action.</label>
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

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  const map = L.map('map').setView([27.7172, 85.3240], 13); // Default: Kathmandu

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  const marker = L.marker([27.7172, 85.3240], { draggable: true }).addTo(map);

  function updateLatLng(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
  }

  function updateLocationInput(address) {
    document.getElementById('location').value = address;
  }

  updateLatLng(27.7172, 85.3240); // initial values

  function onMarkerDrag() {
    marker.on('dragend', function (e) {
      const latLng = e.target.getLatLng();
      updateLatLng(latLng.lat, latLng.lng);

      // Reverse geocode to get address from lat,lng
      fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latLng.lat}&lon=${latLng.lng}`)
        .then(response => response.json())
        .then(data => {
          if (data && data.display_name) {
            updateLocationInput(data.display_name);
            document.getElementById('locationSearch').value = data.display_name; // optional: update search box too
          } else {
            updateLocationInput('Address not found');
          }
        })
        .catch(() => {
          updateLocationInput('Address not found');
        });
    });
  }
  onMarkerDrag();

  // SEARCH + AUTOCOMPLETE FUNCTIONALITY (DEBOUNCED + FIXED)
  const searchInput = document.getElementById('locationSearch');
  const suggestionsBox = document.getElementById('suggestions');
  let debounceTimeout;

  searchInput.addEventListener('input', function () {
    const query = this.value.trim();

    clearTimeout(debounceTimeout);

    if (query.length < 3) {
      suggestionsBox.innerHTML = '';
      return;
    }

    debounceTimeout = setTimeout(() => {
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=np`)
        .then(response => response.json())
        .then(results => {
          suggestionsBox.innerHTML = '';
          if (results.length === 0) {
            suggestionsBox.innerHTML = '<div style="padding:8px;">No results found</div>';
            return;
          }

          results.forEach(place => {
            const div = document.createElement('div');
            div.textContent = place.display_name;
            div.style.padding = '8px';
            div.style.cursor = 'pointer';
            div.style.borderBottom = '1px solid #eee';

            div.addEventListener('click', () => {
              const lat = parseFloat(place.lat);
              const lon = parseFloat(place.lon);

              marker.setLatLng([lat, lon]);
              updateLatLng(lat, lon);
              searchInput.value = place.display_name;
              updateLocationInput(place.display_name);
              map.setView([lat, lon], 15);
              suggestionsBox.innerHTML = '';

              // Remove old dragend event and bind new one (fix)
              marker.off('dragend');
              onMarkerDrag();
            });

            suggestionsBox.appendChild(div);
          });
        })
        .catch(error => {
          console.error('Error fetching location:', error);
          suggestionsBox.innerHTML = '<div style="padding:8px;">No results found</div>';
        });
    }, 500); // 500ms debounce
  });

  // IMAGE UPLOAD PREVIEW & CLICK TO SELECT
  function setupImageUpload(previewId, inputId) {
    const previewImg = document.getElementById(previewId);
    const fileInput = document.getElementById(inputId);

    previewImg.addEventListener('click', () => {
      fileInput.click();
    });

    fileInput.addEventListener('change', () => {
      const file = fileInput.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          previewImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  setupImageUpload('previewImage1', 'image');
  setupImageUpload('previewImage2', 'image2');
  setupImageUpload('previewImage3', 'image3');
  setupImageUpload('previewImage4', 'image4');
</script>
</body>
</html>
